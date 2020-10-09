<?php

function azm_user_nonce($action, $id) {
    if ($id) {
        $hash = get_user_meta($id, 'azm-hash', true);
        if (!$hash) {
            $hash = uniqid();
            update_user_meta($id, 'azm-hash', $hash);
        }
        return substr(wp_hash($action . '|' . $id . '|' . $hash, 'nonce'), -12, 10);
    }
    return '';
}

function azm_lead_nonce($action, $id) {
    if ($id) {
        $hash = get_post_meta($id, '_hash', true);
        return substr(wp_hash($action . '|' . $id . '|' . $hash, 'nonce'), -12, 10);
    }
    return '';
}

add_action('phpmailer_init', 'azm_phpmailer_init');

function azm_phpmailer_init($phpmailer) {
    $phpmailer->Sender = $phpmailer->From;
}

function azm_get_lead_visitor_id($lead) {
    if (get_class($lead) == 'WP_User') {
        return get_user_meta($lead->ID, 'azr-visitor', true);
    } else {
        return get_post_meta($lead->ID, '_azr_visitor', true);
    }
    return false;
}

function azm_send_email($action, $lead, $context = array()) {
    $lead_id = false;
    $lead_meta = false;
    if (is_object($lead)) {
        if (get_class($lead) == 'WP_User') {
            $lead_meta = get_user_meta($lead->ID);
        } else {
            $lead_meta = get_post_meta($lead->ID);
        }
        $lead_id = $lead->ID;
        foreach ($lead_meta as $key => $value) {
            $lead_meta[$key] = reset($value);
        }
    }

    $email_field = $context['email_field'];

    $from_email = $action['from_email'];
    $from_name = $action['from_name'];
    $reply_to = $action['reply_to'];
    $unsubscribe_page_id = $action['unsubscribe_page'];
    $email_template = $action['email_template'];
    if (!isset($action['email_template']) || $action['email_template'] === false) {
        return false;
    }

    $unsubscribe_url = '';
    if (is_object($lead)) {
        if (get_class($lead) == 'WP_User') {
            $unsubscribe_url = add_query_arg(array('unsubscribe' => azm_user_nonce('azm-unsubscribe', $lead_id), 'user_id' => $lead_id, 'campaign' => $context['rule']), get_permalink($unsubscribe_page_id));
        } else {
            $unsubscribe_url = add_query_arg(array('unsubscribe' => azm_lead_nonce('azm-unsubscribe', $lead_id), 'lead_id' => $lead_id, 'campaign' => $context['rule']), get_permalink($unsubscribe_page_id));
        }
    }

    //prevent doubles
    if (is_object($lead) && get_class($lead) == 'WP_Post' && isset($lead_meta[$email_field])) {
        $leads = get_posts(array(
            'post_type' => isset($context['email_subscription_post_type']) ? $context['email_subscription_post_type'] : 'azf_submission',
            'post_status' => 'any',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => $email_field,
                    'value' => $lead_meta[$email_field]
                ),
                array(
                    'key' => '_email_campaign_' . $context['rule'],
                    'value' => 'sent'
                ),
            )
        ));
        if (!empty($leads)) {
            update_post_meta($lead->ID, '_email_campaign_' . $context['rule'], 'sent');
            return true;
        }
    }

    if (is_numeric($email_template) && (int) $email_template > 0) {
        $email_template = get_post($email_template);
        $subject = $email_template->post_title;

        $styles = false;
        $stylesheets = false;
        static $styles_cache = array();
        if (isset($styles_cache[$email_template->ID])) {
            $styles = $styles_cache[$email_template->ID];
        }
        static $stylesheets_cache = array();
        if (isset($stylesheets_cache[$email_template->ID])) {
            $stylesheets = $stylesheets_cache[$email_template->ID];
        }
        if ((!$styles || !$stylesheets) && function_exists('azh_filesystem')) {
            azh_filesystem();
            global $wp_filesystem;
            $styles = get_post_meta($email_template->ID, '_styles', true);
            if ($styles) {
                $styles = $wp_filesystem->get_contents($styles);
                $styles_cache[$email_template->ID] = $styles;
            }
            $stylesheets = get_post_meta($email_template->ID, '_stylesheets', true);
            if ($stylesheets) {
                $stylesheets = $wp_filesystem->get_contents($stylesheets);
                $stylesheets_cache[$email_template->ID] = $stylesheets;
            }
        }
        $content = $email_template->post_content;
        if (get_post_meta($email_template->ID, 'azh', true) && function_exists('azh_get_post_content')) {
            $content = azh_get_post_content($email_template);
        }
    } else if (is_numeric($email_template) && (int) $email_template == 0) {
        $content = base64_decode($action['email_body'], ENT_QUOTES);
        $subject = $action['email_subject'];
    } else {
        $content = base64_decode($email_template, ENT_QUOTES);
        $subject = $action['email_subject'];
    }

    $content = apply_filters('azm_send_email_template', $content, $action, $context, $lead, $lead_meta);

    if (is_object($lead)) {
        $url_params = array(
            'campaign' => $context['rule'],
        );
        if (get_class($lead) == 'WP_User') {
            $url_params['user_id'] = $lead->ID;
            $url_params['click'] = azm_user_nonce('azm-click', $lead->ID);
        } else {
            $url_params['lead_id'] = $lead->ID;
            $url_params['click'] = azm_lead_nonce('azm-click', $lead->ID);
        }
        $url_params = apply_filters('azr_action_url_params', $url_params, $action, $context);
        $click_query = array();
        foreach ($url_params as $name => $value) {
            $click_query[] = "$name=$value";
        }
        $click_query = implode('&', $click_query);
        $content = str_replace('click=click', $click_query, $content);
    }

    if ($lead_meta) {
        foreach ($lead_meta as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
    }
    if (!empty($context)) {
        $content = azm_tokens($content, $context);
    }
    if ($lead_meta) {
        //submission
        if (isset($lead_meta['_azr_visitor'])) {
            $content = azr_visitor_tokens($content, $lead_meta['_azr_visitor']);
        }
        //user
        if (isset($lead_meta['azr-visitor'])) {
            $content = azr_visitor_tokens($content, $lead_meta['azr-visitor']);
        }
    }
    $content = do_shortcode($content);

    $unsubscribe = true;
    if (strpos($content, '{unsubscribe}') !== false) {
        $content = str_replace('{unsubscribe}', $unsubscribe_url, $content);
        $unsubscribe = false;
    }
    if (strpos($content, 'sr_unsubscribe') !== false) {
        $content = str_replace('sr_unsubscribe', $unsubscribe_url, $content); //StampReady
        $unsubscribe = false;
    }

    $headers = array();

    if (is_object($email_template)) {
        $tracking_url = '';
        if (is_object($lead)) {
            if (get_class($lead) == 'WP_User') {
                $tracking_url = add_query_arg(array('open' => azm_user_nonce('azm-open', $lead_id), 'user_id' => $lead_id, 'campaign' => $context['rule']), home_url());
            } else {
                $tracking_url = add_query_arg(array('open' => azm_lead_nonce('azm-open', $lead_id), 'lead_id' => $lead_id, 'campaign' => $context['rule']), home_url());
            }
        }
        $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
                . '<html xmlns="http://www.w3.org/1999/xhtml">'
                . '<head>'
                . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'
                . '<meta name="viewport" content="width=device-width" />'
                . '<title>' . $subject . '</title>'
                . $stylesheets
                . '<style type="text/css">'
                . $styles
                . '</style>'
                . '</head>'
                . '<body>'
                . $content
                . ($unsubscribe ? ('<a href="' . $unsubscribe_url . '">' . __('Unsubscribe', 'azm') . '</a>') : '')
                . '<img src="' . $tracking_url . '" alt="" width="1" height="1">'
                . '</body>'
                . '</html>';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
    } else if (is_numeric($email_template) && (int) $email_template == 0) {
        $message = $content;
        $headers[] = 'Content-type: text/html; charset=UTF-8';
    } else {
        $message = $content;
        $headers[] = 'Content-type: text/plain; charset=UTF-8';
    }

    if ($from_email) {
        if ($from_name) {
            $headers[] = 'From: "' . $from_name . '" <' . $from_email . '>';
        } else {
            $headers[] = 'From: ' . $from_email;
        }
    }
    if ($reply_to) {
        $headers[] = 'Reply-to: ' . $reply_to;
    } else {
        $headers[] = 'Reply-to: ' . $from_email;
    }
    $bounce_to = false;
    $bouncing_settings = get_option('azh-bouncing-settings');
    if (isset($bouncing_settings['bounce-email'])) {
        $bounce_to = $bouncing_settings['bounce-email'];
    }
    if ($bounce_to) {
        $headers[] = 'Return-path: <' . $bounce_to . '>';
    } else if ($reply_to) {
        $headers[] = 'Return-path: <' . $reply_to . '>';
    } else {
        $headers[] = 'Return-path: <' . $from_email . '>';
    }
    $headers[] = 'Precedence: bulk';
    $time = time();
    if (is_object($lead)) {
        if (get_class($lead) == 'WP_User') {
            $headers[] = 'X-Message-ID: ' . get_user_meta($lead_id, 'azm-hash', true);
            //$headers[] = 'List-unsubscribe: <mailto:' . $reply_to . '?subject=' . __('Unsubscribe', 'azm') . '>, <' . add_query_arg(array('unsubscribe' => azm_user_nonce('azm-unsubscribe', $lead_id), 'user_id' => $lead_id, 'campaign' => $context['rule']), get_permalink($unsubscribe_page_id)) . '>';
            $headers[] = 'List-unsubscribe: <' . add_query_arg(array('unsubscribe' => azm_user_nonce('azm-unsubscribe', $lead_id), 'user_id' => $lead_id, 'campaign' => $context['rule']), get_permalink($unsubscribe_page_id)) . '>';
            if (wp_mail($lead->data->user_email, $subject, $message, $headers)) {
                update_user_meta($lead_id, '_email_campaign_' . $context['rule'], 'sent');
                update_user_meta($lead_id, '_email_campaign_' . $context['rule'] . '_sent', $time);
                do_action('azm_email_campaign_user_sent', $lead_id, $context['rule'], $time);
                azr_counter_increment($context['rule'], '_email_sent');
                return true;
            } else {
                update_user_meta($lead_id, '_email_campaign_' . $context['rule'], 'failed');
                do_action('azm_email_campaign_user_failed', $lead_id, $context['rule'], $time);
                azr_counter_increment($context['rule'], '_email_failed');
            }
        } else {
            $headers[] = 'X-AZEXO-Campaign: ' . $context['rule'];
            $headers[] = 'X-AZEXO-Lead: ' . $lead_id;
            $headers[] = 'X-Message-ID: ' . get_post_meta($lead_id, '_hash', true);
            $headers[] = 'List-unsubscribe: <mailto:' . $reply_to . '?subject=' . __('Unsubscribe', 'azm') . '>, <' . add_query_arg(array('unsubscribe' => azm_lead_nonce('azm-unsubscribe', $lead_id), 'lead_id' => $lead_id, 'campaign' => $context['rule']), get_permalink($unsubscribe_page_id)) . '>';
            if (!empty($lead_meta[$email_field])) {
                if (wp_mail($lead_meta[$email_field], $subject, $message, $headers)) {
                    update_post_meta($lead_id, '_email_campaign_' . $context['rule'], 'sent');
                    $time = time();
                    update_post_meta($lead_id, '_email_campaign_' . $context['rule'] . '_sent', $time);
                    do_action('azm_email_campaign_lead_sent', $lead_id, $context['rule'], $time);
                    azr_counter_increment($context['rule'], '_email_sent');
                    return true;
                } else {
                    update_post_meta($lead_id, '_email_campaign_' . $context['rule'], 'failed');
                    do_action('azm_email_campaign_lead_failed', $lead_id, $context['rule'], $time);
                    azr_counter_increment($context['rule'], '_email_failed');
                }
            }
        }
    } else {
        if (is_string($lead)) {
            return wp_mail($lead, $subject, $message, $headers);
        }
    }
    return false;
}

add_action('init', 'azm_open');

function azm_open() {
    if (isset($_GET['open']) && isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
        if (isset($_GET['lead_id']) && is_numeric($_GET['lead_id'])) {
            if ($_GET['open'] == azm_lead_nonce('azm-open', (int) $_GET['lead_id'])) {
                if (!get_post_meta((int) $_GET['lead_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_open', true)) {
                    $time = time();
                    update_post_meta((int) $_GET['lead_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_open', $time);
                    do_action('azm_email_campaign_lead_open', (int) $_GET['lead_id'], (int) $_GET['campaign'], $time);
                    if (isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
                        azr_counter_increment((int) $_GET['campaign'], '_email_open');
                    }
                }

                header('Content-Type: image/png');
                print base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
                exit;
            }
        }
        if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
            if ($_GET['open'] == azm_user_nonce('azm-open', (int) $_GET['user_id'])) {
                if (!get_user_meta((int) $_GET['user_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_open', true)) {
                    $time = time();
                    update_user_meta((int) $_GET['user_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_open', $time);
                    do_action('azm_email_campaign_user_open', (int) $_GET['user_id'], (int) $_GET['campaign'], $time);
                    if (isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
                        azr_counter_increment((int) $_GET['campaign'], '_email_open');
                    }
                }

                header('Content-Type: image/png');
                print base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
                exit;
            }
        }
    }
}

add_action('init', 'azm_click');

function azm_click() {
    if (isset($_GET['click']) && isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
        if (isset($_GET['lead_id']) && is_numeric($_GET['lead_id'])) {
            if ($_GET['click'] == azm_lead_nonce('azm-click', (int) $_GET['lead_id'])) {
                azr_check_submission_visitor_id((int) $_GET['lead_id']);
                if (!get_post_meta((int) $_GET['lead_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_click', true)) {
                    $time = time();
                    update_post_meta((int) $_GET['lead_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_click', $time);
                    do_action('azm_email_campaign_lead_click', (int) $_GET['lead_id'], (int) $_GET['campaign'], $time);
                    if (isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
                        azr_counter_increment((int) $_GET['campaign'], '_email_clicks');
                    }

                    if (isset($_GET['redirect'])) {
                        exit(wp_redirect($_GET['redirect']));
                    }
                }
            }
        }
        if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
            if ($_GET['click'] == azm_user_nonce('azm-click', (int) $_GET['user_id'])) {
                if (!get_user_meta((int) $_GET['user_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_click', true)) {
                    $time = time();
                    update_user_meta((int) $_GET['user_id'], '_email_campaign_' . (int) $_GET['campaign'] . '_click', $time);
                    do_action('azm_email_campaign_user_click', (int) $_GET['user_id'], (int) $_GET['campaign'], $time);
                    if (isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
                        azr_counter_increment((int) $_GET['campaign'], '_email_clicks');
                    }

                    if (isset($_GET['redirect'])) {
                        exit(wp_redirect($_GET['redirect']));
                    }
                }
            }
        }
    }
}

add_action('init', 'azm_unsubscribe');

function azm_unsubscribe() {
    if (isset($_GET['unsubscribe'])) {
        if (isset($_GET['lead_id']) && is_numeric($_GET['lead_id'])) {
            if ($_GET['unsubscribe'] == azm_lead_nonce('azm-unsubscribe', (int) $_GET['lead_id'])) {
                azr_check_submission_visitor_id((int) $_GET['lead_id']);
                if (!get_post_meta((int) $_GET['lead_id'], '_unsubscribed', true)) {
                    $time = time();
                    update_post_meta((int) $_GET['lead_id'], '_unsubscribed', $time);
                    do_action('azm_lead_unsubscribe', (int) $_GET['lead_id'], (int) $_GET['campaign'], $time);
                    if (isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
                        azr_counter_increment((int) $_GET['campaign'], '_email_unsubscribes');
                    }
                }
            }
        }
        if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
            if ($_GET['unsubscribe'] == azm_user_nonce('azm-unsubscribe', (int) $_GET['user_id'])) {
                if (!get_user_meta((int) $_GET['user_id'], '_unsubscribed', true)) {
                    $time = time();
                    update_user_meta((int) $_GET['user_id'], '_unsubscribed', $time);
                    do_action('azm_user_unsubscribe', (int) $_GET['user_id'], (int) $_GET['campaign'], $time);
                    if (isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
                        azr_counter_increment((int) $_GET['campaign'], '_email_unsubscribes');
                    }
                }
            }
        }
    }
}

add_action('wp_mail_failed', 'azm_mail_failed');

function azm_mail_failed($error) {
    global $azm_mail_failed;
    $azm_mail_failed = $error->get_error_message();
}

add_action('wp_ajax_azm_email_sending_test', 'azm_email_sending_test');

function azm_email_sending_test() {
    if (isset($_POST['parameters'])) {
        $action = wp_unslash(sanitize_text_field($_POST['parameters']));
        $action = json_decode($action, true);
        if (azm_send_email($action, get_bloginfo('admin_email'))) {
            print __('Test email was sent to: ' . get_bloginfo('admin_email'), 'azm');
        } else {
            global $azm_mail_failed;
            if ($azm_mail_failed) {
                print $azm_mail_failed;
            }
        }
    }
    wp_die();
}

function azm_dec($encoded) {
    $decoded = "";
    $strlen = strlen($encoded);
    for ($i = 0; $i < strlen($encoded); $i++) {
        $b = ord($encoded[$i]);
        $a = $b ^ 7;
        $decoded .= chr($a);
    }
    return $decoded;
}

add_filter('azr_settings', 'azm_email_settings');

function azm_email_settings($azr) {
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => -1,
        'numberposts' => -1,
    ));
    $pages_options = array();
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $pages_options[$page->ID] = $page->post_title;
        }
    }
    $templates = get_posts(array(
        'post_type' => 'azm_email_template',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => -1,
        'numberposts' => -1
    ));
    $templates_options = array(0 => __('Use editor below', 'azm'));
    if (!empty($templates)) {
        foreach ($templates as $template) {
            $templates_options[$template->ID] = $template->post_title;
        }
    }


    $azr['actions']['send_text_email'] = array(
        'name' => __('Send text email', 'azm'),
        'group' => __('Send', 'azm'),
        'required_context' => array('visitors', 'email_campaign', 'email_subscription_form', 'email_field'),
        'condition_dependency' => array('is_registered_user', 'email_subscription'),
        'parameters' => array(
            'from_email' => array(
                'type' => 'email',
                'label' => __('From email', 'azm'),
                'required' => true,
            ),
            'from_name' => array(
                'type' => 'text',
                'label' => __('From name', 'azm'),
                'required' => true,
            ),
            'reply_to' => array(
                'type' => 'email',
                'label' => __('Reply to', 'azm'),
                'required' => true,
            ),
            'unsubscribe_page' => array(
                'type' => 'dropdown',
                'label' => __('Unsubscribe page', 'azm'),
                'required' => true,
                'no_options' => __('Please create a page', 'azm'),
                'options' => $pages_options,
            ),
            'email_subject' => array(
                'type' => 'text',
                'label' => __('Email subject', 'azm'),
                'required' => true,
            ),
            'email_template' => array(
                'type' => 'textarea',
                'label' => __('Email template', 'azm'),
                'required' => true,
            ),
            'batch_size' => array(
                'type' => 'number',
                'label' => __('Emails number per batch', 'azm'),
                'required' => true,
                'default' => '5',
                'event_dependency' => array('scheduler', 'new_post'),
            ),
            'batch_delay' => array(
                'type' => 'number',
                'label' => __('Delay between batches (seconds)', 'azm'),
                'required' => true,
                'default' => '60',
                'event_dependency' => array('scheduler', 'new_post'),
            ),
            'batch_sleep' => array(
                'type' => 'number',
                'label' => __('Delay between every email send (milliseconds)', 'azm'),
                'required' => true,
                'default' => '100',
                'event_dependency' => array('scheduler', 'new_post'),
            ),
        ),
    );
    $azr['actions']['send_html_email'] = array(
        'name' => __('Send HTML email', 'azm'),
        'group' => __('Send', 'azm'),
        'required_context' => array('visitors', 'email_campaign', 'email_subscription_form', 'email_field'),
        'condition_dependency' => array('is_registered_user', 'email_subscription'),
        'parameters' => array(
            'from_email' => array(
                'type' => 'email',
                'label' => __('From email', 'azm'),
                'required' => true,
            ),
            'from_name' => array(
                'type' => 'text',
                'label' => __('From name', 'azm'),
                'required' => true,
            ),
            'reply_to' => array(
                'type' => 'email',
                'label' => __('Reply to', 'azm'),
                'required' => true,
            ),
            'unsubscribe_page' => array(
                'type' => 'dropdown',
                'label' => __('Unsubscribe page', 'azm'),
                'required' => true,
                'no_options' => __('Please create a page', 'azm'),
                'options' => $pages_options,
            ),
            'email_template' => array(
                'type' => 'dropdown',
                'label' => __('Email template', 'azm'),
                'required' => true,
                'no_options' => sprintf(__('Please <a href="%s">create a email template</a> via AZEXO Builder', 'azm'), admin_url('post-new.php?post_type=azm_email_template')),
                'options' => $templates_options,
                'default' => '0',
            ),
            'email_subject' => array(
                'type' => 'text',
                'label' => __('Email subject', 'azm'),
                'dependencies' => array(
                    'email_template' => array('0'),
                ),
            ),
            'email_body' => array(
                'type' => 'richtext',
                'label' => __('Email body', 'azm'),
                'dependencies' => array(
                    'email_template' => array('0'),
                ),
            ),
            'batch_size' => array(
                'type' => 'number',
                'label' => __('Emails number per batch', 'azm'),
                'required' => true,
                'default' => '5',
                'event_dependency' => array('scheduler', 'new_post'),
            ),
            'batch_delay' => array(
                'type' => 'number',
                'label' => __('Delay between batches (seconds)', 'azm'),
                'required' => true,
                'default' => '60',
                'event_dependency' => array('scheduler', 'new_post'),
            ),
            'batch_sleep' => array(
                'type' => 'number',
                'label' => __('Delay between every email send (milliseconds)', 'azm'),
                'required' => true,
                'default' => '100',
                'event_dependency' => array('scheduler', 'new_post'),
            ),
        ),
    );
    return $azr;
}

add_filter('azr_process_action', 'azm_process_email_sending', 10, 2);

function azm_process_email_sending($context, $action) {
    switch ($action['type']) {
        case 'send_text_email':
        case 'send_html_email':
            if (isset($context['visitors'])) {
                global $wpdb;
                if (!empty($context['visitor_id'])) {
                    update_post_meta($context['rule'], '_email_receivers', __('Unknown', 'azm'));

                    $db_query = azr_get_db_query($context['visitors']);
                    $visitors = $wpdb->get_results($db_query, ARRAY_A);
                    azm_send_emails($visitors, $action, $context);
                    azr_visitors_prcessed($context['rule'], count($visitors));
                } else {
                    $num_rows = $wpdb->get_var(azr_get_count_db_query($context['visitors']));
                    update_post_meta($context['rule'], '_email_receivers', $num_rows);

                    if ($action['batch_size']) {
                        $batches_number = floor($num_rows / $action['batch_size']);
                        if ($num_rows % $action['batch_size']) {
                            $batches_number = $batches_number + 1;
                        }
                        for ($i = 0; $i < $batches_number; $i++) {
                            wp_schedule_single_event(time() + $i * $action['batch_delay'], 'azm_send_emails_process', array(
                                'action' => $action,
                                'context' => $context,
                                'offset' => $i * $action['batch_size'],
                            ));
                        }
                        azr_visitors_prcessed($context['rule'], $num_rows);
                    }
                }
                azr_action_executed($context['rule']);
            }
            break;
    }
    return $context;
}

function azm_send_emails($visitors, $action, $context) {
    if (isset($context['email_subscription_form']) && isset($context['email_field'])) {
        foreach ($visitors as $visitor) {
            $leads = get_posts(
                    array(
                        'post_type' => 'azf_submission',
                        'post_status' => 'any',
                        'ignore_sticky_posts' => 1,
                        'offset' => 0,
                        'posts_per_page' => 1,
                        'meta_query' => array(
                            array(
                                'key' => '_azr_visitor',
                                'value' => $visitor['visitor_id'],
                            ),
                            array(
                                'key' => 'form_title',
                                'value' => $context['email_subscription_form'],
                            ),
                            array(
                                'key' => $context['email_field'],
                                'compare' => 'EXISTS'
                            ),
                        )
                    )
            );
            if (!empty($leads)) {
                $lead = reset($leads);
                azm_send_email($action, $lead, $context);
                usleep($action['batch_sleep'] * 1000);
            }
        }
    }
    if (isset($context['email_subscription_post_type']) && isset($context['email_field'])) {
        foreach ($visitors as $visitor) {
            $leads = get_posts(
                    array(
                        'post_type' => $context['email_subscription_post_type'],
                        'post_status' => 'any',
                        'ignore_sticky_posts' => 1,
                        'offset' => 0,
                        'posts_per_page' => 1,
                        'meta_query' => array(
                            array(
                                'key' => '_azr_visitor',
                                'value' => $visitor['visitor_id'],
                            ),
                            array(
                                'key' => $context['email_field'],
                                'compare' => 'EXISTS'
                            ),
                        )
                    )
            );
            if (!empty($leads)) {
                $lead = reset($leads);
                azm_send_email($action, $lead, $context);
                usleep($action['batch_sleep'] * 1000);
            }
        }
    }
    if (isset($context['registered'])) {
        $users = array_map(function($value) {
            return $value['user_id'];
        }, $visitors);
        $users = array_filter($users);
        $users = array_unique($users);
        foreach ($users as $user_id) {
            $userdata = get_userdata($user_id);
            azm_send_email($action, $userdata, $context);
            usleep($action['batch_sleep'] * 1000);
        }
    }
}

add_action('azm_send_emails_process', 'azm_send_emails_process', 10, 3);

function azm_send_emails_process($action, $context, $offset) {
    if (azr_is_run($context['rule'])) {
        global $wpdb;
        $db_query = azr_get_db_query($context['visitors']);
        $db_query = $db_query . ' LIMIT ' . $offset . ',' . $action['batch_size'];
        $visitors = $wpdb->get_results($db_query, ARRAY_A);
        azm_send_emails($visitors, $action, $context);
    }
}

add_filter('azr_get_action_results', 'azm_email_results', 10, 3);

function azm_email_results($results, $action, $rule_id) {
    switch ($action['type']) {
        case 'send_text_email':
        case 'send_html_email':
            $results .= '<div>' . __('Email receivers', 'azm') . ': ' . (int) get_post_meta($rule_id, '_email_receivers', true) . '</div>';
            $results .= '<div>' . __('Email sent', 'azm') . ': ' . (int) get_post_meta($rule_id, '_email_sent', true) . '</div>';
            $results .= '<div>' . __('Email open', 'azm') . ': ' . (int) get_post_meta($rule_id, '_email_open', true) . '</div>';
            $results .= '<div>' . __('Email clicks', 'azm') . ': ' . (int) get_post_meta($rule_id, '_email_clicks', true) . '</div>';
            $results .= '<div>' . __('Email unsubscribes', 'azm') . ': ' . (int) get_post_meta($rule_id, '_email_unsubscribes', true) . '</div>';
            $results .= '<div>' . __('Email bounces', 'azm') . ': ' . (int) get_post_meta($rule_id, '_email_bounces', true) . '</div>';
            $results .= '<div>' . __('Email failed', 'azm') . ': ' . (int) get_post_meta($rule_id, '_email_failed', true) . '</div>';
            break;
    }
    return $results;
}

add_action('wp_insert_post', 'azm_email_campaign_insert_post', 10, 3);

function azm_email_campaign_insert_post($post_id, $post, $update) {
    if ($post->post_type == 'azr_rule') {
        $rule = get_post_meta($post->ID, '_rule', true);
        $rule = json_decode($rule, true);

        $context = azr_prepare_context_by_event($rule);
        if (isset($context['visitors'])) {
            $context = azr_process_conditions($rule, $context);
            if (empty($context['visitor_id'])) {
                global $wpdb;
                $db_query = azr_get_count_db_query($context['visitors']);
                $num_rows = $wpdb->get_var($db_query);
                update_post_meta($post->ID, '_email_receivers', $num_rows);
            } else {
                update_post_meta($post->ID, '_email_receivers', __('Unknown', 'azm'));
            }
            $sent = 0;
            $open = 0;
            $clicks = 0;
            $unsubscribes = 0;
            $failed = 0;
            if (isset($context['registered'])) {
                $sent = $sent + azm_get_users(array(array('key' => '_email_campaign_' . $post->ID, 'value' => 'sent')))->total_users;
                $open = $open + azm_get_users(array(array('key' => '_email_campaign_' . $post->ID . '_open', 'compare' => 'EXISTS')))->total_users;
                $clicks = $clicks + azm_get_users(array(array('key' => '_email_campaign_' . $post->ID . '_click', 'compare' => 'EXISTS')))->total_users;
                $unsubscribes = $unsubscribes + azm_get_users(array(array('key' => '_unsubscribed', 'compare' => 'EXISTS')))->total_users;
                $failed = $failed + azm_get_users(array(array('key' => '_email_campaign_' . $post->ID, 'value' => 'failed')))->total_users;                
            }
            if (isset($context['email_subscription_form']) && isset($context['email_field'])) {
                $sent = $sent + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID, 'value' => 'sent')))->found_posts;
                $open = $open + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID . '_open', 'compare' => 'EXISTS')))->found_posts;
                $clicks = $clicks + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID . '_click', 'compare' => 'EXISTS')))->found_posts;
                $unsubscribes = $unsubscribes + azm_get_email_receivers($context, array(array('key' => '_unsubscribed', 'compare' => 'EXISTS')))->found_posts;
                $failed = $failed + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID, 'value' => 'failed')))->found_posts;
            }
            if (isset($context['email_subscription_post_type']) && isset($context['email_field'])) {
                $sent = $sent + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID, 'value' => 'sent')))->found_posts;
                $open = $open + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID . '_open', 'compare' => 'EXISTS')))->found_posts;
                $clicks = $clicks + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID . '_click', 'compare' => 'EXISTS')))->found_posts;
                $unsubscribes = $unsubscribes + azm_get_email_receivers($context, array(array('key' => '_unsubscribed', 'compare' => 'EXISTS')))->found_posts;
                $failed = $failed + azm_get_email_receivers($context, array(array('key' => '_email_campaign_' . $post->ID, 'value' => 'failed')))->found_posts;
            }
            update_post_meta($post->ID, '_email_sent', $sent);
            update_post_meta($post->ID, '_email_open', $open);
            update_post_meta($post->ID, '_email_clicks', $clicks);
            update_post_meta($post->ID, '_email_unsubscribes', $unsubscribes);
            update_post_meta($post->ID, '_email_failed', $failed);
        }
    }
}

function azm_get_users($meta_query = array()) {
    $args = array(
        'meta_query' => array(
        )
    );
    foreach ($meta_query as $mq) {
        $args['meta_query'][] = $mq;
    }

    $query = new WP_User_Query($args);

    return $query;
}

function azm_get_email_receivers($context, $meta_query = array()) {
    $args = array(
        'post_type' => isset($context['email_subscription_post_type']) ? $context['email_subscription_post_type'] : 'azf_submission',
        'post_status' => 'any',
        'ignore_sticky_posts' => 1,
        'offset' => 0,
        'posts_per_page' => 10,
        'meta_query' => array(
            array(
                'key' => $context['email_field'],
                'compare' => 'EXISTS'
            ),
        )
    );
    if (isset($context['email_subscription_form'])) {
        $args['meta_query'][] = array(
            'key' => 'form_title',
            'value' => (array) $context['email_subscription_form'],
            'compare' => 'IN'
        );
    }
    foreach ($meta_query as $mq) {
        $args['meta_query'][] = $mq;
    }

    $query = new WP_Query($args);

    return $query;
}

add_filter('azm_activity_history', 'azm_email_activity_history', 10, 2);

function azm_email_activity_history($activity_history, $post) {
    global $wpdb;
    $visitor_id = $wpdb->get_var("SELECT visitor_id FROM {$wpdb->prefix}azr_visitor_posts WHERE post_id = " . $post->ID);
    if ($visitor_id) {
        $posts = $wpdb->get_col("SELECT post_id FROM {$wpdb->prefix}azr_visitor_posts WHERE visitor_id = '" . $visitor_id . "'");
        foreach ($posts as $post_id) {
            $meta = get_post_meta($post_id);
            foreach ($meta as $key => $value) {
                $v = reset($value);
                preg_match('/_email_campaign_(\d+)_sent/', $key, $matches);
                if (!empty($matches)) {
                    $activity_history[$v] = esc_html__('Email campaign', 'azm') . ' <a href="' . get_edit_post_link($matches[1]) . '">' . get_the_title($matches[1]) . '</a> ' . esc_html__('was sent', 'azm');
                }
                preg_match('/_email_campaign_(\d+)_open/', $key, $matches);
                if (!empty($matches)) {
                    $activity_history[$v] = esc_html__('Email campaign', 'azm') . ' <a href="' . get_edit_post_link($matches[1]) . '">' . get_the_title($matches[1]) . '</a> ' . esc_html__('was opened', 'azm');
                }
                preg_match('/_email_campaign_(\d+)_click/', $key, $matches);
                if (!empty($matches)) {
                    $activity_history[$v] = esc_html__('Email campaign', 'azm') . ' <a href="' . get_edit_post_link($matches[1]) . '">' . get_the_title($matches[1]) . '</a> ' . esc_html__('was clicked', 'azm');
                }
            }
        }
    }
    return $activity_history;
}

add_shortcode('azm_debug_backtrace', 'azm_debug_backtrace');

function azm_debug_backtrace($atts, $content = null, $tag = null) {
    return print_r(debug_backtrace(), true);
}

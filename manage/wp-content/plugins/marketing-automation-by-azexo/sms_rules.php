<?php
add_filter('azr_settings', 'azm_sms_settings');

function azm_sms_settings($azr) {
    $gmt_offset = get_option('gmt_offset') * HOUR_IN_SECONDS;
    $azr['actions']['send_sms'] = array(
        'name' => __('Send SMS', 'azm'),
        'group' => __('Send', 'azm'),
        'required_context' => array('visitors', 'phone_subscription_form', 'phone_field'),
        'condition_dependency' => array('phone_subscription'),
        'parameters' => array(
            'sms_template' => array(
                'type' => 'textarea',
                'label' => __('SMS template (shortcodes supported)', 'azm'),
                'required' => true,
            ),
            'batch_size' => array(
                'type' => 'number',
                'label' => __('SMS number per batch', 'azm'),
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
                'label' => __('Delay between every SMS send (milliseconds)', 'azm'),
                'required' => true,
                'default' => '100',
                'event_dependency' => array('scheduler', 'new_post'),
            ),
        ),
    );
    global $wpdb;
    $campaigns = get_posts(array(
        'post_type' => 'azr_rule',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => -1,
        'numberposts' => -1,
    ));
    $campaign_options = array();
    if (!empty($campaigns)) {
        foreach ($campaigns as $campaign) {
            $campaign_options[$campaign->ID] = $campaign->post_title;
        }
    }


    $azr['conditions']['sms_campaign_status'] = array(
        'name' => __('SMS campaign status', 'azm'),
        'group' => __('SMS campaign', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'campaign' => array(
                'type' => 'dropdown',
                'label' => __('Campaign name', 'azm'),
                'required' => true,
                'no_options' => sprintf(__('Please <a href="%s">create a campaign</a>', 'azm'), admin_url('post-new.php?post_type=azr_rule')),
                'options' => $campaign_options,
            ),
            'status' => array(
                'type' => 'dropdown',
                'label' => __('Status', 'azm'),
                'required' => true,
                'options' => array(
                    'clicked' => __('Clicked', 'azm'),
//                    'did_not_clicked' => __('Did not clicked', 'azm'),
                    'was_sent' => __('Was sent', 'azm'),
//                    'was_not_sent' => __('Was not sent', 'azm'),
                ),
                'where_clauses' => array(
                    'clicked' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_click' AND ec.meta_value IS NOT NULL) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_click' AND ec.meta_value IS NOT NULL)",
//                    'did_not_clicked' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id AND ec.meta_key = '_sms_campaign_{campaign}_click' WHERE ec.meta_value IS NULL AND fs.meta_key = '_azr_visitor') OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id AND ec.meta_key = '_sms_campaign_{campaign}_click' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}})",
                    'was_sent' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_sent' AND ec.meta_value IS NOT NULL)",
//                    'was_not_sent' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id AND ec.meta_key = '_sms_campaign_{campaign}_sent' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}})",
                ),
                'default' => 'opened',
            ),
        ),
    );
    $azr['conditions']['sms_campaign_sent_date'] = array(
        'name' => __('SMS campaign sent date', 'azm'),
        'group' => __('SMS campaign', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'campaign' => array(
                'type' => 'dropdown',
                'label' => __('Campaign name', 'azm'),
                'required' => true,
                'no_options' => sprintf(__('Please <a href="%s">create a campaign</a>', 'azm'), admin_url('post-new.php?post_type=azr_rule')),
                'options' => $campaign_options,
            ),
            'sent' => array(
                'type' => 'dropdown',
                'label' => __('Sent date', 'azm'),
                'required' => true,
                'options' => array(
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'where_clauses' => array(
                    'is_after' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_before' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_sent' AND CAST(ec.meta_value AS DECIMAL(10, 0)) >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                    'is_not_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_sent' AND CAST(ec.meta_value AS DECIMAL(10, 0)) < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                ),
                'default' => 'is_before',
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'sent' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'sent' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );

    $azr['conditions']['sms_campaign_cliked_date'] = array(
        'name' => __('SMS campaign clicked date', 'azm'),
        'group' => __('SMS campaign', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'campaign' => array(
                'type' => 'dropdown',
                'label' => __('Campaign name', 'azm'),
                'required' => true,
                'no_options' => sprintf(__('Please <a href="%s">create a campaign</a>', 'azm'), admin_url('post-new.php?post_type=azr_rule')),
                'options' => $campaign_options,
            ),
            'clicked' => array(
                'type' => 'dropdown',
                'label' => __('Clicked date', 'azm'),
                'required' => true,
                'options' => array(
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'where_clauses' => array(
                    'is_after' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_before' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_click' AND CAST(ec.meta_value AS DECIMAL(10, 0)) >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                    'is_not_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_sms_campaign_{campaign}_click' AND CAST(ec.meta_value AS DECIMAL(10, 0)) < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                ),
                'default' => 'is_before',
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'clicked' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'clicked' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );

    return $azr;
}

add_filter('azr_process_action', 'azm_sms_process_action', 10, 2);

function azm_sms_process_action($context, $action) {
    switch ($action['type']) {
        case 'send_sms':
            if (isset($context['visitors']) && isset($context['phone_subscription_form']) && isset($context['phone_field'])) {
                global $wpdb;
                if (!empty($context['visitor_id'])) {
                    update_post_meta($context['rule'], '_sms_receivers', __('Unknown', 'azm'));

                    $db_query = azr_get_db_query($context['visitors']);
                    $visitors = $wpdb->get_results($db_query, ARRAY_A);
                    $visitors = array_map(function($value) {
                        return $value['visitor_id'];
                    }, $visitors);
                    azm_send_sms_campaign($visitors, $action, $context);
                    azr_visitors_prcessed($context['rule'], count($visitors));
                } else {
                    $num_rows = $wpdb->get_var(azr_get_count_db_query($context['visitors']));
                    update_post_meta($context['rule'], '_sms_receivers', $num_rows);

                    $batches_number = floor($num_rows / $action['batch_size']);
                    if ($num_rows % $action['batch_size']) {
                        $batches_number = $batches_number + 1;
                    }
                    for ($i = 0; $i < $batches_number; $i++) {
                        wp_schedule_single_event(time() + $i * $action['batch_delay'], 'azm_send_sms_process', array(
                            'action' => $action,
                            'context' => $context,
                            'offset' => $i * $action['batch_size'],
                        ));
                    }
                    azr_visitors_prcessed($context['rule'], $num_rows);
                }
                azr_action_executed($context['rule']);
            }
            break;
    }
    return $context;
}

add_action('azm_send_sms_process', 'azm_send_sms_process', 10, 3);

function azm_send_sms_process($action, $context, $offset) {
    if (azr_is_run($context['rule'])) {
        global $wpdb;
        $db_query = azr_get_db_query($context['visitors']);
        $db_query = $db_query . ' LIMIT ' . $offset . ',' . $action['batch_size'];
        $visitors = $wpdb->get_results($db_query, ARRAY_A);
        $visitors = array_map(function($value) {
            return $value['visitor_id'];
        }, $visitors);
        azm_send_sms_campaign($visitors, $action, $context);
    }
}

add_filter('azr_get_action_results', 'azm_sms_results', 10, 3);

function azm_sms_results($results, $action, $rule_id) {
    switch ($action['type']) {
        case 'send_sms':
            $results .= '<div>' . __('SMS receivers', 'azm') . ': ' . (int) get_post_meta($rule_id, '_sms_receivers', true) . '</div>';
            $results .= '<div>' . __('SMS sent', 'azm') . ': ' . (int) get_post_meta($rule_id, '_sms_sent', true) . '</div>';
            $results .= '<div>' . __('SMS clicks', 'azm') . ': ' . (int) get_post_meta($rule_id, '_sms_clicks', true) . '</div>';
            $results .= '<div>' . __('SMS failed', 'azm') . ': ' . (int) get_post_meta($rule_id, '_sms_failed', true) . '</div>';
            break;
    }
    return $results;
}

function azm_send_sms_campaign($visitors, $action, $context) {
    foreach ($visitors as $visitor_id) {
        $leads = get_posts(
                array(
                    'post_type' => 'azf_submission',
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'offset' => 0,
                    'posts_per_page' => 1,
                    'meta_query' => array(
                        array(
                            'key' => '_azr_visitor',
                            'value' => $visitor_id,
                        ),
                        array(
                            'key' => 'form_title',
                            'value' => $context['phone_subscription_form'],
                        ),
                        array(
                            'key' => $context['phone_field'],
                            'compare' => 'EXISTS'
                        ),
                    )
                )
        );
        if (!empty($leads)) {
            $lead = reset($leads);
            azm_send_sms($action, $lead, $context);
            usleep($action['batch_sleep'] * 1000);
        }
    }
}

function azm_send_sms($action, $lead, $context = array()) {
    $settings = get_option('azh-sms-settings');

    $lead_id = false;
    $lead_meta = false;
    $lead_id = $lead->ID;
    $lead_meta = get_post_meta($lead->ID);
    foreach ($lead_meta as $key => $value) {
        $lead_meta[$key] = reset($value);
    }
    $campaign = get_post($context['rule']);

    $phone_field = $context['phone_field'];

    $message = base64_decode($action['sms_template'], ENT_QUOTES);
    if (!$message) {
        return false;
    }


    //prevent doubles
    if (isset($lead_meta[$phone_field])) {
        $leads = get_posts(array(
            'post_type' => 'azf_submission',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => $phone_field,
                    'value' => $lead_meta[$phone_field]
                ),
                array(
                    'key' => '_sms_campaign_' . $campaign->ID,
                    'value' => 'sent'
                ),
            )
        ));
        if (!empty($leads)) {
            update_post_meta($lead->ID, '_sms_campaign_' . $campaign->ID, 'sent');
            return true;
        }
    }

    $url_params = array(
        'campaign' => $context['rule'],
        'lead_id' => $lead_id,
        'click' => azm_lead_nonce('azm-sms-click', $lead_id),
    );
    $url_params = apply_filters('azr_action_url_params', $url_params, $action, $context);
    $regex = '"\b(https?://\S+)"';
    $message = preg_replace_callback($regex, function( $url ) use($url_params, $settings) {
        $new_url = add_query_arg($url_params, $url[0]);
        if (!empty($settings['google-api-key'])) {
            $new_url = azm_url_shorten($new_url, $settings['google-api-key']);
        }
        return $new_url;
    }, $message);


    foreach ($lead_meta as $key => $value) {
        $message = str_replace('{' . $key . '}', $value, $message);
    }
    if (!empty($context)) {
        $message = azm_tokens($message, $context);
    }
    if (isset($lead_meta['_azr_visitor']) && function_exists('azr_visitor_tokens')) {
        $message = azr_visitor_tokens($message, $lead_meta['_azr_visitor']);
    }
    $message = do_shortcode($message);

    $time = time();
    if (!empty($lead_meta[$phone_field])) {
        $result = azm_send_twilio_sms($lead_meta[$phone_field], $message);
        if (is_wp_error($result)) {
            update_post_meta($lead->ID, '_sms_campaign_' . $campaign->ID, 'failed');
            do_action('azm_sms_campaign_lead_failed', $lead->ID, $campaign->ID, $time);
            azr_counter_increment($campaign->ID, '_sms_failed');
        } else {
            update_post_meta($lead->ID, '_sms_campaign_' . $campaign->ID, 'sent');
            update_post_meta($lead->ID, '_sms_campaign_' . $campaign->ID . '_sent', $time);
            do_action('azm_sms_campaign_lead_sent', $lead->ID, $campaign->ID, $time);
            azr_counter_increment($campaign->ID, '_sms_sent');
            return true;
        }
    } else {
        update_post_meta($lead->ID, '_sms_campaign_' . $campaign->ID, 'failed');
        do_action('azm_sms_campaign_lead_failed', $lead->ID, $campaign->ID, $time);
        azr_counter_increment($campaign->ID, '_sms_failed');
    }
    return false;
}

add_action('init', 'azm_sms_click');

function azm_sms_click() {
    if (isset($_GET['click']) && isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
        if (isset($_GET['lead_id']) && is_numeric($_GET['lead_id'])) {
            if ($_GET['click'] == azm_lead_nonce('azm-sms-click', (int) $_GET['lead_id'])) {
                azr_check_submission_visitor_id((int) $_GET['lead_id']);
                if (!get_post_meta((int) $_GET['lead_id'], '_sms_campaign_' . (int) $_GET['campaign'] . '_click', true)) {
                    $time = time();
                    update_post_meta((int) $_GET['lead_id'], '_sms_campaign_' . (int) $_GET['campaign'] . '_click', $time);
                    do_action('azm_sms_campaign_lead_click', (int) $_GET['lead_id'], (int) $_GET['campaign'], $time);
                    if (isset($_GET['campaign']) && is_numeric($_GET['campaign'])) {
                        azr_counter_increment((int) $_GET['campaign'], '_sms_clicks');
                    }
                    if (isset($_GET['redirect'])) {
                        exit(wp_redirect($_GET['redirect']));
                    }
                }
            }
        }
    }
}

add_action('wp_ajax_azm_sms_sending_test', 'azm_sms_sending_test');

function azm_sms_sending_test() {
    if (isset($_POST['parameters']) && isset($_POST['phone'])) {
        $action = wp_unslash(sanitize_text_field($_POST['parameters']));
        $action = json_decode($action, true);
        $message = base64_decode($action['sms_template'], ENT_QUOTES);
        $result = azm_send_twilio_sms(sanitize_text_field($_POST['phone']), $message);
        if (!is_wp_error($result)) {
            print __('Test SMS was sent', 'azm');
        } else {
            print $result->get_error_message();
        }
    }
    wp_die();
}

add_action('wp_insert_post', 'azm_sms_campaign_insert_post', 10, 3);

function azm_sms_campaign_insert_post($post_id, $post, $update) {
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
                update_post_meta($post->ID, '_sms_receivers', $num_rows);
            } else {
                update_post_meta($post->ID, '_sms_receivers', __('Unknown', 'azm'));
            }
            if (isset($context['phone_subscription_form']) && isset($context['phone_field'])) {
                $sent = azm_get_sms_receivers($context, array(array('key' => '_sms_campaign_' . $post->ID, 'value' => 'sent')))->found_posts;
                update_post_meta($post->ID, '_sms_sent', $sent);
                $clicks = azm_get_sms_receivers($context, array(array('key' => '_sms_campaign_' . $post->ID . '_click', 'compare' => 'EXISTS')))->found_posts;
                update_post_meta($post->ID, '_sms_clicks', $clicks);
                $failed = azm_get_sms_receivers($context, array(array('key' => '_sms_campaign_' . $post->ID, 'value' => 'failed')))->found_posts;
                update_post_meta($post->ID, '_sms_failed', $failed);
            }
        }
    }
}

function azm_get_sms_receivers($context, $meta_query = array()) {
    $args = array(
        'post_type' => 'azf_submission',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'offset' => 0,
        'posts_per_page' => 10,
        'meta_query' => array(
            array(
                'key' => $context['phone_field'],
                'compare' => 'EXISTS'
            )
        )
    );
    $args['meta_query'][] = array(
        'key' => 'form_title',
        'value' => (array) $context['phone_subscription_form'],
        'compare' => 'IN'
    );
    foreach ($meta_query as $mq) {
        $args['meta_query'][] = $mq;
    }

    $query = new WP_Query($args);

    return $query;
}

add_action('admin_menu', 'azm_sms_admin_menu', 13);

function azm_sms_admin_menu() {
    add_submenu_page('edit.php?post_type=azr_rule', __('SMS settings', 'azm'), __('SMS settings', 'azm'), 'edit_pages', 'azh-sms-settings', 'azm_sms_page');
}

function azm_sms_page() {
    ?>

    <div class="wrap">
        <h2><?php _e('SMS settings', 'azm'); ?></h2>

        <form method="post" action="options.php" class="azh-form">
            <?php
            settings_errors();
            settings_fields('azh-sms-settings');
            do_settings_sections('azh-sms-settings');
            submit_button(__('Save Settings', 'azm'));
            ?>
        </form>
    </div>

    <?php
}

function azm_general_options_callback() {
    
}

add_action('admin_init', 'azm_sms_options');

function azm_sms_options() {
    register_setting('azh-sms-settings', 'azh-sms-settings', array('sanitize_callback' => 'azh_settings_sanitize_callback'));
    add_settings_section(
            'azh_sms_section', // Section ID
            esc_html__('SMS settings', 'azm'), // Title above settings section
            'azm_general_options_callback', // Name of function that renders a description of the settings section
            'azh-sms-settings'                     // Page to show on
    );
    add_settings_field(
            'google-api-key', // Field ID
            esc_html__('Google API Key', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-sms-settings', // Page to show on
            'azh_sms_section', // Associate with which settings section?
            array(
        'id' => 'google-api-key',
        'desc' => esc_html__('For URL shortener', 'azm'),
            )
    );
}

add_filter('azm_activity_history', 'azm_sms_activity_history', 10, 2);

function azm_sms_activity_history($activity_history, $post) {
    global $wpdb;
    $visitor_id = $wpdb->get_var("SELECT visitor_id FROM {$wpdb->prefix}azr_visitor_posts WHERE post_id = " . $post->ID);
    if ($visitor_id) {
        $posts = $wpdb->get_col("SELECT post_id FROM {$wpdb->prefix}azr_visitor_posts WHERE visitor_id = '" . $visitor_id . "'");
        foreach ($posts as $post_id) {
            $meta = get_post_meta($post_id);
            foreach ($meta as $key => $value) {
                $v = reset($value);
                preg_match('/_sms_campaign_(\d+)_sent/', $key, $matches);
                if (!empty($matches)) {
                    $activity_history[$v] = esc_html__('SMS campaign', 'azm') . ' <a href="' . get_edit_post_link($matches[1]) . '">' . get_the_title($matches[1]) . '</a> ' . esc_html__('was sent', 'azm');
                }
                preg_match('/_sms_campaign_(\d+)_click/', $key, $matches);
                if (!empty($matches)) {
                    $activity_history[$v] = esc_html__('SMS campaign', 'azm') . ' <a href="' . get_edit_post_link($matches[1]) . '">' . get_the_title($matches[1]) . '</a> ' . esc_html__('was clicked', 'azm');
                }
            }
        }
    }
    return $activity_history;
}

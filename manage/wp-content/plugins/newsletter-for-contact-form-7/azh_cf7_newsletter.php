<?php

/*
  Plugin Name: Newsletter for Contact Form 7
  Description: Contact Form 7 integration with Marketing Automation by AZEXO
  Author: azexo
  Author URI: http://azexo.com
  Version: 1.27.1
  Text Domain: azm
 */

add_action('plugins_loaded', 'azm_cf7_plugins_loaded');

function azm_cf7_plugins_loaded() {
    load_plugin_textdomain('azm', FALSE, basename(dirname(__FILE__)) . '/languages/');
}

add_action('admin_notices', 'azm_cf7_admin_notices');

function azm_cf7_admin_notices() {
    if (!defined('AZM_VERSION')) {
        $plugin_data = get_plugin_data(__FILE__);
        print '<div class="updated notice error is-dismissible"><p>' . $plugin_data['Name'] . ': ' . __('please install <a href="https://codecanyon.net/item/marketing-automation-by-azexo/21402648">Marketing Automation by AZEXO</a> plugin.', 'azm') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'azm') . '</span></button></div>';
    }
}

add_action('init', 'azm_cf7_init');

function azm_cf7_init() {
    register_post_type('azf_submission', array(
        'labels' => array(
            'name' => __('Submission', 'azm'),
            'singular_name' => __('Submission', 'azm'),
            'add_new' => __('Add Submission', 'azm'),
            'add_new_item' => __('Add New Submission', 'azm'),
            'edit_item' => __('Edit Submission', 'azm'),
            'new_item' => __('New Submission', 'azm'),
            'view_item' => __('View Submission', 'azm'),
            'search_items' => __('Search Submissions', 'azm'),
            'not_found' => __('No Submission found', 'azm'),
            'not_found_in_trash' => __('No Submission found in Trash', 'azm'),
            'parent_item_colon' => __('Parent Submission:', 'azm'),
            'menu_name' => __('Forms Submissions', 'azm'),
        ),
        'query_var' => false,
        'rewrite' => false,
        'hierarchical' => true,
        'supports' => array('title', 'custom-fields', 'author', 'comments'),
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'public' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    ));
}

add_filter('default_hidden_meta_boxes', 'azm_cf7_default_hidden_meta_boxes', 10, 2);

function azm_cf7_default_hidden_meta_boxes($hidden, $screen) {
    global $post;
    if ($post && $post->post_type === 'azf_submission') {
        $i = array_search('postcustom', $hidden);
        unset($hidden[$i]);
    }
    return $hidden;
}

function azm_cf7_files() {
    $files = array();
    if (!empty($_FILES)) {
        $upload_overrides = array('test_form' => false);
        if (!function_exists('wp_handle_upload')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        foreach ($_FILES as $name => $file) {
            $attachments = array();
            if (is_array($file['name'])) {
                foreach ($file['name'] as $key => $value) {
                    if ($file['name'][$key]) {
                        $attachments[] = array(
                            'name' => $file['name'][$key],
                            'type' => $file['type'][$key],
                            'tmp_name' => $file['tmp_name'][$key],
                            'error' => $file['error'][$key],
                            'size' => $file['size'][$key]
                        );
                    }
                }
            } else {
                $attachments[] = $file;
            }
            $files[$name] = array();
            foreach ($attachments as $attachment) {
                $upload = wp_handle_upload($attachment, $upload_overrides);
                if ($upload && !isset($upload['error'])) {
                    $files[$name][] = $upload['file'];
                }
            }
        }
    }
    return $files;
}

add_filter('azr_forms', 'azm_cf7_forms');

function azm_cf7_forms($forms) {
    if (!(isset($_GET['page']) && $_GET['page'] == 'wpcf7')) {
        if (function_exists('wpcf7_contact_form')) {
            $cf7_forms = get_posts(array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1));
            foreach ($cf7_forms as $cf7_form) {
                $contact_form = wpcf7_contact_form($cf7_form);
                $fields = array();
                $mail_tags = $contact_form->collect_mail_tags();
                foreach ($mail_tags as $mail_tag) {
                    $fields[$mail_tag] = array(
                        'label' => $mail_tag,
                    );
                }
                $forms[$cf7_form->post_title] = $fields;
            }
        }
    }
    return $forms;
}

function azm_cf7_visitor_post($submission_id) {
    global $wpdb;
    $visitor_id = azr_get_current_visitor();
    $user_id = false;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $wpdb->query("REPLACE INTO {$wpdb->prefix}azr_visitor_posts (post_id, visitor_id, user_id) VALUES (" . $submission_id . ", '$visitor_id', " . ($user_id ? $user_id : 'NULL') . ")");
    update_post_meta($submission_id, '_azr_visitor', $visitor_id);
}

function azm_cf7_form_submit($form_title, $fields, $files = array(), $post_id = NULL) {
    $submission_id = wp_insert_post(array(
        'post_title' => $form_title,
        'post_type' => 'azf_submission',
        'post_status' => 'publish',
        'post_parent' => $post_id,
            ), true);
    if (!is_wp_error($submission_id)) {
        update_post_meta($submission_id, '_hash', uniqid());
        update_post_meta($submission_id, 'form_title', $form_title);
        foreach ($fields as $name => $value) {
            if (is_array($fields[$name])) {
                update_post_meta($submission_id, trim($name), sanitize_text_field(trim(implode(', ', $value))));
            } else {
                update_post_meta($submission_id, trim($name), sanitize_text_field(trim($value)));
            }
            if (isset($files[trim($name)])) {
                update_post_meta($submission_id, trim($name), implode(', ', $files[trim($name)]));
            }
        }
        return $submission_id;
    }
}

add_action('wpcf7_before_send_mail', 'azm_cf7_before_send_email');

function azm_cf7_before_send_email($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $cf7d_no_save_fields = array('_wpcf7', '_wpcf7_container_post', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_is_ajax_call');
        $posted_data = $submission->get_posted_data();
        $fields = array();
        foreach ($posted_data as $name => $value) {
            if (in_array($name, $cf7d_no_save_fields)) {
                continue;
            } else {
                if (is_array($value)) {
                    $value = implode("\n", $value);
                }
                $name = htmlspecialchars($name);
                $value = htmlspecialchars($value);
                $fields[$name] = $value;
            }
        }
        $container_post_id = (isset($posted_data['_wpcf7_container_post']) ? $posted_data['_wpcf7_container_post'] : NULL);

        $submission_id = azm_cf7_form_submit($contact_form->title(), $fields, $submission->uploaded_files(), $contact_form->id());
        azm_cf7_visitor_post($submission_id);
        if (function_exists('aza_submit_lead')) {
            aza_submit_lead($submission_id);
        }
        do_action('azr_form_submit', $contact_form->title(), $fields, $submission_id);
    }
}

add_action('admin_enqueue_scripts', 'azm_cf7_admin_scripts');

function azm_cf7_admin_scripts() {
    if (isset($_GET['page']) && $_GET['page'] == 'wpcf7') {
        wp_enqueue_script('azm_cf7_admin', plugins_url('js/admin.js', __FILE__), array('jquery'), false, true);
        wp_localize_script('azm_cf7_admin', 'azm_cf7', array(
            'send_email' => admin_url('admin.php?azm_cf7_send={id}'),
            'submissions_list' => admin_url('edit.php?post_type=azf_submission&parent_id={id}'),
            'i18n' => array(
                'send_email' => __('Send email to all subscribers', 'azm'),
                'submissions_list' => __('Submissions list', 'azm'),
            ),
        ));
    }
}

add_action('wp_loaded', 'azm_cf7_wp_loaded');

function azm_cf7_wp_loaded() {
    if (isset($_GET['azm_cf7_send']) && is_numeric($_GET['azm_cf7_send'])) {
        $cf7_id = $_GET['azm_cf7_send'];
        $rule_id = azm_cf7_get_defualt_campaign($cf7_id);
        exit(wp_redirect(html_entity_decode(get_edit_post_link($rule_id))));
    }
}

function azm_cf7_get_defualt_campaign($cf7_id) {
    $rule_id = get_post_meta($cf7_id, 'azr-rule', true);
    if ($rule_id) {
        $post = get_post($rule_id);
        if ($post && $post->post_status != 'trash') {
            return $rule_id;
        }
    }
    $form = get_post($cf7_id);
    $rule_id = wp_insert_post(
            array(
        'post_title' => esc_html__('Send email to subscribers of ', 'azm') . $form->post_title,
        'post_type' => 'azr_rule',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
            ), true
    );
    update_post_meta($rule_id, '_rule', '
        {
            "event": {
                "type": "scheduler",
                "when": "immediately"
            },
            "context": {
                "rule": ' . $rule_id . '
            },
            "conditions": [{
                "type": "email_subscription",
                "email_subscription_form": "' . $form->post_title . '",
                "email_field": "your-email",
                "email_subscription_status": "subscribed"
            }, {
                "type": "email_campaign_status",
                "campaign": "' . $rule_id . '",
                "status": "was_not_sent"
            }],
            "actions": [{
                "type": "send_html_email",
                "from_email": "' . get_bloginfo('admin_email') . '",
                "from_name": "' . get_bloginfo('name') . '",
                "reply_to": "' . get_bloginfo('admin_email') . '",                    
                "email_subject": "' . esc_html__('We have news for you', 'azm') . '",
                "email_template": "0",
                "email_body": "' . base64_encode('Hello') . '"
            }]
        }            
        ');
    update_post_meta($cf7_id, 'azr-rule', $rule_id);
    return $rule_id;
}

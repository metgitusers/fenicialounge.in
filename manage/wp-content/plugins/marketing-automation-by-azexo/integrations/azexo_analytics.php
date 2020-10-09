<?php

//visitor id sync

add_filter('azr_action_url_params', 'azr_analytics_url_params', 10, 3);

function azr_analytics_url_params($url_params, $action, $context) {
    if (defined('AZA_VERSION')) {
        //utm_term - link text ?
        $url_params['utm_source'] = 'internal';
        $url_params['utm_campaign'] = $context['rule'];
        if (in_array($action['type'], array('send_text_email', 'send_html_email'))) {
            $url_params['utm_medium'] = 'email';
            if ($action['type'] == 'send_html_email') {
                $url_params['utm_content'] = $action['email_template'];
            }
        }
        if (in_array($action['type'], array('send_sms'))) {
            $url_params['utm_medium'] = 'sms';
        }
        if (in_array($action['type'], array('send_fb_message'))) {
            $url_params['utm_medium'] = 'fb_message';
        }
        if (in_array($action['type'], array('show_azh_widget'))) {
            $url_params['utm_medium'] = 'azh';
            $url_params['utm_content'] = $action['azh_widget'];
        }
    }
    return $url_params;
}

add_filter('aza_settings', 'azm_settings');

function azm_settings($settings) {
    global $wpdb;
    if (!isset($settings['dimensions']['utm_source']['converters'])) {
        $settings['dimensions']['utm_source']['converters'] = array();
    }
    $settings['dimensions']['utm_source']['converters'][] = array(
        'map' => array(
            'internal' => __('Site internal', 'azm'),
        ),
    );

    if (!isset($settings['dimensions']['utm_medium']['converters'])) {
        $settings['dimensions']['utm_medium']['converters'] = array();
    }
    $settings['dimensions']['utm_medium']['converters'][] = array(
        'map' => array(
            'email' => __('Email', 'azm'),
            'sms' => __('SMS', 'azm'),
            'fb_message' => __('Facebook message', 'azm'),
            'azh' => __('AZH Widget', 'azm'),
        ),
    );

    if (!isset($settings['dimensions']['utm_campaign']['converters'])) {
        $settings['dimensions']['utm_campaign']['converters'] = array();
    }
    $settings['dimensions']['utm_campaign']['converters'][] = array(
        'condition' => array(
            'column' => 'utm_source',
            'value' => 'internal',
        ),
        'table' => $wpdb->prefix . 'posts',
        'from' => 'ID',
        'to' => 'post_title',
    );


    if (!isset($settings['dimensions']['utm_content']['converters'])) {
        $settings['dimensions']['utm_content']['converters'] = array();
    }
    $settings['dimensions']['utm_content']['converters'][] = array(
        'condition' => array(
            'column' => 'utm_medium',
            'value' => 'email',
        ),
        'table' => $wpdb->prefix . 'posts',
        'from' => 'ID',
        'to' => 'post_title',
    );
    $settings['dimensions']['utm_content']['converters'][] = array(
        'condition' => array(
            'column' => 'utm_medium',
            'value' => 'azh',
        ),
        'table' => $wpdb->prefix . 'posts',
        'from' => 'ID',
        'to' => 'post_title',
    );


    return $settings;
}

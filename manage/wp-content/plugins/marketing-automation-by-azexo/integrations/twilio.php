<?php

function azm_send_twilio_sms($number_to, $message) {
    $settings = get_option('azh-sms-settings');
    if (!empty($settings['account-sid']) && !empty($settings['auth-token'])) {
        require_once( plugin_dir_path(__FILE__) . '../twilio-php/Twilio/autoload.php' );

        $client = new Twilio\Rest\Client($settings['account-sid'], $settings['auth-token']);
        try {
            $args = array('body' => $message);
            if (!empty($settings['number-from'])) {
                $args['from'] = $settings['number-from'];
            }
            return $client->messages->create($number_to, $args);
        } catch (\Exception $e) {
            return new WP_Error('api-error', $e->getMessage(), $e);
        }
    }
    return new WP_Error('settings-error', __('Wrong Twilio account settings', 'azm'));
}


add_action('admin_init', 'azm_twilio_options', 11);

function azm_twilio_options() {
    add_settings_section(
            'azh_twilio_section', // Section ID
            esc_html__('Twilio settings', 'azm'), // Title above settings section
            'azm_general_options_callback', // Name of function that renders a description of the settings section
            'azh-sms-settings'                     // Page to show on
    );
    add_settings_field(
            'account-sid', // Field ID
            esc_html__('Account SID', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-sms-settings', // Page to show on
            'azh_twilio_section', // Associate with which settings section?
            array(
        'id' => 'account-sid',
        'desc' => __('To view API credentials visit <a href="https://www.twilio.com/user/account/voice-sms-mms">your twilio account</a>', 'azm'),
            )
    );
    add_settings_field(
            'auth-token', // Field ID
            esc_html__('Auth-token', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-sms-settings', // Page to show on
            'azh_twilio_section', // Associate with which settings section?
            array(
        'id' => 'auth-token',
        'desc' => __('To view API credentials visit <a href="https://www.twilio.com/user/account/voice-sms-mms">your twilio account</a>', 'azm'),
            )
    );
    add_settings_field(
            'number-from', // Field ID
            esc_html__('Twilio Number', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-sms-settings', // Page to show on
            'azh_twilio_section', // Associate with which settings section?
            array(
        'id' => 'number-from',
        'desc' => __('Country code + 10-digit Twilio phone number (i.e. +16175551212)', 'azm'),
            )
    );
}

<?php
register_activation_hook(AZM_FILE, 'azm_bh_activate');

function azm_bh_activate() {
    if (!wp_next_scheduled('azm_bh_cron_process')) {
        wp_schedule_event(time(), 'every_five_minutes', 'azm_bh_cron_process');
    }
}

add_filter('cron_schedules', 'azm_bh_cron_schedules');

function azm_bh_cron_schedules($schedules) {
    $schedules['every_five_minutes'] = array('interval' => 5 * MINUTE_IN_SECONDS, 'display' => __('Every five minutes', 'azm'));
    return $schedules;
}

add_action('azm_bh_cron_process', 'azm_bh_cron_process');

function azm_bh_cron_process() {
    $messages = azm_bh_get_bounce_messages();
    if (is_array($messages)) {
        foreach ($messages as $lead_id => $campaign_id) {
            wp_delete_post($lead_id);
            $bounces = get_post_meta($campaign_id, '_email_bounces', true);
            $bounces = $bounces + 1;
            update_post_meta($campaign_id, '_email_bounces', $bounces);
        }
    }
}

add_action('admin_menu', 'azm_bh_admin_menu', 12);

function azm_bh_admin_menu() {
    add_submenu_page('edit.php?post_type=azr_rule', __('Bouncing settings', 'azm'), __('Bouncing settings', 'azm'), 'edit_pages', 'azh-bouncing-settings', 'azm_bouncing_page');
}

function azm_bouncing_page() {
    ?>

    <div class="wrap">
        <h2><?php _e('Bouncing settings', 'azm'); ?></h2>

        <form method="post" action="options.php" class="azh-form">
            <?php
            settings_errors();
            settings_fields('azh-bouncing-settings');
            do_settings_sections('azh-bouncing-settings');
            submit_button(__('Save Settings', 'azm'));
            ?>
        </form>
    </div>

    <?php
}

function azm_bh_test_callback() {
    ?>
    <button class="bounce-test button button-primary button-small"><?php _e('Test bounce settings', 'azm'); ?></button>
    <?php
}

add_action('admin_enqueue_scripts', 'azm_bh_admin_scripts');

function azm_bh_admin_scripts() {
    if (isset($_GET['page']) && $_GET['page'] == 'azh-bouncing-settings') {
        wp_enqueue_style('azm_admin', plugins_url('css/admin.css', __FILE__));
        wp_enqueue_script('azm_admin', plugins_url('js/admin.js', __FILE__), array('jquery'), AZM_PLUGIN_VERSION, true);
        wp_localize_script('azm_admin', 'azm', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'i18n' => array(
                'successful' => __('Successful', 'azm'),
                'failed' => __('Failed', 'azm'),
            ),
        ));
    }
}

add_action('admin_init', 'azm_bh_options');

function azm_bh_options() {
    register_setting('azh-bouncing-settings', 'azh-bouncing-settings', array('sanitize_callback' => 'azh_settings_sanitize_callback'));

    add_settings_section(
            'azh_bouncing_section', // Section ID
            esc_html__('Bounce Email', 'azm'), // Title above settings section
            'azm_general_options_callback', // Name of function that renders a description of the settings section
            'azh-bouncing-settings'                     // Page to show on
    );
    add_settings_field(
            'bounce-email', // Field ID
            esc_html__('Bounce Email', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-bouncing-settings', // Page to show on
            'azh_bouncing_section', // Associate with which settings section?
            array(
        'id' => 'bounce-email',
            )
    );
    add_settings_field(
            'test', // Field ID
            esc_html__('Test', 'azh'), // Label to the left
            'azm_bh_test_callback', // Name of function that renders options on the page
            'azh-bouncing-settings', // Page to show on
            'azh_bouncing_section' // Associate with which settings section?
    );

    add_settings_section(
            'azh_bouncing_pop3_section', // Section ID
            esc_html__('POP3 credentials', 'azm'), // Title above settings section
            'azm_general_options_callback', // Name of function that renders a description of the settings section
            'azh-bouncing-settings'                     // Page to show on
    );
    add_settings_field(
            'server-address', // Field ID
            esc_html__('Server Address', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-bouncing-settings', // Page to show on
            'azh_bouncing_pop3_section', // Associate with which settings section?
            array(
        'id' => 'server-address',
            )
    );
    add_settings_field(
            'port', // Field ID
            esc_html__('Port', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-bouncing-settings', // Page to show on
            'azh_bouncing_pop3_section', // Associate with which settings section?
            array(
        'id' => 'port',
            )
    );
    add_settings_field(
            'ssl', // Field ID
            esc_html__('SSL', 'azm'), // Label to the left
            'azh_checkbox', // Name of function that renders options on the page
            'azh-bouncing-settings', // Page to show on
            'azh_bouncing_pop3_section', // Associate with which settings section?
            array(
        'id' => 'ssl',
        'options' => array(
            'yes' => __('Use SSL', 'azm')
        )
            )
    );
    add_settings_field(
            'username', // Field ID
            esc_html__('Username', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-bouncing-settings', // Page to show on
            'azh_bouncing_pop3_section', // Associate with which settings section?
            array(
        'id' => 'username',
            )
    );
    add_settings_field(
            'password', // Field ID
            esc_html__('Password', 'azm'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-bouncing-settings', // Page to show on
            'azh_bouncing_pop3_section', // Associate with which settings section?
            array(
        'id' => 'password',
            )
    );
}

add_action('wp_ajax_azm_bounce_test', 'azm_bh_bounce_test');

function azm_bh_bounce_test() {
    if (isset($_POST['send']) && $_POST['send']) {
        if (azm_bh_send_test()) {
            print '1';
        } else {
            print __('Can not send email', 'azm');
        }
    }
    if (isset($_POST['check']) && $_POST['check']) {
        $messages = azm_bh_get_bounce_messages();
        if (is_array($messages)) {
            print count($messages);
        } else {
            if (is_string($messages)) {
                print $messages;
            }
        }
    }
    wp_die();
}

function azm_bh_send_test() {
    $settings = get_option('azh-bouncing-settings');
    if (isset($settings['bounce-email'])) {
        $headers = array();
        $headers[] = 'X-AZEXO-Campaign: 0';
        $headers[] = 'X-AZEXO-Lead: 0';
        return wp_mail($settings['bounce-email'], 'Bouncing test', 'Bouncing test', $headers);
    }
    return false;
}

function azm_bh_get_bounce_messages() {
    $settings = get_option('azh-bouncing-settings');
    if (empty($settings['server-address']) || empty($settings['port']) || empty($settings['username']) || empty($settings['password'])) {
        return __('Incorrect POP3 settings', 'azm');
    }
    require_once ABSPATH . WPINC . '/class-pop3.php';
    $mailbox = new POP3();
    $mailbox->TIMEOUT = 60;
    $server = $settings['server-address'];
    if (isset($settings['ssl']['yes']) && $settings['ssl']['yes']) {
        $server = 'ssl://' . $server;
    }
    $mailbox->connect($server, $settings['port']);
    if (!empty($mailbox->ERROR)) {
        return $mailbox->ERROR;
    }
    $mailbox->user($settings['username']);
    if (!empty($mailbox->ERROR)) {
        return $mailbox->ERROR;
    }
    $msgcount = $mailbox->pass($settings['password']);
    if (!empty($mailbox->ERROR)) {
        return $mailbox->ERROR;
    }

    $messages = array();
    for ($i = 1; $i <= $msgcount; $i++) {
        $message = $mailbox->get($i);
        if (!$message) {
            continue;
        }
        $message = implode($message);
        if (preg_match('/X-AZEXO-Campaign *: *(\d+)/i', $message, $campaign)) {
            if (preg_match('/X-AZEXO-Lead *: *(\d+)/i', $message, $lead)) {
                $messages[$lead[1]] = $campaign[1];
            }
        }
    }
    for ($i = 1; $i <= $msgcount; $i++) {
        $mailbox->delete($i);
    }

    $mailbox->quit();

    return $messages;
}

function azm_get_bounced_code($msg) {
    $rules = azm_bh_rules();
    $ret = ['msg' => null, 'action' => null];

    foreach ($rules as $key => $value) {
        if (preg_match('/' . $value['regex'] . '/i', $msg, $result)) {
            $ret['msg'] = $value['name'];
            if ($value['key'] === 'nohandle') {
                $ret['action'] = 'weird_forward';
            } else {
                $ret['action'] = $value['key'];
            }

            return $ret;
        }
    }
}

function azm_bh_rules() {
    $arr = [
        [
            "key" => "mailbox_full",
            "name" => __('Mailbox Full', 'azm'),
            "title" => __('When mailbox is full', 'azm'),
            "regex" => '((mailbox|mailfolder|storage|quota|space) *(is)? *(over)? *(exceeded|size|storage|allocation|full|quota|maxi))|((over|exceeded|full) *(mail|storage|quota))'
        ],
        [
            "key" => "mailbox_not_available",
            "name" => __('Mailbox not available', 'azm'),
            "title" => __('When mailbox is not available', 'azm'),
            "regex" => '(Invalid|no such|unknown|bad|des?activated|undelivered|inactive|unrouteable|delivery|mail ID|failed to|may not|no known user|email account) *(mail|destination|recipient|user|address|person|failure|has failed|does not exist|deliver to|exist|with this email|is closed)|RecipNotFound|status(-code)? *(:|=)? *5\.(1\.[1-6]|0\.0|4\.[0123467])|(user|mailbox|address|recipients?|host|account|domain) *(is|has been)? *(error|disabled|failed|unknown|unavailable|not *(found|available)|.{1,30}inactiv)|recipient *address *rejected|does *not *like *recipient|no *mailbox *here|user does.?n.t have.{0,20}account'
        ],
        [
            "key" => "message_delayed",
            "name" => __('Message delayed', 'azm'),
            "title" => __('When message is delayed', 'azm'),
            "regex" => 'possible *mail *loop|too *many *hops|Action: *delayed|has.*been.*delayed|delayed *mail|temporary *failure'
        ],
        [
            "key" => "failed_permanent",
            "name" => __('Failed Permanently', 'azm'),
            "title" => __('When failed permanently', 'azm'),
            "regex" => 'failed *permanently|permanent *(fatal)? *(failure|error)|Unrouteable *address|not *accepting *(any)? *mail'
        ],
        [
            "key" => "action_required",
            "name" => __('Action Required', 'azm'),
            "title" => __('When you need to confirm you\'re a human being, forward to:', 'azm'),
            "regex" => 'action *required|verif'
        ],
        [
            "key" => "blocked_ip",
            "name" => __('Blocked IP', 'azm'),
            "title" => __('When you are flagged as a spammer forward the bounced message to', 'azm'),
            "regex" => 'is *(currently)? *blocked *by|block *list|spam *detected|(unacceptable|banned|offensive|filtered|blocked) *(content|message|e-?mail)|administratively *denied'
        ],
        [
            "key" => "nohandle",
            "name" => __('Final Rule', 'azm'),
            "title" => __('When the bounce is weird and we\'re not sure what to do, forward to:', 'azm'),
            "regex" => '.'
        ]
    ];

    return $arr;
}

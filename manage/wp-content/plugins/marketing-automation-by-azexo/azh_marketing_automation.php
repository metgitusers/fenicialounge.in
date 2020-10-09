<?php
/*
  Plugin Name: Marketing Automation by AZEXO
  Description: Marketing Automation by AZEXO
  Author: azexo
  Author URI: http://azexo.com
  Version: 1.27.80
  Text Domain: azm
 */

define('AZM_VERSION', '1.27');
define('AZM_PLUGIN_VERSION', '1.27.80');
define('AZM_FILE', __FILE__);
define('AZM_URL', plugins_url('', __FILE__));
define('AZM_DIR', trailingslashit(dirname(__FILE__)));


if (is_admin()) {
    include_once(AZM_DIR . 'settings.php' );
}
if (file_exists(AZM_DIR . 'integrations/twilio.php')) {
    include_once(AZM_DIR . 'integrations/twilio.php' );
}
if (file_exists(AZM_DIR . 'integrations/azexo_analytics.php')) {
    include_once(AZM_DIR . 'integrations/azexo_analytics.php' );
}
if (file_exists(AZM_DIR . 'integrations/woocommerce.php')) {
    include_once(AZM_DIR . 'integrations/woocommerce.php' );
}
if (file_exists(AZM_DIR . 'rules.php')) {
    include_once(AZM_DIR . 'rules.php' );
}
if (file_exists(AZM_DIR . 'email_rules.php')) {
    include_once(AZM_DIR . 'email_rules.php' );
}
if (file_exists(AZM_DIR . 'sms_rules.php')) {
    include_once(AZM_DIR . 'sms_rules.php' );
}
if (file_exists(AZM_DIR . 'bounce_handling.php')) {
    include_once(AZM_DIR . 'bounce_handling.php' );
}



add_action('plugins_loaded', 'azm_plugins_loaded');

function azm_plugins_loaded() {
    load_plugin_textdomain('azm', FALSE, basename(dirname(__FILE__)) . '/languages/');
}

add_action('admin_notices', 'azm_admin_notices');

function azm_admin_notices() {
    if (!defined('AZH_VERSION')) {
        $plugin_data = get_plugin_data(__FILE__);
        //print '<div class="updated notice error is-dismissible"><p>' . $plugin_data['Name'] . ': ' . __('please install <a href="https://wordpress.org/plugins/page-builder-by-azexo/">Page builder by AZEXO</a> plugin.', 'azm') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'azm') . '</span></button></div>';
    }
    if (!defined('AZF_VERSION')) {
        $plugin_data = get_plugin_data(__FILE__);
        //print '<div class="updated notice error is-dismissible"><p>' . $plugin_data['Name'] . ': ' . __('please install <a href="https://wordpress.org/plugins/cost-calculator-by-azexo/">Form builder by AZEXO</a> plugin.', 'azm') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'azm') . '</span></button></div>';
    }
    $screen = get_current_screen();
    if ($screen->id == 'edit-azf_submission') {
        print '<form class="azm-filters" method="post">';
        $rule = get_option('azm-submissions-filter', array('context' => array(), 'conditions' => array(),));
        $rule = json_encode($rule);
        print '<textarea class="azr-rule" name="azm-submissions-filter" hidden data-context="visitors">' . $rule . '</textarea>';
        print '<input type="submit" class="button button-primary button-hero" value="' . esc_html__('Filter submissions', 'azm') . '">';
        print '</form>';
    }
}

add_action('init', 'azm_save_azr_conditions');

function azm_save_azr_conditions() {
    if (is_admin()) {
        if (isset($_POST['azm-submissions-filter'])) {
            $rule = wp_unslash(sanitize_textarea_field($_POST['azm-submissions-filter']));
            $rule = json_decode($rule, true);
        }
        if (!empty($rule)) {
            update_option('azm-submissions-filter', $rule);
        }
    }
}

add_filter('posts_where', 'azm_posts_where', 10, 2);

function azm_posts_where($where, $query) {
    if (is_admin() && $query->is_main_query()) {
        $screen = get_current_screen();
        if ($screen->id == 'edit-azf_submission') {
            global $wpdb;
            $rule = get_option('azm-submissions-filter', array('context' => array(), 'conditions' => array(),));
            if (!empty($rule['conditions'])) {
                $rule['event'] = array('type' => 'visit');
                $context = azr_prepare_context_by_event($rule);
                if ($context['visitors']) {
                    $context = azr_process_conditions($rule, $context);
                    $db_query = $context['visitors'];
                    $query = 'SELECT DISTINCT vp.post_id FROM ' . $db_query['from'];
                    $query .= " INNER JOIN {$wpdb->prefix}azr_visitor_posts as vp ON vp.visitor_id = v.visitor_id ";
                    if (!empty($db_query['where'])) {
                        $query .= ' WHERE ' . implode(' AND ', $db_query['where']);
                    }
                    $query = azr_remove_injections($query);
                    $where .= " AND {$wpdb->posts}.ID IN ($query) ";
                }
            }
            if (isset($_GET['parent_id'])) {
                $parent_id = (int) $_GET['parent_id'];
                $where .= " AND {$wpdb->posts}.post_parent =$parent_id ";
            }
        }
    }
    return $where;
}

add_action('admin_menu', 'azm_admin_menu_email', 11);

function azm_admin_menu_email() {
    if (defined('AZH_VERSION')) {
        add_submenu_page('edit.php?post_type=azr_rule', __('Create AZH Widget with Form', 'azm'), __('Create AZH Widget with Form', 'azm'), 'edit_pages', '?azm-action=new-form');
    }
    if (defined('AZP_VERSION')) {
        add_submenu_page('edit.php?post_type=azr_rule', __('Create AZH Widget with Popup', 'azm'), __('Create AZH Widget with Popup', 'azm'), 'edit_pages', '?azm-action=new-popup');
    }
    if (defined('AZH_VERSION')) {
        add_submenu_page('edit.php?post_type=azr_rule', __('Email Templates', 'azm'), __('Email Templates', 'azm'), 'edit_pages', 'edit.php?post_type=azm_email_template');
        add_submenu_page('edit.php?post_type=azr_rule', __('Upload Email Template', 'azm'), __('Upload Email Template', 'azm'), 'edit_pages', 'azh-email-templates-settings', 'azm_email_templates_page');
    }
}

add_action('admin_menu', 'azm_import_export', 14);

function azm_import_export() {
    if (defined('AZF_VERSION')) {
        add_submenu_page('edit.php?post_type=azr_rule', __('Import/Export', 'azm'), __('Import/Export', 'azm'), 'edit_pages', 'azm-import-export', 'azm_import_export_page');
    }
}

function azm_import_export_page() {
    ?>
    <div class="wrap azm-import-export">
        <h2><?php _e('Import/Export', 'azm'); ?></h2>
        <div class="azm-progress"><div class="azm-bar"><div class="azm-operation"></div><div class="azm-status"></div></div></div>
        <input id="azm-leads-import" type="file">
        <button class="button button-primary button-hero azm-leads-import"><?php _e('Import form submissions', 'azm'); ?></button>
        <button class="button button-primary button-hero azm-leads-delete"><?php _e('Delete form submissions', 'azm'); ?></button>
        <a href="<?php print AZM_URL . '/leads_export.php'; ?>" class="button button-primary button-hero azm-leads-export"><?php _e('Export form submissions', 'azm'); ?></a>
    </div>
    <?php
}

function azm_email_templates_page() {
    ?>

    <div class="wrap">
        <h2><?php _e('Upload Email Template', 'azm'); ?></h2>

        <form method="post" action="options.php" class="azh-form">
            <?php
            settings_errors();
            settings_fields('azh-email-templates-settings');
            do_settings_sections('azh-email-templates-settings');
//            submit_button(__('Save Settings', 'azm'));
            ?>
        </form>
    </div>

    <?php
}

add_action('admin_init', 'azm_options');

function azm_options() {
    register_setting('azh-email-templates-settings', 'azh-email-templates-settings', array('sanitize_callback' => 'azh_settings_sanitize_callback'));

    add_settings_section(
            'azh_email_templates_section', // Section ID
            esc_html__('Upload StampReady Email Template', 'azm'), // Title above settings section
            'azm_general_options_callback', // Name of function that renders a description of the settings section
            'azh-email-templates-settings'                     // Page to show on
    );
    add_settings_field(
            'azh_email_template_upload', // Field ID
            __('ZIP file of email template in <a href="https://themeforest.net/tags/stampready" target="_blank">StampReady</a> format', 'azm'), // Label to the left
            'azm_email_template_upload', // Name of function that renders options on the page
            'azh-email-templates-settings', // Page to show on
            'azh_email_templates_section', // Associate with which settings section?
            array()
    );
}

function azm_email_template_upload() {
    ?>
    <p>
        <input id="azm-email-template-upload" type="file">
        <a href="#" class="button button-primary azm-email-template-upload">
            <?php esc_html_e('Click to start upload', 'azm'); ?>
        </a>
        <span class="azm-progress"><span class="azm-status"></span></span>
    </p>
    <p>
        <em>
            <?php _e('Template will be uploaded right after choose a template zip-file.<br> ZIP-filename must contain single folder with index.html file.<br> ZIP-file and folder must have same name - <b>template name</b>.', 'azm'); ?>
        </em>
    </p>
    <?php
}

function azm_get_templates() {
    $wp_upload_dir = wp_upload_dir();
    $templates = array();
    if (is_dir($wp_upload_dir['basedir'] . '/email_templates')) {
        $templates_iterator = new DirectoryIterator($wp_upload_dir['basedir'] . '/email_templates');
        foreach ($templates_iterator as $templateInfo) {
            if ($templateInfo->isDir() && !$templateInfo->isDot()) {
                $template_name = $templateInfo->getFilename();
                $templates[$template_name] = array(
                    'name' => $template_name,
                    'url' => file_exists($templateInfo->getPathname() . '/index.html') ? $wp_upload_dir['baseurl'] . '/email_templates/' . $template_name . '/index.html' : false,
                    'template_preview' => file_exists($templateInfo->getPathname() . '/index.jpg') ? $wp_upload_dir['baseurl'] . '/email_templates/' . $template_name . '/index.jpg' : false,
                    'styles' => file_exists($templateInfo->getPathname() . '/styles.css') ? $wp_upload_dir['basedir'] . '/email_templates/' . $template_name . '/styles.css' : false,
                    'stylesheets' => file_exists($templateInfo->getPathname() . '/stylesheets.html') ? $wp_upload_dir['basedir'] . '/email_templates/' . $template_name . '/stylesheets.html' : false,
                    'sections' => array(),
                );
                if (is_dir($templateInfo->getPathname() . '/sections')) {
                    $sections_iterator = new DirectoryIterator($templateInfo->getPathname() . '/sections');
                    foreach ($sections_iterator as $sectionInfo) {
                        if ($sectionInfo->isFile() && $sectionInfo->getExtension() == 'html') {
                            $preview = $wp_upload_dir['baseurl'] . '/email_templates/' . $template_name . '/sections/' . $sectionInfo->getBasename('.html') . '.jpg';
                            if (!file_exists($preview)) {
                                $preview = $wp_upload_dir['baseurl'] . '/email_templates/' . $template_name . '/sections/' . $sectionInfo->getBasename('.html') . '.png';
                            }
                            $templates[$template_name]['sections'][$sectionInfo->getBasename('.html')] = array(
                                'html' => $wp_upload_dir['baseurl'] . '/email_templates/' . $template_name . '/sections/' . $sectionInfo->getFilename(),
                                'preview' => $preview,
                            );
                        }
                    }
                }
            }
        }
    }
    return $templates;
}

add_action('admin_enqueue_scripts', 'azm_admin_scripts');

function azm_admin_scripts() {
    if (isset($_GET['page']) && $_GET['page'] == 'azh-email-templates-settings' || get_post_type() === 'azm_email_template' || get_post_type() === 'azr_rule' || (isset($_GET['post_type']) && ($_GET['post_type'] == 'azr_rule'))) {
        wp_enqueue_style('azm_admin', plugins_url('css/admin.css', __FILE__));
        wp_enqueue_script('azm_admin', plugins_url('js/admin.js', __FILE__), array('jquery'), AZM_PLUGIN_VERSION, true);
        wp_enqueue_script('simplemodal', plugins_url('js/jquery.simplemodal.js', __FILE__), array('jquery'), AZM_PLUGIN_VERSION, true);
        $current_user = wp_get_current_user();
        $forms = function_exists('azh_get_forms_from_pages') ? azh_get_forms_from_pages() : array();
        $forms = apply_filters('azr_forms', $forms);
        wp_localize_script('azm_admin', 'azm', array(
            'nonce' => wp_create_nonce('ajax'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'jquery' => home_url() . wp_scripts()->registered['jquery-core']->src,
            'html2canvas' => plugins_url('js/html2canvas.js', __FILE__),
            'templates' => azm_get_templates(),
            'admin_email' => get_bloginfo('admin_email'),
            'forms' => $forms,
            'import_forms' => is_array(get_user_meta(get_current_user_id(), 'import_forms', true)) ? get_user_meta(get_current_user_id(), 'import_forms', true) : array('import' => array('email' => 'email')),
            'user_id' => get_current_user_id(),
            'i18n' => array(
                'something_went_wrong' => __('Something went wrong', 'azm'),
                'send_test_email_to' => __('Send test email to: ', 'azm'),
                'send_test_sms' => __('Send test SMS', 'azm'),
                'enter_receiver_phone_number' => __('Enter receiver phone number', 'azm'),
                'done' => __('Done', 'azm'),
                'ok' => __('OK', 'azm'),
                'cancel' => __('Cancel', 'azm'),
                'select_columns' => __('Select columns', 'azm'),
                'select_available_field' => __('Select available field', 'azm'),
                'define_which_column_represents_which_field' => __('Define which column represents which field', 'azm'),
                'upload_progress' => __('Upload progress', 'azm'),
                'import_progress' => __('Import progress', 'azm'),
                'or_define_new_field_name' => __('or define new field name', 'azm'),
                'existing_leads' => __('Existing leads', 'azm'),
                'use_as_id' => __('Use as id', 'azm'),
                'skip' => __('skip', 'azm'),
                'import' => __('Import', 'azm'),
                'rows' => __('rows', 'azm'),
                'overwrite' => __('overwrite', 'azm'),
                'merge' => __('merge', 'azm'),
                'source_of_leads' => __('Source of leads', 'azm'),
                'form_name' => __('Form name', 'azm'),
                'leads_export' => __('Leads export', 'azm'),
                'fields' => __('Fields', 'azm'),
                'lead_id' => __('Lead ID', 'azm'),
                'lead_timestamp' => __('Lead timestamp', 'azm'),
                'lead_post_date' => __('Lead post date', 'azm'),
                'page_of_lead' => __('Page of lead', 'azm'),
                'form_of_lead' => __('Form of lead', 'azm'),
            ),
        ));
    }
//    if (get_post_type() === 'azm_email_template') {
//        $settings = wp_enqueue_code_editor(array('type' => 'text/html'));
//        if (false === $settings) {
//            return;
//        }
//        wp_add_inline_script(
//                'code-editor', sprintf(
//                        'jQuery( function() { wp.codeEditor.initialize( "content", %s ); } );', wp_json_encode($settings)
//                )
//        );
//    }
}

add_action('wp_ajax_azm_upload_template', 'azm_upload_template');

function azm_upload_template() {
    $file_name = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
    if ($file_name) {
        $file_path = get_temp_dir() . $file_name;
        $hSource = fopen('php://input', 'r');
        $hDest = fopen($file_path, 'w');
        while (!feof($hSource)) {
            $chunk = fread($hSource, 1024);
            fwrite($hDest, $chunk);
        }
        fclose($hSource);
        fclose($hDest);
        azh_filesystem();
        $wp_upload_dir = wp_upload_dir();
        if (!is_dir($wp_upload_dir['basedir'] . '/email_templates')) {
            wp_mkdir_p($wp_upload_dir['basedir'] . '/email_templates');
        }
        if (unzip_file($file_path, $wp_upload_dir['basedir'] . '/email_templates')) {
            if ($_GET['format'] == 'stampready') {
                azm_process_stampready_template(pathinfo($file_name, PATHINFO_FILENAME));
            }
            $templates = azm_get_templates();
            print json_encode($templates[pathinfo($file_name, PATHINFO_FILENAME)]); //zip filename must have same name as template
        }
    }
    wp_die();
}

function azm_add_image_to_library($path) {
//    static $added = array();
//    if (!isset($added[$path]) && file_exists($path)) {
//        $added[$path] = true;
//        $wp_upload_dir = wp_upload_dir();
//        $new_file_path = $wp_upload_dir['path'] . '/' . basename($path);
//        $filetype = wp_check_filetype(basename($path), null);
//        $i = 1;
//        while (file_exists($new_file_path)) {
//            $i++;
//            $new_file_path = $wp_upload_dir['path'] . '/' . $i . '-' . basename($path);
//        }
//        if (move_uploaded_file($path, $new_file_path)) {
//            $attachment = array(
//                'guid' => $new_file_path,
//                'post_mime_type' => $filetype['type'],
//                'post_title' => preg_replace('/\.[^.]+$/', '', basename($path)),
//                'post_content' => '',
//                'post_status' => 'inherit'
//            );
//            wp_insert_attachment($attachment, $new_file_path);
//        }
//    }
}

function azm_process_stampready_template($name) {
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['basedir'] . '/email_templates/' . $name . '/index.html';
    if (file_exists($path)) {
        global $wp_filesystem;
        azh_filesystem();
        $content = $wp_filesystem->get_contents($path);
        include_once(AZH_DIR . 'simple_html_dom.php' );
        $html = str_get_html($content);
        if ($html) {

            foreach ($html->find('comment') as $comment) {
                $comment->outertext = '';
            }

            $styles = '';
            foreach ($html->find('style') as $style) {
                $styles .= $style->innertext;
            }

            $stylesheets = '';
            foreach ($html->find('link[type="text/css"]') as $stylesheet) {
                $stylesheets .= $stylesheet->outertext;
            }

            foreach ($html->find('img[src]') as $img) {
                $url = $wp_upload_dir['baseurl'] . '/email_templates/' . $name . '/' . $img->src;
                if (strpos($img->src, 'http') === false) {
                    $img->src = $url;
                    $path = $wp_upload_dir['basedir'] . '/email_templates/' . $name . '/' . $img->src;
                    azm_add_image_to_library($path);
                }
            }

            foreach ($html->find('[background]') as $background) {
                $url = $wp_upload_dir['baseurl'] . '/email_templates/' . $name . '/' . $background->background;
                if (strpos($background->background, 'http') === false) {
                    $background->background = $url;
                    $path = $wp_upload_dir['basedir'] . '/email_templates/' . $name . '/' . $background->background;
                    azm_add_image_to_library($path);
                }
            }

            foreach ($html->find('[style*="background-image"]') as $background_image) {
                $background_image->style = preg_replace_callback('/(background-image\:[^;]*url\([\'\"]?)([^\'\"\)]+)([\'\"]?\))/i', function($m) use ($name, $wp_upload_dir) {
                    if (strpos($m[2], 'http') === false) {
                        $url = $wp_upload_dir['baseurl'] . '/email_templates/' . $name . '/' . $m[2];
                        $path = $wp_upload_dir['basedir'] . '/email_templates/' . $name . '/' . $m[2];
                        azm_add_image_to_library($path);
                        return $m[1] . $url . $m[3];
                    } else {
                        return $m[1] . $m[2] . $m[3];
                    }
                }, (string) $background_image->style);
            }

            foreach ($html->find('[style*="background:"]') as $background_image) {
                $background_image->style = preg_replace_callback('/(background\:[^;]*url\([\'\"]?)([^\'\"\)]+)([\'\"]?\))/i', function($m) use ($name, $wp_upload_dir) {
                    if (strpos($m[2], 'http') === false) {
                        $url = $wp_upload_dir['baseurl'] . '/email_templates/' . $name . '/' . $m[2];
                        $path = $wp_upload_dir['basedir'] . '/email_templates/' . $name . '/' . $m[2];
                        azm_add_image_to_library($path);
                        return $m[1] . $url . $m[3];
                    } else {
                        return $m[1] . $m[2] . $m[3];
                    }
                }, (string) $background_image->style);
            }

            $wp_filesystem->mkdir($wp_upload_dir['basedir'] . '/email_templates/' . $name . '/sections');
            $preview = false;
            foreach ($html->find('[data-module]') as $module) {
                if ($module->{'data-thumb'}) {
                    $thumbnail = $wp_upload_dir['basedir'] . '/email_templates/' . $name . '/thumbnails/' . $module->{'data-thumb'};
                    if (!file_exists($thumbnail)) {
                        $thumbnail = $wp_upload_dir['basedir'] . '/email_templates/' . $name . '/' . $module->{'data-thumb'};
                    }
                    $thumbnail = $wp_filesystem->get_contents($thumbnail);
                    if (!$preview) {
                        $preview = $thumbnail;
                    }
                    $ext = pathinfo($module->{'data-thumb'}, PATHINFO_EXTENSION);
                    $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . $name . '/sections/' . $module->{'data-module'} . '.' . $ext, $thumbnail);
                }
                $content = $module->outertext;
                $content = preg_replace('/ ([a-zA-Z]+):([a-zA-Z]+=[\'\"][^\'\"]*[\'\"])/', ' ', $content);
                $content = preg_replace('/ ([a-zA-Z]+):([a-zA-Z]+)/', ' ', $content);
                $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . $name . '/sections/' . $module->{'data-module'} . '.html', $content);
            }
            if ($preview) {
                $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . $name . '/index.jpg', $preview);
            }
            $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . $name . '/styles.css', $styles);
            $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . $name . '/stylesheets.html', $stylesheets);
        }
    }
}

add_action('wp_ajax_azm_upload_section', 'azm_upload_section');

function azm_upload_section() {
    global $wp_filesystem;
    azh_filesystem();
    $wp_upload_dir = wp_upload_dir();
    $img = sanitize_text_field($_REQUEST['preview']);
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $preview = base64_decode($img);
    if ($_REQUEST['name'] == 'index') {
        $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . sanitize_text_field($_REQUEST['template']) . '/index.jpg', $preview);
        $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . sanitize_text_field($_REQUEST['template']) . '/styles.css', stripslashes($_REQUEST['styles']));
        $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . sanitize_text_field($_REQUEST['template']) . '/stylesheets.html', stripslashes($_REQUEST['stylesheets']));
    } else {
        $wp_filesystem->mkdir($wp_upload_dir['basedir'] . '/email_templates/' . sanitize_text_field($_REQUEST['template']) . '/sections');
        $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . sanitize_text_field($_REQUEST['template']) . '/sections/' . sanitize_text_field($_REQUEST['name']) . '.jpg', $preview);
        $html = stripslashes($_REQUEST['html']);
        $html = preg_replace('/ ([a-zA-Z]+):([a-zA-Z]+=[\'\"][^\'\"]*[\'\"])/', ' ', $html);
        $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/email_templates/' . sanitize_text_field($_REQUEST['template']) . '/sections/' . sanitize_text_field($_REQUEST['name']) . '.html', $html);
    }

    wp_die();
}

add_filter('azh_directory', 'azm_directory', 20);

function azm_directory($dir) {
    if ((is_singular() || is_admin()) && in_array(get_post_type(), array('azm_email_template', 'azd_email_campaign'))) {
        $wp_upload_dir = wp_upload_dir();
        $template = isset($_GET['template']) && is_dir($wp_upload_dir['basedir'] . '/email_templates/' . sanitize_text_field($_GET['template'])) ? sanitize_text_field($_GET['template']) : false;
        if ($template) {
            $old_template = get_post_meta(get_the_ID(), '_template', true);
            if ($old_template != $template) {
                update_post_meta(get_the_ID(), '_template', $template);
                azh_set_post_content('', get_the_ID());
                update_post_meta(get_the_ID(), 'azh', 'azh');
                $templates = azm_get_templates();
                update_post_meta(get_the_ID(), '_stylesheets', $templates[$template]['stylesheets']);
                update_post_meta(get_the_ID(), '_styles', $templates[$template]['styles']);
            }
        } else {
            $template = get_post_meta(get_the_ID(), '_template', true);
        }
        if ($template) {
            return array(
                $wp_upload_dir['basedir'] . '/email_templates/' . $template => $wp_upload_dir['baseurl'] . '/email_templates/' . $template
            );
        } else {
            return array(
                $wp_upload_dir['basedir'] . '/email_templates/' => $wp_upload_dir['baseurl'] . '/email_templates/'
            );
        }
    }
    return $dir;
}

add_filter('azh_get_object', 'azm_get_object');

function azm_get_object($azh) {
    if ((is_singular() || is_admin()) && in_array(get_post_type(), array('azm_email_template', 'azd_email_campaign'))) {
        $azh['responsive'] = false;
        $azh['editor_toolbar'] = array('boldButton', 'italicButton', 'linkButton', 'sizeSelector', 'colorInput');
        $azh['elements_hierarchy'] = false;
        $azh['table_editor'] = false;
        $azh['recognition'] = true;
    }
    return $azh;
}

add_filter('azh_get_library', 'azm_get_library');

function azm_get_library($library) {
    if ((is_singular() || is_admin()) && in_array(get_post_type(), array('azm_email_template', 'azd_email_campaign'))) {
        foreach ($library['sections_categories'] as $category => $flag) {
            if (strpos($category, '/sections') === false) {
                unset($library['sections_categories'][$category]);
            }
        }
        foreach ($library['sections'] as $path => $name) {
            if (in_array($name, array('index.html', 'stylesheets.html'))) {
                unset($library['sections'][$path]);
            }
        }
    }
    return $library;
}

add_filter('azh_set_post_content', 'azm_set_post_content', 10, 2);

function azm_set_post_content($content, $post_id) {
    $post = get_post($post_id);
    if (in_array($post->post_type, array('azm_email_template', 'azd_email_campaign', 'azh_widget')) && ($post->post_author == get_current_user_id())) {
        include_once(AZH_DIR . 'simple_html_dom.php');
        $html = str_get_html($content);
        if ($html) {
            foreach ($html->find('a[href]') as $link) {
                $url = $link->href;
                if (strpos($url, 'http') !== false) {
                    if (strpos($url, '?') !== false) {
                        if (strpos($url, 'click=click') === false) {
                            $url .= '&click=click';
                        }
                    } else {
                        $url .= '?click=click';
                    }
                }
                $link->href = $url;
            }
            return $html->save();
        }
    }
    return $content;
}

add_filter('azh_meta_box_post_types', 'azm_meta_box_post_types');

function azm_meta_box_post_types($post_types) {
    $post_types[] = 'azm_email_template';
    $post_types[] = 'azd_email_campaign';
    return $post_types;
}

add_action('init', 'azm_init');

function azm_init() {
    if (defined('AZH_VERSION')) {
        register_post_type('azm_email_template', array(
            'labels' => array(
                'name' => __('Email template', 'azm'),
                'singular_name' => __('Email template', 'azm'),
                'add_new' => __('Add Email template', 'azm'),
                'add_new_item' => __('Add New Email template', 'azm'),
                'edit_item' => __('Edit Email template', 'azm'),
                'new_item' => __('New Email template', 'azm'),
                'view_item' => __('View Email template', 'azm'),
                'search_items' => __('Search Email templates', 'azm'),
                'not_found' => __('No Email template found', 'azm'),
                'not_found_in_trash' => __('No Email template found in Trash', 'azm'),
                'parent_item_colon' => __('Parent Email template:', 'azm'),
                'menu_name' => __('Email templates', 'azm'),
            ),
            'query_var' => true,
            'rewrite' => array('slug' => 'email_template'),
            'hierarchical' => true,
            'supports' => array('title', 'editor', 'custom-fields', 'author'),
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => false,
            'public' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
                )
        );

        $settings = get_option('azh-settings');
        if (!isset($settings['post-types']['azm_email_template'])) {
            $settings['post-types']['azm_email_template'] = true;
            update_option('azh-settings', $settings);
        }
        if (is_singular() && in_array(get_post_type(), array_keys($settings['post-types']))) {
            update_option('azh-library', array());
        }
    }
}

add_action('current_screen', 'azm_current_screen');

function azm_current_screen() {
    if (is_admin()) {
        $screen = get_current_screen();
        if (isset($screen->post_type) && is_array($screen->post_type)) {
            $settings = get_option('azh-settings');
            if (in_array($screen->post_type, array_keys($settings['post-types']))) {
                update_option('azh-library', array());
            }
        }
    }
}

add_action('wp_loaded', 'azm_wp_loaded');

function azm_wp_loaded() {
    if (defined('AZH_VERSION')) {
        if (isset($_GET['azm-action']) && $_GET['azm-action'] === 'new-form') {
            $post_id = wp_insert_post(array(
                'post_title' => (isset($_GET['azm-name']) ? $_GET['azm-name'] : 'contact') . ' ' . esc_html__('form', 'azm'),
                'post_type' => 'azh_widget',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_content' => '
<div data-section="form">
    <div data-element="general/form-container.htm" class="az-bottom-left-controls">
        <form class="az-form" action="#" method="post" data-azh-form="[azh_form]" enctype="multipart/form-data" data-success="" data-error="" data-success-redirect="">
            <input type="hidden" name="form_title" value="' . (isset($_GET['azm-name']) ? $_GET['azm-name'] : 'contact') . '" />
            <div data-cloneable="" class="az-elements-list">
                <div data-element=""></div>
            </div>
            <button type="submit" class="az-contenteditable">' . esc_html__('Submit', 'azm') . '</button>
            </form>
    </div>
</div>',
                    ), true);
            if (!is_wp_error($post_id)) {
                $post_type_object = get_post_type_object('azh_widget');
                exit(wp_redirect(html_entity_decode(add_query_arg('azh', 'customize', admin_url(sprintf($post_type_object->_edit_link . '&action=edit', $post_id))))));
            }
        }
        if (isset($_GET['azm-action']) && $_GET['azm-action'] === 'new-popup') {
            $post_id = wp_insert_post(array(
                'post_title' => esc_html__('Popup', 'azm'),
                'post_type' => 'azh_widget',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_content' => '
<div data-section="popup">
    <div data-element="general/popup.htm" class="">
        <div class="az-popup azPopup" data-popupwidth="300px" data-popuptype="none" data-autoclose="false" data-setcookie="false" data-cookiescope="page" data-overlayvisible="true" data-pageanimationeffect="none" data-popuplocation="bottomRight" data-popupanimationeffect="fadeIn"
            data-popupanimationduration="0.4" data-popupbackground="rgba(255,255,255,1)" data-popupradius="0px" data-popupmargin="20px" data-popuppadding="20px" data-popupboxshadow="0px 0px 60px 0px rgba(0,0,0,0.24)" data-contentanimation="false" data-addclosebutton="true"
            data-buttonstyle="tag" data-enableesc="true" data-showonmobile="true" data-responsive="true" data-mobilebreakpoint="480px" data-mobilelocation="center" data-overlayclosespopup="true" data-overlaycolor="rgba(0,0,0,0.77)" data-cookietriggerclass="setCookie"
            data-cookiename="azpPopup" data-reopenclass="openAZPPopup">
            <div class="azpWindow">
                <div data-cloneable="" class="az-elements-list">
                    <div data-element=""></div>
                </div>
            </div>
        </div>
    </div>
</div>',
                    ), true);
            if (!is_wp_error($post_id)) {
                $post_type_object = get_post_type_object('azh_widget');
                exit(wp_redirect(html_entity_decode(add_query_arg('azh', 'customize', admin_url(sprintf($post_type_object->_edit_link . '&action=edit', $post_id))))));
            }
        }
    }
}

add_filter('template_include', 'azm_template_include');

function azm_template_include($template) {
    if (is_singular() && get_post_type() == 'azm_email_template') {
        $template = locate_template('email-template.php');
        if (!$template) {
            $template = plugin_dir_path(__FILE__) . 'email-template.php';
        }
        return $template;
    }
    return $template;
}

add_action('add_meta_boxes', 'azm_add_meta_boxes', 10, 2);

function azm_add_meta_boxes($post_type, $post) {
    if ($post_type === 'azm_email_template') {
        add_meta_box('azm', __('Email template', 'azm'), 'azm_meta_box', $post_type, 'advanced', 'default');
    }
    add_meta_box(
            'azm-visitor-info', // Unique ID
            esc_html__('Visitor info', 'aza'), // Title
            'azm_visitor_info_meta_box', // Callback function
            'azf_submission', // Admin page (or post type)
            'side', // Context
            'low'         // Priority
    );
    add_meta_box(
            'azm-activity-history', // Unique ID
            esc_html__('Visitor history', 'aza'), // Title
            'azm_activity_history_meta_box', // Callback function
            'azf_submission', // Admin page (or post type)
            'side', // Context
            'low'         // Priority
    );
}

function azm_visitor_info_meta_box($post) {
    $visitor_info = array();
    $visitor_info = apply_filters('azm_visitor_info', $visitor_info, $post);
    ?>
    <div class="azm-visitor-info">
        <table class="widefat">
            <tbody>
                <?php
                foreach ($visitor_info as $info) {
                    ?>
                    <tr>
                        <th><?php print $info['label']; ?></th>
                        <td><?php print $info['value']; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

function azm_activity_history_meta_box($post) {
    wp_enqueue_style('azm_admin', plugins_url('css/admin.css', __FILE__));
    $activity_history = array();
    $activity_history = apply_filters('azm_activity_history', $activity_history, $post);
    krsort($activity_history);
    ?>
    <div class="azm-visitor-history">
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php esc_html_e('Time', 'azm'); ?></th>
                    <th><?php esc_html_e('Activity', 'azm'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($activity_history as $timestamp => $text) {
                    ?>
                    <tr>
                        <td><?php print date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp); ?></td>
                        <td><?php print $text; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

function azm_meta_box($post = NULL, $metabox = NULL, $post_type = 'page') {
    $templates = azm_get_templates();
    if (!empty($templates)) {
        ?>
        <div class="azm-templates">
            <?php
            foreach ($templates as $template) {
                ?>
                <a href="<?php print add_query_arg('template', $template['name'], get_edit_post_link()) ?>" class="azm-template <?php print ($template['name'] == get_post_meta($post->ID, '_template', true) ? 'azm-active' : ''); ?>">
                    <img src="<?php print $template['template_preview'] ?>"/>
                    <div class="azm-name"><?php print $template['name'] ?></div>
                </a>
                <?php
            }
            ?>
        </div>
        <?php
    } else {
        ?>
        <div class="wp-ui-text-notification"><?php printf(__('For email template creation you need <a href="%s">upload</a> base email template.', 'azm'), admin_url('admin.php?page=azh-email-templates-settings')); ?></div>
        <?php
    }
}

add_filter('admin_body_class', 'azm_admin_body_class');

function azm_admin_body_class($classes) {
    global $pagenow;
    if (in_array($pagenow, array('post.php', 'post-new.php')) && get_post_type() == 'azm_email_template') {
        $post = get_post();
        if (get_post_meta($post->ID, '_template', true)) {
            $classes .= ' azm-template';
        }
    }

    return $classes;
}

add_filter('user_can_richedit', 'azm_user_can_richedit');

function azm_user_can_richedit($default) {
    global $post;
    if ('azm_email_template' == get_post_type($post)) {
        return false;
    }
    return $default;
}

add_filter('azh_wp_post_content', 'azm_wp_post_content', 10, 3);

function azm_wp_post_content($override, $content, $post_id) {
    if (!empty($content) && 'azm_email_template' == get_post_type($post_id)) {
        $styles = '';
        $stylesheets = '';
        if (function_exists('azh_filesystem')) {
            azh_filesystem();
            global $wp_filesystem;
            $styles = get_post_meta($post_id, '_styles', true);
            if ($styles && file_exists($styles)) {
                $styles = $wp_filesystem->get_contents($styles);
            }
            $stylesheets = get_post_meta($post_id, '_stylesheets', true);
            if ($stylesheets && file_exists($stylesheets)) {
                $stylesheets = $wp_filesystem->get_contents($stylesheets);
            }
        }
        $fonts_url = azh_get_google_fonts_url(false, $content);
        if ($fonts_url) {
            $stylesheets .= '<link href="' . $fonts_url . '" rel="stylesheet" type="text/css" />';
        }

        $override = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
                . '<html xmlns="http://www.w3.org/1999/xhtml">'
                . '<head>'
                . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'
                . '<meta name="viewport" content="width=device-width" />'
                . $stylesheets
                . '<style type="text/css">'
                . $styles
                . '</style>'
                . '</head>'
                . '<body>'
                . $content
                . '</body>'
                . '</html>';
    }
    return $override;
}

class AZH_Subscription_Widget extends WP_Widget {

    public static function get_body() {
        return '<form class="az-form" action="#" method="post" data-azh-form="[azh_form]"  data-group="form" enctype="multipart/form-data" data-success="' . __('You subscribed successfully', 'azm') . '" data-error="" data-success-redirect="">
            <input type="hidden" name="form_title" value="subscription"/>
            <div data-cloneable="">
                <div data-element="">
                    <p>
                        <input class="az-field" name="email" type="email" value="" placeholder="' . __('Your email', 'azm') . '" required maxlength="" data-mask="" style="width: 100%"/>
                    </p>
                </div>
            </div>
            <button type="submit" style="width: 100%">
                ' . __('Subscribe', 'azm') . '
            </button>    
        </form>';
    }

    function __construct() {
        parent::__construct('azh_subscription_widget', __('AZEXO Subscription Widget', 'azm'));
    }

    function widget($args, $instance) {

        print $args['before_widget'];
        if ($title) {
            print $args['before_title'] . $title . $args['after_title'];
        }

        print do_shortcode(AZH_Subscription_Widget::get_body());

        print $args['after_widget'];
    }

}

add_action('widgets_init', 'azm_register_widgets');

function azm_register_widgets() {
    register_widget('AZH_Subscription_Widget');
}

add_filter('azh_get_forms_from_page', 'azm_get_forms_from_page', 10, 2);

function azm_get_forms_from_page($forms, $page) {
    return array_merge_recursive(azh_get_forms_from_content(AZH_Subscription_Widget::get_body()), $forms);
}

function azm_remove_utf8_bom($text) {
    $bom = pack('H*', 'EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

add_action('wp_ajax_azm_leads_import', 'azm_leads_import');

function azm_leads_import() {
    $user_id = get_current_user_id();
    if ($user_id) {
        if (isset($_POST['file_path']) && isset($_POST['form_title']) && isset($_POST['options']) && isset($_POST['mapping']) && isset($_POST['position']) && is_numeric($_POST['position'])) {
            global $wpdb;
            $imported = 0;
            $position = 0;
            $id_fields = array();
            if (isset($_POST['options']['id_fields'])) {
                $id_fields = $_POST['options']['id_fields'];
            }
            if (($handle = fopen($_POST['file_path'], 'r')) !== false) {
                $header = fgetcsv($handle);
                $header = array_map("azm_remove_utf8_bom", $header);
                if ($_POST['position'] > 0) {
                    fseek($handle, $_POST['position']);
                }
                while (($data = fgetcsv($handle)) !== false) {
                    $imported++;
                    if (count($header) == count($data)) {
                        $row = array_combine($header, $data);
                        if ($row) {
                            $existing = array();
                            foreach ($_POST['mapping'] as $name => $field_name) {
                                if (isset($id_fields[$name]) && $id_fields[$name]) {
                                    if (!empty($row[$name])) {
                                        $ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} as m INNER JOIN {$wpdb->posts} as p ON m.post_id = p.id WHERE p.post_author = %d AND meta_value = %s", $user_id, sanitize_text_field($row[$name])));
                                        $existing = array_merge($existing, $ids);
                                    }
                                } else {
                                    
                                }
                            }
                            $existing = array_unique($existing);

                            if (empty($existing)) {
                                $lead_id = wp_insert_post(array(
                                    'post_title' => isset($_POST['form_title']) ? sanitize_text_field($_POST['form_title']) : '',
                                    'post_type' => 'azf_submission',
                                    'post_status' => 'publish',
                                    'post_author' => $user_id,
                                        ), true);
                                if (!is_wp_error($lead_id)) {
                                    update_post_meta($lead_id, '_hash', uniqid());
                                    update_post_meta($lead_id, 'form_title', sanitize_text_field($_POST['form_title']));

                                    global $wpdb;
                                    $visitor_id = uniqid();
                                    update_post_meta($lead_id, '_azr_visitor', $visitor_id);
                                    $wpdb->query("INSERT INTO {$wpdb->prefix}azr_visitors (visitor_id, last_visit_timestamp) VALUES ('$visitor_id', " . time() . ")");
                                    $wpdb->query("REPLACE INTO {$wpdb->prefix}azr_visitor_posts (post_id, visitor_id, user_id) VALUES (" . $lead_id . ", '$visitor_id', NULL)");

                                    foreach ($_POST['mapping'] as $name => $field_name) {
                                        update_post_meta($lead_id, $field_name, sanitize_text_field($row[$name]));
                                    }
                                }
                            } else {
                                switch ($_POST['options']['existing_leads']) {
                                    case 'skip':
                                        break;
                                    case 'overwrite':
                                        foreach ($existing as $lead_id) {
                                            foreach ($_POST['mapping'] as $name => $field_name) {
                                                update_post_meta($lead_id, $field_name, sanitize_text_field($row[$name]));
                                            }
                                        }
                                        break;
                                    case 'merge':
                                        foreach ($existing as $lead_id) {
                                            foreach ($_POST['mapping'] as $name => $field_name) {
                                                if (!get_post_meta($lead_id, $field_name)) {
                                                    update_post_meta($lead_id, $field_name, sanitize_text_field($row[$name]));
                                                }
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                    }
                    unset($data);
                    if ($imported > 100) {
                        $position = ftell($handle);
                        break;
                    }
                }
                fclose($handle);
            }
            print json_encode(array(
                'position' => $position,
                'imported' => $imported
            ));
        } else {
            $file_name = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
            if ($file_name) {
                $file_path = get_temp_dir() . $file_name;
                $hSource = fopen('php://input', 'r');
                $hDest = fopen($file_path, 'w');
                while (!feof($hSource)) {
                    $chunk = fread($hSource, 1024);
                    fwrite($hDest, $chunk);
                }
                fclose($hSource);
                fclose($hDest);


                $examples = array();
                $total = 0;
                if (($handle = fopen($file_path, 'r')) !== false) {
                    $header = fgetcsv($handle);
                    $header = array_map("azm_remove_utf8_bom", $header);
                    while (($data = fgetcsv($handle)) !== false) {
                        $total++;
                        if (count($examples) < 5) {
                            $examples[] = array_combine($header, $data);
                        }
                        unset($data);
                    }
                    fclose($handle);
                }

                print json_encode(array(
                    'examples' => $examples,
                    'file_path' => $file_path,
                    'total' => $total,
                ));
            }
        }
    }
    wp_die();
}

add_action('wp_ajax_azm_leads_delete', 'azm_leads_delete');

function azm_leads_delete() {
    if (wp_verify_nonce($_POST['nonce'], 'ajax')) {
        $args = array(
            'post_type' => 'azf_submission',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => (isset($_POST['number']) && is_numeric($_POST['number'])) ? $_POST['number'] : 10,
            'meta_query' => array()
        );
        if (isset($_POST['form']) && !empty($_POST['form'])) {
            $forms = (array) $_POST['form'];
            foreach ($forms as &$form) {
                $form = sanitize_text_field($form);
            }
            if (!empty($forms)) {
                $args['meta_query'][] = array(
                    'key' => 'form_title',
                    'value' => $forms,
                    'compare' => 'IN'
                );
            }
        }
        $leads = new WP_Query($args);
        if ($leads->post_count) {
            foreach ($leads->posts as $lead) {
                wp_delete_post($lead->ID, true);
            }
        }
        print ($leads->found_posts - $leads->post_count);
    }
    wp_die();
}

add_action('wp_ajax_azm_update_user', 'azm_update_user');

function azm_update_user() {
    if (isset($_POST['user']) && isset($_POST['user']['ID']) && is_numeric($_POST['user']['ID'])) {
        $user_id = get_current_user_id();
        if ($user_id && $user_id == $_POST['user']['ID']) {
            $user_data = $_POST['user'];
            if (count($user_data) > 1) {
                wp_update_user($user_data);
            }

            if (isset($_POST['meta']) && is_array($_POST['meta'])) {
                $user_meta = $_POST['meta'];
                foreach ($user_meta as $key => $value) {
                    update_user_meta($user_id, $key, $value);
                }
            }
        }
    }
    wp_die();
}

function azm_log($message) {
    global $wp_filesystem;
    azh_filesystem();
    $wp_upload_dir = wp_upload_dir();
    $contents = $wp_filesystem->get_contents($wp_upload_dir['basedir'] . '/log.txt');
    $contents .= $message;
    $wp_filesystem->put_contents($wp_upload_dir['basedir'] . '/log.txt', $contents);
}

function azm_sql_tokens($string, $fields) {
    $string = preg_replace_callback('#\({([\w\d\_\-]+)}\)#', function($m) use ($fields) {
        if (isset($fields[strtolower($m[1])])) { // If it exists in our array            
            if (is_array($fields[strtolower($m[1])])) {
                $values = $fields[strtolower($m[1])];
                if (empty($values)) {
                    return '';
                } else {
                    $v = reset($values);
                    if (is_numeric($v)) {
                        return "(" . implode(',', $values) . ")"; // Then replace it from our array
                    } else {
                        return "('" . implode("','", $values) . "')"; // Then replace it from our array
                    }
                }
            } else {
                if (is_numeric($fields[strtolower($m[1])])) {
                    return "(" . $fields[strtolower($m[1])] . ")"; // Then replace it from our array
                } else {
                    return "('" . $fields[strtolower($m[1])] . "')"; // Then replace it from our array
                }
            }
        } else {
            return $m[0]; // Otherwise return the whole match (basically we won't change it)
        }
    }, $string);
    $string = preg_replace_callback('#{([\w\d\_\-]+)}#', function($m) use ($fields, $string) {
        if (isset($fields[strtolower($m[1])])) { // If it exists in our array            
            if (is_array($fields[strtolower($m[1])])) {
                $values = $fields[strtolower($m[1])];
                if (empty($values)) {
                    return '';
                } else {
                    return implode(',', $values); // Then replace it from our array
                }
            } else {
                return $fields[strtolower($m[1])]; // Then replace it from our array
            }
        } else {
            return $m[0]; // Otherwise return the whole match (basically we won't change it)
        }
    }, $string);
    return $string;
}

function azm_tokens($string, $fields) {
    $string = preg_replace_callback('#{([\w\d\_\-]+)}#', function($m) use ($fields) {
        if (isset($fields[strtolower($m[1])])) { // If it exists in our array            
            if (is_array($fields[strtolower($m[1])])) {
                return implode(',', $values); // Then replace it from our array
            } else {
                return $fields[strtolower($m[1])]; // Then replace it from our array
            }
        } else {
            return $m[0]; // Otherwise return the whole match (basically we won't change it)
        }
    }, $string);
    return $string;
}

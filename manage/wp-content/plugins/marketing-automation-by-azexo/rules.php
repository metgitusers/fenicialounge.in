<?php
register_activation_hook(AZM_FILE, 'azr_activate');

function azr_activate() {
    global $wpdb;
    $collate = '';

    if ($wpdb->has_cap('collation')) {
        $collate = $wpdb->get_charset_collate();
    }

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}azr_visitors (
                visitor_id varchar(32) NOT NULL,
                user_id bigint(20),
                last_visit_timestamp int(11) unsigned NOT NULL,
                points int(11) DEFAULT 0,
                country_code varchar(5),
                city_name varchar(50),
                KEY country (country_code),
                KEY city (city_name),
                UNIQUE KEY visitor (visitor_id),
                UNIQUE KEY user (user_id)
    ) $collate;");
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}azr_visitor_tags (
                visitor_id varchar(32) NOT NULL,
                tag varchar(50) NOT NULL,
                UNIQUE KEY visitor_tag (visitor_id, tag),
                KEY tag (tag)
    ) $collate;");
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}azr_page_visits (
                visitor_id varchar(32) NOT NULL,
                page_id bigint(20) NOT NULL,
                last_visit_timestamp int(11) unsigned NOT NULL,
                visits_count int(11) DEFAULT 1,
                UNIQUE KEY visitor (visitor_id, page_id),
                KEY page_id (page_id)
    ) $collate;");
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}azr_visitor_posts (
                post_id bigint(20) NOT NULL,
                visitor_id varchar(32),
                user_id bigint(20),
                UNIQUE KEY post (post_id),
                KEY visitor (visitor_id),
                KEY user (user_id)
    ) $collate;");

//    $wpdb->query("ALTER TABLE {$wpdb->postmeta} ADD INDEX(meta_value(30))");
//    if (!empty($wpdb->is_mysql) && version_compare($wpdb->db_version(), '5.5')) {
//        $wpdb->query("ALTER TABLE {$wpdb->comments} ADD FULLTEXT(comment_content)");
//    }
    if (!wp_next_scheduled('azr_cron_process')) {
        wp_schedule_event(time(), 'every_one_minute', 'azr_cron_process');
    }
}

function azr_db_update() {
    global $wpdb;
    $columns = $wpdb->query("SHOW COLUMNS FROM {$wpdb->prefix}azr_visitors LIKE 'country_code'");
    if (!$columns) {
        $wpdb->query("ALTER TABLE {$wpdb->prefix}azr_visitors ADD country_code varchar(5)");
        $wpdb->query("ALTER TABLE {$wpdb->prefix}azr_visitors ADD city_name varchar(50)");
        $wpdb->query("ALTER TABLE {$wpdb->prefix}azr_visitors ADD INDEX(country_code)");
        $wpdb->query("ALTER TABLE {$wpdb->prefix}azr_visitors ADD INDEX(city_name)");
    }
}

add_filter('cron_schedules', 'azr_cron_schedules');

function azr_cron_schedules($schedules) {
    $schedules['every_one_minute'] = array('interval' => 1 * MINUTE_IN_SECONDS, 'display' => __('Every one minute', 'azm'));
    return $schedules;
}

add_action('admin_enqueue_scripts', 'azr_admin_scripts');

function azr_admin_scripts() {
    wp_enqueue_style('select2', plugins_url('css/select2.css', __FILE__));
    wp_enqueue_script('select2', plugins_url('js/select2.js', __FILE__), array('jquery'), AZM_PLUGIN_VERSION, true);
    wp_enqueue_style('azr_rules', plugins_url('css/rules.css', __FILE__));
    wp_enqueue_script('azr_rules', plugins_url('js/rules.js', __FILE__), array('jquery', 'jquery-ui-sortable', 'jquery-ui-autocomplete'), AZM_PLUGIN_VERSION, true);

    $forms = function_exists('azh_get_forms_from_pages') ? azh_get_forms_from_pages() : array();
    $forms = apply_filters('azr_forms', $forms);
    $azr = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'settings' => azr_get_settings(),
        'forms' => $forms,
        'import_forms' => is_array(get_user_meta(get_current_user_id(), 'import_forms', true)) ? get_user_meta(get_current_user_id(), 'import_forms', true) : array(),
        'i18n' => array(
            'remove' => __('Remove', 'azm'),
            'add_condition' => __('Add condition', 'azm'),
            'add_action' => __('Add action', 'azm'),
            'add_or' => __('Add OR', 'azm'),
            'add_and' => __('Add AND', 'azm'),
            'or' => __('OR - any of the following conditions:', 'azm'),
            'and' => __('AND - all of the following conditions:', 'azm'),
            'negate' => __('Negate', 'azm'),
            'event' => __('Event', 'azm'),
            'conditions' => __('CONDITIONS', 'azm'),
            'actions' => __('ACTIONS (performed if conditions logical expression is true)', 'azm'),
            'event' => __('EVENT (moment when conditions will be checked)', 'azm'),
            'wp_users' => __('WP users', 'azm'),
            'form' => __('Form', 'azm'),
            'email_field' => __('Email field', 'azm'),
            'required_event' => __('Required event:', 'azm'),
            'required_condition' => __('Required condition:', 'azm'),
            'minutes' => __('Minutes', 'azm'),
            'hours' => __('Hours', 'azm'),
            'days' => __('Days', 'azm'),
            'weeks' => __('Weeks', 'azm'),
            'select_an_option' => __('Select an option', 'azm'),
        ),
    );
    $azr = apply_filters('azr_object', $azr);
    wp_localize_script('azr_rules', 'azr', $azr);
}

function azr_get_settings() {
    static $azr = false;
    if ($azr) {
        return $azr;
    }
    azr_db_update();
    $gmt_offset = get_option('gmt_offset') * HOUR_IN_SECONDS;
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

    $azh_widgets = get_posts(array(
        'post_type' => 'azh_widget',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => -1,
        'numberposts' => -1,
    ));
    $azh_widget_options = array();
    if (!empty($azh_widgets)) {
        foreach ($azh_widgets as $azh_widget) {
            $azh_widget_options[$azh_widget->ID] = $azh_widget->post_title;
        }
    }

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
    $forms_options = array();
    $forms_helpers = array();
    $fields_options = array();
    $forms_fields = array();
    $forms = function_exists('azh_get_forms_from_pages') ? azh_get_forms_from_pages() : array();
    $forms = apply_filters('azr_forms', $forms);
    if (is_array($forms)) {
        foreach ($forms as $form_title => $fields) {
            $forms_options[$form_title] = $form_title;
            $forms_fields[$form_title] = array();
            $forms_helpers[$form_title] = '<div class="azr-tokens"><label>' . __('Available tokens for template:', 'azm') . '</label>';
            foreach ($fields as $name => $field) {
                if ($name != 'form_title') {
                    $fields_options[$name] = $name;
                    $forms_fields[$form_title][$name] = $name;
                    $forms_helpers[$form_title] .= '<input type="text" value="{' . $name . '}"/>';
                }
            }
            $forms_helpers[$form_title] .= '</div>';
        }
    }



    $post_types = get_post_types(array(), 'objects');
    $post_types_options = array();
    if (is_array($post_types) && !empty($post_types)) {
        foreach ($post_types as $slug => $post_type) {
            if ($slug !== 'revision' && $slug !== 'nav_menu_item'/* && $slug !== 'attachment' */) {
                $post_types_options[$slug] = $post_type->label;
            }
        }
    }
    $tags_options = array();
    $tags = get_terms(array(
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ));
    if ($tags) {
        foreach ($tags as $tag) {
            $tags_options[$tag->term_id] = $tag->name;
        }
    }
    $categories_options = array();
    $categories = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
    ));
    if ($categories) {
        foreach ($categories as $category) {
            $categories_options[$category->term_id] = $category->name;
        }
    }
    global $wp_locale, $wpdb;
    $visitor_tags_options = $wpdb->get_results("SELECT DISTINCT tag FROM {$wpdb->prefix}azr_visitor_tags", ARRAY_A);
    $visitor_tags_options = array_map(function($value) {
        return $value['tag'];
    }, $visitor_tags_options);
    $visitor_tags_options = array_combine($visitor_tags_options, $visitor_tags_options);

    require_once ABSPATH . 'wp-admin/includes/user.php';
    $user_role_options = array();
    $user_roles = get_editable_roles();
    foreach ($user_roles as $slug => $role) {
        $user_role_options[$slug] = $role['name'];
    }


    $countries = $wpdb->get_results("SELECT DISTINCT country_code FROM {$wpdb->prefix}azr_visitors", ARRAY_A);
    $countries_autocomplete = array();
    foreach ($countries as $country) {
        if ($country['country_code']) {
            $countries_autocomplete[$country['country_code']] = azr_get_country_name($country['country_code']);
        }
    }

    $cities = $wpdb->get_results("SELECT DISTINCT city_name FROM {$wpdb->prefix}azr_visitors", ARRAY_A);
    $cities = array_map(function($value) {
        return $value['city_name'];
    }, $cities);
    $cities = array_filter($cities);
    $cities = array_values($cities);
    $cities = array_combine($cities, $cities);


    $azr = array(
        'events' => array(
            'scheduler' => array(
                'name' => __('Scheduler', 'azm'),
                'description' => __('Conditions  will be applied to all known site visitors', 'azm'),
                'set_context' => array('visitors' => true),
                'parameters' => array(
                    'when' => array(
                        'type' => 'dropdown',
                        'label' => __('When', 'azm'),
                        'options' => array(
                            'immediately' => __('Immediately', 'azm'),
                            'start_at_date' => __('Start at date', 'azm'),
                            'every_hour' => __('Every hour', 'azm'),
                            'every_day' => __('Every day', 'azm'),
                            'every_week_day' => __('Every week day', 'azm'),
                            'every_month_day' => __('Every month day', 'azm'),
                        ),
                        'default' => 'immediately',
                    ),
                    'date' => array(
                        'type' => 'date',
                        'label' => __('Date', 'azm'),
                        'dependencies' => array(
                            'when' => array('start_at_date'),
                        ),
                    ),
                    'week_day' => array(
                        'type' => 'dropdown',
                        'label' => __('Week day', 'azm'),
                        'options' => array(
                            '0' => $wp_locale->get_weekday(0),
                            '1' => $wp_locale->get_weekday(1),
                            '2' => $wp_locale->get_weekday(2),
                            '3' => $wp_locale->get_weekday(3),
                            '4' => $wp_locale->get_weekday(4),
                            '5' => $wp_locale->get_weekday(5),
                            '6' => $wp_locale->get_weekday(6),
                        ),
                        'dependencies' => array(
                            'when' => array('every_week_day'),
                        ),
                    ),
                    'month_day' => array(
                        'type' => 'dropdown',
                        'label' => __('Month day', 'azm'),
                        'options' => array_combine(range(1, 31), range(1, 31)),
                        'dependencies' => array(
                            'when' => array('every_month_day'),
                        ),
                    ),
                ),
            ),
            'form_submit' => array(
                'name' => __('Form submit', 'azm'),
                'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
                'set_context' => array('visitors' => true, 'visitor_id' => true),
                'where_clause' => "v.visitor_id = '{visitor_id}'",
                'parameters' => array(
                    'form' => array(
                        'type' => 'dropdown',
                        'label' => __('Form title', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'options' => $forms_options,
                        'helpers' => $forms_helpers,
                    ),
                    'delay' => array(
                        'type' => 'number',
                        'label' => __('Reaction delay (days)', 'azm'),
                        'required' => true,
                        'step' => '0.1',
                        'default' => '0',
                    ),
                ),
            ),
            'new_post' => array(
                'name' => __('New post', 'azm'),
                'description' => __('Conditions  will be applied to all known site visitors', 'azm'),
                'helpers' => '<div class="azr-tokens"><label>' . __('Available tokens for template:', 'azm') . '</label><input type="text" value="{post_url}"/><input type="text" value="{post_title}"/></div>',
                'set_context' => array('visitors' => true),
                'parameters' => array(
                    'post_type' => array(
                        'type' => 'dropdown',
                        'label' => __('Post type', 'azm'),
                        'required' => true,
                        'options' => $post_types_options,
                    ),
                ),
            ),
            'visitor_leave_comment' => array(
                'name' => __('Visitor leave comment', 'azm'),
                'group' => __('Visitor leave', 'azm'),
                'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
                'set_context' => array('visitors' => true, 'visitor_id' => true),
                'where_clause' => "v.visitor_id = '{visitor_id}'",
                'parameters' => array(
                    'delay' => array(
                        'type' => 'number',
                        'label' => __('Reaction delay (days)', 'azm'),
                        'required' => true,
                        'step' => '0.1',
                        'default' => '0',
                    ),
                ),
            ),
            'visit' => array(
                'name' => __('Visit', 'azm'),
                'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
                'set_context' => array('visitors' => true, 'visitor_id' => true),
                'where_clause' => "v.visitor_id = '{visitor_id}'",
                'parameters' => array(
                    'site_place' => array(
                        'type' => 'dropdown',
                        'label' => __('Site place', 'azm'),
                        'options' => array(
                            'any' => __('Any place', 'azm'),
                            'page' => __('Page', 'azm'),
                            'child_page' => __('Child page', 'azm'),
                            'post_type' => __('Post type', 'azm'),
                            'post_type_archive' => __('Post type archive', 'azm'),
                            'home' => __('Home page', 'azm'),
                            'tags' => __('Tags', 'azm'),
                            'categories' => __('Categories', 'azm'),
                        ),
                        'default' => 'any',
                    ),
                    'page' => array(
                        'type' => 'multiselect',
                        'label' => __('Page name', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a page', 'azm'),
                        'options' => $pages_options,
                        'dependencies' => array(
                            'site_place' => array('page', 'child_page'),
                        ),
                    ),
                    'post_type' => array(
                        'type' => 'dropdown',
                        'label' => __('Post type', 'azm'),
                        'required' => true,
                        'options' => $post_types_options,
                        'dependencies' => array(
                            'site_place' => array('post_type'),
                        ),
                    ),
                    'post_type_archive' => array(
                        'type' => 'dropdown',
                        'label' => __('Post type archive', 'azm'),
                        'required' => true,
                        'options' => $post_types_options,
                        'dependencies' => array(
                            'site_place' => array('post_type_archive'),
                        ),
                    ),
                    'tags' => array(
                        'type' => 'multiselect',
                        'label' => __('Tags', 'azm'),
                        'required' => true,
                        'options' => $tags_options,
                        'dependencies' => array(
                            'site_place' => array('tags'),
                        ),
                    ),
                    'categories' => array(
                        'type' => 'multiselect',
                        'label' => __('Categories', 'azm'),
                        'required' => true,
                        'options' => $categories_options,
                        'dependencies' => array(
                            'site_place' => array('categories'),
                        ),
                    ),
                    'delay' => array(
                        'type' => 'number',
                        'label' => __('Reaction delay (days)', 'azm'),
                        'required' => true,
                        'step' => '0.1',
                        'default' => '0',
                    ),
                ),
            ),
        ),
        'contexts' => array(
            'visitors' => array(
                'db_query' => array(
                    'fields' => array("v.visitor_id", "v.user_id"),
                    'from' => "{$wpdb->prefix}azr_visitors as v",
                    'where' => array(),
                ),
            ),
        ),
        'conditions' => array(
            'performing_period' => array(
                'name' => __('Period', 'azm'),
                'group' => __('Rule performing', 'azm'),
                'context' => true,
                'parameters' => array(
                    'performing_from_date' => array(
                        'type' => 'date',
                        'label' => __('From date', 'azm'),
                    ),
                    'performing_to_date' => array(
                        'type' => 'date',
                        'label' => __('To date', 'azm'),
                    ),
                ),
            ),
            'performing_months' => array(
                'name' => __('Months', 'azm'),
                'group' => __('Rule performing', 'azm'),
                'context' => true,
                'parameters' => array(
                    'performing_months' => array(
                        'type' => 'multiselect',
                        'label' => __('Month', 'azm'),
                        'options' => array(
                            '01' => $wp_locale->get_month('01'),
                            '02' => $wp_locale->get_month('02'),
                            '03' => $wp_locale->get_month('03'),
                            '04' => $wp_locale->get_month('04'),
                            '05' => $wp_locale->get_month('05'),
                            '06' => $wp_locale->get_month('06'),
                            '07' => $wp_locale->get_month('07'),
                            '08' => $wp_locale->get_month('08'),
                            '09' => $wp_locale->get_month('09'),
                            '10' => $wp_locale->get_month('10'),
                            '11' => $wp_locale->get_month('11'),
                            '12' => $wp_locale->get_month('12'),
                        ),
                    ),
                ),
            ),
            'performing_week_days' => array(
                'name' => __('Week day', 'azm'),
                'group' => __('Rule performing', 'azm'),
                'context' => true,
                'parameters' => array(
                    'performing_week_days' => array(
                        'type' => 'multiselect',
                        'label' => __('Week day', 'azm'),
                        'options' => array(
                            '0' => $wp_locale->get_weekday(0),
                            '1' => $wp_locale->get_weekday(1),
                            '2' => $wp_locale->get_weekday(2),
                            '3' => $wp_locale->get_weekday(3),
                            '4' => $wp_locale->get_weekday(4),
                            '5' => $wp_locale->get_weekday(5),
                            '6' => $wp_locale->get_weekday(6),
                        ),
                    ),
                ),
            ),
            'performing_hours' => array(
                'name' => __('Hours', 'azm'),
                'group' => __('Rule performing', 'azm'),
                'context' => true,
                'parameters' => array(
                    'performing_hours' => array(
                        'type' => 'multiselect',
                        'label' => __('Hours', 'azm'),
                        'options' => array_combine(range(0, 23), range(0, 23)),
                    ),
                ),
            ),
            'is_registered_user' => array(
                'name' => __('Is registered user', 'azm'),
                'group' => __('Registered user', 'azm'),
                'helpers' => '<div class="azr-tokens"><label>' . __('Available tokens for template:', 'azm') . '</label><input type="text" value="{nickname}"/><input type="text" value="{first_name}"/><input type="text" value="{last_name}"/></div>',
                'query_where' => true,
                'required_context' => array('visitors'),
                'set_context' => array('registered' => true),
                'where_clause' => "v.user_id IS NOT NULL",
            ),
            'visitor_has_tag' => array(
                'name' => __('Visitor has tag', 'azm'),
                'group' => __('Visitor', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'where_clause' => "v.visitor_id IN (SELECT visitor_id FROM {$wpdb->prefix}azr_visitor_tags WHERE tag = '{tag}' {{AND visitor_id IN ({visitor_id})}})",
                'parameters' => array(
                    'tag' => array(
                        'type' => 'dropdown',
                        'label' => __('Tag', 'azm'),
                        'required' => true,
                        'options' => $visitor_tags_options,
                    ),
                ),
            ),
            'visitor_points' => array(
                'name' => __('Visitor points', 'azm'),
                'group' => __('Visitor', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'relation' => array(
                        'type' => 'dropdown',
                        'label' => __('Relation', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is_greater_than' => __('Is greater than', 'azm'),
                            'is_less_than' => __('Is less than', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is_greater_than' => "v.points > {points}",
                            'is_less_than' => "v.points < {points}",
                        ),
                        'default' => 'is_greater_than',
                    ),
                    'points' => array(
                        'type' => 'number',
                        'label' => __('Points', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'user_has_role' => array(
                'name' => __('User has role', 'azm'),
                'group' => __('Registered user', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'where_clause' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = 'wp_capabilities' AND um.meta_value LIKE '%\"{role}\"%')",
                'parameters' => array(
                    'role' => array(
                        'type' => 'dropdown',
                        'label' => __('Role', 'azm'),
                        'required' => true,
                        'options' => $user_role_options,
                    ),
                ),
            ),
            'user_has_meta_key' => array(
                'name' => __('User has meta-key', 'azm'),
                'group' => __('Registered user', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'where_clause' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}')",
                'parameters' => array(
                    'meta-key' => array(
                        'type' => 'text',
                        'label' => __('Meta-key', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'user_meta_field' => array(
                'name' => __('User meta-field', 'azm'),
                'group' => __('Registered user', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'meta-key' => array(
                        'type' => 'text',
                        'label' => __('Meta-key', 'azm'),
                        'required' => true,
                    ),
                    'relation' => array(
                        'type' => 'dropdown',
                        'label' => __('Relation', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is' => __('Is', 'azm'),
                            'is_not' => __('Is not', 'azm'),
                            'contains' => __('Contains', 'azm'),
                            'does_not_contain' => __('Does not contain', 'azm'),
                            'starts_with' => __('Starts with', 'azm'),
                            'ends_with' => __('Ends with', 'azm'),
                            'is_greater_than' => __('Is greater than', 'azm'),
                            'is_less_than' => __('Is less than', 'azm'),
                            'is_blank' => __('Is blank', 'azm'),
                            'is_not_blank' => __('Is not blank', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND um.meta_value = '{text_value}')",
                            'is_not' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND um.meta_value = '{text_value}')",
                            'contains' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND um.meta_value LIKE '%{text_value}%')",
                            'does_not_contain' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND um.meta_value LIKE '%{text_value}%')",
                            'starts_with' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND um.meta_value LIKE '%{text_value}')",
                            'ends_with' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND um.meta_value LIKE '{text_value}%')",
                            'is_greater_than' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND CAST(um.meta_value AS DECIMAL(10, 2)) > {number_value})",
                            'is_less_than' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND CAST(um.meta_value AS DECIMAL(10, 2)) < {number_value})",
                            'is_blank' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND (um.meta_value = '' OR um.meta_value IS NULL))",
                            'is_not_blank' => "v.user_id IN (SELECT um.user_id FROM {$wpdb->usermeta} as um WHERE {{um.user_id IN ({user_id}) AND}} um.meta_key = '{meta-key}' AND (um.meta_value <> '' AND um.meta_value IS NOT NULL))",
                        ),
                        'default' => 'is',
                    ),
                    'text_value' => array(
                        'type' => 'text',
                        'label' => __('Value', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'relation' => array('is', 'is_not', 'contains', 'does_not_contain', 'starts_with', 'ends_with'),
                        ),
                    ),
                    'number_value' => array(
                        'type' => 'number',
                        'label' => __('Value', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'relation' => array('is_greater_than', 'is_less_than'),
                        ),
                    ),
                ),
            ),
            'user_comment' => array(
                'name' => __('User comment', 'azm'),
                'group' => __('Registered user', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'status' => array(
                        'type' => 'dropdown',
                        'label' => __('Status', 'azm'),
                        'required' => true,
                        'options' => array(
                            'leave' => __('Leave at least one', 'azm'),
//                            'not_leave' => __('Did not leave any', 'azm'),
                        ),
                        'where_clauses' => array(
                            'leave' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->comments} as c {{WHERE c.user_id IN ({user_id})}})",
//                            'not_leave' => "v.user_id IS NOT NULL AND v.user_id NOT IN (SELECT DISTINCT c.user_id FROM {$wpdb->comments} as c)",
                        ),
                        'default' => 'leave',
                    ),
                ),
            ),
            'user_comment_count' => array(
                'name' => __('User comment count', 'azm'),
                'group' => __('Registered user', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'relation' => array(
                        'type' => 'dropdown',
                        'label' => __('Relation', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is_greater_than' => __('Is greater than', 'azm'),
                            'is_less_than' => __('Is less than', 'azm'),
                        ),
                        'default' => 'is_greater_than',
                        'where_clauses' => array(
                            'is_greater_than' => "v.user_id IN (SELECT r.user_id FROM (SELECT c.user_id as user_id, count(c.comment_ID) as comments FROM {$wpdb->comments} as c {{WHERE c.user_id IN ({user_id}) AND}} GROUP BY user_id HAVING comments > {count}) as r)",
                            'is_less_than' => "v.user_id IN (SELECT r.user_id FROM (SELECT c.user_id as user_id, count(c.comment_ID) as comments FROM {$wpdb->comments} as c {{WHERE c.user_id IN ({user_id}) AND}} GROUP BY user_id HAVING comments < {count}) as r)",
                        ),
                    ),
                    'count' => array(
                        'type' => 'number',
                        'label' => __('Count', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'last_user_comment_date' => array(
                'name' => __('Last user comment date', 'azm'),
                'group' => __('Registered user', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'comment_date' => array(
                        'type' => 'dropdown',
                        'label' => __('Comment date', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is_within' => __('Is within', 'azm'),
                            'is_not_within' => __('Is not within', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->comments} as c WHERE {{c.user_id IN ({user_id}) AND}} c.comment_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                            'is_not_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->comments} as c WHERE {{c.user_id IN ({user_id}) AND}} c.comment_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                        ),
                        'default' => 'is_within',
                    ),
                    'days' => array(
                        'type' => 'number',
                        'label' => __('Days', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'email_subscription' => array(
                'name' => __('Email subscription', 'azm'),
                'group' => __('Forms', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'context' => true,
                'parameters' => array(
                    'email_subscription_form' => array(
                        'type' => 'dropdown',
                        'label' => __('Subscription form', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'options' => $forms_options,
                        'helpers' => $forms_helpers,
                    ),
                    'email_field' => array(
                        'type' => 'dropdown',
                        'label' => __('Field of this form which contain email of subscriber', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'dependency' => 'email_subscription_form',
                        'dependent_options' => $forms_fields,
                    ),
                    'email_subscription_status' => array(
                        'type' => 'dropdown',
                        'label' => __('Subscription status', 'azm'),
                        'options' => array(
                            'subscribed' => __('Subscribed', 'azm'),
                            'unsubscribed' => __('Unsubscribed', 'azm'),
//                            'non-subscribed' => __('Non-subscribed', 'azm'),
                        ),
                        'default' => 'subscribed',
                        'where_clauses' => array(
                            'subscribed' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ft ON ft.post_id = fs.post_id INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id LEFT JOIN {$wpdb->postmeta} as ss ON ss.post_id = fs.post_id AND ss.meta_key = '_unsubscribed' WHERE ss.meta_value IS NULL AND {{fs.visitor_id IN ({visitor_id}) AND}} ft.meta_key = 'form_title' AND ft.meta_value = '{email_subscription_form}' AND ff.meta_key = '{email_field}' AND ff.meta_value IS NOT NULL)",
                            'unsubscribed' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ft ON ft.post_id = fs.post_id INNER JOIN {$wpdb->postmeta} as ss ON ss.post_id = fs.post_id AND ss.meta_key = '_unsubscribed' AND ss.meta_value IS NOT NULL WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ft.meta_key = 'form_title' AND ft.meta_value = '{email_subscription_form}')",
//                            'non-subscribed' => "v.visitor_id NOT IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ft ON ft.post_id = fs.post_id INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE ft.meta_key = 'form_title' AND ft.meta_value = '{email_subscription_form}' AND ff.meta_key = '{email_field}' AND ff.meta_value IS NOT NULL)",
                        ),
                    ),
                ),
            ),
            'phone_subscription' => array(
                'name' => __('Phone subscription', 'azm'),
                'group' => __('Forms', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'context' => true,
                'parameters' => array(
                    'phone_subscription_form' => array(
                        'type' => 'dropdown',
                        'label' => __('Subscription form', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'options' => $forms_options,
                        'helpers' => $forms_helpers,
                    ),
                    'phone_field' => array(
                        'type' => 'dropdown',
                        'label' => __('Field of this form which contain phone number of subscriber', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'dependency' => 'phone_subscription_form',
                        'dependent_options' => $forms_fields,
                    ),
                    'phone_subscription_status' => array(
                        'type' => 'dropdown',
                        'label' => __('Subscription status', 'azm'),
                        'options' => array(
                            'subscribed' => __('Subscribed', 'azm'),
                            'unsubscribed' => __('Unsubscribed', 'azm'),
//                            'non-subscribed' => __('Non-subscribed', 'azm'),
                        ),
                        'default' => 'subscribed',
                        'where_clauses' => array(
                            'subscribed' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ft ON ft.post_id = fs.post_id INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id LEFT JOIN {$wpdb->postmeta} as ss ON ss.post_id = fs.post_id AND ss.meta_key = '_unsubscribed' WHERE ss.meta_value IS NULL AND {{fs.visitor_id IN ({visitor_id}) AND}} ft.meta_key = 'form_title' AND ft.meta_value = '{phone_subscription_form}' AND ff.meta_key = '{phone_field}' AND ff.meta_value IS NOT NULL)",
                            'unsubscribed' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ft ON ft.post_id = fs.post_id INNER JOIN {$wpdb->postmeta} as ss ON ss.post_id = fs.post_id AND ss.meta_key = '_unsubscribed' AND ss.meta_value IS NOT NULL WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ft.meta_key = 'form_title' AND ft.meta_value = '{phone_subscription_form}')",
//                            'non-subscribed' => "v.visitor_id NOT IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ft ON ft.post_id = fs.post_id INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ft.meta_key = 'form_title' AND ft.meta_value = '{phone_subscription_form}' AND ff.meta_key = '{phone_field}' AND ff.meta_value IS NOT NULL)",
                        ),
                    ),
                ),
            ),
            'form' => array(
                'name' => __('Form', 'azm'),
                'group' => __('Forms', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'form_title' => array(
                        'type' => 'dropdown',
                        'label' => __('Form title', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'options' => $forms_options,
                        'helpers' => $forms_helpers,
                    ),
                    'status' => array(
                        'type' => 'dropdown',
                        'label' => __('Status', 'azm'),
                        'required' => true,
                        'options' => array(
                            'submitted' => __('Submited', 'azm'),
//                            'not_submitted' => __('Not submitted', 'azm'),
                        ),
                        'where_clauses' => array(
                            'submitted' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = 'form_title' AND ff.meta_value = '{form_title}')",
//                            'not_submitted' => "v.visitor_id NOT IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE ff.meta_key = 'form_title' AND ff.meta_value = '{form_title}')",
                        ),
                        'default' => 'submitted',
                    ),
                ),
            ),
            'page' => array(
                'name' => __('Page', 'azm'),
                'group' => __('Visits', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'page' => array(
                        'type' => 'dropdown',
                        'label' => __('Page name', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a page', 'azm'),
                        'options' => $pages_options,
                    ),
                    'status' => array(
                        'type' => 'dropdown',
                        'label' => __('Status', 'azm'),
                        'required' => true,
                        'options' => array(
                            'visited' => __('Visited', 'azm'),
//                            'not_visited' => __('Not visited', 'azm'),
                        ),
                        'where_clauses' => array(
                            'visited' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE {{pv.visitor_id IN ({visitor_id}) AND}} pv.page_id = {page})",
//                            'not_visited' => "v.visitor_id NOT IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE pv.page_id = {page})",
                        ),
                        'default' => 'visited',
                    ),
                ),
            ),
            'page_visit_date' => array(
                'name' => __('Page visit date', 'azm'),
                'group' => __('Visits', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'page' => array(
                        'type' => 'dropdown',
                        'label' => __('Page name', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a page', 'azm'),
                        'options' => $pages_options,
                    ),
                    'visited' => array(
                        'type' => 'dropdown',
                        'label' => __('Visited', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is_after' => __('Is after', 'azm'),
                            'is_before' => __('Is before', 'azm'),
                            'is' => __('Is', 'azm'),
                            'is_within' => __('Is within', 'azm'),
                            'is_not_within' => __('Is not within', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is_after' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE {{pv.visitor_id IN ({visitor_id}) AND}} pv.page_id = {page} AND DATE(FROM_UNIXTIME(pv.last_visit_timestamp + $gmt_offset)) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_before' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE {{pv.visitor_id IN ({visitor_id}) AND}} pv.page_id = {page} AND DATE(FROM_UNIXTIME(pv.last_visit_timestamp + $gmt_offset)) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE {{pv.visitor_id IN ({visitor_id}) AND}} pv.page_id = {page} AND DATE(FROM_UNIXTIME(pv.last_visit_timestamp + $gmt_offset)) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_within' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE {{pv.visitor_id IN ({visitor_id}) AND}} pv.page_id = {page} AND pv.last_visit_timestamp >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                            'is_not_within' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE {{pv.visitor_id IN ({visitor_id}) AND}} pv.page_id = {page} AND pv.last_visit_timestamp < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                        ),
                        'default' => 'is',
                    ),
                    'date' => array(
                        'type' => 'date',
                        'label' => __('Date', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'visited' => array('is_after', 'is_before', 'is'),
                        ),
                    ),
                    'days' => array(
                        'type' => 'number',
                        'label' => __('Days', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'visited' => array('is_within', 'is_not_within'),
                        ),
                    ),
                ),
            ),
            'page_visits_count' => array(
                'name' => __('Page visits count', 'azm'),
                'group' => __('Visits', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'page' => array(
                        'type' => 'dropdown',
                        'label' => __('Page name', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a page', 'azm'),
                        'options' => $pages_options,
                    ),
                    'visits' => array(
                        'type' => 'dropdown',
                        'label' => __('Visits', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is' => __('Is', 'azm'),
                            'is_greater_than' => __('Is greater than', 'azm'),
                            'is_less_than' => __('Is less than', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE pv.page_id = {page} AND pv.visits_count = {count})",
                            'is_greater_than' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE pv.page_id = {page} AND pv.visits_count > {count})",
                            'is_less_than' => "v.visitor_id IN (SELECT pv.visitor_id FROM {$wpdb->prefix}azr_page_visits pv WHERE pv.page_id = {page} AND pv.visits_count < {count})",
                        ),
                        'default' => 'is',
                    ),
                    'count' => array(
                        'type' => 'number',
                        'label' => __('Count', 'azm'),
                        'default' => '1',
                        'required' => true,
                    ),
                ),
            ),
            'email_campaign_status' => array(
                'name' => __('Email campaign status', 'azm'),
                'group' => __('Email campaign', 'azm'),
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
                            'opened' => __('Opened', 'azm'),
                            'clicked' => __('Clicked', 'azm'),
                            'was_sent' => __('Was sent', 'azm'),
//                            'did_not_open' => __('Did not open', 'azm'),
//                            'did_not_clicked' => __('Did not clicked', 'azm'),
//                            'was_not_sent' => __('Was not sent', 'azm'),
                        ),
                        'where_clauses' => array(
                            'opened' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_open' AND ec.meta_value IS NOT NULL) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_open' AND ec.meta_value IS NOT NULL)",
                            'clicked' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND ec.meta_value IS NOT NULL) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND ec.meta_value IS NOT NULL)",
                            'was_sent' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND ec.meta_value IS NOT NULL) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND ec.meta_value IS NOT NULL)",
//                            'did_not_open' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id AND ec.meta_key = '_email_campaign_{campaign}_open' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}}) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs LEFT JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id AND ec.meta_key = '_email_campaign_{campaign}_open' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}})",
//                            'did_not_clicked' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id AND ec.meta_key = '_email_campaign_{campaign}_click' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}}) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs LEFT JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id AND ec.meta_key = '_email_campaign_{campaign}_click' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}})",
//                            'was_not_sent' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id AND ec.meta_key = '_email_campaign_{campaign}_sent' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}}) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs LEFT JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id AND ec.meta_key = '_email_campaign_{campaign}_sent' WHERE ec.meta_value IS NULL {{AND fs.visitor_id IN ({visitor_id})}})",
                        ),
                        'default' => 'opened',
                    ),
                ),
            ),
            'email_campaign_sent_date' => array(
                'name' => __('Email campaign sent date', 'azm'),
                'group' => __('Email campaign', 'azm'),
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
                            'is_after' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) < DATE(STR_TO_DATE('{date}','%Y-%m-%d'))) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_before' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) > DATE(STR_TO_DATE('{date}','%Y-%m-%d'))) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) = DATE(STR_TO_DATE('{date}','%Y-%m-%d'))) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND CAST(ec.meta_value AS DECIMAL(10, 0)) >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY)) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND CAST(ec.meta_value AS DECIMAL(10, 0)) >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                            'is_not_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND CAST(ec.meta_value AS DECIMAL(10, 0)) < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY)) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_sent' AND CAST(ec.meta_value AS DECIMAL(10, 0)) < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
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
            ),
            'email_campaign_clicked_date' => array(
                'name' => __('Email campaign clicked date', 'azm'),
                'group' => __('Email campaign', 'azm'),
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
                            'is_after' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) < DATE(STR_TO_DATE('{date}','%Y-%m-%d'))) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_before' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) > DATE(STR_TO_DATE('{date}','%Y-%m-%d'))) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) = DATE(STR_TO_DATE('{date}','%Y-%m-%d'))) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND DATE(FROM_UNIXTIME(ec.meta_value + $gmt_offset)) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND CAST(ec.meta_value AS DECIMAL(10, 0)) >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY)) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND CAST(ec.meta_value AS DECIMAL(10, 0)) >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
                            'is_not_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ec ON ec.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND CAST(ec.meta_value AS DECIMAL(10, 0)) < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY)) OR v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitors as fs INNER JOIN {$wpdb->usermeta} as ec ON ec.user_id = fs.user_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ec.meta_key = '_email_campaign_{campaign}_click' AND CAST(ec.meta_value AS DECIMAL(10, 0)) < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY))",
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
            ),
            'last_visit' => array(
                'name' => __('Last visit', 'azm'),
                'group' => __('Visits', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'last_visit' => array(
                        'type' => 'dropdown',
                        'label' => __('Last visit', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is_within' => __('Is within', 'azm'),
                            'is_not_within' => __('Is not within', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is_within' => "v.last_visit_timestamp >= UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY)",
                            'is_not_within' => "v.last_visit_timestamp < UNIX_TIMESTAMP(NOW() - INTERVAL {days} DAY)",
                        ),
                        'default' => 'is_within',
                    ),
                    'days' => array(
                        'type' => 'number',
                        'label' => __('Days', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'form_submission_date' => array(
                'name' => __('Form submission date', 'azm'),
                'group' => __('Forms', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'form_title' => array(
                        'type' => 'dropdown',
                        'label' => __('Form', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'options' => $forms_options,
                        'helpers' => $forms_helpers,
                    ),
                    'submitted' => array(
                        'type' => 'dropdown',
                        'label' => __('Submitted', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is_after' => __('Is after', 'azm'),
                            'is_before' => __('Is before', 'azm'),
                            'is' => __('Is', 'azm'),
                            'is_within' => __('Is within', 'azm'),
                            'is_not_within' => __('Is not within', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is_after' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id INNER JOIN {$wpdb->posts} as p ON p.ID = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = 'form_title' AND ff.meta_value = '{form_title}' AND DATE(p.post_date) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_before' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id INNER JOIN {$wpdb->posts} as p ON p.ID = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = 'form_title' AND ff.meta_value = '{form_title}' AND DATE(p.post_date) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id INNER JOIN {$wpdb->posts} as p ON p.ID = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = 'form_title' AND ff.meta_value = '{form_title}' AND DATE((p.post_date) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                            'is_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id INNER JOIN {$wpdb->posts} as p ON p.ID = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = 'form_title' AND ff.meta_value = '{form_title}' AND p.post_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                            'is_not_within' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id INNER JOIN {$wpdb->posts} as p ON p.ID = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = 'form_title' AND ff.meta_value = '{form_title}' AND p.post_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                        ),
                        'default' => 'is',
                    ),
                    'date' => array(
                        'type' => 'date',
                        'label' => __('Date', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'submitted' => array('is_after', 'is_before', 'is'),
                        ),
                    ),
                    'days' => array(
                        'type' => 'number',
                        'label' => __('Days', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'submitted' => array('is_within', 'is_not_within'),
                        ),
                    ),
                ),
            ),
            'submitted_form_field' => array(
                'name' => __('Submitted form field', 'azm'),
                'group' => __('Forms', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'parameters' => array(
                    'field' => array(
                        'type' => 'dropdown',
                        'label' => __('Field', 'azm'),
                        'required' => true,
                        'no_options' => __('Please create a form via AZEXO Builder', 'azm'),
                        'options' => $fields_options,
                    ),
                    'relation' => array(
                        'type' => 'dropdown',
                        'label' => __('Relation', 'azm'),
                        'required' => true,
                        'options' => array(
                            'is' => __('Is', 'azm'),
//                            'is_not' => __('Is not', 'azm'),
                            'contains' => __('Contains', 'azm'),
//                            'does_not_contain' => __('Does not contain', 'azm'),
                            'starts_with' => __('Starts with', 'azm'),
                            'ends_with' => __('Ends with', 'azm'),
                            'is_greater_than' => __('Is greater than', 'azm'),
                            'is_less_than' => __('Is less than', 'azm'),
                            'is_blank' => __('Is blank', 'azm'),
//                            'is_not_blank' => __('Is not blank', 'azm'),
                        ),
                        'where_clauses' => array(
                            'is' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = '{field}' AND ff.meta_value = '{text_value}')",
//                            'is_not' => "v.visitor_id NOT IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE ff.meta_key = '{field}' AND ff.meta_value = '{text_value}')",
                            'contains' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = '{field}' AND ff.meta_value LIKE '%{text_value}%')",
//                            'does_not_contain' => "v.visitor_id NOT IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE ff.meta_key = '{field}' AND ff.meta_value LIKE '%{text_value}%')",
                            'starts_with' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = '{field}' AND ff.meta_value LIKE '%{text_value}')",
                            'ends_with' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = '{field}' AND ff.meta_value LIKE '{text_value}%')",
                            'is_greater_than' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = '{field}' AND CAST(ff.meta_value AS DECIMAL(10, 2)) > {number_value})",
                            'is_less_than' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = '{field}' AND CAST(ff.meta_value AS DECIMAL(10, 2)) < {number_value})",
                            'is_blank' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id AND ff.meta_key = '{field}' AND (ff.meta_value = '' OR ff.meta_value IS NULL) {{WHERE fs.visitor_id IN ({visitor_id})}})",
//                            'is_not_blank' => "v.visitor_id NOT IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs LEFT JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id AND ff.meta_key = '{field}' AND (ff.meta_value = '' OR ff.meta_value IS NULL))",
                        ),
                        'default' => 'is',
                    ),
                    'text_value' => array(
                        'type' => 'text',
                        'label' => __('Value', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'relation' => array('is', 'is_not', 'contains', 'does_not_contain', 'starts_with', 'ends_with'),
                        ),
                    ),
                    'number_value' => array(
                        'type' => 'number',
                        'label' => __('Value', 'azm'),
                        'required' => true,
                        'dependencies' => array(
                            'relation' => array('is_greater_than', 'is_less_than'),
                        ),
                    ),
                ),
            ),
            'visitor_country' => array(
                'name' => __('Visitor country', 'azm'),
                'group' => __('Visitor', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'where_clause' => "v.visitor_id IN (SELECT visitor_id FROM {$wpdb->prefix}azr_visitors WHERE {{visitor_id IN ({visitor_id}) AND}} country_code = '{country}')",
                'description' => __('Require GeoIP Detection plugin', 'azm'),
                'parameters' => array(
                    'country' => array(
                        'type' => 'dropdown',
                        'label' => __('Country', 'azm'),
                        'required' => true,
                        'options' => $countries_autocomplete,
                    ),
                ),
            ),
            'visitor_city' => array(
                'name' => __('Visitor city', 'azm'),
                'group' => __('Visitor', 'azm'),
                'query_where' => true,
                'required_context' => array('visitors'),
                'where_clause' => "v.visitor_id IN (SELECT visitor_id FROM {$wpdb->prefix}azr_visitors WHERE {{visitor_id IN ({visitor_id}) AND}} city_name = '{city}')",
                'description' => __('Require GeoIP Detection plugin', 'azm'),
                'parameters' => array(
                    'city' => array(
                        'type' => 'dropdown',
                        'label' => __('City', 'azm'),
                        'required' => true,
                        'options' => $cities,
                    ),
                ),
            ),
        ),
        'actions' => array(
            'user_register' => array(
                'name' => __('User register', 'azm'),
                'group' => __('User', 'azm'),
                'required_context' => array('visitors'),
                'event_dependency' => array('form_submit'),
                'description' => __('Form fields mapping. Leave empty if not used.', 'azm'),
                'parameters' => array(
                    'role' => array(
                        'type' => 'dropdown',
                        'label' => __('Role', 'azm'),
                        'required' => true,
                        'options' => $user_role_options,
                    ),
                    'auto_login' => array(
                        'type' => 'checkbox',
                        'label' => __('Auto login', 'azm'),
                    ),
                    'email' => array(
                        'type' => 'dropdown',
                        'label' => __('Email field', 'azm'),
                        'required' => true,
                        'options' => $fields_options,
                    ),
                    'login' => array(
                        'type' => 'dropdown',
                        'label' => __('Login field', 'azm'),
                        'options' => $fields_options,
                    ),
                    'password' => array(
                        'type' => 'dropdown',
                        'label' => __('Password field', 'azm'),
                        'options' => $fields_options,
                    ),
                    'first_name' => array(
                        'type' => 'dropdown',
                        'label' => __('First name field', 'azm'),
                        'options' => $fields_options,
                    ),
                    'last_name' => array(
                        'type' => 'dropdown',
                        'label' => __('Last name field', 'azm'),
                        'options' => $fields_options,
                    ),
                ),
            ),
            'user_login' => array(
                'name' => __('User login', 'azm'),
                'group' => __('User', 'azm'),
                'required_context' => array('visitors'),
                'event_dependency' => array('form_submit'),
                'description' => __('Form fields mapping. Leave empty if not used.', 'azm'),
                'parameters' => array(
                    'email' => array(
                        'type' => 'dropdown',
                        'label' => __('Email field', 'azm'),
                        'options' => $fields_options,
                    ),
                    'login' => array(
                        'type' => 'dropdown',
                        'label' => __('Login field', 'azm'),
                        'options' => $fields_options,
                    ),
                    'password' => array(
                        'type' => 'dropdown',
                        'label' => __('Password field', 'azm'),
                        'required' => true,
                        'options' => $fields_options,
                    ),
                ),
            ),
            'add_user_role' => array(
                'name' => __('Add user role', 'azm'),
                'group' => __('User', 'azm'),
                'required_context' => array('visitors'),
                'parameters' => array(
                    'role' => array(
                        'type' => 'dropdown',
                        'label' => __('Role', 'azm'),
                        'required' => true,
                        'options' => $user_role_options,
                    ),
                ),
            ),
            'remove_user_role' => array(
                'name' => __('Remove user role', 'azm'),
                'group' => __('User', 'azm'),
                'required_context' => array('visitors'),
                'parameters' => array(
                    'role' => array(
                        'type' => 'dropdown',
                        'label' => __('Role', 'azm'),
                        'required' => true,
                        'options' => $user_role_options,
                    ),
                ),
            ),
            'add_visitor_tag' => array(
                'name' => __('Add visitor tag', 'azm'),
                'group' => __('Visitor', 'azm'),
                'required_context' => array('visitors'),
                'parameters' => array(
                    'tag' => array(
                        'type' => 'autocomplete',
                        'label' => __('Tag', 'azm'),
                        'required' => true,
                        'options' => $visitor_tags_options,
                    ),
                ),
            ),
            'remove_visitor_tag' => array(
                'name' => __('Remove visitor tag', 'azm'),
                'group' => __('Visitor', 'azm'),
                'required_context' => array('visitors'),
                'parameters' => array(
                    'tag' => array(
                        'type' => 'autocomplete',
                        'label' => __('Tag', 'azm'),
                        'required' => true,
                        'options' => $visitor_tags_options,
                    ),
                ),
            ),
            'add_points_to_visitor' => array(
                'name' => __('Add points to visitor', 'azm'),
                'group' => __('Visitor', 'azm'),
                'required_context' => array('visitors'),
                'parameters' => array(
                    'points' => array(
                        'type' => 'number',
                        'label' => __('Points', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'reset_visitor_points' => array(
                'name' => __('Reset visitor points', 'azm'),
                'group' => __('Visitor', 'azm'),
                'required_context' => array('visitors'),
            ),
            'update_user_meta' => array(
                'name' => __('Update user meta', 'azm'),
                'group' => __('User', 'azm'),
                'required_context' => array('visitors'),
                'description' => __('Meta-value can be token based on form submitted data if it available in this rule.', 'azm'),
                'parameters' => array(
                    'meta-key' => array(
                        'type' => 'text',
                        'label' => __('Meta-key', 'azm'),
                        'required' => true,
                    ),
                    'meta-value' => array(
                        'type' => 'text',
                        'label' => __('Meta-value', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'show_azh_widget' => array(
                'name' => __('Show AZH widget', 'azm'),
                'group' => __('Show', 'azm'),
                'required_context' => array('visitors', 'visitor_id'),
                'event_dependency' => array('visit'),
                'where_clause' => "v.visitor_id = '{visitor_id}'",
                'description' => __('AZH widget will be hidden by default except this case. Make effect immediately on currently visited page', 'azm'),
                'parameters' => array(
                    'azh_widget' => array(
                        'type' => 'dropdown',
                        'label' => __('AZH Widget', 'azm'),
                        'required' => true,
                        'options' => $azh_widget_options,
                    ),
                ),
            ),
            'lock_content' => array(
                'name' => __('Lock content', 'azm'),
                'group' => __('Lock', 'azm'),
                'required_context' => array('visitors', 'visitor_id'),
                'event_dependency' => array('visit'),
                'where_clause' => "v.visitor_id = '{visitor_id}'",
                'description' => __('Lock content on single post page. Make effect immediately at the current visit moment.', 'azm'),
                'parameters' => array(
                    'locking_text' => array(
                        'type' => 'textarea',
                        'label' => __('Locking text (shortcodes supported)', 'azm'),
                        'required' => true,
                    ),
                ),
            ),
            'webhook' => array(
                'name' => __('Send context to a WebHook', 'azm'),
                'group' => __('Send', 'azm'),
                'parameters' => array(
                    'url' => array(
                        'type' => 'text',
                        'label' => __('WebHook URL', 'azm'),
                        'required' => true,
                    ),
                    'format' => array(
                        'type' => 'dropdown',
                        'label' => __('Format', 'azm'),
                        'options' => array(
                            'post' => __('POST', 'azm'),
                            'json' => __('JSON', 'azm'),
                        ),
                        'default' => 'post',
                    ),
                ),
            ),
        ),
    );
    //
    //restrict access to files
    //
    //condition - Popup showed
    //action - show popup
    //popup statistic - shows, clicks
    //
    //condition - language (is, is not)
    //refer-a-friend - social shares
    $azr = apply_filters('azr_settings', $azr);
    return $azr;
}

function azr_update_visitor_id($old_visitor_id, $new_visitor_id) {
    global $wpdb, $azr_visitor_id;

    $azr_visitor_id = $new_visitor_id;
    setcookie('azr-visitor', $new_visitor_id, time() + 365 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);

    $wpdb->query("DELETE FROM {$wpdb->prefix}azr_visitors WHERE visitor_id = '$old_visitor_id'");
    $wpdb->query("UPDATE {$wpdb->prefix}azr_page_visits SET visitor_id='$new_visitor_id' WHERE visitor_id = '$old_visitor_id'");
    $wpdb->query("UPDATE {$wpdb->prefix}azr_visitor_posts SET visitor_id='$new_visitor_id' WHERE visitor_id = '$old_visitor_id'");
    $wpdb->query("UPDATE {$wpdb->postmeta} SET meta_value='$new_visitor_id' WHERE meta_key = '_azr_visitor' AND meta_value = '$old_visitor_id'");
    do_action('azr_update_visitor_id', $old_visitor_id, $new_visitor_id);
}

function azr_check_visitor_id($visitor_id) {
    global $azr_visitor_id;
    if ($azr_visitor_id != $visitor_id) {
        azr_update_visitor_id($azr_visitor_id, $visitor_id);
    }
}

function azr_check_submission_visitor_id($submission_id) {
    global $wpdb;
    $visitor_id = $wpdb->get_var("SELECT visitor_id FROM {$wpdb->prefix}azr_visitor_posts WHERE post_id = " . $submission_id);
    if (!$visitor_id) {
        $visitor_id = get_post_meta($submission_id, '_azr_visitor', true);
    }
    if ($visitor_id) {
        azr_check_visitor_id($visitor_id);
    }
}

global $azr_visitor_id;
$azr_visitor_id = false;

function azr_get_current_visitor() {
    global $azr_visitor_id;
    if ($azr_visitor_id) {
        return $azr_visitor_id;
    }
    if (isset($_COOKIE['azr-visitor'])) {
        $azr_visitor_id = $_COOKIE['azr-visitor'];
    } else {
        $azr_visitor_id = uniqid();
        setcookie('azr-visitor', $azr_visitor_id, time() + 365 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        if (!is_user_logged_in()) {
            azr_verify_human($azr_visitor_id);
        }
    }
    if (is_user_logged_in()) {
        $meta_visitor_id = get_user_meta(get_current_user_id(), 'azr-visitor', true);
        if (empty($meta_visitor_id)) {
            update_user_meta(get_current_user_id(), 'azr-visitor', $azr_visitor_id);
        } else {
            azr_check_visitor_id($meta_visitor_id);
        }
    }
    return $azr_visitor_id;
}

function azr_verify_human($visitor_id) {
    global $verify_human_timestamp, $verify_human_visitor_id;
    $verify_human_timestamp = time() + 1 * HOUR_IN_SECONDS;
    $verify_human_visitor_id = $visitor_id;
    wp_schedule_single_event($verify_human_timestamp, 'azr_remove_visitor', array(
        'visitor_id' => $visitor_id
    ));
}

add_action('azr_remove_visitor', 'azr_remove_visitor', 10, 1);

function azr_remove_visitor($visitor_id) {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->prefix}azr_visitors WHERE visitor_id = '$visitor_id'");
    $wpdb->query("DELETE FROM {$wpdb->prefix}azr_page_visits WHERE visitor_id = '$visitor_id'");
}

add_action('wp_ajax_azr_verify_human', 'azr_verify_human_callback');

function azr_verify_human_callback() {
    if (isset($_POST['timestamp']) && is_numeric($_POST['timestamp']) && isset($_POST['visitor_id'])) {
        wp_unschedule_event((int) $_POST['timestamp'], 'azr_remove_visitor', array(
            'visitor_id' => sanitize_text_field($_POST['visitor_id'])
        ));
    }
    wp_die();
}

add_action('wp_footer', 'azr_footer');

function azr_footer() {
    global $verify_human_timestamp, $verify_human_visitor_id;
    if ($verify_human_timestamp && $verify_human_visitor_id) {
        ?>
        <script>
            (function ($) {
                $(function () {
                    $.post('<?php print admin_url('admin-ajax.php'); ?>', {
                        action: 'azr_verify_human',
                        timestamp: <?php print $verify_human_timestamp; ?>,
                        visitor_id: '<?php print $verify_human_visitor_id; ?>'
                    }, function (data) {
                    });
                });
            })(window.jQuery);
        </script>
        <?php
    }
}

add_action('init', 'azr_init', 100);

function azr_init() {
    static $initialized = false;
    if (!$initialized) {
        $initialized = true;
        global $wpdb;
        $visitor_id = azr_get_current_visitor();
        $user_id = 'NULL';
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
        }
        $visitor = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}azr_visitors WHERE visitor_id = '$visitor_id'", ARRAY_A);
        if (empty($visitor)) {
            $country_code = '';
            $city_name = '';
            if (function_exists('geoip_detect2_get_info_from_current_ip')) {
                $userInfo = geoip_detect2_get_info_from_current_ip();
                $country_code = $userInfo->country->isoCode;
                $city_name = $userInfo->city->name;
            }
            $wpdb->query("INSERT INTO {$wpdb->prefix}azr_visitors (visitor_id, user_id, country_code, city_name) VALUES ('$visitor_id', $user_id, '$country_code', '$city_name')");
        } else {
            if (empty($visitor['user_id']) && is_user_logged_in()) {
                $wpdb->query("UPDATE {$wpdb->prefix}azr_visitors SET user_id=$user_id WHERE visitor_id = '$visitor_id'");
                $wpdb->query("UPDATE {$wpdb->prefix}azr_visitor_posts SET user_id=$user_id WHERE visitor_id = '$visitor_id'");
            }
            //azr_visitor_posts - set visitor_id based on user_id
        }
        $wpdb->query("UPDATE {$wpdb->prefix}azr_visitors SET last_visit_timestamp = " . time() . " WHERE visitor_id = '$visitor_id'");

        azr_rules_init();
    }
}

add_action('wp', 'azr_wp', 5);

function azr_wp() {
    global $wpdb;
    if (is_page()) {
        $visitor_id = azr_get_current_visitor();
        $page_id = get_the_ID();
        $wpdb->query("INSERT INTO {$wpdb->prefix}azr_page_visits (visitor_id, page_id, last_visit_timestamp) VALUES ('$visitor_id', $page_id, " . time() . ") ON DUPLICATE KEY UPDATE last_visit_timestamp = " . time() . ", visits_count = visits_count + 1");
    }
}

function azr_rules_init() {
    $visitor_id = azr_get_current_visitor();
    $user_id = false;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $settings = azr_get_settings();
    $rules = get_option('azr-active-rules', array());
    foreach ($rules as $i => $rule) {
        if (!empty($rule['event'])) {
            $context = azr_prepare_context_by_event($rule, $visitor_id, $user_id);
            switch ($rule['event']['type']) {
                case 'scheduler':
                    switch ($rule['event']['when']) {
                        case 'immediately':
                            if (empty($rule['event']['processed'])) {
                                $rules[$i]['event']['processed'] = true;
                                update_option('azr-active-rules', $rules);
                                azr_process_rule($rule, $context);
                            }
                            break;
                    }
                    break;
                case 'form_submit':
                    add_action('azr_form_submit', function ($form_title, $fields, $submission_id) use($rule, $context) {
                        if (isset($form_title) && $form_title == $rule['event']['form']) {
                            $context['submitted_form'] = azh_recursive_sanitize_text_field($fields);
                            $context['submission_id'] = $submission_id;
                            azr_delayed_process_rule($rule, $context);
                        }
                    }, 100, 3);
                    add_filter('azf_process_form', function ($response, $form_settings, $submission_id) use($rule, $context) {
                        if (isset($_POST['form_title']) && $_POST['form_title'] == $rule['event']['form']) {
                            $context['submitted_form'] = azh_recursive_sanitize_text_field($_POST);
                            $context['submission_id'] = $submission_id;
                            azr_delayed_process_rule($rule, $context);
                        }
                        return $response;
                    }, 100, 3);
                    break;
                case 'visitor_leave_comment':
                    add_action('comment_post', function ($comment_ID, $comment_approved, $commentdata) use($rule, $context) {
                        azr_delayed_process_rule($rule, $context);
                    }, 10, 3);
                    break;
                case 'new_post':
                    unset($context['visitor_id']); // broadcast event
                    add_action('wp_insert_post', function ($post_id, $post, $update) use($rule, $context) {
                        if ($post->post_type == $rule['event']['post_type']) {
                            $context['post_title'] = $post->post_title;
                            $context['post_url'] = get_permalink($post_id);
                            azr_process_rule($rule, $context);
                        }
                    }, 10, 3);
                    break;
                case 'visit':
                    switch ($rule['event']['site_place']) {
                        case 'any':
                            azr_delayed_process_rule($rule, $context);
                            break;
                    }
                    add_action('wp', function () use($rule, $context) {
                        switch ($rule['event']['site_place']) {
                            case 'page':
                                if (is_page($rule['event']['page'])) {
                                    azr_delayed_process_rule($rule, $context);
                                }
                                break;
                            case 'child_page':
                                if (is_page()) {
                                    $queried_object = get_queried_object();
                                    if (in_array($queried_object->post_parent, $rule['event']['page'])) {
                                        azr_delayed_process_rule($rule, $context);
                                    }
                                }
                                break;
                            case 'post_type':
                                if (is_singular()) {
                                    $queried_object = get_queried_object();
                                    if ($queried_object) {
                                        if (in_array($queried_object->post_type, (array) $rule['event']['post_type'])) {
                                            azr_delayed_process_rule($rule, $context);
                                        }
                                    }
                                }
                                break;
                            case 'post_type_archive':
                                if (is_post_type_archive($rule['event']['post_type_archive'])) {
                                    azr_delayed_process_rule($rule, $context);
                                }
                                break;
                            case 'home':
                                if (is_front_page()) {
                                    azr_delayed_process_rule($rule, $context);
                                }
                                break;
                            case 'tags':
                                if (is_singular()) {
                                    $queried_object = get_queried_object();
                                    if ($queried_object && get_class($queried_object) == 'WP_Post') {
                                        if (has_term($rule['event']['tags'], 'post_tag', $queried_object)) {
                                            azr_delayed_process_rule($rule, $context);
                                        }
                                    }
                                }
                                break;
                            case 'categories':
                                if (is_singular()) {
                                    $queried_object = get_queried_object();
                                    if ($queried_object && get_class($queried_object) == 'WP_Post') {
                                        if (has_term($rule['event']['categories'], 'category', $queried_object)) {
                                            azr_delayed_process_rule($rule, $context);
                                        }
                                    }
                                }
                                break;
                        }
                    });
                    break;
            }
        }
        $context = apply_filters('azr_rule_init', $context, $rule);
    }
}

function azr_delayed_process_rule($rule, $context) {
    $context['event_timestamp'] = time();
    if (empty($rule['event']['delay'])) {
        azr_process_rule($rule, $context);
    } else {
        wp_schedule_single_event(time() + (float) $rule['event']['delay'] * DAY_IN_SECONDS, 'azr_process_rule', array(
            'rule' => $rule,
            'context' => $context,
        ));
    }
}

function azr_prepare_context_by_event($rule, $visitor_id = false, $user_id = false) {
    $settings = azr_get_settings();
    if (is_array($rule['context'])) {
        $context = $rule['context'];
    } else {
        $context = array();
    }
    if (isset($settings['events'][$rule['event']['type']]['set_context'])) {
        foreach ($settings['events'][$rule['event']['type']]['set_context'] as $name => $data) {
            $context[$name] = $data;
        }
    }
    if (isset($context['visitor_id']) && $visitor_id) {
        $context['visitor_id'] = $visitor_id;
    } else {
        unset($context['visitor_id']);
    }
    if ($user_id) {
        $context['user_id'] = $user_id;
    } else {
        unset($context['user_id']);
    }

    $context = apply_filters('azr_prepare_context_by_event', $context, $rule, $visitor_id);

    foreach ($context as $name => &$data) {
        if (isset($settings['contexts'][$name]['db_query'])) {
            if (!is_array($data)) {
                $data = $settings['contexts'][$name]['db_query'];
            }
            if (isset($settings['events'][$rule['event']['type']]['where_clause'])) {
                $where_clause = azm_sql_tokens($settings['events'][$rule['event']['type']]['where_clause'], $context);
                if (!preg_match('#{([\w\d-_]+)}#', $where_clause)) {
                    $data['where'][] = $where_clause;
                }
            }
        }
    }

    return $context;
}

add_action('azr_cron_process', 'azr_cron_process');

function azr_cron_process() {
    $settings = azr_get_settings();
    $rules = get_option('azr-active-rules', array());
    foreach ($rules as $i => $rule) {
        if (!empty($rule['event'])) {
            $context = azr_prepare_context_by_event($rule);
            switch ($rule['event']['type']) {
                case 'scheduler':
                    switch ($rule['event']['when']) {
                        case 'start_at_date':
                            if (empty($rule['event']['processed']) && $rule['event']['date'] == date_i18n('Y-m-d')) {
                                $rules[$i]['event']['processed'] = true;
                                update_option('azr-active-rules', $rules);
                                azr_process_rule($rule, $context);
                            }
                            break;
                        case 'every_hour':
                            if (empty($rule['event']['processed']) || $rule['event']['processed'] != date_i18n('Y-m-d H')) {
                                $rules[$i]['event']['processed'] = date_i18n('Y-m-d H');
                                update_option('azr-active-rules', $rules);
                                azr_process_rule($rule, $context);
                            }
                            break;
                        case 'every_day':
                            if (empty($rule['event']['processed']) || $rule['event']['processed'] != date_i18n('Y-m-d')) {
                                $rules[$i]['event']['processed'] = date_i18n('Y-m-d');
                                update_option('azr-active-rules', $rules);
                                azr_process_rule($rule, $context);
                            }
                            break;
                        case 'every_week_day':
                            if ((empty($rule['event']['processed']) || $rule['event']['processed'] != date_i18n('w')) && $rule['event']['week_day'] == date_i18n('w')) {
                                $rules[$i]['event']['processed'] = date_i18n('w');
                                update_option('azr-active-rules', $rules);
                                azr_process_rule($rule, $context);
                            }
                            break;
                        case 'every_month_day':
                            if ((empty($rule['event']['processed']) || $rule['event']['processed'] != date_i18n('j')) && $rule['event']['month_day'] == date_i18n('j')) {
                                $rules[$i]['event']['processed'] = date_i18n('j');
                                update_option('azr-active-rules', $rules);
                                azr_process_rule($rule, $context);
                            }
                            break;
                    }
                    break;
            }
            $context = apply_filters('azr_rule_init_by_cron', $context, $rule);
        }
    }
}

function azr_get_country_name($iso_code) {
    if (class_exists('\YellowTree\GeoipDetect\Geonames\CountryInformation')) {
        $countryInfo = new \YellowTree\GeoipDetect\Geonames\CountryInformation;
        $data = $countryInfo->getInformationAboutCountry($iso_code);
        return $data['country']['names']['en'];
    }
    return $iso_code;
}

add_filter('azf_process_form', 'azr_process_form', 10, 3);

function azr_process_form($response, $form_settings, $submission_id) {
    global $wpdb;
    $visitor_id = azr_get_current_visitor();
    $user_id = false;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $wpdb->query("REPLACE INTO {$wpdb->prefix}azr_visitor_posts (post_id, visitor_id, user_id) VALUES (" . $submission_id . ", '$visitor_id', " . ($user_id ? $user_id : 'NULL') . ")");
    update_post_meta($submission_id, '_azr_visitor', $visitor_id);
    return $response;
}

function azr_build_where_clause($context_name, $conditions, $operator) {
    $settings = azr_get_settings();
    $where_clause = array();
    foreach ($conditions as $condition) {
        if ($condition['type'] === 'or' && is_array($condition['conditions'])) {
            $wc = azr_build_where_clause($context_name, $condition['conditions'], ' OR ');
            if ($wc) {
                $where_clause[] = $wc;
            }
        } else if ($condition['type'] === 'and' && is_array($condition['conditions'])) {
            $wc = azr_build_where_clause($context_name, $condition['conditions'], ' AND ');
            if ($wc) {
                $where_clause[] = $wc;
            }
        } else {
            if (isset($settings['conditions'][$condition['type']]['required_context']) && is_array($settings['conditions'][$condition['type']]['required_context']) && in_array($context_name, $settings['conditions'][$condition['type']]['required_context'])) {
                if (isset($settings['conditions'][$condition['type']]['where_clause'])) {
                    if (isset($condition['negate']) && $condition['negate']) {
                        $where_clause[] = 'NOT (' . azm_sql_tokens($settings['conditions'][$condition['type']]['where_clause'], $condition) . ')';
                    } else {
                        $where_clause[] = azm_sql_tokens($settings['conditions'][$condition['type']]['where_clause'], $condition);
                    }
                }
                if (isset($settings['conditions'][$condition['type']]['parameters']) && is_array($settings['conditions'][$condition['type']]['parameters'])) {
                    foreach ($settings['conditions'][$condition['type']]['parameters'] as $name => $parameter) {
                        if (isset($parameter['where_clauses']) && is_array($parameter['where_clauses'])) {
                            if (isset($condition[$name]) && isset($parameter['where_clauses'][$condition[$name]])) {
                                if (isset($condition['negate']) && $condition['negate']) {
                                    $where_clause[] = 'NOT (' . azm_sql_tokens($parameter['where_clauses'][$condition[$name]], $condition) . ')';
                                } else {
                                    $where_clause[] = azm_sql_tokens($parameter['where_clauses'][$condition[$name]], $condition);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if (empty($where_clause)) {
        return false;
    } else {
        return '(' . implode($operator, $where_clause) . ')';
    }
}

function azr_prepare_context_by_conditions($conditions, $context) {
    $settings = azr_get_settings();
    foreach ($conditions as $condition) {
        if (isset($condition['conditions']) && is_array($condition['conditions'])) {
            $context = azr_prepare_context_by_conditions($condition['conditions'], $context);
        } else {
            if (isset($settings['conditions'][$condition['type']]['set_context'])) {
                foreach ($settings['conditions'][$condition['type']]['set_context'] as $name => $data) {
                    if (!isset($context[$name])) {
                        $context[$name] = $data;
                    }
                }
            }
            if (isset($settings['conditions'][$condition['type']]['context']) && $settings['conditions'][$condition['type']]['context']) {
                if (isset($settings['conditions'][$condition['type']]['parameters'])) {
                    foreach ($settings['conditions'][$condition['type']]['parameters'] as $name => $parameter) {
                        $context[$name] = $condition[$name];
                    }
                }
            }
        }
    }
    return $context;
}

function azr_prepare_context_by_actions($actions, $context) {
    $settings = azr_get_settings();
    foreach ($actions as $action) {
        if (isset($settings['actions'][$action['type']]['set_context'])) {
            foreach ($settings['actions'][$action['type']]['set_context'] as $name => $data) {
                if (!isset($context[$name])) {
                    $context[$name] = $data;
                }
            }
        }
    }
    return $context;
}

function azm_timezone_string_() {
    $offset = get_option('gmt_offset');
    $tzstring = get_option('timezone_string');
    //Manual offset...
    //@see http://us.php.net/manual/en/timezones.others.php
    //@see https://bugs.php.net/bug.php?id=45543
    //@see https://bugs.php.net/bug.php?id=45528
    //IANA timezone database that provides PHP's timezone support uses POSIX (i.e. reversed) style signs
    if (empty($tzstring) && 0 != $offset && floor($offset) == $offset) {
        $offset_st = $offset > 0 ? "-$offset" : '+' . absint($offset);
        $tzstring = 'Etc/GMT' . $offset_st;
    }

    //Issue with the timezone selected, set to 'UTC'
    if (empty($tzstring)) {
        $tzstring = 'UTC';
    }
    return $tzstring;
}

function azm_timezone_string() {
    // If site timezone string exists, return it.
    if ($timezone = get_option('timezone_string')) {
        return $timezone;
    }
    // Get UTC offset, if it isn't set then return UTC.
    if (0 === ( $utc_offset = intval(get_option('gmt_offset', 0)) )) {
        return 'UTC';
    }
    // Adjust UTC offset from hours to seconds.
    $utc_offset *= 3600;
    // Attempt to guess the timezone string from the UTC offset.
    if ($timezone = timezone_name_from_abbr('', $utc_offset)) {
        return $timezone;
    }
    // Last try, guess timezone string manually.
    foreach (timezone_abbreviations_list() as $abbr) {
        foreach ($abbr as $city) {
            if ((bool) date('I') === (bool) $city['dst'] && $city['timezone_id'] && intval($city['offset']) === $utc_offset) {
                return $city['timezone_id'];
            }
        }
    }
    // Fallback to UTC.
    return 'UTC';
}

function azm_timezone() {
    $timezone = new DateTimeZone(azm_timezone_string());
    return $timezone;
}

function azr_process_condition($condition, $context) {
    $settings = azr_get_settings();
    if (isset($condition['conditions']) && is_array($condition['conditions'])) {
        if ($condition['type'] === 'and') {
            foreach ($condition['conditions'] as $condition) {
                $result = azr_process_condition($condition, $context);
                if (!$result) {
                    $context = false;
                    break;
                }
            }
        } else if ($condition['type'] === 'or') {
            $or = false;
            foreach ($condition['conditions'] as $condition) {
                $result = azr_process_condition($condition, $context);
                if ($result) {
                    $or = true;
                    break;
                }
            }
            if (!$or) {
                $context = false;
            }
        }
    } else {
        $result = null;
        switch ($condition['type']) {
            case 'performing_hours':
                $result = in_array(date('G', time() + get_option('gmt_offset') * HOUR_IN_SECONDS), $condition['performing_hours']);
                break;
            case 'performing_week_days':
                $result = in_array(date('w', time() + get_option('gmt_offset') * HOUR_IN_SECONDS), $condition['performing_week_days']);
                break;
            case 'performing_months':
                $result = in_array(date('m', time() + get_option('gmt_offset') * HOUR_IN_SECONDS), $condition['performing_months']);
                break;
            case 'performing_period':
                if (!empty($condition['performing_from_date'])) {
                    $d = new DateTime($condition['performing_from_date'], azm_timezone());
                    $result = time() >= $d->getTimestamp();
                }
                if (!empty($condition['performing_to_date'])) {
                    $d = new DateTime($condition['performing_to_date'], azm_timezone());
                    $result = time() <= $d->getTimestamp();
                }
                if (!empty($condition['performing_from_date']) && !empty($condition['performing_to_date'])) {
                    $fd = new DateTime($condition['performing_from_date'], azm_timezone());
                    $td = new DateTime($condition['performing_to_date'], azm_timezone());
                    $result = time() >= $fd->getTimestamp() && time() <= $td->getTimestamp();
                }
                break;
        }
        if (is_null($result)) {
            $result = apply_filters('azr_process_condition', null, $context, $condition);
        }
        if (!is_null($result)) {
            if (empty($settings['conditions'][$condition['type']]['query_where']) && isset($condition['negate']) && $condition['negate']) {
                if ($result) {
                    $context = false;
                }
            } else {
                if (!$result) {
                    $context = false;
                }
            }
        }
    }
    return $context;
}

function azr_remove_injections($query) {
    //remove unused injections
    $query = preg_replace('/{{[^}]*{[^}]+}[^}]*}}/', '', $query);
    //remove used injections
    $query = str_replace('{{', '', $query);
    $query = str_replace('}}', '', $query);
    return $query;
}

function azr_get_db_query($db_query) {
    $query = 'SELECT DISTINCT ' . implode(', ', $db_query['fields']) . ' FROM ' . $db_query['from'];
    if (!empty($db_query['where'])) {
        $query .= ' WHERE ' . implode(' AND ', $db_query['where']);
    }
    return azr_remove_injections($query);
}

function azr_get_count_db_query($db_query) {
    $query = 'SELECT COUNT(DISTINCT v.visitor_id) FROM ' . $db_query['from'];
    if (!empty($db_query['where'])) {
        $query .= ' WHERE ' . implode(' AND ', $db_query['where']);
    }
    return azr_remove_injections($query);
}

function azr_process_conditions($rule, $context) {
    $settings = azr_get_settings();
    $context = azr_prepare_context_by_conditions($rule['conditions'], $context);
    $context = apply_filters('azr_prepare_context_by_conditions', $context, $rule['conditions']);

    foreach ($context as $name => &$data) {
        if (isset($settings['contexts'][$name])) {
            if (isset($settings['contexts'][$name]['db_query'])) {
                if (!is_array($data)) {
                    $data = $settings['contexts'][$name]['db_query'];
                }
                $where_clause = azr_build_where_clause($name, $rule['conditions'], ' AND ');
                if ($where_clause) {
                    $data['where'][] = azm_sql_tokens($where_clause, $context);
                }
            }
        }
    }

    foreach ($rule['conditions'] as $condition) {
        if (empty($settings['conditions'][$condition['type']]['query_where'])) {
            $context = azr_process_condition($condition, $context);
            if (!$context) {
                break;
            }
        }
    }
    $context = apply_filters('azr_process_conditions', $context, $rule);
    return $context;
}

add_action('azr_process_rule', 'azr_process_rule', 10, 2);

function azr_process_rule($rule, $context) {
    $settings = azr_get_settings();
    $context = azr_process_conditions($rule, $context);
    $context = azr_prepare_context_by_actions($rule['actions'], $context);
    if ($context) {
        $new_context = $context;
        foreach ($context as $name => $data) {
            if (isset($settings['contexts'][$name])) {
                if (isset($settings['contexts'][$name]['db_query'])) {
                    if (!is_array($data)) {
                        $new_context[$name] = $settings['contexts'][$name]['db_query'];
                    }
                }
            }
        }
        foreach ($context as $name => $data) {
            if (isset($settings['contexts'][$name])) {
                if (isset($settings['contexts'][$name]['db_query'])) {
                    foreach ($rule['actions'] as $action) {
                        if (isset($settings['actions'][$action['type']]['where_clause'])) {
                            $new_context[$name]['where'][] = azm_sql_tokens($settings['actions'][$action['type']]['where_clause'], $context);
                        }
                        if (isset($settings['actions'][$action['type']]['required_context']) && in_array($name, $settings['actions'][$action['type']]['required_context'])) {
                            $new_context = azr_process_action($action, $new_context);
                        }
                    }
                }
            }
        }
        $context = $new_context;
        foreach ($rule['actions'] as $action) {
            if (!isset($settings['actions'][$action['type']]['required_context'])) {
                $context = azr_process_action($action, $context);
            }
        }
        $context = apply_filters('azr_process_actions', $context, $rule);
    }
    return $context;
}

function azr_process_action($action, $context) {
    switch ($action['type']) {
        case 'webhook':
            if ($action['format'] == 'post') {
                $result = wp_remote_post($action['url'], array(
                    'body' => $context,
                ));
            }
            if ($action['format'] == 'json') {
                $result = wp_remote_post($action['url'], array(
                    'body' => json_encode($context),
                    'headers' => array('Content-Type' => 'application/json'),
                ));
            }
            if (!is_wp_error($result)) {
                azr_action_executed($context['rule']);
            }
            break;
        case 'user_register':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                $visitors = array_filter($visitors);
                $visitors = array_unique($visitors);
                foreach ($visitors as $visitor_id) {

                    $email = $context['submitted_form'][$action['email']];
                    $user_email = sanitize_email($email);
                    if (!email_exists($user_email)) {
                        if ($action['login']) {
                            $user_login = sanitize_user($context['submitted_form'][$action['login']], true);
                        } else {
                            $user_login = sanitize_user($user_email, true);
                            $user_login = explode('@', $user_login);
                            $user_login = $user_login[0];
                        }
                        if (username_exists($user_login)) {
                            $i = 1;
                            $user_login_tmp = $user_login;
                            do {
                                $user_login_tmp = $user_login . '_' . ($i++);
                            } while (username_exists($user_login_tmp));
                            $user_login = $user_login_tmp;
                        }

                        $user_fields = array(
                            'user_login' => $user_login,
                            'user_email' => $user_email,
                            'user_pass' => ($action['password'] ? $context['submitted_form'][$action['password']] : wp_generate_password()),
                            'first_name' => ($action['first_name'] ? $context['submitted_form'][$action['first_name']] : ''),
                            'last_name' => ($action['last_name'] ? $context['submitted_form'][$action['last_name']] : ''),
                            'role' => $action['role']
                        );
                        $user_id = wp_insert_user($user_fields);

                        if ($action['auto_login']) {
                            $user_data = get_userdata($user_id);
                            wp_set_current_user($user_id, $user_data->user_login);
                            wp_clear_auth_cookie();
                            wp_set_auth_cookie($user_id, true);
                            do_action('wp_login', $user_data->user_login, $user_data);
                            $wpdb->query("UPDATE {$wpdb->prefix}azr_visitors SET user_id=$user_id WHERE visitor_id = '$visitor_id'");
                            update_user_meta($user_id, 'azr-visitor', $visitor_id);
                        }
                    }
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'user_login':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                $visitors = array_filter($visitors);
                $visitors = array_unique($visitors);
                foreach ($visitors as $visitor_id) {
                    if ($action['email']) {
                        $user_email = sanitize_email($context['submitted_form'][$action['email']]);
                        $user_id = email_exists($user_email);
                    }
                    if ($action['login']) {
                        $user_login = sanitize_user($context['submitted_form'][$action['login']], true);
                        $user_id = username_exists($user_login);
                    }
                    if ($user_id) {
                        $user_data = get_userdata($user_id);
                        if (wp_check_password($context['submitted_form'][$action['password']], $user_data->user_pass, $user_id)) {
                            wp_set_current_user($user_id, $user_data->user_login);
                            wp_clear_auth_cookie();
                            wp_set_auth_cookie($user_id, true);
                            do_action('wp_login', $user_data->user_login, $user_data);
                            $wpdb->query("UPDATE {$wpdb->prefix}azr_visitors SET user_id=$user_id WHERE visitor_id = '$visitor_id'");
                            update_user_meta($user_id, 'azr-visitor', $visitor_id);
                        }
                    }
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'add_user_role':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['user_id'];
                }, $visitors);
                $visitors = array_filter($visitors);
                $visitors = array_unique($visitors);
                foreach ($visitors as $user_id) {
                    $user = new WP_User($user_id);
                    $user->add_role($action['role']);
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'remove_user_role':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['user_id'];
                }, $visitors);
                $visitors = array_filter($visitors);
                $visitors = array_unique($visitors);
                foreach ($visitors as $user_id) {
                    $user = new WP_User($user_id);
                    $user->remove_role($action['role']);
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'add_visitor_tag':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                foreach ($visitors as $visitor_id) {
                    $wpdb->query("REPLACE INTO {$wpdb->prefix}azr_visitor_tags (visitor_id, tag) VALUES ('$visitor_id', '" . $action['tag'] . "')");
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'remove_visitor_tag':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                foreach ($visitors as $visitor_id) {
                    $wpdb->query("DELETE FROM {$wpdb->prefix}azr_visitor_tags WHERE visitor_id = '$visitor_id' AND tag = '" . $action['tag'] . "'");
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'update_user_meta':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['user_id'];
                }, $visitors);
                $visitors = array_filter($visitors);
                $visitors = array_unique($visitors);
                foreach ($visitors as $user_id) {
                    if (isset($context['submitted_form'])) {
                        update_user_meta($user_id, $action['meta-key'], azm_tokens($action['meta-value'], $context['submitted_form']));
                    } else {
                        update_user_meta($user_id, $action['meta-key'], $action['meta-value']);
                    }
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'add_points_to_visitor':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                foreach ($visitors as $visitor_id) {
                    $wpdb->query("UPDATE {$wpdb->prefix}azr_visitors SET points = points + (" . $action['points'] . ") WHERE visitor_id = '$visitor_id'");
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'reset_visitor_points':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                foreach ($visitors as $visitor_id) {
                    $wpdb->query("UPDATE {$wpdb->prefix}azr_visitors SET points = 0 WHERE visitor_id = '$visitor_id'");
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'show_azh_widget':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                if (!empty($visitors)) {
                    add_filter('azh_widget_' . $action['azh_widget'] . '_visible', function($visible) use ($context) {
                        azr_counter_increment($context['rule'], '_azh_widget_impressions');
                        return true;
                    });
                    add_filter('the_content', function($content) use($action, $context) {
                        global $post;
                        if ($action['azh_widget'] == $post->ID) {
                            $url_params = apply_filters('azr_action_url_params', array(), $action, $context);
                            $click_query = array();
                            foreach ($url_params as $name => $value) {
                                $click_query[] = "$name=$value";
                            }
                            $click_query = implode('&', $click_query);
                            return str_replace('click=click', $click_query, $content);
                        }
                        return $content;
                    }, 5);
                } else {
                    add_filter('azh_widget_' . $action['azh_widget'] . '_visible', '__return_false', 5);
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
        case 'lock_content':
            if (isset($context['visitors'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                if (!empty($visitors)) {
                    add_filter('the_content', function($content) use($action, $context) {
                        global $post;
                        if (get_queried_object_id() == $post->ID) {
                            if (strpos($content, '<span id="more-' . $post->ID . '"></span>') !== false) {
                                return substr_replace($content, base64_decode($action['locking_text'], ENT_QUOTES), strpos($content, '<span id="more-' . $post->ID . '"></span>'));
                            } else {
                                return base64_decode($action['locking_text'], ENT_QUOTES);
                            }
                        }
                        return $content;
                    }, 5);
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
    }
    $context = apply_filters('azr_process_action', $context, $action);
    return $context;
}

function azr_dec($encoded) {
    $decoded = "";
    $strlen = strlen($encoded);
    for ($i = 0; $i < strlen($encoded); $i++) {
        $b = ord($encoded[$i]);
        $a = $b ^ 7;
        $decoded .= chr($a);
    }
    return $decoded;
}

add_action('init', 'azr_rule');

function azr_rule() {
    register_post_type('azr_rule', array(
        'labels' => array(
            'name' => __('Rule', 'azm'),
            'singular_name' => __('Rule', 'azm'),
            'add_new' => __('Add Rule', 'azm'),
            'add_new_item' => __('Add New Rule', 'azm'),
            'edit_item' => __('Edit Rule', 'azm'),
            'new_item' => __('New Rule', 'azm'),
            'view_item' => __('View Rule', 'azm'),
            'search_items' => __('Search Rules', 'azm'),
            'not_found' => __('No Rule found', 'azm'),
            'not_found_in_trash' => __('No Rule found in Trash', 'azm'),
            'parent_item_colon' => __('Parent Rule:', 'azm'),
            'menu_name' => __('Automation', 'azm'),
        ),
        'query_var' => true,
        'hierarchical' => true,
        'supports' => array('title', 'editor'),
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'public' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
            )
    );
}

add_filter('manage_azr_rule_posts_columns', 'azr_rule_columns');

function azr_rule_columns($columns) {
    $columns['status'] = __('Status', 'azm');
    $columns['results'] = __('Results', 'azm');
    return $columns;
}

add_action('manage_azr_rule_posts_custom_column', 'azr_rule_custom_columns', 10, 2);

function azr_rule_custom_columns($column, $post_id) {
    switch ($column) {
        case 'status' :
            ?>
            <div data-rule="<?php print $post_id; ?>" data-status="<?php print get_post_meta($post_id, '_status', true); ?>">
                <div class="azm-draft"><b><?php esc_html_e('Draft', 'azm'); ?></b></div>
                <div class="azm-running"><b><?php esc_html_e('Running', 'azm'); ?></b></div>
                <div class="azm-paused"><b><?php esc_html_e('Paused', 'azm'); ?></b></div>
                <div class="azm-finished"><b><?php esc_html_e('Finished', 'azm'); ?></b></div>
                <div class="azm-pause button button-primary button-large"><?php esc_html_e('Pause', 'azm'); ?></div>
                <div class="azm-run button button-primary button-large"><?php esc_html_e('Run', 'azm'); ?></div>
            </div>
            <?php
            break;
        case 'results' :
            $rule = get_post_meta($post_id, '_rule', true);
            $rule = json_decode($rule, true);
            if (isset($rule['actions']) && is_array($rule['actions'])) {
                foreach ($rule['actions'] as $action) {
                    print '<div data-action="' . $action['type'] . '">' . azr_get_action_results($action, $post_id) . '</div>';
                }
            }
            break;
    }
}

function azr_counter_increment($rule_id, $metakey, $number = 1) {
    $count = get_post_meta($rule_id, $metakey, true);
    $count = (int) $count + (int) $number;
    update_post_meta($rule_id, $metakey, $count);
}

function azr_action_executed($rule_id) {
    azr_counter_increment($rule_id, '_action_executes_count');
}

function azr_visitors_prcessed($rule_id, $visitors_count) {
    azr_counter_increment($rule_id, '_visitors_processed', $visitors_count);
}

function azr_get_action_results($action, $rule_id) {
    $results = '';
    $results .= '<div>' . __('Visitors segment size', 'azm') . ': ' . (int) get_post_meta($rule_id, '_visitors_segment_size', true) . '</div>';
    $results .= '<div>' . __('Action executes count', 'azm') . ': ' . (int) get_post_meta($rule_id, '_action_executes_count', true) . '</div>';
    $results .= '<div>' . __('Visitors number processed', 'azm') . ': ' . (int) get_post_meta($rule_id, '_visitors_processed', true) . '</div>';
    switch ($action['type']) {
        case 'show_azh_widget':
            $results .= '<div>' . __('AZH Widget impressions', 'azm') . ': ' . (int) get_post_meta($rule_id, '_azh_widget_impressions', true) . '</div>';
            $results .= '<div>' . __('AZH Widget clicks', 'azm') . ': ' . (int) get_post_meta($rule_id, '_azh_widget_clicks', true) . '</div>';
            break;
    }
    return apply_filters('azr_get_action_results', $results, $action, $rule_id);
}

add_action('add_meta_boxes', 'azr_rule_meta_boxes', 10, 2);

function azr_rule_meta_boxes($post_type, $post) {
    if ($post_type === 'azr_rule') {
        add_meta_box('azm', __('Rule settings', 'azm'), 'azr_rule_meta_box', $post_type, 'advanced', 'default');
        add_meta_box(
                'azr-results', // Unique ID
                esc_html__('Rule results', 'aza'), // Title
                'azr_rule_results', // Callback function
                $post_type, // Admin page (or post type)
                'side', // Context
                'low'         // Priority
        );
    }
}

add_action('save_post', 'azr_rule_save_post', 10, 3);

function azr_rule_save_post($post_id, $post, $update) {
    if (!isset($_POST['_azr_rule_nonce']) || !wp_verify_nonce($_POST['_azr_rule_nonce'], basename(__FILE__))) {
        return;
    }
    $rule = wp_unslash(sanitize_textarea_field($_POST['_rule']));
    update_post_meta($post_id, '_rule', $rule);
    if (get_post_meta($post_id, '_status', true) == 'running') {
        azr_pause($post_id);
        azr_run($post_id);
    }

    $rule = json_decode($rule, true);
    $context = azr_prepare_context_by_event($rule);
    if ($context['visitors']) {
        $context = azr_process_conditions($rule, $context);
        global $wpdb;
        $db_query = azr_get_count_db_query($context['visitors']);
        $num_rows = $wpdb->get_var($db_query);
        update_post_meta($post_id, '_visitors_segment_size', $num_rows);
    } else {
        update_post_meta($post_id, '_visitors_segment_size', 0);
    }
}

function azr_rule_meta_box($post = NULL, $metabox = NULL, $post_type = 'page') {
    wp_enqueue_style('azm_admin', plugins_url('css/admin.css', __FILE__));
    wp_enqueue_script('azm_admin', plugins_url('js/admin.js', __FILE__), array('jquery'), AZM_PLUGIN_VERSION, true);
    wp_nonce_field(basename(__FILE__), '_azr_rule_nonce');

    $rule = get_post_meta($post->ID, '_rule', true);
    if (empty($rule)) {
        $rule = array(
            'event' => array(
                'type' => 'scheduler',
                'when' => 'immediately',
            ),
            'context' => array(
                'rule' => $post->ID,
            ),
            'conditions' => array(
            ),
            'actions' => array(
            ),
        );
        $rule = json_encode($rule);
    }
    ?>
    <textarea class="azr-rule" name="_rule" hidden/><?php print $rule; ?></textarea>
    <?php
}

function azr_rule_results($post = NULL, $metabox = NULL, $post_type = 'page') {
    azr_rule_custom_columns('results', $post->ID);
    print '<br>';
    azr_rule_custom_columns('status', $post->ID);
}

add_filter('pre_trash_post', 'azr_pre_trash_post', 10, 2);

function azr_pre_trash_post($check, $post) {
    if ($post->post_type == 'azr_rule') {
        update_post_meta($post->ID, '_status', 'paused');
        $rules = get_option('azr-active-rules', array());
        foreach ($rules as $i => $rule) {
            if (isset($rule['context']['rule']) && $rule['context']['rule'] == $post->ID) {
                array_splice($rules, $i, 1);
                update_option('azr-active-rules', $rules);
                break;
            }
        }
    }
    return $check;
}

function azr_is_run($rule_id) {
    $rule_rule = get_post_meta($rule_id, '_rule', true);
    if (!empty($rule_rule)) {
        $rule_rule = json_decode($rule_rule, true);
        $rules = get_option('azr-active-rules', array());
        foreach ($rules as $i => $rule) {
            if (isset($rule['context']['rule']) && $rule['context']['rule'] == $rule_id) {
                return true;
            }
        }
    }
    return false;
}

function azr_run($rule_id) {
    $rule_rule = get_post_meta($rule_id, '_rule', true);
    if (!empty($rule_rule)) {
        $rule_rule = json_decode($rule_rule, true);
        $rules = get_option('azr-active-rules', array());
        $active = false;
        foreach ($rules as $i => $rule) {
            if (isset($rule['context']['rule']) && $rule['context']['rule'] == $rule_id) {
                $active = true;
            }
        }
        if (!$active) {
            $rules[] = $rule_rule;
            update_option('azr-active-rules', $rules);
            do_action('azr_run', $rule_id);
        }
    }
}

function azr_pause($rule_id) {
    $rule_rule = get_post_meta($rule_id, '_rule', true);
    if (!empty($rule_rule)) {
        $rule_rule = json_decode($rule_rule, true);
        $rules = get_option('azr-active-rules', array());
        foreach ($rules as $i => $rule) {
            if (isset($rule['context']['rule']) && $rule['context']['rule'] == $rule_id) {
                array_splice($rules, $i, 1);
                update_option('azr-active-rules', $rules);
                do_action('azr_pause', $rule_id);
                break;
            }
        }
    }
}

add_action('azr_pause', 'azr_cron_pause', 10, 1);

function azr_cron_pause($rule_id) {
    $crons = _get_cron_array();
    foreach ($crons as $timestamp => $hooks) {
        foreach ($hooks as $hook => $events) {
            foreach ($events as $key => $event) {
                if (isset($event['args']['context']['rule']) && $event['args']['context']['rule'] == $rule_id) {
                    wp_unschedule_event($timestamp, $hook, $event['args']);
                }
            }
        }
    }
}


add_action('wp_ajax_azr_run_rule', 'azr_run_rule');

function azr_run_rule() {
    if (isset($_POST['rule']) && is_numeric($_POST['rule'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $rule = get_post((int) $_POST['rule']);
            if ($rule->post_author == $user_id) {
                update_post_meta((int) $_POST['rule'], '_status', 'running');
                azr_run((int) $_POST['rule']);
                print 'running';
            }
        }
    }
    wp_die();
}

add_action('wp_ajax_azr_pause_rule', 'azr_pause_rule');

function azr_pause_rule() {
    if (isset($_POST['rule']) && is_numeric($_POST['rule'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $rule = get_post((int) $_POST['rule']);
            if ($rule->post_author == $user_id) {
                update_post_meta((int) $_POST['rule'], '_status', 'paused');
                azr_pause((int) $_POST['rule']);
                print 'paused';
            }
        }
    }
    wp_die();
}

function azr_user_tokens($content, $user_id) {
    $visitor_id = get_user_meta($user_id, 'azr-visitor', true);
    $content = azr_visitor_tokens($content, $visitor_id);
    return $content;
}

function azr_visitor_tokens($content, $visitor_id) {
    static $visitors_tokens = array();

    if (!isset($visitor_tokens[$visitor_id])) {
        $visitor_tokens[$visitor_id] = array();
        global $wpdb;
        $ids = $wpdb->get_col("SELECT post_id FROM {$wpdb->prefix}azr_visitor_posts WHERE visitor_id = '" . $visitor_id . "'");
        $leads = get_posts(array(
            'post_type' => 'azf_submission',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'post__in' => $ids,
        ));
        if (!empty($leads)) {
            foreach ($leads as $lead) {
                $lead_meta = get_post_meta($lead->ID);
                foreach ($lead_meta as $key => $value) {
                    $visitor_tokens[$visitor_id][$key] = reset($value);
                }
            }
        }
    }

    $content = azm_tokens($content, $visitor_tokens[$visitor_id]);
    return $content;
}

function azm_url_shorten($url, $google_api_key) {
    $result = wp_remote_post(add_query_arg('key', $google_api_key, 'https://www.googleapis.com/urlshortener/v1/url'), array(
        'body' => json_encode(array('longUrl' => esc_url_raw($url))),
        'headers' => array('Content-Type' => 'application/json'),
    ));

    if (is_wp_error($result)) {
        return $url;
    }

    $result = json_decode($result['body']);
    $shortlink = $result->id;
    if ($shortlink) {
        return $shortlink;
    }

    return $url;
}

add_filter('azm_activity_history', 'azm_activity_history', 10, 2);

function azm_activity_history($activity_history, $post) {
    global $wpdb;
    $visitor_id = $wpdb->get_var("SELECT visitor_id FROM {$wpdb->prefix}azr_visitor_posts WHERE post_id = " . $post->ID);
    if ($visitor_id) {
        $results = $wpdb->get_results("SELECT vp.post_id, p.post_date, p.post_title FROM {$wpdb->prefix}azr_visitor_posts as vp INNER JOIN {$wpdb->posts} as p ON p.ID = vp.post_id WHERE vp.visitor_id = '" . $visitor_id . "'", ARRAY_A);
        foreach ($results as $result) {
            $activity_history[strtotime($result['post_date'])] = '<a href="' . get_edit_post_link($result['post_id']) . '">' . $result['post_title'] . '</a> ' . esc_html__('form submitted', 'azm');
        }
        $results = $wpdb->get_results("SELECT pv.page_id, pv.last_visit_timestamp, pv.visits_count, p.post_title FROM {$wpdb->prefix}azr_page_visits as pv INNER JOIN {$wpdb->posts} as p ON p.ID = pv.page_id WHERE pv.visitor_id = '" . $visitor_id . "'", ARRAY_A);
        foreach ($results as $result) {
            $activity_history[$result['last_visit_timestamp']] = '<a href="' . get_permalink($result['page_id']) . '">' . $result['post_title'] . '</a> ' . esc_html__('page visited', 'azm') . ' ' . esc_html__(sprintf('(visits count: %d)', $result['visits_count']), 'azm');
        }
    }
    return $activity_history;
}

add_filter('azm_visitor_info', 'azm_visitor_info', 10, 2);

function azm_visitor_info($visitor_info, $post) {
    global $wpdb;
    $visitor = $wpdb->get_row("SELECT v.* FROM {$wpdb->prefix}azr_visitors as v INNER JOIN {$wpdb->prefix}azr_visitor_posts as vp ON v.visitor_id = vp.visitor_id WHERE vp.post_id = " . $post->ID, ARRAY_A);
    if ($visitor) {
        $visitor_info[] = array(
            'label' => esc_html__('Points', 'azm'),
            'value' => (int) $visitor['points'],
        );
        $visitor_info[] = array(
            'label' => esc_html__('Country', 'azm'),
            'value' => azr_get_country_name($visitor['country_code']),
        );
        $visitor_info[] = array(
            'label' => esc_html__('City', 'azm'),
            'value' => $visitor['city_name'],
        );
        $tags = $wpdb->get_col("SELECT tag FROM {$wpdb->prefix}azr_visitor_tags WHERE visitor_id = '" . $visitor['visitor_id'] . "'");
        $visitor_info[] = array(
            'label' => esc_html__('Tags', 'azm'),
            'value' => implode(', ', $tags),
        );
    }
    return $visitor_info;
}

add_action('wp_ajax_azm_get_wp_editor', 'azm_get_wp_editor');

function azm_get_wp_editor() {
    ob_start();
    wp_editor('', sanitize_text_field($_POST['id']), array(
        'dfw' => false,
        'auto_focus' => false,
        'media_buttons' => true,
        'tabfocus_elements' => 'insert-media-button',
        'editor_height' => 360,
        'wpautop' => false,
        'drag_drop_upload' => true,
    ));
    $editor = ob_get_contents();
    ob_end_clean();
    print $editor;
    die();
}

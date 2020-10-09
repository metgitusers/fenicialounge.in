<?php

if (isset($_GET['fields']) && !empty($_GET['fields'])) {
    define('WP_USE_THEMES', false);
    global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
    require('../../../wp-load.php');


    header("Content-Type: text/csv");
    header('Content-Disposition: filename="leads.csv"');
    header("Cache-Control: max-age=31536000");

    $user_id = get_current_user_id();
    if ($user_id) {
        $args = array(
            'post_type' => 'azf_submission',
            'post_status' => 'publish',
            'author' => $user_id,
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array()
        );
        if (isset($_GET['page'])) {
            $pages = explode(',', $_GET['page']);
            foreach ($pages as $i => &$page) {
                if (!is_numeric($page)) {
                    unset($pages[$i]);
                }
            }
            if (!empty($pages)) {
                $args['post_parent__in'] = $pages;
            }
        }
        if (isset($_GET['form']) && !empty($_GET['form'])) {
            $forms = explode(',', $_GET['form']);
            foreach ($forms as &$form) {
                $form = sanitize_text_field($form);
            }
            $args['meta_query'][] = array(
                'key' => 'form_title',
                'value' => (array) $forms,
                'compare' => 'IN'
            );
        }
        $query = new WP_Query($args);
        $fields = explode(',', $_GET['fields']);
        if ($query->post_count) {
            $out = fopen('php://output', 'w');
            fputs($out, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            $header = array();
            foreach ($fields as $name) {
                switch ($name) {
                    case 'id':
                        $header[] = __('Lead ID', 'azm');
                        break;
                    case 'timestamp':
                        $header[] = __('Lead timestamp', 'azm');
                        break;
                    case 'post_date':
                        $header[] = __('Lead post date', 'azm');
                        break;
                    case 'page':
                        $header[] = __('Page of lead', 'azm');
                        break;
                    case 'form_title':
                        $header[] = __('Form of lead', 'azm');
                        break;
                    default:
                        $header[] = $name;
                }
            }
            fputcsv($out, $header);
            foreach ($query->posts as $post) {
                $values = array();
                foreach ($fields as $name) {
                    switch ($name) {
                        case 'id':
                            $values[] = $post->ID;
                            break;
                        case 'timestamp':
                            $values[] = strtotime($post->post_date);
                            break;
                        case 'post_date':
                            $values[] = date_i18n(get_option('date_format'), strtotime($post->post_date)) . ' ' . date_i18n(get_option('time_format'), strtotime($post->post_date));
                            break;
                        case 'page':
                            $values[] = $post->post_parent;
                            break;
                        case 'form_title':
                            $values[] = get_post_meta($post->ID, 'form_title', true);
                            break;
                        default:
                            $values[] = get_post_meta($post->ID, $name, true);
                    }
                }
                fputcsv($out, $values);
            }
            fclose($out);
        }
    }
}
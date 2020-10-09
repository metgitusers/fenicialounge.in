<?php

add_action('admin_enqueue_scripts', 'azm_woo_admin_enqueue_scripts', 11);

function azm_woo_admin_enqueue_scripts() {    
    $screen = get_current_screen();
    if ($screen && isset($screen->post_type) && $screen->post_type == 'azr_rule') {
        wp_deregister_script('selectWoo');
    }    
}

add_action('woocommerce_new_order', 'azm_woo_update_order', 10, 1);
add_action('woocommerce_update_order', 'azm_woo_update_order', 10, 1);

function azm_woo_update_order($order_id) {
    global $wpdb;
    $user_id = get_post_meta($order_id, '_customer_user', true);
    if ($user_id) {
        $visitor_id = $wpdb->get_var("SELECT visitor_id FROM {$wpdb->prefix}azr_visitors WHERE user_id = " . $user_id);
        $wpdb->query("REPLACE INTO {$wpdb->prefix}azr_visitor_posts (post_id, visitor_id, user_id) VALUES (" . $order_id . ", '$visitor_id', " . $user_id . ")");
        update_post_meta($order_id, '_azr_visitor', $visitor_id);
    } else {
        if (!is_user_logged_in()) {
            $visitor_id = azr_get_current_visitor();
            $wpdb->query("REPLACE INTO {$wpdb->prefix}azr_visitor_posts (post_id, visitor_id) VALUES (" . $order_id . ", '$visitor_id')");
            update_post_meta($order_id, '_azr_visitor', $visitor_id);
        }
    }
}

function azm_woo_get_order_visitor_id($order_id) {
    global $wpdb;
    return $wpdb->get_var("SELECT visitor_id FROM {$wpdb->prefix}azr_visitor_posts WHERE post_id = $order_id");
}

register_activation_hook(AZM_FILE, 'azm_woo_activate');

function azm_woo_activate() {
    wp_schedule_single_event(time() + 1, 'azm_woo_activate', array(
    ));
}

add_filter('azm_woo_activate', 'azm_woo_indexes_creation');

function azm_woo_indexes_creation() {
    if (function_exists('wc_get_order_types')) {
        global $wpdb;
        $orders = get_posts(array(
            'numberposts' => -1,
            'post_type' => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses()),
        ));
        if ($orders) {
            foreach ($orders as $order) {
                $user_id = get_post_meta($order->ID, '_customer_user', true);
                if ($user_id) {
                    $wpdb->query("REPLACE INTO {$wpdb->prefix}azr_visitor_posts (post_id, visitor_id, user_id) VALUES (" . $order->ID . ", NULL, " . $user_id . ")");
                }
            }
        }
    }
}

add_filter('azr_settings', 'azm_woo_settings', 11);

function azm_woo_settings($azr) {
    global $wpdb;
    $gmt_offset = get_option('gmt_offset') * HOUR_IN_SECONDS;
    $azr['contexts']['products'] = array(
        'db_query' => array(
            'fields' => array("p.ID"),
            'from' => "{$wpdb->posts} as p",
            'where' => array(
                "(p.post_type = 'product' OR p.post_type = 'product_variation') AND p.post_status = 'publish'"
            ),
        ),
    );

    $categories_options = array();
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));
    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $categories_options[$category->term_id] = $category->name;
        }
    }
    $tags_options = array();
    $tags = get_terms(array(
        'taxonomy' => 'product_tag',
        'hide_empty' => false,
    ));
    if ($tags && !is_wp_error($tags)) {
        foreach ($tags as $tag) {
            $tags_options[$tag->term_id] = $tag->name;
        }
    }
    $attributes_options = array();
    if (function_exists('wc_get_attribute_taxonomies')) {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        if ($attribute_taxonomies) {
            foreach ($attribute_taxonomies as $attribute_taxonomy) {
                $attributes = get_terms(array(
                    'taxonomy' => 'pa_' . $attribute_taxonomy->attribute_name,
                    'hide_empty' => false,
                ));
                if ($attributes && !is_wp_error($attributes)) {
                    foreach ($attributes as $attribute) {
                        $attributes_options[$attribute->term_id] = $attribute_taxonomy->attribute_label . ': ' . $attribute->name;
                    }
                }
            }
        }
    }
    $coupons = get_posts(array(
        'post_type' => 'shop_coupon',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => -1,
        'numberposts' => -1,
    ));
    $coupons_options = array();
    if (!empty($coupons)) {
        foreach ($coupons as $coupon) {
            $coupons_options[$coupon->post_title] = $coupon->post_title;
        }
    }

    $azr['events']['cart_calculate_fees'] = array(
        'name' => __('Cart calculate fees', 'azm'),
        'description' => __('All conditions will be linked with current site visitor', 'azm'),
        'set_context' => array('visitors' => true),
    );
    $azr['events']['visit']['parameters']['site_place']['options']['product_categories'] = __('Product categories', 'azm');
    $azr['events']['visit']['parameters']['site_place']['options']['product_tags'] = __('Product tags', 'azm');
    $azr['events']['visit']['parameters']['site_place']['options']['shop'] = __('Shop page', 'azm');
    $azr['events']['visit']['parameters']['product_categories'] = array(
        'type' => 'multiselect',
        'label' => __('Categories', 'azm'),
        'required' => true,
        'options' => $categories_options,
        'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
        'dependencies' => array(
            'site_place' => array('product_categories'),
        ),
    );
    $azr['events']['visit']['parameters']['product_tags'] = array(
        'type' => 'multiselect',
        'label' => __('Tags', 'azm'),
        'required' => true,
        'options' => $tags_options,
        'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
        'dependencies' => array(
            'site_place' => array('product_tags'),
        ),
    );
    $azr['events']['first_purchase'] = array(
        'name' => __('First Purchase', 'azm'),
        'group' => __('Purchase', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true),
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
    );
    $azr['events']['purchase'] = array(
        'name' => __('Any Purchase', 'azm'),
        'group' => __('Purchase', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true),
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
    );
    $azr['events']['purchase_specific_product'] = array(
        'name' => __('Purchase specific product', 'azm'),
        'group' => __('Purchase', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true),
        'where_clause' => "v.visitor_id = '{visitor_id}'",
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
            'delay' => array(
                'type' => 'number',
                'label' => __('Reaction delay (days)', 'azm'),
                'required' => true,
                'step' => '0.1',
                'default' => '0',
            ),
        ),
    );
    $azr['events']['purchase_product_from_categories'] = array(
        'name' => __('Purchase product from categories', 'azm'),
        'group' => __('Purchase', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true),
        'where_clause' => "v.visitor_id = '{visitor_id}'",
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Categories', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
            'delay' => array(
                'type' => 'number',
                'label' => __('Reaction delay (days)', 'azm'),
                'required' => true,
                'step' => '0.1',
                'default' => '0',
            ),
        ),
    );
    $azr['events']['purchase_product_from_tags'] = array(
        'name' => __('Purchase product from tags', 'azm'),
        'group' => __('Purchase', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true),
        'where_clause' => "v.visitor_id = '{visitor_id}'",
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Tags', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
            'delay' => array(
                'type' => 'number',
                'label' => __('Reaction delay (days)', 'azm'),
                'required' => true,
                'step' => '0.1',
                'default' => '0',
            ),
        ),
    );
    $azr['events']['visitor_leave_review'] = array(
        'name' => __('Visitor leave review', 'azm'),
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
    );

    $azr['conditions']['guest_order_email_subscription'] = array(
        'name' => __('Guest order email subscription', 'azm'),
        'group' => __('Forms', 'azm'),
        'helpers' => '<div class="azr-tokens"><label>' . __('Available tokens for template:', 'azm') . '</label><input type="text" value="{_billing_first_name}"/><input type="text" value="{_billing_last_name}"/></div>',
        'query_where' => true,
        'required_context' => array('visitors'),
        'set_context' => array('email_subscription_post_type' => 'shop_order', 'email_field' => '_billing_email'),
        'parameters' => array(
            'email_subscription_status' => array(
                'type' => 'dropdown',
                'label' => __('Subscription status', 'azm'),
                'options' => array(
                    'subscribed' => __('Subscribed', 'azm'),
                    'unsubscribed' => __('Unsubscribed', 'azm'),
                ),
                'default' => 'subscribed',
                'where_clauses' => array(
                    'subscribed' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ff ON ff.post_id = fs.post_id LEFT JOIN {$wpdb->postmeta} as ss ON ss.post_id = fs.post_id AND ss.meta_key = '_unsubscribed' AND ss.meta_value IS NULL WHERE {{fs.visitor_id IN ({visitor_id}) AND}} ff.meta_key = '{email_field}' AND ff.meta_value IS NOT NULL)",
                    'unsubscribed' => "v.visitor_id IN (SELECT fs.visitor_id FROM {$wpdb->prefix}azr_visitor_posts as fs INNER JOIN {$wpdb->postmeta} as ss ON ss.post_id = fs.post_id AND ss.meta_key = '_unsubscribed' AND ss.meta_value IS NOT NULL {{WHERE fs.visitor_id IN ({visitor_id}) AND}})",
                ),
            ),
        ),
    );
    $azr['actions']['send_text_email']['condition_dependency'][] = 'guest_order_email_subscription';
    $azr['actions']['send_html_email']['condition_dependency'][] = 'guest_order_email_subscription';

    $azr['conditions']['review_rating'] = array(
        'name' => __('Review rating', 'azm'),
        'group' => __('Review', 'azm'),
        'event_dependency' => array('visitor_leave_review'),
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
            ),
            'rating' => array(
                'type' => 'number',
                'label' => __('Rating', 'azm'),
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['order_total'] = array(
        'name' => __('Order Total', 'azm'),
        'group' => __('Order', 'azm'),
        'event_dependency' => array('purchase', 'first_purchase', 'purchase_specific_product', 'purchase_product_from_categories', 'purchase_product_from_tags'),
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
            ),
            'amount' => array(
                'type' => 'number',
                'step' => '0.01',
                'label' => __('Amount', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['last_purchase_date'] = array(
        'name' => __('Last purchase date', 'azm'),
        'group' => __('Purchases history', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'purchase_date' => array(
                'type' => 'dropdown',
                'label' => __('Purchase date', 'azm'),
                'required' => true,
                'options' => array(
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'where_clauses' => array(
                    'is_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} o.post_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                    'is_not_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} o.post_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                ),
                'default' => 'is_within',
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['last_purchase_amount'] = array(
        'name' => __('Last purchase amount', 'azm'),
        'group' => __('Purchases history', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'purchase_date' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'is_greater_than' => __('Is greater than', 'azm'),
                    'is_less_than' => __('Is less than', 'azm'),
                ),
                'default' => 'is_greater_than',
                'where_clauses' => array(
                    'is_greater_than' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->postmeta} as om ON om.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} om.meta_key = '_order_total' AND CAST(om.meta_value AS DECIMAL(10, 2)) > {amount})",
                    'is_less_than' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->postmeta} as om ON om.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} om.meta_key = '_order_total' AND CAST(om.meta_value AS DECIMAL(10, 2)) < {amount})",
                ),
            ),
            'amount' => array(
                'type' => 'number',
                'step' => '0.01',
                'label' => __('Amount', 'azm'),
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['average_purchase_amount'] = array(
        'name' => __('Average purchase amount', 'azm'),
        'group' => __('Purchases history', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'purchase_date' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'is_greater_than' => __('Is greater than', 'azm'),
                    'is_less_than' => __('Is less than', 'azm'),
                ),
                'default' => 'is_greater_than',
                'where_clauses' => array(
                    'is_greater_than' => "v.user_id IN (SELECT ot.uid FROM (SELECT c.user_id as uid, AVG(CAST(om.meta_value AS DECIMAL(10, 2))) as order_total FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->postmeta} as om ON om.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} om.meta_key = '_order_total' GROUP BY uid HAVING order_total > {amount}) as ot)",
                    'is_less_than' => "v.user_id IN (SELECT ot.uid FROM (SELECT c.user_id as uid, AVG(CAST(om.meta_value AS DECIMAL(10, 2))) as order_total FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->postmeta} as om ON om.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} om.meta_key = '_order_total' GROUP BY uid HAVING order_total < {amount}) as ot)",
                ),
            ),
            'amount' => array(
                'type' => 'number',
                'step' => '0.01',
                'label' => __('Amount', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['has_purchases'] = array(
        'name' => __('Has purchases', 'azm'),
        'group' => __('Purchases history', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'where_clause' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' {{AND c.user_id IN ({user_id})}})",
    );

    $azr['conditions']['total_amount_spent'] = array(
        'name' => __('Total amount spent', 'azm'),
        'group' => __('Purchases history', 'azm'),
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
                    'is_greater_than' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->postmeta} as ot ON ot.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} ot.meta_key = '_order_total' AND CAST(ot.meta_value AS DECIMAL(10, 2)) > {amount})",
                    'is_less_than' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->postmeta} as ot ON ot.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} ot.meta_key = '_order_total' AND CAST(ot.meta_value AS DECIMAL(10, 2)) < {amount})",
                ),
            ),
            'amount' => array(
                'type' => 'number',
                'step' => '0.01',
                'label' => __('Amount', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['quantity_purchased_product'] = array(
        'name' => __('Quantity purchased - products', 'azm'),
        'group' => __('Purchases history - Quantity', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty > {quantity}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty < {quantity}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty >= {quantity}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty <= {quantity}) as uid)",
                ),
            ),
            'quantity' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['quantity_purchased_product_variation'] = array(
        'name' => __('Quantity purchased - variations', 'azm'),
        'group' => __('Purchases history - Quantity', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product variation', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product_variation',
                'required' => true,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty > {quantity}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty < {quantity}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty >= {quantity}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_qty' GROUP BY customer HAVING qty <= {quantity}) as uid)",
                ),
            ),
            'quantity' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['quantity_purchased_product_cat'] = array(
        'name' => __('Quantity purchased - categories', 'azm'),
        'group' => __('Purchases history - Quantity', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Categories', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty > {quantity}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty < {quantity}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty >= {quantity}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty <= {quantity}) as uid)",
                ),
            ),
            'quantity' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['quantity_purchased_product_tag'] = array(
        'name' => __('Quantity purchased - tags', 'azm'),
        'group' => __('Purchases history - Quantity', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Tags', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty > {quantity}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty < {quantity}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty >= {quantity}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty <= {quantity}) as uid)",
                ),
            ),
            'quantity' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['quantity_purchased_product_attribute'] = array(
        'name' => __('Quantity purchased - attributes', 'azm'),
        'group' => __('Purchases history - Quantity', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'attributes' => array(
                'type' => 'multiselect',
                'label' => __('Attributes', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add attributes', 'azm'),
                'options' => $attributes_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty > {quantity}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty < {quantity}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty >= {quantity}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_qty' GROUP BY customer HAVING qty <= {quantity}) as uid)",
                ),
            ),
            'quantity' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
            ),
        ),
    );


    $azr['conditions']['value_purchased_product'] = array(
        'name' => __('Value purchased - products', 'azm'),
        'group' => __('Purchases history - Value', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty > {value}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty < {value}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty >= {value}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty <= {value}) as uid)",
                ),
            ),
            'value' => array(
                'type' => 'number',
                'label' => __('Value', 'azm'),
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['value_purchased_product_variation'] = array(
        'name' => __('Value purchased - variations', 'azm'),
        'group' => __('Purchases history - Value', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product variation', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product_variation',
                'required' => true,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty > {value}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty < {value}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty >= {value}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty <= {value}) as uid)",
                ),
            ),
            'value' => array(
                'type' => 'number',
                'label' => __('Value', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['value_purchased_product_cat'] = array(
        'name' => __('Value purchased - categories', 'azm'),
        'group' => __('Purchases history - Value', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Categories', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty > {value}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty < {value}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty >= {value}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty <= {value}) as uid)",
                ),
            ),
            'value' => array(
                'type' => 'number',
                'label' => __('Value', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['value_purchased_product_tag'] = array(
        'name' => __('Value purchased - tags', 'azm'),
        'group' => __('Purchases history - Value', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Tags', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty > {value}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty < {value}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty >= {value}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty <= {value}) as uid)",
                ),
            ),
            'value' => array(
                'type' => 'number',
                'label' => __('Value', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['value_purchased_product_attribute'] = array(
        'name' => __('Value purchased - attributes', 'azm'),
        'group' => __('Purchases history - Value', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'attributes' => array(
                'type' => 'multiselect',
                'label' => __('Attributes', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add attributes', 'azm'),
                'options' => $attributes_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty > {value}) as uid)",
                    'less_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty < {value}) as uid)",
                    'at_least' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty >= {value}) as uid)",
                    'not_more_than' => "v.user_id IN (SELECT customer FROM (SELECT c.user_id as customer, SUM(CAST(q.meta_value AS DECIMAL(10, 2))) as qty FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as qty ON woi.order_item_id = q.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND q.meta_key = '_line_subtotal' GROUP BY customer HAVING qty <= {value}) as uid)",
                ),
            ),
            'value' => array(
                'type' => 'number',
                'label' => __('Value', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['purchased_product'] = array(
        'name' => __('Date of purchased specific product', 'azm'),
        'group' => __('Purchases history - Date', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'all_time' => __('All time', 'azm'),
                    'current_day' => __('Current day', 'azm'),
                    'current_week' => __('Current week', 'azm'),
                    'current_month' => __('Current month', 'azm'),
                    'current_year' => __('Current year', 'azm'),
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'all_time' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}))",
                    'current_day' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND DATE(o.post_date) = DATE(NOW()))",
                    'current_week' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND WEEK(o.post_date) = WEEK(NOW()))",
                    'current_month' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND MONTH(o.post_date) = MONTH(NOW()))",
                    'current_year' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND YEAR(o.post_date) = YEAR(NOW()))",
                    'is_after' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND DATE(o.post_date) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_before' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND DATE(o.post_date) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND DATE((o.post_date) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND o.post_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                    'is_not_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN ({product_id}) AND o.post_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                ),
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );
    $azr['conditions']['purchased_product_variation'] = array(
        'name' => __('Date of purchased specific variation', 'azm'),
        'group' => __('Purchases history - Date', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product variation', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product_variation',
                'required' => true,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'all_time' => __('All time', 'azm'),
                    'current_day' => __('Current day', 'azm'),
                    'current_week' => __('Current week', 'azm'),
                    'current_month' => __('Current month', 'azm'),
                    'current_year' => __('Current year', 'azm'),
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'all_time' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}))",
                    'current_day' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND DATE(o.post_date) = DATE(NOW()))",
                    'current_week' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND WEEK(o.post_date) = WEEK(NOW()))",
                    'current_month' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND MONTH(o.post_date) = MONTH(NOW()))",
                    'current_year' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND YEAR(o.post_date) = YEAR(NOW()))",
                    'is_after' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND DATE(o.post_date) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_before' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND DATE(o.post_date) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND DATE((o.post_date) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND o.post_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                    'is_not_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_variation_id' AND woim.meta_value IN ({product_id}) AND o.post_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                ),
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );
    $azr['conditions']['purchased_product_cat'] = array(
        'name' => __('Date of purchased product with category', 'azm'),
        'group' => __('Purchases history - Date', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Categories', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'all_time' => __('All time', 'azm'),
                    'current_day' => __('Current day', 'azm'),
                    'current_week' => __('Current week', 'azm'),
                    'current_month' => __('Current month', 'azm'),
                    'current_year' => __('Current year', 'azm'),
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'all_time' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})))",
                    'current_day' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND DATE(o.post_date) = DATE(NOW()))",
                    'current_week' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND WEEK(o.post_date) = WEEK(NOW()))",
                    'current_month' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND MONTH(o.post_date) = MONTH(NOW()))",
                    'current_year' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND YEAR(o.post_date) = YEAR(NOW()))",
                    'is_after' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND DATE(o.post_date) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_before' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND DATE(o.post_date) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND DATE((o.post_date) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND o.post_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                    'is_not_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories})) AND o.post_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                ),
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );
    $azr['conditions']['purchased_product_tag'] = array(
        'name' => __('Date of purchased product with tag', 'azm'),
        'group' => __('Purchases history - Date', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Tags', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'all_time' => __('All time', 'azm'),
                    'current_day' => __('Current day', 'azm'),
                    'current_week' => __('Current week', 'azm'),
                    'current_month' => __('Current month', 'azm'),
                    'current_year' => __('Current year', 'azm'),
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'all_time' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})))",
                    'current_day' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND DATE(o.post_date) = DATE(NOW()))",
                    'current_week' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND WEEK(o.post_date) = WEEK(NOW()))",
                    'current_month' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND MONTH(o.post_date) = MONTH(NOW()))",
                    'current_year' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND YEAR(o.post_date) = YEAR(NOW()))",
                    'is_after' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND DATE(o.post_date) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_before' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND DATE(o.post_date) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND DATE((o.post_date) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND o.post_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                    'is_not_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags})) AND o.post_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                ),
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );
    $azr['conditions']['purchased_product_attribute'] = array(
        'name' => __('Date of purchased product with attribute', 'azm'),
        'group' => __('Purchases history - Date', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'parameters' => array(
            'attributes' => array(
                'type' => 'multiselect',
                'label' => __('Attributes', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add attributes', 'azm'),
                'options' => $attributes_options,
            ),
            'purchased' => array(
                'type' => 'dropdown',
                'label' => __('Purchased', 'azm'),
                'required' => true,
                'options' => array(
                    'all_time' => __('All time', 'azm'),
                    'current_day' => __('Current day', 'azm'),
                    'current_week' => __('Current week', 'azm'),
                    'current_month' => __('Current month', 'azm'),
                    'current_year' => __('Current year', 'azm'),
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'default' => 'all_time',
                'where_clauses' => array(
                    'all_time' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})))",
                    'current_day' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND DATE(o.post_date) = DATE(NOW()))",
                    'current_week' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND WEEK(o.post_date) = WEEK(NOW()))",
                    'current_month' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND MONTH(o.post_date) = MONTH(NOW()))",
                    'current_year' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND YEAR(o.post_date) = YEAR(NOW()))",
                    'is_after' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND DATE(o.post_date) > DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_before' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND DATE(o.post_date) < DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND DATE((o.post_date) = DATE(STR_TO_DATE('{date}','%Y-%m-%d')))",
                    'is_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND o.post_date >= (DATE(NOW()) - INTERVAL {days} DAY))",
                    'is_not_within' => "v.user_id IN (SELECT DISTINCT c.user_id FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID INNER JOIN {$wpdb->prefix}woocommerce_order_items as woi ON o.ID = woi.order_id INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woim ON woi.order_item_id = woim.order_item_id WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' AND {{c.user_id IN ({user_id}) AND}} woim.meta_key = '_product_id' AND woim.meta_value IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE tt.term_id IN ({attributes})) AND o.post_date < (DATE(NOW()) - INTERVAL {days} DAY))",
                ),
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'purchased' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );

    $azr['conditions']['orders_number'] = array(
        'name' => __('Number of orders', 'azm'),
        'group' => __('Purchases history', 'azm'),
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
                    'is_greater_than' => "v.user_id IN (SELECT r.user_id FROM (SELECT c.user_id as user_id, count(o.ID) as orders FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' {{AND c.user_id IN ({user_id})}} GROUP BY user_id HAVING orders > {number}) as r)",
                    'is_less_than' => "v.user_id IN (SELECT r.user_id FROM (SELECT c.user_id as user_id, count(o.ID) as orders FROM {$wpdb->posts} as o INNER JOIN {$wpdb->prefix}azr_visitor_posts as c ON c.post_id = o.ID WHERE o.post_type = 'shop_order' AND o.post_status = 'wc-completed' {{AND c.user_id IN ({user_id})}} GROUP BY user_id HAVING orders < {number}) as r)",
                ),
            ),
            'number' => array(
                'type' => 'number',
                'label' => __('Number', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['actions']['order_total_to_points'] = array(
        'name' => __('Reward visitor by points based on order total', 'azm'),
        'group' => __('Visitor', 'azm'),
        'event_dependency' => array('purchase', 'first_purchase', 'purchase_specific_product', 'purchase_product_from_categories', 'purchase_product_from_tags'),
        'required_context' => array('visitors'),
        'parameters' => array(
            'ratio' => array(
                'type' => 'number',
                'label' => __('Conversion ratio', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );


    $azr['events']['product_added_to_cart'] = array(
        'name' => __('Any product added to cart', 'azm'),
        'group' => __('Added to cart', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true, 'visitor_id' => true),
        'where_clause' => "v.visitor_id = '{visitor_id}'",
        'parameters' => array(
            'delay' => array(
                'type' => 'number',
                'label' => __('Reaction delay (days)', 'azm'),
                'required' => true,
                'step' => '0.1',
                'default' => '1',
            ),
        ),
    );
    $azr['events']['specific_product_added_to_cart'] = array(
        'name' => __('Specific product added to cart', 'azm'),
        'group' => __('Added to cart', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true, 'visitor_id' => true),
        'where_clause' => "v.visitor_id = '{visitor_id}'",
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
            'delay' => array(
                'type' => 'number',
                'label' => __('Reaction delay (days)', 'azm'),
                'required' => true,
                'step' => '0.1',
                'default' => '1',
            ),
        ),
    );
    $azr['events']['product_with_category_added_to_cart'] = array(
        'name' => __('Product with category added to cart', 'azm'),
        'group' => __('Added to cart', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true, 'visitor_id' => true),
        'where_clause' => "v.visitor_id = '{visitor_id}'",
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Categories', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
            'delay' => array(
                'type' => 'number',
                'label' => __('Reaction delay (days)', 'azm'),
                'required' => true,
                'step' => '0.1',
                'default' => '1',
            ),
        ),
    );
    $azr['events']['product_with_tag_added_to_cart'] = array(
        'name' => __('Product with tag added to cart', 'azm'),
        'group' => __('Added to cart', 'azm'),
        'description' => __('All conditions will be linked with current site visitor and will be checked after this delay or immediately if it zero', 'azm'),
        'set_context' => array('visitors' => true, 'visitor_id' => true),
        'where_clause' => "v.visitor_id = '{visitor_id}'",
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Tags', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
            'delay' => array(
                'type' => 'number',
                'label' => __('Reaction delay (days)', 'azm'),
                'required' => true,
                'step' => '0.1',
                'default' => '1',
            ),
        ),
    );

    $subscription_products = array();
    $products = get_posts(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_ywsbs_subscription',
                'value' => 'yes',
            ),
        )
    ));
    if (is_array($products)) {
        foreach ($products as $product) {
            $subscription_products[$product->ID] = $product->post_title;
        }
    }
    $azr['conditions']['active_yith_woocommerce_subscription'] = array(
        'name' => __('Has active YITH WooCommerce Subscription', 'azm'),
        'group' => __('User', 'azm'),
        'query_where' => true,
        'required_context' => array('visitors'),
        'where_clause' => "v.user_id IN (SELECT u.meta_value FROM {$wpdb->posts} as s INNER JOIN {$wpdb->postmeta} as u ON s.ID = u.post_id INNER JOIN {$wpdb->postmeta} as ss ON s.ID = ss.post_id) INNER JOIN {$wpdb->postmeta} as p ON s.ID = p.post_id) WHERE u.meta_key = '_user_id' AND ss.meta_key = '_status' AND ss.meta_value = 'active' AND p.meta_key = '_product_id' AND p.meta_value = '{subscription_product}')",
        'parameters' => array(
            'subscription_product' => array(
                'type' => 'dropdown',
                'label' => __('Subscription product', 'azm'),
                'required' => true,
                'no_options' => __('Please install YITH WooCommerce Subscription plugin and add subscription products', 'azm'),
                'options' => $subscription_products,
            ),
        ),
    );

    $azr['conditions']['product'] = array(
        'name' => __('Specific product', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'where_clause' => "p.ID IN ({product_id})",
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['variable_product'] = array(
        'name' => __('Specific variable product (all variations)', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'where_clause' => "p.post_parent IN ({product_id})",
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_variable_product',
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['product_variation'] = array(
        'name' => __('Specific product variation', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'where_clause' => "p.ID IN ({product_id})",
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product variation', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product_variation',
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['product_cat'] = array(
        'name' => __('Product category', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'where_clause' => "p.ID IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE {{tr.object_id IN ({product_id}) AND}} tt.taxonomy = 'product_cat' AND tt.term_id IN ({categories}))",
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Categories', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
        ),
    );
    $azr['conditions']['product_tag'] = array(
        'name' => __('Product tag', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'where_clause' => "p.ID IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE {{tr.object_id IN ({product_id}) AND}} tt.taxonomy = 'product_tag' AND tt.term_id IN ({tags}))",
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Tags', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
        ),
    );

    $azr['conditions']['product_attributes'] = array(
        'name' => __('Product attributes', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'where_clause' => "p.ID IN (SELECT tr.object_id FROM {$wpdb->term_relationships} as tr INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies as wat ON  tt.taxonomy = CONCAT('pa_', wat.attribute_name) WHERE {{tr.object_id IN ({product_id}) AND}} tt.term_id IN ({attributes}))",
        'parameters' => array(
            'attributes' => array(
                'type' => 'multiselect',
                'label' => __('Attributes', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add attributes', 'azm'),
                'options' => $attributes_options,
            ),
        ),
    );
    $azr['conditions']['product_meta_field'] = array(
        'name' => __('Product meta-field', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
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
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'where_clauses' => array(
                    'is' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND pm.meta_value = '{text_value}')",
                    'is_not' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND pm.meta_value = '{text_value}')",
                    'contains' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND pm.meta_value LIKE '%{text_value}%')",
                    'does_not_contain' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND pm.meta_value LIKE '%{text_value}%')",
                    'starts_with' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND pm.meta_value LIKE '%{text_value}')",
                    'ends_with' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND pm.meta_value LIKE '{text_value}%')",
                    'is_greater_than' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND CAST(pm.meta_value AS DECIMAL(10, 2)) > {number_value})",
                    'is_less_than' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND CAST(pm.meta_value AS DECIMAL(10, 2)) < {number_value})",
                    'is_blank' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND (pm.meta_value = '' OR pm.meta_value IS NULL))",
                    'is_not_blank' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND (pm.meta_value <> '' AND pm.meta_value IS NOT NULL))",
                    'is_within' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND CAST(pm.meta_value AS DECIMAL(10, 2)) >= {days} * 86400)",
                    'is_not_within' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '{meta-key}' AND CAST(pm.meta_value AS DECIMAL(10, 2)) < {days} * 86400)",
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
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'relation' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );
    $azr['conditions']['product_regular_price'] = array(
        'name' => __('Product regular price', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
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
                    'is_greater_than' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '_regular_price' AND CAST(pm.meta_value AS DECIMAL(10, 2)) > {number_value})",
                    'is_less_than' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '_regular_price' AND CAST(pm.meta_value AS DECIMAL(10, 2)) < {number_value})",
                ),
                'default' => 'is_greater_than',
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
    );
    $azr['conditions']['product_is_on_sale'] = array(
        'name' => __('Product is on sale', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'where_clause' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '_sale_price' AND pm.meta_value <> '' AND pm.meta_value IS NOT NULL)",
    );
    $azr['conditions']['product_stock_qty'] = array(
        'name' => __('Product stock quantity', 'azm'),
        'group' => __('Products filter', 'azm'),
        'description' => __('Take effect only with products-performing actions', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
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
                    'is_greater_than' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '_stock' AND CAST(pm.meta_value AS DECIMAL(10, 2)) > {number_value})",
                    'is_less_than' => "p.ID IN (SELECT pm.post_id FROM {$wpdb->postmeta} as pm WHERE {{pm.post_id IN ({product_id}) AND}} pm.meta_key = '_stock' AND CAST(pm.meta_value AS DECIMAL(10, 2)) < {number_value})",
                ),
                'default' => 'is_greater_than',
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
    );
    $azr['conditions']['product_created_date'] = array(
        'name' => __('Product created date', 'azm'),
        'group' => __('Products filter', 'azm'),
        'query_where' => true,
        'set_context' => array('products' => true),
        'required_context' => array('products'),
        'parameters' => array(
            'created' => array(
                'type' => 'dropdown',
                'label' => __('Created', 'azm'),
                'required' => true,
                'options' => array(
                    'is_after' => __('Is after', 'azm'),
                    'is_before' => __('Is before', 'azm'),
                    'is' => __('Is', 'azm'),
                    'is_within' => __('Is within', 'azm'),
                    'is_not_within' => __('Is not within', 'azm'),
                ),
                'where_clauses' => array(
                    'is_after' => "DATE(p.post_date) > DATE(STR_TO_DATE('{date}','%Y-%m-%d'))",
                    'is_before' => "DATE(p.post_date) < DATE(STR_TO_DATE('{date}','%Y-%m-%d'))",
                    'is' => "DATE((p.post_date) = DATE(STR_TO_DATE('{date}','%Y-%m-%d'))",
                    'is_within' => "p.post_date >= (DATE(NOW()) - INTERVAL {days} DAY)",
                    'is_not_within' => "p.post_date < (DATE(NOW()) - INTERVAL {days} DAY)",
                ),
                'default' => 'is',
            ),
            'date' => array(
                'type' => 'date',
                'label' => __('Date', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'created' => array('is_after', 'is_before', 'is'),
                ),
            ),
            'days' => array(
                'type' => 'number',
                'label' => __('Days', 'azm'),
                'required' => true,
                'dependencies' => array(
                    'created' => array('is_within', 'is_not_within'),
                ),
            ),
        ),
    );

    $azr['conditions']['user_review_count'] = array(
        'name' => __('User review count', 'azm'),
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
                    'is_greater_than' => "v.user_id IN (SELECT r.user_id FROM (SELECT c.user_id as user_id, count(c.comment_ID) as comments FROM {$wpdb->comments} as c INNER JOIN {$wpdb->posts} as p ON c.comment_post_ID = p.ID AND p.post_type='product' {{WHERE c.user_id IN ({user_id})}} GROUP BY user_id HAVING comments > {count}) as r)",
                    'is_less_than' => "v.user_id IN (SELECT r.user_id FROM (SELECT c.user_id as user_id, count(c.comment_ID) as comments FROM {$wpdb->comments} as c INNER JOIN {$wpdb->posts} as p ON c.comment_post_ID = p.ID AND p.post_type='product' {{WHERE c.user_id IN ({user_id})}} GROUP BY user_id HAVING comments < {count}) as r)",
                ),
            ),
            'count' => array(
                'type' => 'number',
                'label' => __('Count', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['cart_subtotal'] = array(
        'name' => __('Cart subtotal', 'azm'),
        'group' => __('Cart', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
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
            ),
            'amount' => array(
                'type' => 'number',
                'step' => '0.01',
                'label' => __('Amount', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['cart_items_count'] = array(
        'name' => __('Cart items count', 'azm'),
        'group' => __('Cart', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'count' => array(
                'type' => 'number',
                'label' => __('Count', 'azm'),
                'required' => true,
            ),
        ),
    );

    $azr['conditions']['cart_total_weight'] = array(
        'name' => __('Cart total weight', 'azm'),
        'group' => __('Cart', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'weight' => array(
                'type' => 'number',
                'step' => '0.01',
                'label' => __('Weight', 'azm'),
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['cart_total_quantity'] = array(
        'name' => __('Cart total quantity', 'azm'),
        'group' => __('Cart', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'quantity' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['coupons_applied'] = array(
        'name' => __('Coupons applied', 'azm'),
        'group' => __('Cart', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'mode' => array(
                'type' => 'dropdown',
                'label' => __('Mode', 'azm'),
                'required' => true,
                'options' => array(
                    'at_least_one_of_any' => __('At least one of any', 'azm'),
                    'at_least_one_of_selected' => __('At least one of selected', 'azm'),
                    'all_of_selected' => __('All of selected', 'azm'),
                    'only_selected' => __('Only selected', 'azm'),
                    'none_of_selected' => __('None of selected', 'azm'),
                    'none_at_all' => __('None at all', 'azm'),
                ),
                'default' => 'more_than',
            ),
            'coupons' => array(
                'type' => 'multiselect',
                'label' => __('Coupons', 'azm'),
                'required' => true,
                'options' => $coupons_options,
                'dependencies' => array(
                    'mode' => array('at_least_one_of_selected', 'all_of_selected', 'only_selected', 'none_of_selected'),
                ),
            ),
        ),
    );


    $azr['conditions']['cart_contain_product'] = array(
        'name' => __('Cart contain product', 'azm'),
        'group' => __('Cart contain', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_variation'] = array(
        'name' => __('Cart contain variation', 'azm'),
        'group' => __('Cart contain', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Variation', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product_variation',
                'required' => true,
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_tag'] = array(
        'name' => __('Cart contain product with tag', 'azm'),
        'group' => __('Cart contain', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Product tag', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_category'] = array(
        'name' => __('Cart contain product with category', 'azm'),
        'group' => __('Cart contain', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Product category', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_attribute'] = array(
        'name' => __('Cart contain product with attribute', 'azm'),
        'group' => __('Cart contain', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'attributes' => array(
                'type' => 'multiselect',
                'label' => __('Product attribute', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $attributes_options,
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_quantity'] = array(
        'name' => __('Cart contain products quantity', 'azm'),
        'group' => __('Cart contain quantity', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'qty' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_variation_quantity'] = array(
        'name' => __('Cart contain variations quantity', 'azm'),
        'group' => __('Cart contain quantity', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product_variation',
                'required' => true,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'qty' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_tag_quantity'] = array(
        'name' => __('Cart contain products quantity with tag', 'azm'),
        'group' => __('Cart contain quantity', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Product tag', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'qty' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_category_quantity'] = array(
        'name' => __('Cart contain products quantity with category', 'azm'),
        'group' => __('Cart contain quantity', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Product category', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'qty' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_attribute_quantity'] = array(
        'name' => __('Cart contain products quantity with attribute', 'azm'),
        'group' => __('Cart contain quantity', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'attributes' => array(
                'type' => 'multiselect',
                'label' => __('Product attribute', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $attributes_options,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'qty' => array(
                'type' => 'number',
                'label' => __('Quantity', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );

    $azr['conditions']['cart_contain_product_subtotal'] = array(
        'name' => __('Cart contain products subtotal', 'azm'),
        'group' => __('Cart contain subtotal', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product',
                'required' => true,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'subtotal' => array(
                'type' => 'number',
                'label' => __('Subtotal', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_variation_subtotal'] = array(
        'name' => __('Cart contain variations subtotal', 'azm'),
        'group' => __('Cart contain subtotal', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'product_id' => array(
                'type' => 'ajax_multiselect',
                'label' => __('Product', 'azm'),
                'url' => admin_url('admin-ajax.php') . '?action=azm_woo_search_product_variation',
                'required' => true,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'subtotal' => array(
                'type' => 'number',
                'label' => __('Subtotal', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_tag_subtotal'] = array(
        'name' => __('Cart contain products subtotal with tag', 'azm'),
        'group' => __('Cart contain subtotal', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'tags' => array(
                'type' => 'multiselect',
                'label' => __('Product tag', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add tags', 'azm'),
                'options' => $tags_options,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'sty' => array(
                'type' => 'number',
                'label' => __('Subtotal', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_category_subtotal'] = array(
        'name' => __('Cart contain products subtotal with category', 'azm'),
        'group' => __('Cart contain subtotal', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'categories' => array(
                'type' => 'multiselect',
                'label' => __('Product category', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $categories_options,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'subtotal' => array(
                'type' => 'number',
                'label' => __('Subtotal', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );
    $azr['conditions']['cart_contain_product_attribute_subtotal'] = array(
        'name' => __('Cart contain products subtotal with attribute', 'azm'),
        'group' => __('Cart contain subtotal', 'azm'),
        'event_dependency' => array('cart_calculate_fees'),
        'parameters' => array(
            'attributes' => array(
                'type' => 'multiselect',
                'label' => __('Product attribute', 'azm'),
                'required' => true,
                'no_options' => __('Please install WooCommerce plugin or add categories', 'azm'),
                'options' => $attributes_options,
            ),
            'relation' => array(
                'type' => 'dropdown',
                'label' => __('Relation', 'azm'),
                'required' => true,
                'options' => array(
                    'more_than' => __('More than', 'azm'), //>
                    'less_than' => __('Less than', 'azm'), //<
                    'at_least' => __('At least', 'azm'), //>=
                    'not_more_than' => __('Not more than', 'azm'), //<=
                ),
                'default' => 'more_than',
            ),
            'subtotal' => array(
                'type' => 'number',
                'label' => __('Subtotal', 'azm'),
                'required' => true,
                'default' => '1',
            ),
        ),
    );

    $azr['actions']['send_html_email']['parameters']['woocommerce_style'] = array(
        'type' => 'checkbox',
        'label' => __('Woocommerce email style', 'azm'),
        'dependencies' => array(
            'email_template' => array('0'),
        ),
    );


    return $azr;
}

add_action('wp_ajax_azm_woo_search_product', 'azm_woo_search_product');

function azm_woo_search_product() {
    if (isset($_REQUEST['values']) && is_array($_REQUEST['values'])) {
        $ids = array_map('sanitize_text_field', $_REQUEST['values']);
        $options = array();
        $posts = get_posts(array(
            'post_type' => 'product',
            'include' => $ids,
        ));
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $options[$post->ID] = $post->post_title;
            }
        }
        print json_encode($options);
        wp_die();
    }
    $results = array(
        'results' => array(),
    );
    if (isset($_REQUEST['term'])) {
        $posts = get_posts(array(
            'post_type' => 'product',
            's' => sanitize_text_field($_REQUEST['term']),
            'posts_per_page' => '10',
        ));
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $results['results'][] = array(
                    'id' => $post->ID,
                    'text' => $post->post_title,
                );
            }
        }
    }
    print json_encode($results);
    wp_die();
}

add_action('wp_ajax_azm_woo_search_variable_product', 'azm_woo_search_variable_product');

function azm_woo_search_variable_product() {
    if (isset($_REQUEST['values']) && is_array($_REQUEST['values'])) {
        $ids = array_map('sanitize_text_field', $_REQUEST['values']);
        $options = array();
        $posts = get_posts(array(
            'post_type' => 'product',
            'include' => $ids,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'variable'
                )
            ),
        ));
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $options[$post->ID] = $post->post_title;
            }
        }
        print json_encode($options);
        wp_die();
    }
    $results = array(
        'results' => array(),
    );
    if (isset($_REQUEST['term'])) {
        $posts = get_posts(array(
            'post_type' => 'product',
            's' => sanitize_text_field($_REQUEST['term']),
            'posts_per_page' => '10',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'variable'
                )
            ),
        ));
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $results['results'][] = array(
                    'id' => $post->ID,
                    'text' => $post->post_title,
                );
            }
        }
    }
    print json_encode($results);
    wp_die();
}

add_action('wp_ajax_azm_woo_search_product_variation', 'azm_woo_search_product_variation');

function azm_woo_search_product_variation() {
    if (isset($_REQUEST['values']) && is_array($_REQUEST['values'])) {
        $ids = array_map('sanitize_text_field', $_REQUEST['values']);
        $options = array();
        $posts = get_posts(array(
            'post_type' => 'product_variation',
            'include' => $ids,
        ));
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $options[$post->ID] = $post->post_title;
            }
        }
        print json_encode($options);
        wp_die();
    }
    $results = array(
        'results' => array(),
    );
    if (isset($_REQUEST['term'])) {
        $posts = get_posts(array(
            'post_type' => 'product_variation',
            's' => sanitize_text_field($_REQUEST['term']),
            'posts_per_page' => '10',
        ));
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $results['results'][] = array(
                    'id' => $post->ID,
                    'text' => $post->post_title,
                );
            }
        }
    }
    print json_encode($results);
    wp_die();
}

add_filter('azr_rule_init', 'azm_woo_rule_init', 10, 2);

function azm_woo_rule_init($context, $rule) {
    switch ($rule['event']['type']) {
        case 'cart_calculate_fees':
            add_action('woocommerce_cart_calculate_fees', function ($cart) use($rule, $context) {
                $context['cart'] = $cart;
                $context['cart_contents'] = $cart->get_cart_for_session();
                $context['product_id'] = array();
                $lines = $cart->get_cart_contents();
                foreach ($lines as $line) {
                    if (isset($line['data'])) {
                        $context['product_id'][] = $line['data']->get_id();
                    }
                }
                $context['visitor_id'] = azr_get_current_visitor();
                azr_process_rule($rule, $context);
            }, 100, 1);
            break;
        case 'visit':
            add_action('wp', function () use($rule, $context) {
                switch ($rule['event']['site_place']) {
                    case 'product_categories':
                        if (is_singular()) {
                            $queried_object = get_queried_object();
                            if ($queried_object && get_class($queried_object) == 'WP_Post') {
                                if (has_term($rule['event']['product_categories'], 'product_cat', $queried_object)) {
                                    azr_delayed_process_rule($rule, $context);
                                }
                            }
                        }
                        break;
                    case 'product_tags':
                        if (is_singular()) {
                            $queried_object = get_queried_object();
                            if ($queried_object && get_class($queried_object) == 'WP_Post') {
                                if (has_term($rule['event']['product_tags'], 'product_tag', $queried_object)) {
                                    azr_delayed_process_rule($rule, $context);
                                }
                            }
                        }
                        break;
                    case 'shop':
                        if (function_exists('is_shop')) {
                            if (is_shop()) {
                                azr_process_rule($rule, $context);
                            }
                        }
                        break;
                }
            });
            break;
        case 'first_purchase':
            add_action('woocommerce_order_status_completed', function ($order_id) use($rule, $context) {
                $order = new WC_Order($order_id);
                $orders = azm_get_user_orders($order->get_user_id());
                if (count($orders) == 1) {
                    $context['order_id'] = $order_id;
                    if ($order->get_user_id()) {
                        $context['user_id'] = $order->get_user_id();
                    }
                    $context['visitor_id'] = get_post_meta($order_id, '_azr_visitor', true);
                    azr_delayed_process_rule($rule, $context);
                }
            });
            break;
        case 'purchase':
            add_action('woocommerce_order_status_completed', function ($order_id) use($rule, $context) {
                $order = new WC_Order($order_id);
                $context['order_id'] = $order_id;
                if ($order->get_user_id()) {
                    $context['user_id'] = $order->get_user_id();
                }
                $context['visitor_id'] = get_post_meta($order_id, '_azr_visitor', true);
                azr_delayed_process_rule($rule, $context);
            });
            break;
        case 'purchase_specific_product':
            add_action('woocommerce_order_status_completed', function ($order_id) use($rule, $context) {
                $order = new WC_Order($order_id);
                $items = $order->get_items();
                foreach ($items as $item_id => $item) {
                    if ($item->get_type() == 'line_item') {
                        if (in_array($item->get_product_id(), $rule['event']['product_id'])) {
                            $context['order_id'] = $order_id;
                            if ($order->get_user_id()) {
                                $context['user_id'] = $order->get_user_id();
                            }
                            $context['visitor_id'] = get_post_meta($order_id, '_azr_visitor', true);
                            azr_delayed_process_rule($rule, $context);
                            break;
                        }
                    }
                }
            });
            break;
        case 'purchase_product_from_categories':
            add_action('woocommerce_order_status_completed', function ($order_id) use($rule, $context) {
                $order = new WC_Order($order_id);
                $items = $order->get_items();
                foreach ($items as $item_id => $item) {
                    if ($item->get_type() == 'line_item') {
                        if (count(array_intersect($rule['event']['categories'], wp_get_object_terms($item->get_product_id(), 'product_cat', array('fields' => 'ids'))))) {
                            $context['order_id'] = $order_id;
                            if ($order->get_user_id()) {
                                $context['user_id'] = $order->get_user_id();
                            }
                            $context['visitor_id'] = get_post_meta($order_id, '_azr_visitor', true);
                            azr_delayed_process_rule($rule, $context);
                            break;
                        }
                    }
                }
            });
            break;
        case 'purchase_product_from_tags':
            add_action('woocommerce_order_status_completed', function ($order_id) use($rule, $context) {
                $order = new WC_Order($order_id);
                $items = $order->get_items();
                foreach ($items as $item_id => $item) {
                    if ($item->get_type() == 'line_item') {
                        if (count(array_intersect($rule['event']['tags'], wp_get_object_terms($item->get_product_id(), 'product_tag', array('fields' => 'ids'))))) {
                            $context['order_id'] = $order_id;
                            if ($order->get_user_id()) {
                                $context['user_id'] = $order->get_user_id();
                            }
                            $context['visitor_id'] = get_post_meta($order_id, '_azr_visitor', true);
                            azr_delayed_process_rule($rule, $context);
                            break;
                        }
                    }
                }
            });
            break;
        case 'visitor_leave_review':
            add_action('comment_post', function ($comment_ID, $comment_approved, $commentdata) use($rule, $context) {
                if (function_exists('wc_get_product')) {
                    $product = wc_get_product($commentdata['comment_post_ID']);
                    if ($product) {
                        $comment_meta = get_comment_meta($comment_ID);
                        foreach ($comment_meta as $key => $value) {
                            $comment_meta[$key] = reset($value);
                        }
                        if (isset($comment_meta['rating'])) {
                            $context['rating'] = $comment_meta['rating'];
                        }
                        azr_delayed_process_rule($rule, $context);
                    }
                }
            }, 10, 3);
            break;
        case 'product_added_to_cart':
        case 'specific_product_added_to_cart':
        case 'product_with_category_added_to_cart':
        case 'product_with_tag_added_to_cart':
            if (!is_admin()) {
                if (is_user_logged_in()) {
                    add_action('woocommerce_add_to_cart', function ($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) use($rule, $context) {
                        global $azm_woo_create_cart;
                        if (!$azm_woo_create_cart) {
                            $context['visitor_id'] = azr_get_current_visitor();
                            $context['cart_contents'] = WC()->cart->get_cart_for_session();
                            $context['user_id'] = get_current_user_id();
                            switch ($rule['event']['type']) {
                                case 'product_added_to_cart':
                                    azr_delayed_process_rule($rule, $context);
                                    break;
                                case 'specific_product_added_to_cart':
                                    if (in_array($product_id, $rule['event']['product_id'])) {
                                        azr_delayed_process_rule($rule, $context);
                                    }
                                    break;
                                case 'product_with_category_added_to_cart':
                                    if (count(array_intersect($rule['event']['categories'], wp_get_object_terms($product_id, 'product_tag', array('fields' => 'ids'))))) {
                                        azr_delayed_process_rule($rule, $context);
                                    }
                                    break;
                                case 'product_with_tag_added_to_cart':
                                    if (count(array_intersect($rule['event']['tags'], wp_get_object_terms($product_id, 'product_tag', array('fields' => 'ids'))))) {
                                        azr_delayed_process_rule($rule, $context);
                                    }
                                    break;
                            }
                        }
                    }, 30, 6);
                } else {
                    add_action('woocommerce_add_to_cart', function ($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) use($rule, $context) {
                        global $azm_woo_create_cart;
                        if (!$azm_woo_create_cart) {
                            $context['visitor_id'] = azr_get_current_visitor();
                            $context['customer_id'] = WC()->session->get_session_cookie()[0];
                            $context['cart_contents'] = unserialize(WC()->session->get_session($context['customer_id'])['cart']);
                            switch ($rule['event']['type']) {
                                case 'product_added_to_cart':
                                    azr_delayed_process_rule($rule, $context);
                                    break;
                                case 'specific_product_added_to_cart':
                                    if (in_array($product_id, $rule['event']['product_id'])) {
                                        azr_delayed_process_rule($rule, $context);
                                    }
                                    break;
                                case 'product_with_category_added_to_cart':
                                    if (count(array_intersect($rule['event']['categories'], wp_get_object_terms($product_id, 'product_tag', array('fields' => 'ids'))))) {
                                        azr_delayed_process_rule($rule, $context);
                                    }
                                    break;
                                case 'product_with_tag_added_to_cart':
                                    if (count(array_intersect($rule['event']['tags'], wp_get_object_terms($product_id, 'product_tag', array('fields' => 'ids'))))) {
                                        azr_delayed_process_rule($rule, $context);
                                    }
                            }
                        }
                    }, 30, 6);
                }
            }
            break;
    }
    return $context;
}

add_filter('azr_process_condition', 'azm_woo_process_condition', 10, 3);

function azm_woo_process_condition($result, $context, $condition) {
    if (is_null($result)) {
        switch ($condition['type']) {
            case 'review_rating':
                if (!empty($context['rating'])) {
                    if ($condition['relation'] == 'is_greater_than') {
                        $result = (float) $context['rating'] >= (float) $condition['rating'];
                    }
                    if ($condition['relation'] == 'is_less_than') {
                        $result = (float) $context['rating'] <= (float) $condition['rating'];
                    }
                }
                break;
            case 'order_total':
                if (!empty($context['order_id'])) {
                    $order = new WC_Order($context['order_id']);
                    if ($condition['relation'] == 'is_greater_than') {
                        $result = (float) $order->get_total() >= (float) $condition['amount'];
                    }
                    if ($condition['relation'] == 'is_less_than') {
                        $result = (float) $order->get_total() <= (float) $condition['amount'];
                    }
                }
                break;
            case 'cart_subtotal':
                if (!empty($context['cart'])) {
                    if ($condition['relation'] == 'is_greater_than') {
                        $result = (float) $context['cart']->get_subtotal() >= (float) $condition['amount'];
                    }
                    if ($condition['relation'] == 'is_less_than') {
                        $result = (float) $context['cart']->get_subtotal() <= (float) $condition['amount'];
                    }
                }
                break;
            case 'cart_items_count':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    if ($condition['relation'] == 'more_than') {
                        $result = count($lines) > (float) $condition['count'];
                    }
                    if ($condition['relation'] == 'less_than') {
                        $result = count($lines) < (float) $condition['count'];
                    }
                    if ($condition['relation'] == 'at_least') {
                        $result = count($lines) >= (float) $condition['count'];
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        $result = count($lines) <= (float) $condition['count'];
                    }
                }
                break;
            case 'cart_total_weight':
                if (!empty($context['cart'])) {
                    if ($condition['relation'] == 'more_than') {
                        $result = (float) $context['cart']->get_cart_contents_weight() > (float) $condition['weight'];
                    }
                    if ($condition['relation'] == 'less_than') {
                        $result = (float) $context['cart']->get_cart_contents_weight() < (float) $condition['weight'];
                    }
                    if ($condition['relation'] == 'at_least') {
                        $result = (float) $context['cart']->get_cart_contents_weight() >= (float) $condition['weight'];
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        $result = (float) $context['cart']->get_cart_contents_weight() <= (float) $condition['weight'];
                    }
                }
                break;
            case 'cart_total_quantity':
                if (!empty($context['cart'])) {
                    if ($condition['relation'] == 'more_than') {
                        $result = (float) $context['cart']->get_cart_contents_count() > (float) $condition['quantity'];
                    }
                    if ($condition['relation'] == 'less_than') {
                        $result = (float) $context['cart']->get_cart_contents_count() < (float) $condition['quantity'];
                    }
                    if ($condition['relation'] == 'at_least') {
                        $result = (float) $context['cart']->get_cart_contents_count() >= (float) $condition['quantity'];
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        $result = (float) $context['cart']->get_cart_contents_count() <= (float) $condition['quantity'];
                    }
                }
                break;
            case 'coupons_applied':
                if (!empty($context['cart'])) {
                    if ($condition['mode'] == 'at_least_one_of_any') {
                        $result = count($context['cart']->get_applied_coupons()) > 0;
                    }
                    if ($condition['mode'] == 'at_least_one_of_selected') {
                        $intersect = array_intersect($condition['coupons'], $context['cart']->get_applied_coupons());
                        $result = count($intersect) > 0;
                    }
                    if ($condition['mode'] == 'all_of_selected') {
                        $intersect = array_intersect($condition['coupons'], $context['cart']->get_applied_coupons());
                        $result = count($intersect) == count($condition['coupons']);
                    }
                    if ($condition['mode'] == 'only_selected') {
                        $intersect = array_intersect($condition['coupons'], $context['cart']->get_applied_coupons());
                        $result = count($intersect) == count($context['cart']->get_applied_coupons());
                    }
                    if ($condition['mode'] == 'none_of_selected') {
                        $intersect = array_intersect($condition['coupons'], $context['cart']->get_applied_coupons());
                        $result = count($intersect) == 0;
                    }
                    if ($condition['mode'] == 'none_at_all') {
                        $result = count($context['cart']->get_applied_coupons()) == 0;
                    }
                }
                break;
            case 'cart_contain_product':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['product_id']) && in_array($line['product_id'], $condition['product_id'])) {
                            $result = true;
                            break;
                        }
                    }
                }
                break;
            case 'cart_contain_product_variation':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (!empty($line['variation_id']) && in_array($line['variation_id'], $condition['product_id'])) {
                            $result = true;
                            break;
                        }
                    }
                }
                break;
            case 'cart_contain_product_category':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $intersect = array_intersect($condition['categories'], wp_get_object_terms($line['product_id'], 'product_cat', array('fields' => 'ids')));
                            if (count($intersect) > 0) {
                                $result = true;
                                break;
                            }
                        }
                    }
                }
                break;
            case 'cart_contain_product_tag':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $intersect = array_intersect($condition['tags'], wp_get_object_terms($line['product_id'], 'product_tag', array('fields' => 'ids')));
                            if (count($intersect) > 0) {
                                $result = true;
                                break;
                            }
                        }
                    }
                }
                break;
            case 'cart_contain_product_attribute':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $product = wc_get_product($line['product_id']);
                            if (isset($product->attributes) && is_array($product->attributes)) {
                                foreach ($product->attributes as $attribute) {
                                    $terms = $attribute->get_terms();
                                    if ($terms) {
                                        foreach ($terms as $term) {
                                            if (in_array($term->term_id, $condition['attributes'])) {
                                                $result = true;
                                                break;
                                            }
                                        }
                                    }
                                    if ($result) {
                                        $result = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case 'cart_contain_product_quantity':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['product_id']) && in_array($line['product_id'], $condition['product_id'])) {
                            if ($condition['relation'] == 'more_than') {
                                if ((int) $line['quantity'] > (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'less_than') {
                                if ((int) $line['quantity'] < (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'at_least') {
                                if ((int) $line['quantity'] >= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'not_more_than') {
                                if ((int) $line['quantity'] <= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                break;
            case 'cart_contain_product_variation_quantity':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['variation_id']) && in_array($line['variation_id'], $condition['product_id'])) {
                            if ($condition['relation'] == 'more_than') {
                                if ((int) $line['quantity'] > (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'less_than') {
                                if ((int) $line['quantity'] < (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'at_least') {
                                if ((int) $line['quantity'] >= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'not_more_than') {
                                if ((int) $line['quantity'] <= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                break;
            case 'cart_contain_product_category_quantity':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    $quantity = 0;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $intersect = array_intersect($condition['categories'], wp_get_object_terms($line['product_id'], 'product_cat', array('fields' => 'ids')));
                            if (count($intersect) > 0) {
                                $quantity = $quantity + $line['quantity'];
                            }
                        }
                    }
                    if ($condition['relation'] == 'more_than') {
                        if ($quantity > (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'less_than') {
                        if ($quantity < (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'at_least') {
                        if ($quantity >= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        if ($quantity <= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                }
                break;
            case 'cart_contain_product_tag_quantity':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    $quantity = 0;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $intersect = array_intersect($condition['tags'], wp_get_object_terms($line['product_id'], 'product_tag', array('fields' => 'ids')));
                            if (count($intersect) > 0) {
                                $quantity = $quantity + $line['quantity'];
                            }
                        }
                    }
                    if ($condition['relation'] == 'more_than') {
                        if ($quantity > (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'less_than') {
                        if ($quantity < (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'at_least') {
                        if ($quantity >= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        if ($quantity <= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                }
                break;
            case 'cart_contain_product_attribute_quantity':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    $quantity = 0;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $product = wc_get_product($line['product_id']);
                            if (isset($product->attributes) && is_array($product->attributes)) {
                                foreach ($product->attributes as $attribute) {
                                    $terms = $attribute->get_terms();
                                    if ($terms) {
                                        foreach ($terms as $term) {
                                            if (in_array($term->term_id, $condition['attributes'])) {
                                                $quantity = $quantity + $line['quantity'];
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($condition['relation'] == 'more_than') {
                        if ($quantity > (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'less_than') {
                        if ($quantity < (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'at_least') {
                        if ($quantity >= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        if ($quantity <= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                }
                break;
            case 'cart_contain_product_subtotal':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['product_id']) && in_array($line['product_id'], $condition['product_id'])) {
                            if ($condition['relation'] == 'more_than') {
                                if ((int) $line['line_subtotal'] > (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'less_than') {
                                if ((int) $line['line_subtotal'] < (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'at_least') {
                                if ((int) $line['line_subtotal'] >= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'not_more_than') {
                                if ((int) $line['line_subtotal'] <= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                break;
            case 'cart_contain_product_variation_subtotal':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    foreach ($lines as $line) {
                        if (isset($line['variation_id']) && in_array($line['variation_id'], $condition['product_id'])) {
                            if ($condition['relation'] == 'more_than') {
                                if ((int) $line['line_subtotal'] > (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'less_than') {
                                if ((int) $line['line_subtotal'] < (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'at_least') {
                                if ((int) $line['line_subtotal'] >= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                            if ($condition['relation'] == 'not_more_than') {
                                if ((int) $line['line_subtotal'] <= (int) $condition['qty']) {
                                    $result = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                break;
            case 'cart_contain_product_category_subtotal':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    $subtotal = 0;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $intersect = array_intersect($condition['categories'], wp_get_object_terms($line['product_id'], 'product_cat', array('fields' => 'ids')));
                            if (count($intersect) > 0) {
                                $subtotal = $subtotal + $line['line_subtotal'];
                            }
                        }
                    }
                    if ($condition['relation'] == 'more_than') {
                        if ($subtotal > (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'less_than') {
                        if ($subtotal < (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'at_least') {
                        if ($subtotal >= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        if ($subtotal <= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                }
                break;
            case 'cart_contain_product_tag_subtotal':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    $subtotal = 0;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $intersect = array_intersect($condition['tags'], wp_get_object_terms($line['product_id'], 'product_tag', array('fields' => 'ids')));
                            if (count($intersect) > 0) {
                                $subtotal = $subtotal + $line['line_subtotal'];
                            }
                        }
                    }
                    if ($condition['relation'] == 'more_than') {
                        if ($subtotal > (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'less_than') {
                        if ($subtotal < (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'at_least') {
                        if ($subtotal >= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        if ($subtotal <= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                }
                break;
            case 'cart_contain_product_attribute_subtotal':
                if (!empty($context['cart']) || !empty($context['cart_contents'])) {
                    $lines = $context['cart_contents'];
                    if (empty($lines)) {
                        $lines = $context['cart']->get_cart_contents();
                    }
                    $result = false;
                    $subtotal = 0;
                    foreach ($lines as $line) {
                        if (isset($line['product_id'])) {
                            $product = wc_get_product($line['product_id']);
                            if (isset($product->attributes) && is_array($product->attributes)) {
                                foreach ($product->attributes as $attribute) {
                                    $terms = $attribute->get_terms();
                                    if ($terms) {
                                        foreach ($terms as $term) {
                                            if (in_array($term->term_id, $condition['attributes'])) {
                                                $subtotal = $subtotal + $line['line_subtotal'];
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($condition['relation'] == 'more_than') {
                        if ($subtotal > (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'less_than') {
                        if ($subtotal < (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'at_least') {
                        if ($subtotal >= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                    if ($condition['relation'] == 'not_more_than') {
                        if ($subtotal <= (int) $condition['qty']) {
                            $result = true;
                        }
                    }
                }
                break;
        }
        return $result;
    }
    return $result;
}

add_filter('azr_process_action', 'azm_woo_process_action', 10, 2);

function azm_woo_process_action($context, $action) {
    switch ($action['type']) {
        case 'order_total_to_points':
            if (isset($context['visitors']) && isset($context['order_id'])) {
                global $wpdb;
                $db_query = azr_get_db_query($context['visitors']);
                $visitors = $wpdb->get_results($db_query, ARRAY_A);
                $visitors = array_map(function($value) {
                    return $value['visitor_id'];
                }, $visitors);
                $visitors = array_filter($visitors);
                $visitors = array_unique($visitors);
                foreach ($visitors as $visitor_id) {
                    if ($visitor_id == $context['visitor_id']) {
                        $order = new WC_Order($context['order_id']);
                        $points = (float) $order->get_total() * (float) $action['ratio'];
                        $wpdb->query("UPDATE {$wpdb->prefix}azr_visitors SET points = points + (" . $points . ") WHERE visitor_id = '" . $context['visitor_id'] . "'");
                    }
                }
                azr_action_executed($context['rule']);
                azr_visitors_prcessed($context['rule'], count($visitors));
            }
            break;
    }
    return $context;
}

function azm_get_user_orders($user_id) {
    global $wpdb;
    return $wpdb->get_col("SELECT vp.post_id FROM {$wpdb->prefix}azr_visitor_posts as vp INNER JOIN {$wpdb->posts} as p ON p.ID = vp.post_id AND p.post_type IN ('" . implode("','", wc_get_order_types()) . "') AND p.post_status IN ('" . implode("','", array_keys(wc_get_order_statuses())) . "') WHERE vp.user_id = " . $user_id);
}

add_action('admin_menu', 'azm_woo_admin_menu', 13);

function azm_woo_admin_menu() {
    if (class_exists('WooCommerce')) {
        add_submenu_page('edit.php?post_type=azr_rule', __('WooCommerce settings', 'azm'), __('WooCommerce settings', 'azm'), 'edit_pages', 'azh-woo-settings', 'azm_woo_page');
    }
}

function azm_woo_page() {
    ?>

    <div class="wrap">
        <h2><?php _e('WooCommerce settings', 'azm'); ?></h2>

        <form method="post" action="options.php" class="azh-form">
    <?php
    settings_errors();
    settings_fields('azh-woo-settings');
    do_settings_sections('azh-woo-settings');
    submit_button(__('Save Settings', 'azm'));
    ?>
        </form>
    </div>

    <?php
}

add_action('admin_init', 'azm_woo_options');

function azm_woo_options() {
    register_setting('azh-woo-settings', 'azh-woo-settings', array('sanitize_callback' => 'azh_settings_sanitize_callback'));
}

add_filter('azm_send_email_template', 'azm_woo_send_email_template', 10, 2);

function azm_woo_send_email_template($content, $action) {
    if (isset($action['woocommerce_style']) && $action['woocommerce_style']) {
        if (file_exists(WP_PLUGIN_DIR . '/woocommerce/includes/emails/class-wc-email.php')) {
            include_once( WP_PLUGIN_DIR . '/woocommerce/includes/emails/class-wc-email.php' );
        }
        if (!class_exists('Emogrifier') && class_exists('DOMDocument')) {
            if (file_exists(WP_PLUGIN_DIR . '/woocommerce/includes/libraries/class-emogrifier.php')) {
                include_once( WP_PLUGIN_DIR . '/woocommerce/includes/libraries/class-emogrifier.php' );
            }
        }

        ob_start();
        wc_get_template('emails/email-header.php', array('email_heading' => $action['email_subject']));
        $header = ob_get_clean();
        ob_start();
        wc_get_template('emails/email-footer.php');
        $footer = ob_get_clean();

        $email = new WC_Email();
        return apply_filters('woocommerce_mail_content', $email->style_inline($header . $content . $footer));
    }
    return $content;
}

function azm_woo_create_cart($cart_contents) {
    if (class_exists('WC_Cart') && is_array($cart_contents)) {


        if (!class_exists('AZM_WC_Cart_Session')) {

            final class AZM_WC_Cart_Session {

                protected $cart;

                public function __construct(&$cart) {
                    if (!is_a($cart, 'WC_Cart')) {
                        throw new Exception('A valid WC_Cart object is required');
                    }

                    $this->cart = $cart;
                }

                public function init() {
                    
                }

                public function get_cart_from_session() {
                    
                }

                public function destroy_cart_session() {
                    
                }

                public function maybe_set_cart_cookies() {
                    
                }

                public function set_session() {
                    
                }

                public function get_cart_for_session() {
                    $cart_session = array();
                    return $cart_session;
                }

                public function persistent_cart_update() {
                    
                }

                public function persistent_cart_destroy() {
                    
                }

                private function set_cart_cookies($set = true) {
                    
                }

            }

        }
        if (!class_exists('AZM_WC_Cart')) {

            class AZM_WC_Cart extends WC_Cart {

                function __construct() {
                    $this->session = new AZM_WC_Cart_Session($this);
                    $this->fees_api = new WC_Cart_Fees($this);
                    $this->tax_display_cart = get_option('woocommerce_tax_display_cart');

                    add_action('woocommerce_check_cart_items', array($this, 'check_cart_items'), 1);
                    add_action('woocommerce_check_cart_items', array($this, 'check_cart_coupons'), 1);
                    add_action('woocommerce_after_checkout_validation', array($this, 'check_customer_coupons'), 1);
                }

            }

        }

        global $wp_actions, $azm_woo_create_cart;
        $azm_woo_create_cart = true;

        if (is_admin() || DOING_CRON) {
            include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
            $wp_actions['woocommerce_cart_loaded_from_session'] = true;
        }


        $cart = new AZM_WC_Cart();

        if (WC()->cart && WC()->cart->session) {
            remove_action('woocommerce_after_calculate_totals', array(WC()->cart->session, 'set_session'));
        }

        foreach ($cart_contents as $key => $values) {
            $cart->add_to_cart($values['product_id'], $values['quantity'], $values['variation_id'], isset($values['variation']) ? $values['variation'] : array(), $values);
        }

        $cart->calculate_totals();

        if (WC()->cart && WC()->cart->session) {
            add_action('woocommerce_after_calculate_totals', array(WC()->cart->session, 'set_session'));
        }

        $azm_woo_create_cart = false;
        return $cart;
    }
    return false;
}

add_action('woocommerce_order_status_completed', 'azm_woo_update_last_purchase_date');
add_action('woocommerce_order_status_processing', 'azm_woo_update_last_purchase_date');
add_action('woocommerce_order_status_on-hold', 'azm_woo_update_last_purchase_date');

function azm_woo_update_last_purchase_date($order_id) {
    $order = wc_get_order($order_id);
    if (count($order->get_items()) > 0) {
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            if ($product_id) {
                global $wpdb;
                $wpdb->query(
                        $wpdb->prepare(
                                "UPDATE {$wpdb->postmeta} SET meta_value = %d WHERE post_id = %d AND meta_key='last_purchase_timestamp'", time(), $product_id
                        )
                );
            }
        }
    }
}

//
//
//group buying
//
//pre-order
//deposit
//
//action - show stock status
//action - disable specific payment gateway
//action - reward customer by coupon
//
//Bulk Attribute Manager 
//bulk variations manager
//action - Modify Existing Product Attributes
//action - Update With New Product Attributes
//
//
//
//action - Group of products
//action - Buy x get y
//
//

<?php

add_action('woocommerce_thankyou', 'hoodsly_redirect_after_purchase');

function hoodsly_redirect_after_purchase($order) {

    //get order object
    $check_order = wc_get_order(intval($order));

    $thankyoupage = get_permalink(10960);

    //making the url redirect
    $order_key = wc_clean($_GET['key']);
    $redirect = $thankyoupage;
    $redirect .= get_option('permalink_structure') === '' ? '&' : '?';
    $redirect .= 'order=' . absint($order) . '&key=' . $order_key;

    wp_redirect($redirect);
}

// Remove Billing and Shipping Details from Invoicing Page


add_filter('woocommerce_billing_fields', 'wpb_custom_billing_fields');

function wpb_custom_billing_fields($fields) {

    if (is_page('invoicing')) {

        unset($fields['billing_first_name']);
        unset($fields['billing_last_name']);
        unset($fields['billing_address_1']);
        unset($fields['billing_address_2']);
        unset($fields['billing_country']);
        unset($fields['billing_state']);
        unset($fields['billing_city']);
        unset($fields['billing_postcode']);
        unset($fields['billing_company']);
        unset($fields['ship-to-different-address-checkbox']);

?>
        <style>
            #billing_state_field,
            #billing_country_field {
                display: none !important;
            }
        </style>
    <?php

        return $fields;
    }

    return $fields;
}



add_filter('woocommerce_checkout_fields', 'virtual_products_less_fields');
function virtual_products_less_fields($fields) {

    // $fields['billing']['billing_phone']['required'] = false;
    $fields['billing']['billing_state']['required'] = false;
    $fields['billing']['billing_country']['required'] = false;

    $fields['shipping']['shipping_state']['required'] = false;
    $fields['shipping']['shipping_country']['required'] = false;


    if (!is_page('checkout')) {
        if (strpos($_SERVER['REQUEST_URI'], 'invoicing') != false) {

            add_filter('woocommerce_cart_needs_shipping_address', '__return_false');

            //Removes Additional Info title and Order Notes
            add_filter('woocommerce_enable_order_notes_field', '__return_false', 9999);

            return $fields;
        }
    }

    return $fields;
}


add_action('wp_ajax_invoice_checkout', 'invoice_checkout');
add_action('wp_ajax_nopriv_invoice_checkout', 'invoice_checkout');
function invoice_checkout() {
    parse_str($_POST['formData'], $parsed_form_data);
    $error = [
        'error_exists' => false,
    ];
    if (!$parsed_form_data) {
        $error['error_exists'] = true;
        $error['error_from'] = 'Empty Fields';
    }
    global $wpdb;
    $table = $wpdb->prefix . 'postmeta';
    $format = [
        '%d',
        '%s',
        '%s',
    ];
    if (isset($parsed_form_data['billing_first_name']) && $parsed_form_data['billing_first_name'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_first_name',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_first_name'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_first_name';
        }
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_shipping_first_name',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_first_name'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_shipping_first_name';
        }
    }
    if (isset($parsed_form_data['billing_last_name']) && $parsed_form_data['billing_last_name'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_last_name',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_last_name'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_last_name';
        }
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_shipping_last_name',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_last_name'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_shipping_last_name';
        }
    }
    if (isset($parsed_form_data['billing_company']) && $parsed_form_data['billing_company'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_company',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_company'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_company';
        }
    }
    if (isset($parsed_form_data['billing_address_1']) && $parsed_form_data['billing_address_1'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_address_1',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_address_1'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_address_1';
        }
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_shipping_address_1',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_address_1'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_shipping_address_1';
        }
    }
    if (isset($parsed_form_data['billing_address_2']) && $parsed_form_data['billing_address_2'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_address_2',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_address_2'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_address_2';
        }
    }
    if (isset($parsed_form_data['billing_city']) && $parsed_form_data['billing_city'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_city',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_city'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_city';
        }
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_shipping_city',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_city'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_shipping_city';
        }
    }
    if (isset($parsed_form_data['billing_state']) && $parsed_form_data['billing_state'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_state',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_state'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_state';
        }
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_shipping_state',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_state'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_shipping_state';
        }
    }
    if (isset($parsed_form_data['billing_postcode']) && $parsed_form_data['billing_postcode'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_postcode',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_postcode'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_postcode';
        }
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_shipping_postcode',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_postcode'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_shipping_postcode';
        }
    }
    if (isset($parsed_form_data['billing_country']) && $parsed_form_data['billing_country'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_country',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_country'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_country';
        }
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_shipping_country',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_country'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_shipping_country';
        }
    }
    if (isset($parsed_form_data['billing_phone']) && $parsed_form_data['billing_phone'] != "") {
        $arg = [
            'post_id' => $parsed_form_data['post_id'],
            'meta_key' => '_billing_phone',
            'meta_value' => sanitize_text_field($parsed_form_data['billing_phone'])
        ];
        if (!$wpdb->insert($table, $arg, $format)) {
            $error['error_exists'] = true;
            $error['error_from'] = '_billing_phone';
        }
    }

    if ($error['error_exists'] == false) {
        echo json_encode($error);
        wp_die();
    } else {
        echo json_encode($error);
        wp_die();
    }
}



// add_action('wp_ajax_custom_paint', 'custom_paint');
// add_action('wp_ajax_nopriv_custom_paint', 'custom_paint');

// function custom_paint() {
//     $post_id = intval($_POST['post_id']);
//     $custom_paint_val = $_POST['_custom_paint'];
//     $meta_val = $custom_paint_val == 'true' ? 1 : 0;

//     $order_total = intval(get_post_meta($post_id, '_order_total', true));

//     $new_order_total = $order_total += 650;

//     global $wpdb;
//     $table = $wpdb->prefix . 'postmeta';
//     $format = [
//         '%d',
//         '%s',
//         '%d',
//     ];

//     $error = [
//                 'error_exists' => false
//             ];

//      $arg = [
//         'post_id' => $post_id,
//         'meta_key' => '_custom_paint',
//         'meta_value' => $meta_val
//         ];


//     if($custom_paint_val == 'true'){

//         $res = $wpdb->get_results("SELECT * FROM " . $table . " WHERE post_id=". $post_id ." AND meta_key='_custom_paint'");

//         if(empty($res)){

//             if ($wpdb->insert($table, $arg, $format) && 
//                 update_post_meta($post_id, '_order_total', $new_order_total)) {
//                 $error['error_exists'] = false;
//             }else{
//                 $error['error_exists'] = true;
//                 $error['error_from'] = '_custom_paint';
//             }

//         }else{

//             if (update_post_meta($post_id, '_custom_paint', $meta_val) && 
//                 update_post_meta($post_id, '_order_total', $new_order_total)) {
//                 $error['error_exists'] = false;
//             }else{
//                 $error['error_exists'] = true;
//                 $error['error_from'] = '_custom_paint';
//             }

//         }

//     }
//     else{

//         $order_total = intval(get_post_meta($post_id, '_order_total', true));
//         $new_order_total = $order_total -= 650;

//         $res = $wpdb->get_results("SELECT * FROM " . $table . " WHERE post_id=". $post_id ." AND meta_key='_custom_paint'");

//         if(empty($res)){

//             if ($wpdb->insert($table, $arg, $format) && 
//                 update_post_meta($post_id, '_order_total', $new_order_total)) {
//                 $error['error_exists'] = false;
//             }else{
//                 $error['error_exists'] = true;
//                 $error['error_from'] = '_custom_paint';
//             }

//         }else{

//             if (update_post_meta($post_id, '_custom_paint', $meta_val) && 
//                 update_post_meta($post_id, '_order_total', $new_order_total)) {
//                 $error['error_exists'] = false;
//             }else{
//                 $error['error_exists'] = true;
//                 $error['error_from'] = '_custom_paint';
//             }

//         }
//     }

//     $error['updated_price'] = intval(get_post_meta($post_id, '_order_total', true));

//     echo json_encode($error);
//     wp_die();
// }

add_action('woocommerce_after_checkout_billing_form', 'custom_paint_field_feature_func');

function custom_paint_field_feature_func($checkout) {

    if (!is_page('invoicing')) {
        return;
    }


    echo '<div id="custom_paint_field_feature"><h4>' . __('Custom Paint Match') . '</h4>';

    woocommerce_form_field(
        'custom_paint',
        array(

            'type' => 'checkbox',

            'class' => 'custom_paint',

            'label' => __('Checking this checkbox will add extra $650 into the cart total'),

        ),

        $checkout->get_value('custom_paint')


    );

    echo '</div>';
}

function hoodsly_invoice_custom_paint($order_id) {

    $order_total = intval(get_post_meta($order_id, '_order_total', true));
    $new_order_total = $order_total += 650;

    if (!update_post_meta($order_id, '_order_total', $new_order_total)) {
        throw new Exception("Custom paint match value could not be added for this product ID: " + $order_id + "");
    }
}

add_action('woocommerce_checkout_order_processed', 'save_custom_paint_meta');

function save_custom_paint_meta($order_id) {
    if ($_POST['custom_paint']) {
        hoodsly_invoice_custom_paint($order_id);
    }
    update_post_meta($order_id, "_custom_paint", wp_unslash($_POST['custom_paint']));
}

function is_custom_paint_price_added($post_id) {
    $added = get_post_meta($post_id, '_custom_paint', true);
    if ($added) {
        return true;
    } else {
        return false;
    }
}

add_action('init', 'download_pdf');

function download_pdf() {

    if (isset($_GET['download_pdf']) && $_GET['download_pdf'] == 'true') {

        $file = 'https://hoodsly.com/wp-content/uploads/woocommerce_uploads/2021/02/CUSTOMER-COLOR-SAMPLE-COLOR-MATCH-TRANSMITTAL-SHIPPING-FORM-mtmrb3.pdf';

        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=" . $file . "");
    }
}

add_action("wp_head", "invoice_hide_payment_gateways");
function invoice_hide_payment_gateways() {
    if (is_page('invoicing')) {
    ?>
        <style>
            .payment_method_stripe {
                display: none;
            }

            .payment_method_cod {
                display: none;
            }
        </style>
<?php
    }
}


// Inventory Counter
add_action('woocommerce_order_details_after_order_table', 'manage_product_inventory_stock', 10);


function manage_product_inventory_stock($order) {

    $order_items           = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));

    //  $sku_code = get_tradewinds_option_product_sku($item->get_formatted_meta_data());

    //  $post_id = wc_get_product_id_by_sku($sku_code);

    //  $product = wc_get_product( $post_id );

    //  $post_id = 0;

    //  if(intval($product->parent_id) == 0){
    //      $post_id = $variant_id;
    //  }else{
    //      $post_id = intval($product->parent_id);
    //  }

    //  reduce_product_stock($product, $post_id);

    // $order_first_item_key = array_keys($order_items)[0];

    // $order_first_item = $order_items[$order_first_item_key];

    foreach ($order_items as $item_id => $item) {

        $sku_code = get_product_sku($item->get_formatted_meta_data());

        $post_id = wc_get_product_id_by_sku($sku_code);

        $product = wc_get_product($post_id);

        if ($product->manage_stock == 'yes') {
            reduce_product_stock($product, $post_id, $product->stock_status);
        }
    }
}

function get_sku_array($items) {

    if ($items) {
        foreach ($items as $item) {
            if (
                $item->display_key == 'Ventilation Options' ||
                $item->display_key == 'Tradewinds Options' ||
                $item->display_key == 'Fantech HL42 Options' ||
                $item->display_key == 'Fantech HL36 Options' ||
                $item->display_key == 'Broan Non-Duct Kit'
            ) {
                return (array) $item;
                break;
            }
        }
    }
}


function reduce_product_stock($product, $post_id, $stock_status) {

    if ($stock_status != 'instock') {
        return;
    }

    if ($post_id != 0) {

        $stock_quantitiy = $product->stock_quantity;

        $product_name = $product->get_name();

        $new_stock_quantity = $stock_quantitiy - 1;


        wc_update_product_stock($product,  $new_stock_quantity, 'set');

        wc_delete_product_transients($post_id);

        send_email_on_low_stock_qunatitiy($new_stock_quantity, $product_name);
    }
}
function get_product_sku($items) {

    $sku = explode(")", explode(":", get_sku_array($items)['display_value'])[1])[0];

    return ltrim(rtrim($sku));
}

function send_email_on_low_stock_qunatitiy($new_stock_quantity, $product_name) {
    if (intval($new_stock_quantity) < 6) {
        $email_list = [
            'kevin@hoodsly.com',
            'matthew@hoodsly.com',
            'hello@corbelsplus.com',
            'dev.ar.arif@gmail.com',
            'richardsetu@gmail.com',
            'richardsetu1@gmail.com'
        ];

        $subject = "" . $product_name . " reached low stock";
        $message = "" . $product_name . " reached stock below " . $new_stock_quantity . "";

        if ($new_stock_quantity < 1) {
            $subject = "" . $product_name . " product stock is empty";
            $message = "" . $product_name . " product stock is empty";
        }

        wp_mail($email_list, $subject, $message);
    }
}



// Send Email When Estimated Shipping date 3 days bellow.
add_filter('cron_schedules', 'hoodsly_add_cron_interval');
function hoodsly_add_cron_interval($schedules) {
    $schedules['e_shipping_one_day'] = array(
        'interval' => 86400,
        'display'  => esc_html__('Every One Day'),
    );
    return $schedules;
}

add_action('wp', 'hoodsly_corn_scheduels_callback');
function hoodsly_corn_scheduels_callback() {
    if (!wp_next_scheduled('hoodsly_cron_event_hook')) {
        wp_schedule_event(time(), 'e_shipping_one_day', 'hoodsly_cron_event_hook');
    }
    // wp_clear_scheduled_hook( 'hoodsly_cron_event_hook' );
}

add_filter('wp_mail_content_type', 'hoodsly_email_set_content_type');
function hoodsly_email_set_content_type() {
    return "text/html";
}

add_action('hoodsly_cron_event_hook', 'hoodsly_estimate_email_send');
function hoodsly_estimate_email_send() {

    $statuses = ['processing'];
    $orders = wc_get_orders(['limit' => -1, 'status' => $statuses]);
    if ($orders) {
        foreach ($orders as $order) {

            $order_date = $order->order_date;
            $order_id   = $order->get_id();
            $shiping_date_time = strtotime($order_date) + (30 * 24 * 60 * 60);

            $items = $order->get_items();
            $product_name = '';
            foreach ($items as $item) {

                $product_name =  $item->get_name();

                $rush = $item->get_meta('rushed_manufacturing');

                $noRushData = $item->get_meta_data();
                $noRush = end($noRushData)->value;

                if ($rush && $noRush != 'no_rush_order') {
                    $shiping_date_time = strtotime($order_date) + (20 * 24 * 60 * 60);
                    break;
                }
            }

            if (is_custom_paint_price_added($order_id)) {

                $shiping_date_time = strtotime($order_date) + (42 * 24 * 60 * 60);
            }


            $shipping_time = $shiping_date_time - time();
            $day3_time = strtotime("5 days") - time();

            $day_left = round((($shipping_time / 24) / 60) / 60);
            $days3 = round((($day3_time / 24) / 60) / 60);


            if ($day_left <= $days3 && $day_left > 0) {

                $to = 'hello@hoodsly.com';
                //$to = 'richardsetu1@gmail.com';
                $headers = 'From: Shipping Date <shippingdate@hoodsly.com>' . "\r\n";
                $subject = "#" . $order_id . " reached 3 days";
                $message = "<b>Product ID:</b> <a href='https://hoodsly.com/wp-admin/post.php?post=$order_id&action=edit'>#" . $order_id . "</a><br> Reached <b>3 days</b> below and remaining <b>" . $day_left . " days</b>";
                wp_mail($to, $subject, $message, $headers);
            }
        }
    }
}

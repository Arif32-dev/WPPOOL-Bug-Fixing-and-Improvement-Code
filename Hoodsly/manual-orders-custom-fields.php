<?php


add_action('woocommerce_admin_order_data_after_billing_address', 'display_manual_order_data_after_billing');
function display_manual_order_data_after_billing($order) {

    $order_id = $order->get_id();

    $added = get_post_meta($order_id, '_custom_paint', true);

    // 	if ( empty( get_post_meta( $order_id, '_ign_one_page_clerk', true ) ) ) {
    // 		return;
    // 	}

    $data_arr = [
        '_custom_paint'      => 'Custom Paint',
    ];

    // 	$data_arr = [
    // 		// '_increase_depth'    => 'Increase Depth',
    // 		// '_chimney_extension' => 'Chimney Extension',
    // 		'_custom_paint'      => 'Custom Paint : Yes',
    // // 		'_wood_species'      => 'Wood Species',
    // // 		'_molding'           => 'Molding',
    // // 		'_specialty_finish'  => 'Specialty Finish',
    // 	];

    printf('<h4>Order Details:</h4>');
    foreach ($data_arr as $key => $label) {
        $value = !empty(get_post_meta($order_id, $key, true)) ? get_post_meta($order_id, $key, true) : '';

        if ($value) {
            $_value = "Yes";
        } else {
            $_value = "No";
        }

        printf('<p><strong>%s:</strong> %s</p>', $label, $_value);
    }
}

<?php

/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited


?>
<style>
    .woocommerce-page.woocommerce-checkout table.shop_table td {
        padding-left: 10px;
    }
</style>



<form name="invoice_checkout" id="invoice_checkout" method="post" class="invoice_checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
    <input type="hidden" name="post_id" id="order_post_id" value="<?php echo explode('/', $_SERVER['REQUEST_URI'])[3] ?>">
    <?php do_action('woocommerce_checkout_billing'); ?>
    <!--<label for="custom_paint">-->
    <!--    <strong>Custom Paint Match</strong>-->
    <!--       <input type="checkbox" name="custom_paint" id ="custom_paint"/>-->
    <!--       <br>-->
    <!--       <i style="color: red;">Checking this checkbox will add extra $650 into the cart total</i>-->
    <!--</label>-->
    <br>
    <br>
</form>



<form id="order_review" method="post">

    <table class="shop_table">
        <thead>
            <tr>
                <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                <th class="product-quantity"><?php esc_html_e('Qty', 'woocommerce'); ?></th>
                <th class="product-total"><?php esc_html_e('Totals', 'woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($order->get_items()) > 0) : ?>
                <?php foreach ($order->get_items() as $item_id => $item) : ?>
                    <?php
                    if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
                        continue;
                    }
                    ?>
                    <tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
                        <td class="product-name">
                            <?php
                            echo apply_filters('woocommerce_order_item_name', esc_html($item->get_name()), $item, false); // @codingStandardsIgnoreLine

                            do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);

                            wc_display_item_meta($item);

                            do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
                            ?>
                        </td>
                        <td class="product-quantity"><?php echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', esc_html($item->get_quantity())) . '</strong>', $item); ?></td><?php // @codingStandardsIgnoreLine 
                                                                                                                                                                                                                                                        ?>
                        <td class="product-subtotal"><?php echo $order->get_formatted_line_subtotal($item); ?></td><?php // @codingStandardsIgnoreLine 
                                                                                                                    ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <?php if ($totals) : ?>
                <?php foreach ($totals as $total) : ?>
                    <tr>
                        <th scope="row" colspan="2"><?php echo $total['label']; ?></th><?php // @codingStandardsIgnoreLine 
                                                                                        ?>
                        <th class="product-total"><?php echo $total['value']; ?></th><?php // @codingStandardsIgnoreLine 
                                                                                        ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tfoot>
    </table>


    <div id="payment">
        <?php if ($order->needs_payment()) : ?>
            <ul class="wc_payment_methods payment_methods methods">
                <?php
                if (!empty($available_gateways)) {
                    foreach ($available_gateways as $gateway) {
                        wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                    }
                } else {
                    echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters('woocommerce_no_available_payment_methods_message', esc_html__('Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce')) . '</li>'; // @codingStandardsIgnoreLine
                }
                ?>
            </ul>
        <?php endif; ?>
        <div class="form-row">
            <input type="hidden" name="woocommerce_pay" value="1" />

            <?php wc_get_template('checkout/terms.php'); ?>

            <?php do_action('woocommerce_pay_order_before_submit'); ?>

            <?php echo apply_filters('woocommerce_pay_order_button_html', '<button type="submit" class="button alt" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); // @codingStandardsIgnoreLine 
            ?>

            <?php do_action('woocommerce_pay_order_after_submit'); ?>

            <?php wp_nonce_field('woocommerce-pay', 'woocommerce-pay-nonce'); ?>
        </div>
    </div>
</form>

<?php

print_r()
?>
<script>
    jQuery(document).ready(function($) {

        let billing_first_name = false;

        let billing_last_name = false;

        let billing_address_1 = false;

        let billing_city = false;

        let billing_postcode = false;

        let billing_phone = false;

        let billing_email = false;



        $('#billing_first_name, #billing_last_name, #billing_address_1, #billing_city, #billing_postcode, #billing_phone, #billing_email').on('input', function(e) {

            if ($(e.currentTarget).attr('id') == 'billing_first_name') {
                if ($(e.currentTarget).val() != '' && $(e.currentTarget).val() != null) {
                    billing_first_name = true;
                } else {
                    billing_first_name = false;
                }
            }

            if ($(e.currentTarget).attr('id') == 'billing_last_name') {
                if ($(e.currentTarget).val() != '' && $(e.currentTarget).val() != null) {
                    billing_last_name = true;
                } else {
                    billing_last_name = false;
                }
            }

            if ($(e.currentTarget).attr('id') == 'billing_address_1') {
                if ($(e.currentTarget).val() != '' && $(e.currentTarget).val() != null) {
                    billing_address_1 = true;
                } else {
                    billing_address_1 = false;
                }
            }

            if ($(e.currentTarget).attr('id') == 'billing_city') {
                if ($(e.currentTarget).val() != '' && $(e.currentTarget).val() != null) {
                    billing_city = true;
                } else {
                    billing_city = false;
                }
            }

            if ($(e.currentTarget).attr('id') == 'billing_postcode') {
                if ($(e.currentTarget).val() != '' && $(e.currentTarget).val() != null) {
                    billing_postcode = true;
                } else {
                    billing_postcode = false;
                }
            }

            if ($(e.currentTarget).attr('id') == 'billing_phone') {
                if ($(e.currentTarget).val() != '' && $(e.currentTarget).val() != null) {
                    billing_phone = true;
                } else {
                    billing_phone = false;
                }
            }

            if ($(e.currentTarget).attr('id') == 'billing_email') {
                if ($(e.currentTarget).val() != '' && $(e.currentTarget).val() != null) {
                    billing_email = true;
                } else {
                    billing_email = false;
                }
            }

            if (billing_first_name && billing_last_name && billing_address_1 && billing_city && billing_postcode && billing_phone && billing_email) {

                $('#place_order').attr('disabled', false);

            } else {
                $('#place_order').attr('disabled', true);
            }
        })



        $('#place_order').on('click', function() {

            if ($('#payment_method_stripe').prop('checked')) {

                if ($('#payment #stripe-card-element').hasClass('empty') ||
                    $('#payment #stripe-card-element').hasClass('invalid')) {
                    alert("Payment method field is emplty or invalid");
                    return;
                }

                if ($('.woocommerce-terms-and-conditions-wrapper #terms').prop('checked') == false) {
                    return;
                }

                sendData();

            } else {

                if ($('.woocommerce-terms-and-conditions-wrapper #terms').prop('checked') == false) {
                    return;
                }

                sendData();
            }

        })

        function sendData() {

            let custom_paint = $('#custom_paint').prop('checked');

            $.ajax({
                type: "POST",
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                data: {
                    action: 'invoice_checkout',
                    formData: $('#invoice_checkout').serialize(),
                    custom_paint: custom_paint
                },
                success: function(response) {

                    console.log(response)

                    if (JSON.parse(response).error_exists == true) {
                        alert('There is something error on saving your information');
                    }
                }
            });

        }

        // 		$('#custom_paint').on('change', (e) => {
        // 		    let post_id = $('#order_post_id').val()
        // 		    let checkbox_val = $(e.currentTarget).prop('checked');
        // 		     $.ajax({
        // 				type: "POST",
        // 				url: '<?php echo admin_url('admin-ajax.php') ?>',
        // 				data: {
        // 					action: 'custom_paint',
        // 					custom_paint: checkbox_val,
        // 					post_id: post_id
        // 				},
        // 				success: function(response) {

        // 					console.log(response)

        // 					if (JSON.parse(response).error_exists == true) {
        // 						alert('Custom paint match could not be added due to error');
        // 					}

        // 					if(JSON.parse(response).error_exists == false){
        // 					    $('#order_review .order_item .woocommerce-Price-amount bdi')[0].innerHTML = '<span class="woocommerce-Price-currencySymbol">$</span>' + parseInt(JSON.parse(response).updated_price)
        // 					}

        // 				}
        // 			});
        // 		})
    })
</script>
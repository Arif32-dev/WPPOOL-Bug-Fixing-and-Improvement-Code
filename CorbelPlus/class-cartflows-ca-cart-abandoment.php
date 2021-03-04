<?php

// Plugin Name :	Woocommerce-Cart-Abandonment-Recovery
// =============Woocommerce-Cart-Abandonment-Recovery===============

// File: woo-cart-abandonment-recovery/modules/cart-abandonment/class-cartflows-ca-cart-abandonment.php	



/**
	 * Restore cart abandonemnt data on checkout page.
	 *
	 * @param  array $fields checkout fields values.
	 * @return array field values
	 */
	public function restore_cart_abandonment_data($fields = array()) {
		global $woocommerce;
		$result = array();
		// Restore only of user is not logged in.
		$wcf_ac_token = filter_input(INPUT_GET, 'wcf_ac_token', FILTER_SANITIZE_STRING);
		if ($this->is_valid_token($wcf_ac_token)) {

			// Check if `wcf_restore_token` exists to restore cart data.
			$token_data = $this->wcf_decode_token($wcf_ac_token);
			if (is_array($token_data) && isset($token_data['wcf_session_id'])) {
				$result = $this->get_checkout_details($token_data['wcf_session_id']);
				if (isset($result) && WCF_CART_ABANDONED_ORDER === $result->order_status || WCF_CART_LOST_ORDER === $result->order_status) {
					WC()->session->set('wcf_session_id', $token_data['wcf_session_id']);
				}
			}

			if ($result) {
				$cart_content = unserialize($result->cart_contents);

				$size_includes_products = [];

				if (get_option('thwepo_custom_sections')['wood_hood_sizes']->fields) {

					foreach (get_option('thwepo_custom_sections')['wood_hood_sizes']->fields as $field) {

						array_push(
							$size_includes_products,
							$field->conditional_rules[0]->condition_rules[0]->condition_sets[0]->conditions[0]->operand[0]
						);
					}
				}

				if ($cart_content) {
					$woocommerce->cart->empty_cart();
					
					$key_array = array_keys($cart_content)[0];
					$size_array_inside_key = array_keys($cart_content[$key_array]['thwepo_options'])[0];
					$size_value_key = $cart_content[$key_array]['thwepo_options'][$size_array_inside_key]['value'];
					$product_id = $cart_content[$key_array]['product_id'];
						
						if (wp_get_post_terms($product_id, 'product_cat')[0]->slug == 'wood-hoods') {
								if (in_array($product_id, $size_includes_products)) {
									if (isset($token_data['wcf_coupon_code']) && !$woocommerce->cart->applied_coupons) {
										
										$size_total = $cart_content[$key_array]['thwepo_options'][$size_array_inside_key]['options'][$size_value_key]['price'];
										if(!isset($_COOKIE['wc_abandoned_discount_cookie']))
										{
											$cart_content[$key_array]['thwepo_options'][$size_array_inside_key]['options'][$size_value_key]['price'] = $this->get_discount_value_on_wood_hood_sizes($size_total, 10);
											$cookie_name = "wc_abandoned_discount_cookie";
											$cookie_value = $size_total;
											setcookie($cookie_name, $cookie_value, time() + (86400 * 1), "/");
										}else{
											$cart_content[$key_array]['thwepo_options'][$size_array_inside_key]['options'][$size_value_key]['price'] = $this->get_discount_value_on_wood_hood_sizes(intval($_COOKIE['wc_abandoned_discount_cookie']), 10);
										}
									
									}
								}
						}
					
					
					wc_clear_notices();
					foreach ($cart_content as $cart_item) {
						
						$cart_item_data = array();
						$variation_data = array();
						$id             = $cart_item['product_id'];
						$qty            = $cart_item['quantity'];

						// Skip bundled products when added main product.
						if (isset($cart_item['bundled_by'])) {
							continue;
						}

						if (isset($cart_item['variation'])) {
							foreach ($cart_item['variation']  as $key => $value) {
								$variation_data[$key] = $value;
							}
						}

						$cart_item_data = $cart_item;

						$woocommerce->cart->add_to_cart($id, $qty, $cart_item['variation_id'], $variation_data, $cart_item_data);
						
					}

// 					if (isset($token_data['wcf_coupon_code']) && !$woocommerce->cart->applied_coupons) {
// 						if ($isProductWoodHood) {
// 							$woocommerce->cart->add_discount($token_data['wcf_coupon_code']);
// 						}
// 					}
				}
				$other_fields = unserialize($result->other_fields);

				$parts = explode(',', $other_fields['wcf_location']);
				if (count($parts) > 1) {
					$country = $parts[0];
					$city    = trim($parts[1]);
				} else {
					$country = $parts[0];
					$city    = '';
				}

				foreach ($other_fields as $key => $value) {
					$key           = str_replace('wcf_', '', $key);
					$_POST[$key] = sanitize_text_field($value);
				}
				$_POST['billing_first_name'] = sanitize_text_field($other_fields['wcf_first_name']);
				$_POST['billing_last_name']  = sanitize_text_field($other_fields['wcf_last_name']);
				$_POST['billing_phone']      = sanitize_text_field($other_fields['wcf_phone_number']);
				$_POST['billing_email']      = sanitize_email($result->email);
				$_POST['billing_city']       = sanitize_text_field($city);
				$_POST['billing_country']    = sanitize_text_field($country);
			}
		}

		return $fields;
	}
	
	public function get_discount_value_on_wood_hood_sizes($total, $percent_value) {
		if ($total > 0) {
			$percentage_value = $percent_value * ($total / 100);
			return $total - $percentage_value;
		} else {
			return null;
		}
	}
<?php

if (!class_exists('WC_ShippingEasy_Integration')) {

	class WC_ShippingEasy_Integration extends WC_Integration {
	
		public function __construct() {

			global $woocommerce;

			$this->id = 'shippingeasy-woocommerce';
			$this->method_title = __('ShippingEasy Settings', 'shippingeasy-woocommerce');
			$this->method_description = __('ShippingEasy provides the easiest shipping app for online sellers. Its cloud-based shipping solution offers the cheapest USPS postage rates, plus the ability to plug in UPS and FedEx accounts. For help with using ShippingEasy with WooCommerce, <a href="https://support.shippingeasy.com/hc/en-us/articles/204115175-How-to-Integrate-your-WooCommerce-store-with-ShippingEasy-step-by-step-guide-with-pictures-">view the documentation</a>.', 'shippingeasy-woocommerce');

			$this->init_form_fields();
			$this->init_settings();

			$this->api_key = $this->get_option('api_key');
			$this->secret_key = $this->get_option('secret_key');
			$this->store_api = $this->get_option('store_api');
			$this->base_url = $this->get_option('base_url');
			$this->shippable_statuses = $this->get_option('shippable_statuses');
			$this->debug_enabled = $this->get_option('debug_enabled');
 
			add_action('woocommerce_update_options_integration_'.$this->id, array($this, 'process_admin_options'));
			add_action('woocommerce_thankyou', array($this, 'shipping_place_order'));
			add_action('woocommerce_payment_complete', array($this, 'shipping_place_order'));
			add_action('woocommerce_order_status_changed', array($this, 'handle_order_status_change'));
			add_action('woocommerce_order_actions', array($this, 'add_manual_ship_action'));
			add_action('woocommerce_order_action_se_send_to_shippingeasy', array($this, 'handle_manual_ship_action'));

			// Create log view page in WooCommerce < 2.2.0
			$wc_version = se_wc_version();
			if ($wc_version['Major'] < 2 || ($wc_version['Major'] == 2 && $wc_version['Minor'] < 2)) {
				function register_old_woo_log() {
					add_submenu_page('woocommerce', 'ShippingEasy Log', 'ShippingEasy Log', 'manage_options', 'old-woo-log', 'old_woo_log_callback' ); 
				}
				function old_woo_log_callback() {
					if (function_exists('WC')) {
						$plugin_path = WC()->plugin_path();
					} else {
						global $woocommerce;
						$plugin_path = $woocommerce->plugin_path();					
					}
					$logs = glob($plugin_path."/logs/shippingeasy*");
					$recent_file = '';
					$recent_time = '';
					echo '<pre style="word-wrap: break-word;">';
					// Get most recent log file and output last 5 lines:
					foreach ($logs as $log) {			
						$mod_time = filemtime($log);
						if (empty($recent_time) || ($mod_time > $recent_time)) {
							$recent_time = $mod_time;
							$recent_file = $log;
						}			
					}
					if ($file = @file($recent_file)) {
						for ($i = count($file)-6; $i < count($file); $i++) {
							if (isset($file[$i])) {
								echo trim($file[$i])."\n";					
							}
						}
					} else {
						echo __('Log empty.', 'shippingeasy-woocommerce');
					}
					echo '</pre>';
				}
				add_action('admin_menu', 'register_old_woo_log',99);
			}

		}
	
		public function init_form_fields() {
			$se_statuses = se_get_order_statuses();
			$se_statuses_array = array();
			foreach ($se_statuses as $id => $status) {
				$se_statuses_array[$status->name] = $status->name;
			}

			$log_link = 'admin.php?page=wc-status&tab=logs';
			$wc_version = se_wc_version();
			if ($wc_version['Major'] < 2 || ($wc_version['Major'] == 2 && $wc_version['Minor'] < 2)) {
				$log_link = 'admin.php?page=old-woo-log';			
			}

			$this->form_fields = array(
				'api_key' => array(
					'title'       => __('API Key', 'shippingeasy-woocommerce'),
					'type'        => 'text',
					'default'     => ''
				),
				'secret_key' => array(
					'title'       => __('Secret Key', 'shippingeasy-woocommerce'),
					'type'        => 'text',
					'default'     => ''
				),
				'store_api' => array(
					'title'       => __('Store API Key', 'shippingeasy-woocommerce'),
					'type'        => 'text',
					'default'     => ''
				),
				'base_url' => array(
					'title'       => __('API URL', 'shippingeasy-woocommerce'),
					'type'        => 'text',
					'default'     => 'https://app.shippingeasy.com'
				),
				'shippable_statuses' => array(
					'title'       => __('Shippable Statuses', 'shippingeasy-woocommerce'),
					'type'        => 'multiselect',
					'css'         => 'width: 25em; height: 130px;',
					'options'     => $se_statuses_array
				),
				'debug_enabled' => array(
					'title'       => __('Debug Log', 'shippingeasy-woocommerce'),
					'description' => sprintf(__('Log errors and API requests in the <a href="%s">WooCommerce logs</a> area?', 'shippingeasy-woocommerce'), $log_link),
					'label'       => __('Debug Enabled', 'shippingeasy-woocommerce'),
					'type'        => 'checkbox',
					'default'     => 'no'
				)
			);
		}

		public function shipping_place_order($order_id, $is_backend_order = false) {

			$already_created = get_post_meta($order_id, 'se_order_created', true);
			if ($already_created == true) {
				return true;
			}

			global $wpdb;
			global $woocommerce;
			global $post;
		
			include_once(plugin_dir_path(__FILE__).'../lib/shipping_easy-php/lib/ShippingEasy.php');

			ShippingEasy::setApiKey($this->get_option('api_key'));
			ShippingEasy::setApiSecret($this->get_option('secret_key'));
			ShippingEasy::setApiBase($this->get_option('base_url'));

			$order = new WC_Order($order_id);
		
			if ($is_backend_order == false) {
				$export_ok = false;
				$statuses = (array)se_get_order_statuses();
				if (!in_array($order->status, $this->get_option('shippable_statuses'))) {
					//se_wc_log_ok(sprintf(__('Order #%d not sent to ShippingEasy as %s orders are not designated as shippable.'), $order_id, $order->status), false);
					return true;				
				}
			}
		
			$download_total = 0;
			$downloads_subtotal = 0;
		
			$total_products = count($order->get_items());
			$check_virtual = array(); $check_virtuals = 0;
			$check_download = array(); $check_downloads = 0;
			foreach ($order->get_items() as $item) {
				$product_id = $item['product_id'];
				$post_meta = get_post_meta($item['product_id']);
				$check_virtual[] = $post_meta['_virtual'][0];
				$check_download[] = $post_meta['_downloadable'][0];
				if ($post_meta['_virtual'][0] == 'yes' || $post_meta['_downloadable'][0] == 'yes') {
					$download_total = $item['line_subtotal'];
					$downloads_subtotal += $download_total;
				}
			}
		
			if (in_array("yes", $check_virtual)) {
				$check_virtuals += 1;
			}
			if (in_array("yes", $check_download)) {
				$check_downloads += 1;
			}

			$total_download_product = $check_virtuals + $check_downloads;
		
			if ($total_products > $total_download_product) {

				$billing_company = $order->billing_company;
				$billing_first_name = $order->billing_first_name;
				$billing_last_name = $order->billing_last_name;
				$billing_address = $order->billing_address_1;
				$billing_address2 = $order->billing_address_2;
				$billing_city = $order->billing_city;
				$billing_state = $order->billing_state;
				$billing_postcode = $order->billing_postcode;
				$billing_country = $order->billing_country;
				$billing_email = $order->billing_email;
				$billing_phone = $order->billing_phone;
				$shipping_company = $order->shipping_company;
				$shipping_first_name = $order->shipping_first_name;
				$shipping_last_name = $order->shipping_last_name;
				$shipping_address = $order->shipping_address_1;
				$shipping_address2 = $order->shipping_address_2;
				$shipping_city = $order->shipping_city;
				$shipping_state = $order->shipping_state;
				$shipping_postcode = $order->shipping_postcode;
				$shipping_country = $order->shipping_country;
				$order_cart_total = $order->order_total;
				$order_totals = $order_cart_total - $downloads_subtotal;
				$order_total = $order_totals;
				$order_tax = $order->order_tax;
				$order_shipping = $order->order_shipping;
				$order_shipping_tax = $order->order_shipping_tax;
				$cart_discount = $order->cart_discount;
		
				// Shipping method variable moved in WC 2.1
				$shipping_method = '';
				if (property_exists($order, 'shipping_method')) { $shipping_method = $order->shipping_method; }
				elseif (method_exists($order, 'get_shipping_method')) { $shipping_method = $order->get_shipping_method(); }

				$item_qty = 0;
				$line_subtotal = 0;
				foreach ($order->get_items() as $item) {
					$item_qty++;
					$line_subtotal += $item['line_subtotal'];
				}

				$total_excluding_tax = $line_subtotal;
				$shipping_cost_including_tax = ($order_shipping + $order_shipping_tax);
		
				$order_comment = $wpdb->get_results("SELECT post_excerpt FROM $wpdb->posts WHERE ID = '$order_id'");
				foreach ($order_comment as $order_comments) {
					$post_excerpt = $order_comments->post_excerpt;
				}
		
				$values = array(
					"external_order_identifier" => "$order_id",
					"ordered_at" => date('Y-m-d H:i:s', time()),
					"order_status" => "awaiting_shipment",
					"subtotal_including_tax" => "$order_total",
					"total_including_tax" => "$order_total",
					"total_excluding_tax" => "$total_excluding_tax",
					"discount_amount" => "$cart_discount",
					"coupon_discount" => "$cart_discount",
					"subtotal_including_tax" => "$order_total",
					"subtotal_excluding_tax" => "$total_excluding_tax",
					"subtotal_excluding_tax" => "$total_excluding_tax",
					"subtotal_tax" => "$order_tax",
					"total_tax" => "$order_tax",
					"base_shipping_cost" => "$order_shipping",
					"shipping_cost_including_tax" => "$shipping_cost_including_tax",
					"shipping_cost_excluding_tax" => "$order_shipping",
					"shipping_cost_tax" => "$order_shipping_tax",
					"base_handling_cost" => "0.00",
					"handling_cost_excluding_tax" => "0.00",
					"handling_cost_including_tax" => "0.00",
					"handling_cost_tax" => "0.00",
					"base_wrapping_cost" => "0.00",
					"wrapping_cost_excluding_tax" => "0.00",
					"wrapping_cost_including_tax" => "0.00",
					"wrapping_cost_tax" => "0.00",
					"notes" => "$post_excerpt",
					"billing_company" => "$billing_company",
					"billing_first_name" => "$billing_first_name",
					"billing_last_name" => "$billing_last_name",
					"billing_address" => "$billing_address",
					"billing_address2" => "$billing_address2",
					"billing_city" => "$billing_city",
					"billing_state" => "$billing_state",
					"billing_postal_code" => "$billing_postcode",
					"billing_country" => "$billing_country",
					"billing_phone_number" => "$billing_phone",
					"billing_email" => "$billing_email",
					"recipients" => array(
						array(
							"first_name" => "$shipping_first_name",
							"last_name" => "$shipping_last_name",
							"company" => "$shipping_company",
							"email" => "$billing_email",
							"phone_number" => "$billing_phone",
							"residential" => "true",
							"address" => "$shipping_address",
							"address2" => "$shipping_address2",
							"province" => "",
							"state" => "$shipping_state",
							"city" => "$shipping_city",
							"postal_code" => "$shipping_postcode",
							"postal_code_plus_4" => "",
							"country" => "$shipping_country",
							"shipping_method" => "$shipping_method",
							"base_cost" => "10.00",
							"cost_excluding_tax" => "10.00",
							"cost_tax" => "0.00",
							"base_handling_cost" => "0.00",
							"handling_cost_excluding_tax" => "0.00",
							"handling_cost_including_tax" => "0.00",
							"handling_cost_tax" => "0.00",
							"shipping_zone_id" => "123",
							"shipping_zone_name" => "XYZ",
							"items_total" => "$item_qty",
							"items_shipped" => "0",
							"line_items" => $this->shipping_order_detail($order_id)
						)
					)
				);
		
				try {
					$order = new ShippingEasy_Order($this->get_option('store_api'), $values);
					$order->create();
					update_post_meta($order_id, 'se_order_created', true);
					se_wc_log_ok(sprintf(__('Submitted order: %s'), json_encode($values)));
				} catch (Exception $e) {				
					$error_message = $e->getMessage().' '.json_encode($values);
					se_wc_log_error(sprintf(__('Sending to ShippingEasy: %s'), $error_message));
				}

			}
			
		}

		public function shipping_order_detail($order_id) {

			$product = array();
			$order = new WC_Order($order_id);
			foreach ($order->get_items() as $item) {
				$product_attr_name_key = array();
				$option_values = array();
				$post = get_post($item['product_id']);
				$product_id = $item['product_id'];
				$post_meta = get_post_meta($item['product_id']);
				$regular_price = get_post_meta($item['product_id'], '_regular_price');
				$sku = get_post_meta($item['product_id'], '_sku');
				$item_name = $item['name'];
				$item_qty = $item['qty'];
				$line_subtotal = $item['line_subtotal'];
				$unit_price = $line_subtotal / $item_qty;
				$line_subtotal = $item['line_subtotal'];
				$check_virtual = $post_meta['_virtual'][0];
				$check_download = $post_meta['_downloadable'][0];
				if ($post_meta['_weight'][0] == '') {
					$weight_to_oz = 0.00;
				} else {
					$weight_to_oz = se_convert_weight($post_meta['_weight'][0], 'oz');
				}

				if ($check_virtual == 'no' && $check_download == 'no') {
	
					$item_sku = $sku[0];
					$item_price = $unit_price;
					$item_total = $line_subtotal;
					$item_weight = $weight_to_oz;
					$item_options = array();

					if (!empty($item['variation_id'])) {
						$variation = new WC_Product_Variation($item['variation_id']);
						$variation_attributes = $variation->get_variation_attributes();
						foreach ($variation_attributes as $name => $value) {
							$default_value = $item[str_replace('attribute_', '', $name)];						
							if (empty($value)) { $value = $default_value; } else {
								if (taxonomy_exists(esc_attr(str_replace('attribute_', '', $name)))) {
									$term = get_term_by('slug', $value, esc_attr(str_replace('attribute_', '', $name)));
									if (!is_wp_error($term) && $term->name)
									$value = $term->name;
								} else {
									$value = ucwords(str_replace('-', ' ', $value));
								}
							}
							if (function_exists('wc_attribute_label')) {
								$formatted_name = wc_attribute_label(str_replace('attribute_', '', $name));
								$item_options[$formatted_name] = urldecode($value);
							} else {
								// Backwards compatibility for Woo 2.0
								$formatted_name = se_wc_attribute_label(str_replace('attribute_', '', $name));
								$item_options[$formatted_name] = urldecode($value);
								if (isset($item['item_meta'][$formatted_name])) {
									$meta_attributes = implode(', ', $item['item_meta'][$formatted_name]);
									$item_options[$formatted_name] = urldecode($meta_attributes);
								}
							}
						}

						$variation_weight = $variation->get_weight();
						$variation_weight = se_convert_weight($variation_weight, 'oz');
						if ($variation_weight != $item_weight) {
							$item_weight = $variation_weight;						
						}
						
						$variation_sku = $variation->get_sku();
						if ($variation_sku != $item_sku) {
							$item_sku = $variation_sku;						
						}

					}

					$product[] = array(
						"item_name" => "$item_name",
						"sku" => "$item_sku",
						"bin_picking_number" => "0",
						"unit_price" => "$item_price",
					//	"total_excluding_tax" => "$item_total",
						"total_excluding_tax" => "$unit_price",
						"weight_in_ounces" => "$item_weight",
						"quantity" => "$item_qty",
						"product_options" => $item_options
					);
				}

			}
		
			$product_count = count($product);
			for ($i = 0; $i < $product_count; $i++) {
				if ($product[$i]['weight_in_ounces'] == 0) {
					unset($product[$i]['weight_in_ounces']);
				}
			}
			return $product;

		}

		public function handle_order_status_change($order_id) {
			$order = new WC_Order($order_id);
			if ($order->status == 'cancelled') {
				/* Handle cancellation */
				include_once(plugin_dir_path(__FILE__).'../lib/shipping_easy-php/lib/ShippingEasy.php');		
				ShippingEasy::setApiKey($this->get_option('api_key'));
				ShippingEasy::setApiSecret($this->get_option('secret_key'));
				ShippingEasy::setApiBase($this->get_option('base_url'));
				try {
					$cancellation = new ShippingEasy_Cancellation($this->get_option('store_api'), "$order_id");
					$cancellation->create();
					se_wc_log_ok(sprintf(__('Order #%d successfully cancelled.'), $order_id));
				} catch (Exception $e) {
					se_wc_log_error(sprintf(__('Order #%d could not be cancelled.'), $order_id));
				}
			} else {
				/* Send to ShippingEasy if status has changed to a shippable status */
				if (in_array($order->status, $this->get_option('shippable_statuses'))) {
					$this->shipping_place_order($order_id, true);
				}
			}
		}

		public function add_manual_ship_action($actions) {
			$actions['se_send_to_shippingeasy'] = __('Send to ShippingEasy', 'shippingeasy-woocommerce');
			return $actions;
		}

		public function handle_manual_ship_action($order) {
			$this->shipping_place_order($order->id, true);
		}

	}

}

?>
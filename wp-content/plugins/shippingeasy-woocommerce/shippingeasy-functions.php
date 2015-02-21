<?php

function se_wc_log_ok($message, $critical=true) {
	$output = __('Notice', 'shippingeasy-woocommerce');
	if ($critical) { $output = __('Success', 'shippingeasy-woocommerce'); }
	$output .= ': '.$message;
	se_wc_log($output);
}

function se_wc_log_error($message, $critical=true) {
	$output = __('Notice', 'shippingeasy-woocommerce');
	if ($critical) { $output = __('Error', 'shippingeasy-woocommerce'); }
	$output .= ': '.$message;
	se_wc_log($output);
}

function se_wc_log($message) {
	$shippingeasy_settings = get_option('woocommerce_shippingeasy-woocommerce_settings');
	if (strtoupper($shippingeasy_settings['debug_enabled']) == 'YES') {
		$log = new WC_Logger();
		$log->add('shippingeasy',$message);	
	}
}

function se_get_order_statuses() {
	/*
	 * In version 2.2 of the WooCommerce plugin, shop_order_status was removed.
	 * get_order_statuses retrieves the order statuses in a common format
	 * for our code to use.
	 *
	 * return format ex.:
	 *      Array (
	 *          [0] (class)
	 *              name = 'pending'
	 *          [1] (class)
	 *              name = 'processing'
	 *          [2] (class)
	 *              name = 'on-hold'
	 *          [3] (class)
	 *              name = 'completed'
	 *          [4] (class)
	 *              name = 'cancelled'
	 *          [5] (class)
	 *              name = 'refunded'
	 *          [6] (class)
	 *              name = 'failed'
	 *      )
	 *
	 */
	$wc_version = se_wc_version();
	// Woo Plugin >= 2.2.0
	if ($wc_version['Major'] > 2 || ($wc_version['Major'] == 2 && $wc_version['Minor'] >= 2)) {
		$statuses = Array();
		$wc_statuses = (array)wc_get_order_statuses();
		foreach($wc_statuses as $wc_key => $wc_status) {
			$new_status = new StdClass();
			$new_status->name = str_replace('wc-', '', $wc_key);
			$statuses[] = $new_status;
		}
	// Woo Plugin < 2.2.0
	} else {
		register_taxonomy('shop_order_status', 'shop_order');
		$statuses = (array)get_terms('shop_order_status', array('hide_empty' => false, 'orderby' => 'id'));
	}
	return $statuses;
}

function se_wc_version() {
	$version = array();
	$version_string = '0.0.0';	
	if (defined('WOOCOMMERCE_VERSION')) { $version_string = WOOCOMMERCE_VERSION; }
	if (defined('WC_VERSION')) { $version_string = WC_VERSION; }
	$wc_version = explode('.', $version_string);
	$version['Major'] = empty($wc_version[0]) ? 0 : $wc_version[0];		
	$version['Minor'] = empty($wc_version[1]) ? 0 : $wc_version[1];		
	$version['Patch'] = empty($wc_version[2]) ? 0 : $wc_version[2];		
	return $version;
}

function se_wc_attribute_label($name) {
	// Woo plugin 2.0 did not have wc_attribute_label()
	// So this emulator is needed for backwards compatibility.
	global $wpdb;	
	if (taxonomy_is_product_attribute($name)) {
		$name = str_replace('pa_', '', $name);
		$name = apply_filters('sanitize_taxonomy_name', urldecode(sanitize_title($name)), $name);
		$label = $wpdb->get_var($wpdb->prepare("SELECT attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $name));
		if (!$label) {
			$label = ucfirst($name);
		}
	} else {
		$label = ucwords(str_replace('-', ' ', $name));
	}
	return apply_filters('woocommerce_attribute_label', $label, $name);
}

function se_convert_weight($input, $to) {
	// woocommerce_get_weight() deprecated in favor of wc_get_weight().
	if (function_exists('woocommerce_get_weight')) {
		$output = woocommerce_get_weight($input, $to);
	} else {
		$output = wc_get_weight($input, $to);
	}
	return $output;
}

?>
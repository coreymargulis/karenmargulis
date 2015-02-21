<?php
/**
 * Plugin Name: ShippingEasy for WooCommerce
 * Description: Plugin to integrate WooCommerce with ShippingEasy. As orders are created in WooCommerce, they will be sent to ShippingEasy. When an order is shipped in ShippingEasy the WooCommerce order will be updated.
 * Author: ShippingEasy
 * Version: 2.1
 *
 * Text Domain: shippingeasy-woocommerce
 * Domain Path: /i18n/languages/
 *
 */

require_once(ABSPATH.'wp-admin/includes/plugin.php');

function load_shippingeasy_textdomain() {
	load_plugin_textdomain('shippingeasy-woocommerce', FALSE, basename(dirname(__FILE__)).'/i18n/languages/');
} add_action('plugins_loaded', 'load_shippingeasy_textdomain');

$woocommerce_installed = preg_grep("/\/woocommerce\.php/", apply_filters('active_plugins', get_option('active_plugins')));
if (empty($woocommerce_installed)) {

	deactivate_plugins(plugin_basename(__FILE__));	
	function no_woocommerce() {
		?><div id="message" class="error"><p><?php _e('The plugin ShippingEasy for WooCommerce can not be used without WooCommerce.', 'shippingeasy-woocommerce'); ?></p></div><?php
	} add_action('admin_notices', 'no_woocommerce');
	if (isset($_GET['activate'])) {
		unset($_GET['activate']);
	}

} else {

	include_once('shippingeasy-functions.php');

	class WC_Integration_ShippingEasy {
	
		public function __construct() {
			add_action('plugins_loaded', array($this, 'init'));
		}
	
		public function init() {
			include_once('class/wc_shippingeasy_integration.php');
			add_filter('woocommerce_integrations', array($this, 'add_integration'));
		}
	
		public function add_integration($integrations) {
			$integrations[] = 'WC_ShippingEasy_Integration';
			return $integrations;
		}
	
	}

	$WC_Integration_ShippingEasy = new WC_Integration_ShippingEasy(__FILE__);

	function pugs_endpoint() {
		add_rewrite_rule('^shipment/callback', 'index.php?shipment=1&callback=1', 'top');		
		include_once('class/pugs_callback.php');
		new Pugs_API_Endpoint();
	}
		
	function pugs_query_vars($vars) {
		$vars[] = 'pugs';
		$vars[] = 'shipment';
		$vars[] = 'callback';
		return $vars;
	}

	function shippingeasy_activate() {
		pugs_endpoint();
		flush_rewrite_rules();
	}
	
	function shippingeasy_deactivate() {
		flush_rewrite_rules();
	}
	
	register_activation_hook(__FILE__, 'shippingeasy_activate');
	register_deactivation_hook(__FILE__, 'shippingeasy_deactivate');
	add_action('init', 'pugs_endpoint');
	add_filter('query_vars', 'pugs_query_vars');

}

?>
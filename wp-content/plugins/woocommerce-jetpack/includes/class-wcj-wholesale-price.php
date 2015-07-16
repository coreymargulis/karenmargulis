<?php
/**
 * WooCommerce Jetpack Wholesale Price
 *
 * The WooCommerce Jetpack Wholesale Price class.
 *
 * @version 2.2.1
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Wholesale_Price' ) ) :

class WCJ_Wholesale_Price extends WCJ_Module {

	/**
	 * Constructor.
	 */
	function __construct() {

		$this->id         = 'wholesale_price';
		$this->short_desc = __( 'Wholesale Price', 'woocommerce-jetpack' );
		$this->desc       = __( 'Set WooCommerce wholesale pricing depending on product quantity in cart (buy more pay less).', 'woocommerce-jetpack' );
		parent::__construct();

		if ( $this->is_enabled() ) {
			add_filter( 'woocommerce_get_price',         array( $this, 'wholesale_price' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_get_sale_price',    array( $this, 'wholesale_price' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_get_regular_price', array( $this, 'wholesale_price' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * get_discount_percent_by_quantity.
	 */
	private function get_discount_percent_by_quantity( $quantity ) {

		$max_qty_level = 1;
		$discount_percent = 0;

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_wholesale_price_levels_number', 1 ) ); $i++ ) {

			$level_qty = get_option( 'wcj_wholesale_price_level_min_qty_' . $i, PHP_INT_MAX );
			if ( $quantity >= $level_qty && $level_qty >= $max_qty_level ) {
				$max_qty_level = $level_qty;
				$discount_percent = get_option( 'wcj_wholesale_price_level_discount_percent_' . $i, 0 );
			}
		}

		return $discount_percent;
	}

	/**
	 * get_wholesale_price.
	 */
	private function get_wholesale_price( $price, $quantity ) {
		$discount_percent = $this->get_discount_percent_by_quantity( $quantity );
		$discount_koef = 1.0 - ( $discount_percent / 100.0 );
		return $price * $discount_koef;
	}

	/**
	 * wholesale_price.
	 */
	function wholesale_price( $price, $product ) {

		if ( ! wcj_is_product_wholesale_enabled( $product->id ) ) return $price;

		// Show only on checkout and cart pages
		$is_ajax = ( is_admin() && ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ? true : false;
		if ( ! ( is_checkout() || is_cart() || $is_ajax ) ) return $price;

		// Get quanitity from cart
		$quanitities = array();
		$the_cart = WC()->cart->get_cart();
		foreach ( $the_cart as $cart_item_key => $values ) {
			if ( ! isset( $quanitities[ $values['product_id'] ] ) ) $quanitities[ $values['product_id'] ] = 0;
			$quanitities[ $values['product_id'] ] += $values['quantity'];
		}
		foreach ( $quanitities as $product_id => $product_quantity ) {
			if ( $product_id == $product->id ) {
				// Found matching product in the cart
				if ( $product_quantity > 1 ) {
					// Setting wholesale price
					$price = $this->get_wholesale_price( $price, $product_quantity );
					$precision = get_option( 'woocommerce_price_num_decimals', 2 );
					return round( $price, $precision );
				}
			}
		}
		return $price;
	}

	/**
	 * get_settings.
	 */
	function get_settings() {

		$products = apply_filters( 'wcj_get_products_filter', array() );

		$settings = array(

			array(
				'title' => __( 'Wholesale Price Levels Options', 'woocommerce-jetpack' ),
				'type'  => 'title',
				'desc'  => __( 'Wholesale Price Levels Options. If you want to display prices table on frontend, use [wcj_product_wholesale_price_table] shortcode.', 'woocommerce-jetpack' ),
				'id'    => 'wcj_wholesale_price_level_options'
			),

			array(
				'title'    	=> 'Product to include',
				'desc'    	=> __( 'Leave blank to include all products.', 'woocommerce-jetpack' ),
				'id'       	=> 'wcj_wholesale_price_products_to_include',
				'default'  	=> '',
				'type'		=> 'multiselect',
				'class'     => 'chosen_select',
				'css'       => 'width: 450px;',
				'options'   => $products,
			),

			array(
				'title'    => __( 'Levels Number', 'woocommerce-jetpack' ),
				'id'       => 'wcj_wholesale_price_levels_number',
				'default'  => 1,
				'type'     => 'custom_number',
				'desc'     => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes' => array_merge(
					is_array( apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) ) ? apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) : array(),
					array('step' => '1', 'min' => '1', ) ),
				'css'	   => 'width:100px;',
			),
		);

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_wholesale_price_levels_number', 1 ) ); $i++ ) {

			$settings[] = array(
				'title'   => __( 'Min Quantity', 'woocommerce-jetpack' ) . ' #' . $i,
				'desc'    => __( 'Min quantity to apply discount', 'woocommerce-jetpack' ),
				'id'      => 'wcj_wholesale_price_level_min_qty_' . $i,
				'default' => 0,
				'type'    => 'number',
				//'css'     => 'width:50%;min-width:300px;height:100px;',
				'custom_attributes' => array('step' => '1', 'min' => '0', ),
			);
			$settings[] = array(
				'title'   => __( 'Discount (%)', 'woocommerce-jetpack' ) . ' #' . $i,
				'desc'    => __( 'Discount (%)', 'woocommerce-jetpack' ),
				'id'      => 'wcj_wholesale_price_level_discount_percent_' . $i,
				'default' => 0,
				'type'    => 'number',
				//'css'     => 'width:50%;min-width:300px;height:100px;',
				'custom_attributes' => array('step' => '0.0001', 'min' => '0', ),
			);
		}

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'wcj_wholesale_price_level_options'
		);

		return $this->add_enable_module_setting( $settings );
	}
}

endif;

return new WCJ_Wholesale_Price();

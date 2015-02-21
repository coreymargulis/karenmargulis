<?php
/**
 * WooCommerce Jetpack Products Shortcodes
 *
 * The WooCommerce Jetpack Products Shortcodes class.
 *
 * @class    WCJ_Products_Shortcodes
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Products_Shortcodes' ) ) :

class WCJ_Products_Shortcodes extends WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->the_shortcodes = array(
			'wcj_product_image',
			'wcj_product_price',
		);

		$this->the_atts = array(
			'product_id'  => 0,
			'image_size'  => 'shop_thumbnail',
			'multiply_by' => '',
		);

		parent::__construct();
    }

    /**
     * init_atts.
     */
	function init_atts( $atts ) {

		// Atts
		$atts['product_id'] = ( 0 == $atts['product_id'] ) ? get_the_ID() : $atts['product_id'];
		if ( 0 == $atts['product_id'] ) return false;
		if ( 'product' !== get_post_type( $atts['product_id'] ) ) return false;

		// Class properties
		$this->the_product = wc_get_product( $atts['product_id'] );
		if ( ! $this->the_product ) return false;

		return $atts;
	}

    /**
     * wcj_product_price.
     */
	function wcj_product_price( $atts ) {
		return ( '' !== $atts['multiply_by'] && is_numeric( $atts['multiply_by'] ) ) ? $this->the_product->get_price() * $atts['multiply_by'] : $this->the_product->get_price();
	}
	
    /**
     * wcj_product_image.
     */
	function wcj_product_image( $atts ) {
		return $this->the_product->get_image( $atts['image_size'] );
	}
}

endif;

return new WCJ_Products_Shortcodes();


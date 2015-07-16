<?php
/**
 * WooCommerce Jetpack Shortcodes
 *
 * The WooCommerce Jetpack Shortcodes class.
 *
 * @version 2.2.1
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Shortcodes' ) ) :

class WCJ_Shortcodes {

	/**
	 * Constructor.
	 */
	public function __construct() {

		foreach( $this->the_shortcodes as $the_shortcode ) {
			add_shortcode( $the_shortcode, array( $this, 'wcj_shortcode' ) );
		}

		add_filter( 'wcj_shortcodes_list', array( $this, 'add_shortcodes_to_the_list' ) );
	}

	/**
	 * add_extra_atts.
	 */
	function add_extra_atts( $atts ) {
		$final_atts = array_merge( $this->the_atts, $atts );
		return $final_atts;
	}

	/**
	 * init_atts.
	 */
	function init_atts( $atts ) {
		return $atts;
	}

	/**
	 * add_shortcodes_to_the_list.
	 */
	function add_shortcodes_to_the_list( $shortcodes_list ) {
		foreach( $this->the_shortcodes as $the_shortcode ) {
			$shortcodes_list[] = $the_shortcode;
		}
		return $shortcodes_list;
	}

	/**
	 * wcj_shortcode.
	 */
	function wcj_shortcode( $atts, $content, $shortcode ) {

		// Init
		if ( empty( $atts ) ) $atts = array();
		$global_defaults = array(
			'before'          => '',
			'after'           => '',
			'visibility'      => '',//user_visibility
			'site_visibility' => '',
			'location'        => '',//user_location
			'wpml_language'   => '',
		);
		$atts = array_merge( $global_defaults, $atts );

		// Check if privileges are ok
		if ( 'admin' === $atts['visibility'] && ! is_super_admin() ) return '';

		// Check if site visibility is ok
		if ( '' != $atts['site_visibility'] ) {
			if (
				( 'single'  === $atts['site_visibility'] && ! is_single() ) ||
				( 'page'    === $atts['site_visibility'] && ! is_page() ) ||
				( 'archive' === $atts['site_visibility'] && ! is_archive() )
			) {
				return '';
			}

		}

		// Check if location is ok
		if ( '' != $atts['location'] && 'all' != $atts['location'] && $atts['location'] != $this->wcj_get_user_location() ) return '';

		// Check if language is ok
		if ( '' != $atts['wpml_language'] ) {
			if ( ! defined( 'ICL_LANGUAGE_CODE' ) || ICL_LANGUAGE_CODE != $atts['wpml_language'] ) return '';
		}

		// Add child class specific atts
		$atts = $this->add_extra_atts( $atts );

		// Check for required atts
		if ( false === ( $atts = $this->init_atts( $atts ) ) ) return '';

		// Run the shortcode function
		$shortcode_function = $shortcode;
		if ( '' !== ( $result = $this->$shortcode_function( $atts, $content ) ) )
			return $atts['before'] . $result . $atts['after'];
		return '';
	}

	function wcj_get_user_location() {
		$country = '';
		if ( isset( $_GET['country'] ) && '' != $_GET['country'] && is_super_admin() ) {
			$country = $_GET['country'];
		} else {
			// Get the country by IP
			$location = WC_Geolocation::geolocate_ip();
			// Base fallback
			if ( empty( $location['country'] ) ) {
				$location = wc_format_country_state_string( apply_filters( 'woocommerce_customer_default_location', get_option( 'woocommerce_default_country' ) ) );
			}
			$country = ( isset( $location['country'] ) ) ? $location['country'] : '';
		}
		return $country;
	}
}

endif;

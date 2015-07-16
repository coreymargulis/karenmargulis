<?php
/**
 * WooCommerce Jetpack General Shortcodes
 *
 * The WooCommerce Jetpack General Shortcodes class.
 *
 * @version 2.2.1
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_General_Shortcodes' ) ) :

class WCJ_General_Shortcodes extends WCJ_Shortcodes {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->the_shortcodes = array(
			'wcj_current_date',
			//'wcj_image',
			'wcj_cart_items_total_weight',
			'wcj_wpml',
			'wcj_wpml_translate',
			'wcj_country_select_drop_down_list',
		);

		$this->the_atts = array(
			'date_format' => get_option( 'date_format' ),
			/*'url'         => '',
			'class'       => '',
			'width'       => '',
			'height'      => '',*/
			'lang'        => '',
			'form_method' => 'get',
			'class'       => '',
			'style'       => '',
		);

		parent::__construct();

	}

	/**
	 * wcj_country_select_drop_down_list.
	 */
	function wcj_country_select_drop_down_list( $atts, $content ) {

		$html = '';

		$form_method  = $atts['form_method'];//get_option( 'wcj_price_by_country_country_selection_box_method', 'get' );
		$select_class = $atts['class'];//get_option( 'wcj_price_by_country_country_selection_box_class', '' );
		$select_style = $atts['style'];//get_option( 'wcj_price_by_country_country_selection_box_style', '' );

		$html .= '<form action="" method="' . $form_method . '">';

		$html .= '<select name="wcj-country" id="wcj-country" style="' . $select_style . '" class="' . $select_class . '" onchange="this.form.submit()">';
		$countries = wcj_get_countries();

		/* if ( 'get' == $form_method ) {
			$selected_country = ( isset( $_GET[ 'wcj-country' ] ) ) ? $_GET[ 'wcj-country' ] : '';
		} else {
			$selected_country = ( isset( $_POST[ 'wcj-country' ] ) ) ? $_POST[ 'wcj-country' ] : '';
		} */
		$selected_country = ( isset( $_SESSION[ 'wcj-country' ] ) ) ? $_SESSION[ 'wcj-country' ] : '';

		foreach ( $countries as $country_code => $country_name ) {

			$html .= '<option value="' . $country_code . '" ' . selected( $country_code, $selected_country, false ) . '>' . $country_name . '</option>';
		}
		$html .= '</select>';

		$html .= '</form>';

		return $html;
	}

	/**
	 * wcj_wpml.
	 */
	function wcj_wpml( $atts, $content ) {
		if ( '' == $atts['lang'] || ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE === $atts['lang'] ) ) return do_shortcode( $content );
		else return '';
	}

	/**
	 * wcj_wpml_translate.
	 */
	function wcj_wpml_translate( $atts, $content ) {
		return $this->wcj_wpml( $atts, $content );
	}

	/**
	 * wcj_cart_items_total_weight.
	 */
	function wcj_cart_items_total_weight( $atts ) {
		$the_cart = WC()->cart;
		return $the_cart->cart_contents_weight;
	}

	/**
	 * wcj_current_date.
	 */
	function wcj_current_date( $atts ) {
		return date_i18n( $atts['date_format'] );
	}

	/**
	 * wcj_image.
	 */
	/*function wcj_image( $atts ) {
		return '<img src="' . $atts['url'] . '" class="' . $atts['class'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '">';
	}*/
}

endif;

return new WCJ_General_Shortcodes();

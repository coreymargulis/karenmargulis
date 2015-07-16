<?php
/**
 * WooCommerce Jetpack Products Shortcodes
 *
 * The WooCommerce Jetpack Products Shortcodes class.
 *
 * @version  2.2.0
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
			'wcj_product_wholesale_price_table',
			'wcj_product_sku',
			'wcj_product_title',
			'wcj_product_weight',
			'wcj_product_excerpt',
			'wcj_product_custom_field',
			'wcj_product_you_save',
			'wcj_product_you_save_percent',
			'wcj_product_tags',
			'wcj_product_purchase_price',
		);

		$this->the_atts = array(
			'product_id'     => 0,
			'image_size'     => 'shop_thumbnail',
			'multiply_by'    => '',
			'hide_currency'  => 'no',
			'excerpt_length' => 0,
			'name'           => '',
			'heading_format' => 'from %level_qty% pcs.',
			'sep'            => ', ',
			'add_links'      => 'yes',
		);

		parent::__construct();
    }

    /**
     * Inits shortcode atts and properties.
	 *
	 * @param array $atts Shortcode atts.
	 *
	 * @return array The (modified) shortcode atts.
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
	 * wcj_product_purchase_price.
	 *
	 * @return string
	 */
	function wcj_product_purchase_price( $atts ) {
		$purchase_price = wc_get_product_purchase_price( $the_product->id );
		return wc_price( $purchase_price ); 
		//return get_post_meta( $the_product->id, '_' . 'wcj_purchase_price', true );
	}

	/**
	 * wcj_product_tags.
	 *
	 * @return string
	 */
	function wcj_product_tags( $atts ) {

		if ( 'yes' === $atts['add_links'] ) {
			return $this->the_product->get_tags( $atts['sep'] );
		}

		$product_tags = get_the_terms( $atts['product_id'], 'product_tag' );
		$product_tags_names = array();
		foreach ( $product_tags as $product_tag ) {
			$product_tags_names[] = $product_tag->name;
		}
		return implode( $atts['sep'], $product_tags_names );
	}

	/**
	 * wcj_product_you_save.
	 *
	 * @return string
	 */
	function wcj_product_you_save( $atts ) {

		if ( $this->the_product->is_type( 'variable' ) ) {
			$you_save = ( $this->the_product->get_variation_regular_price( 'max' ) - $this->the_product->get_variation_sale_price( 'max' ) );
		} else {
			$you_save = ( $this->the_product->get_regular_price() - $this->the_product->get_sale_price() );
		}
		return wc_price( $you_save );
	}

	/**
	 * wcj_product_you_save_percent.
	 *
	 * @return string
	 */
	function wcj_product_you_save_percent( $atts ) {

		if ( $this->the_product->is_type( 'variable' ) ) {
			$you_save      = ( $this->the_product->get_variation_regular_price( 'max' ) - $this->the_product->get_variation_sale_price( 'max' ) );
			$regular_price = $this->the_product->get_variation_regular_price( 'max' );
		} else {
			$you_save      = ( $this->the_product->get_regular_price() - $this->the_product->get_sale_price() );
			$regular_price = $this->the_product->get_regular_price();
		}

		return ( 0 != $regular_price ) ? intval( $you_save / $regular_price * 100 ) : '';
	}

	/**
	 * Get product custom field.
	 *
	 * @return string
	 */
	function wcj_product_custom_field( $atts ) {
		$product_custom_fields = get_post_custom( $atts['product_id'] );
		return ( isset( $product_custom_fields[ $atts['name'] ][0] ) ) ? $product_custom_fields[ $atts['name'] ][0] : '';
		//return get_post_meta( $atts['product_id'], $atts['name'], true );
	}

    /**
     * Returns product (modified) price.
	 *
	 * @todo Variable products: a)not range, and b)price by country.
	 *
	 * @return string The product (modified) price
     */
	function wcj_product_price( $atts ) {
		// Variable
		if ( $this->the_product->is_type( 'variable' ) ) {
			$min = $this->the_product->get_variation_price( 'min', false );
			$max = $this->the_product->get_variation_price( 'max', false );
			if ( '' !== $atts['multiply_by'] && is_numeric( $atts['multiply_by'] ) ) {
				$min = $min * $atts['multiply_by'];
				$max = $max * $atts['multiply_by'];
			}
			if ( 'yes' !== $atts['hide_currency'] ) {
				$min = wc_price( $min );
				$max = wc_price( $max );
			}
			return sprintf( '%s-%s', $min, $max );
		}
		// Simple etc.
		else {
			$the_price = $this->the_product->get_price();
			if ( '' !== $atts['multiply_by'] && is_numeric( $atts['multiply_by'] ) ) $the_price = $the_price * $atts['multiply_by'];
			return ( 'yes' === $atts['hide_currency'] ) ? $the_price : wc_price( $the_price );
		}
	}

    /**
	 * wcj_product_wholesale_price_table.
     */
	function wcj_product_wholesale_price_table( $atts ) {

		if ( ! wcj_is_product_wholesale_enabled( $this->the_product->id ) ) return '';

		$wholesale_price_levels = array();

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_wholesale_price_levels_number', 1 ) ); $i++ ) {
			$level_qty        = get_option( 'wcj_wholesale_price_level_min_qty_' . $i, PHP_INT_MAX );
			$discount_percent = get_option( 'wcj_wholesale_price_level_discount_percent_' . $i, 0 );			
			$discount_koef = 1.0 - ( $discount_percent / 100.0 );
			$wholesale_price_levels[] = array( 'quantity' => $level_qty, 'koef' => $discount_koef, );
		}
		
		$data_qty = array();
		$data_price = array();
		
		foreach ( $wholesale_price_levels as $wholesale_price_level ) {

			$the_price = '';

			// Variable
			if ( $this->the_product->is_type( 'variable' ) ) {
				$min = $this->the_product->get_variation_price( 'min', false );
				$max = $this->the_product->get_variation_price( 'max', false );
				if ( '' !== $wholesale_price_level['koef'] && is_numeric( $wholesale_price_level['koef'] ) ) {
					$min = $min * $wholesale_price_level['koef'];
					$max = $max * $wholesale_price_level['koef'];
				}
				if ( 'yes' !== $atts['hide_currency'] ) {
					$min = wc_price( $min );
					$max = wc_price( $max );
				}
				$the_price = sprintf( '%s-%s', $min, $max );
			}
			// Simple etc.
			else {
				//$the_price = wc_price( round( $this->the_product->get_price() * $wholesale_price_level['koef'], $precision ) );

				$the_price = $this->the_product->get_price();
				if ( '' !== $wholesale_price_level['koef'] && is_numeric( $wholesale_price_level['koef'] ) ) {
					$the_price = $the_price * $wholesale_price_level['koef'];
				}
				if ( 'yes' !== $atts['hide_currency'] ) {
					$the_price = wc_price( $the_price );
				}

			}

			$data_qty[] = str_replace( '%level_qty%', $wholesale_price_level['quantity'], $atts['heading_format'] ) ;
			$data_price[] = $the_price;
		}

		return wcj_get_table_html(
			array( $data_qty, $data_price ),
			array( 'columns_styles' => array( 'text-align: center;', 'text-align: center;', 'text-align: center;', ), )
		);
	}

	/**
	 * For wcj_product_excerpt function.
	 */
	/* private */ function custom_excerpt_length( $length ) {
		global $product_excerpt_length;
		return $product_excerpt_length;
	}
	/**
	 * Get product excerpt.
	 *
	 * @return string
	 */
	function wcj_product_excerpt( $atts ) {
		global $post;
		global $product_excerpt_length;
		$post = get_post( $atts['product_id'] );
		setup_postdata( $post );

		$product_excerpt_length = $atts['excerpt_length'];
		if ( 0 != $atts['excerpt_length'] )    add_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ), PHP_INT_MAX );
		$the_excerpt = get_the_excerpt();
		if ( 0 != $atts['excerpt_length'] ) remove_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ), PHP_INT_MAX );

		wp_reset_postdata();
		//wcj_log( $the_excerpt );
		return $the_excerpt;

	}

	/**
	 * Get SKU (Stock-keeping unit) - product unique ID.
	 *
	 * @return string
	 */
	function wcj_product_sku( $atts ) {
		return $this->the_product->get_sku();
	}

	/**
	 * Get the title of the product.
	 *
	 * @return string
	 */
	function wcj_product_title( $atts ) {
		return $this->the_product->get_title();
	}

	/**
	 * Get the product's weight.
	 *
	 * @return string
	 */
	function wcj_product_weight( $atts ) {
		return $this->the_product->get_weight();
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

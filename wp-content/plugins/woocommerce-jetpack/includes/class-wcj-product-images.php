<?php
/**
 * WooCommerce Jetpack Product Images
 *
 * The WooCommerce Jetpack Product Images class.
 *
 * @version 2.2.0
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Product_Images' ) ) :

class WCJ_Product_Images extends WCJ_Module {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id         = 'product_images';
		$this->short_desc = __( 'Product Images', 'woocommerce-jetpack' );
		$this->desc       = __( 'Customize WooCommerce products images, thumbnails and sale flashes.', 'woocommerce-jetpack' );
		parent::__construct();

		if ( $this->is_enabled() ) {

			// Product Image & Thumbnails
			if ( 'yes' === get_option( 'wcj_product_images_and_thumbnails_enabled', 'no' ) ) {

				if ( 'yes' === get_option( 'wcj_product_images_and_thumbnails_hide_on_single', 'no' ) ) {
					add_action( 'init', array( $this, 'product_images_and_thumbnails_hide_on_single' ), PHP_INT_MAX );
				} else {
					add_filter( 'woocommerce_single_product_image_html',           array( $this, 'customize_single_product_image_html' ) );
					add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'customize_single_product_image_thumbnail_html' ) );
				}
				if ( 'yes' === get_option( 'wcj_product_images_hide_on_archive', 'no' ) ) {
					add_action( 'init', array( $this, 'product_images_hide_on_archive' ), PHP_INT_MAX );
				}

				// Single Product Thumbnails Columns Number
				add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'change_product_thumbnails_columns_number' ) );
			}

			// Sale flash
			if ( 'yes' === get_option( 'wcj_product_images_sale_flash_enabled', 'no' ) ) {
				add_filter( 'woocommerce_sale_flash', array( $this, 'customize_sale_flash' ), PHP_INT_MAX, 3 );
			}
		}
	}

	/**
	 * product_images_and_thumbnails_hide_on_single.
	 */
	public function product_images_and_thumbnails_hide_on_single() {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	}

	/**
	 * product_images_hide_on_archive.
	 */
	public function product_images_hide_on_archive() {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	}

	/**
	 * customize_sale_flash.
	 */
	public function customize_sale_flash( $sale_flash_html, $post, $product ) {

		// Hiding
		if ( 'yes' === get_option( 'wcj_product_images_sale_flash_hide_on_archives', 'no' ) && is_archive() ) return '';
		if ( 'yes' === get_option( 'wcj_product_images_sale_flash_hide_on_single', 'no' )   && is_single() && get_the_ID() === $product->id ) return '';

		// Content
		return do_shortcode(
			get_option( 'wcj_product_images_sale_flash_html' ,
			'<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>' )
		);
	}

	/**
	 * customize_single_product_image_html.
	 */
	public function customize_single_product_image_html( $image_link ) {
		return ( 'yes' === get_option( 'wcj_product_images_hide_on_single', 'no' ) ) ? '' : $image_link;
	}

	/**
	 * customize_single_product_image_thumbnail_html.
	 */
	public function customize_single_product_image_thumbnail_html( $image_link ) {
		return ( 'yes' === get_option( 'wcj_product_images_thumbnails_hide_on_single', 'no' ) ) ? '' : $image_link;
	}

	/**
	 * change_product_thumbnails_columns.
	 */
	public function change_product_thumbnails_columns_number( $columns_number ) {
		return get_option( 'wcj_product_images_thumbnails_columns', 3 );
	}

	/**
	 * get_settings.
	 */
	function get_settings() {

		$settings = array(

			array( 'title' => __( 'Product Image and Thumbnails', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_images_and_thumbnails_options' ),

			array(
				'title'   => __( 'Enable Section', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_and_thumbnails_enabled',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Image and Thumbnails on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_and_thumbnails_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Image on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Thumbnails on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_thumbnails_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Image on Archives', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_hide_on_archive',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Single Product Thumbnails Columns', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_thumbnails_columns',
				'default' => 3,
				'type'    => 'number',
			),

			array( 'type' => 'sectionend', 'id' => 'wcj_product_images_and_thumbnails_options' ),

			array( 'title' => __( 'Product Images Sale Flash', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_images_sale_flash_options' ),

			array(
				'title'   => __( 'Enable Section', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_enabled',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'HTML', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_html',
				'default' => '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>',
				'type'    => 'textarea',
				'css'     => 'width:300px;height:100px;',
			),

			array(
				'title'   => __( 'Hide on Archives (Categories)', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_hide_on_archives',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Hide on Single', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array( 'type' => 'sectionend', 'id' => 'wcj_product_images_sale_flash_options' ),
		);

		return $this->add_enable_module_setting( $settings );
	}
}

endif;

return new WCJ_Product_Images();

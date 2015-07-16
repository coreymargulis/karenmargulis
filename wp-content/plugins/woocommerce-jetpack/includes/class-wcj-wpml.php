<?php
/**
 * WooCommerce Jetpack WPML
 *
 * The WooCommerce Jetpack WPML class.
 *
 * @version 2.2.0
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_WPML' ) ) :

class WCJ_WPML extends WCJ_Module {

    /**
     * Constructor.
     */
    function __construct() {

		$this->id         = 'wpml';
		$this->short_desc = __( 'WPML', 'woocommerce-jetpack' );
		$this->desc       = __( 'Basic WPML support for WooCommerce Jetpack.', 'woocommerce-jetpack' );
		parent::__construct();

        if ( $this->is_enabled() ) {
			add_action( 'woojetpack_after_settings_save', array( $this, 'create_wpml_xml_file' ) );
        }
    }

    /**
     * get_settings.
     */
    function get_settings() {
        $settings = array();
        return $this->add_enable_module_setting( $settings );
    }

    /**
     * create_wpml_xml_file.
     */
    function create_wpml_xml_file( $sections ) {

		if ( false !== ( $handle = fopen( wcj_plugin_path() . '/wpml-config.xml', 'w' ) ) ) {

			fwrite( $handle, '<wpml-config>' . PHP_EOL );
			fwrite( $handle, "\t" );
			fwrite( $handle, '<admin-texts>' . PHP_EOL );

			//$sections = $this->get_sections();
			foreach ( $sections as $section => $section_title ) {

				//$settings = $this->get_settings( $section );
				$settings = apply_filters( 'wcj_settings_' . $section, array() );

				foreach ( $settings as $value ) {
					if ( $this->is_wpml_value( $section, $value ) ) {
						fwrite( $handle, "\t\t" );
						fwrite( $handle, '<key name="' . $value['id'] . '" />' . PHP_EOL );
					}
				}
			}

			fwrite( $handle, "\t" );
			fwrite( $handle, '</admin-texts>' . PHP_EOL );
			fwrite( $handle, '</wpml-config>' . PHP_EOL );

			fclose( $handle );
		}

	}

	function is_wpml_value( $section, $value ) {

		$is_type_ok = ( 'textarea' === $value['type'] || 'text' === $value['type'] ) ? true : false;

		$sections_with_wpml = array(
			'call_for_price',
			'price_labels',
			'add_to_cart',
			'more_button_labels',

			'product_info',
			'product_tabs',
			'sorting',
			'product_input_fields',

			'cart',
			'mini_cart',
			'checkout_core_fields',
			'checkout_custom_fields',
			'checkout_custom_info',

			'orders',

			'pdf_invoicing_templates',
			'pdf_invoicing_header',
			'pdf_invoicing_footer',
			'pdf_invoicing_display',

			'pdf_invoices',
		);
		$is_section_ok = ( in_array( $section, $sections_with_wpml ) ) ? true : false;

		$values_to_skip = array(
			'wcj_product_info_products_to_exclude',

			'wcj_custom_product_tabs_title_global_hide_in_product_ids_',
			'wcj_custom_product_tabs_title_global_hide_in_cats_ids_',
			'wcj_custom_product_tabs_title_global_show_in_product_ids_',
			'wcj_custom_product_tabs_title_global_show_in_cats_ids_',

			'wcj_empty_cart_div_style',
		);
		//$is_id_ok = ( in_array( $value['id'], $values_to_skip ) ) ? false : true;
		$is_id_ok = true;
		foreach ( $values_to_skip as $value_to_skip ) {
			if ( false !== strpos( $value['id'], $value_to_skip ) ) {
				$is_id_ok = false;
				break;
			}
		}

		return ( $is_type_ok && $is_section_ok && $is_id_ok );
	}

}

endif;

return new WCJ_WPML();

<?php
/**
 * WooCommerce Jetpack Module
 *
 * The WooCommerce Jetpack Module class.
 *
 * @version 2.2.0
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Module' ) ) :

class WCJ_Module {

	public $id;
	public $short_desc;
	public $desc;
	public $parent_id; // for `submodule` only
	public $type;      // `module` or `submodule`

	/**
	 * Constructor.
	 */
	public function __construct( $type = 'module' ) {
		// Settings hooks
		add_filter( 'wcj_settings_sections',     array( $this, 'settings_section' ) );
		add_filter( 'wcj_settings_' . $this->id, array( $this, 'get_settings' ), 100 );
		$this->type = $type;
		if ( 'module' === $this->type ) {
			$this->parent_id = '';
			add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
		}
	}

	/**
	 * is_enabled.
	 */
	public function is_enabled() {
		$the_id = ( 'module' === $this->type ) ? $this->id : $this->parent_id;
		return ( 'yes' === get_option( 'wcj_' . $the_id . '_enabled' ) ) ? true : false;
	}

	/**
	 * add_enabled_option.
	 * only for `module`
	 */
	public function add_enabled_option( $settings ) {
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		return $settings;
	}

	/**
	 * settings_section.
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->short_desc;
		return $sections;
	}
	/**
	 * settings_section.
	 * only for `module`
	 */
	function add_enable_module_setting( $settings ) {
		$enable_module_setting = array(
			array(
				'title' => $this->short_desc . ' ' . __( 'Options', 'woocommerce-jetpack' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'wcj_' . $this->id . '_options',
			),
			array(
				'title'    => $this->short_desc,
				'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
				'desc_tip' => $this->desc,
				'id'       => 'wcj_' . $this->id . '_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wcj_' . $this->id . '_options',
			),
		);
		return array_merge( $enable_module_setting, $settings );
	}
}

endif;

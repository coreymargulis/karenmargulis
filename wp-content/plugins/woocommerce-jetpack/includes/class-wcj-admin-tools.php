<?php
/**
 * WooCommerce Jetpack Admin Tools
 *
 * The WooCommerce Jetpack Admin Tools class.
 *
 * @version 2.2.1
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Admin_Tools' ) ) :

class WCJ_Admin_Tools {

    /**
     * Constructor.
     */
    public function __construct() {

        // Main hooks
        if ( 'yes' === get_option( 'wcj_admin_tools_enabled' ) ) {
			add_filter( 'wcj_tools_tabs',             array( $this, 'add_tool_tab' ), 100 );
			add_action( 'wcj_tools_' . 'admin_tools', array( $this, 'create_tool' ), 100 );
        }

        // Settings hooks
        add_filter( 'wcj_settings_sections',    array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_admin_tools', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status',      array( $this, 'add_enabled_option' ), 100 );
		add_action( 'wcj_tools_dashboard',      array( $this, 'add_tool_info_to_tools_dashboard' ), 100 );
    }

	/**
	 * add_tool_info_to_tools_dashboard.
	 */
	public function add_tool_info_to_tools_dashboard() {
		echo '<tr>';
		if ( 'yes' === get_option( 'wcj_admin_tools_enabled') )
			$is_enabled = '<span style="color:green;font-style:italic;">' . __( 'enabled', 'woocommerce-jetpack' ) . '</span>';
		else
			$is_enabled = '<span style="color:gray;font-style:italic;">' . __( 'disabled', 'woocommerce-jetpack' ) . '</span>';
		echo '<td>' . __( 'Admin Tools', 'woocommerce-jetpack' ) . '</td>';
		echo '<td>' . $is_enabled . '</td>';
		echo '<td>' . __( 'Log.', 'woocommerce-jetpack' ) . '</td>';
		echo '</tr>';
	}

	/**
	 * add_tool_tab.
	 */
	public function add_tool_tab( $tabs ) {
		$tabs[] = array(
			'id'		=> 'admin_tools',
			'title'		=> __( 'Admin Tools', 'woocommerce-jetpack' ),
		);
		return $tabs;
	}

    /**
     * create_tool.
     */
	public function create_tool() {

		$the_notice = '';
		if ( isset( $_GET['wcj_delete_log'] ) && is_super_admin() ) {
			update_option( 'wcj_log', '' );
			$the_notice .= __( 'Log deleted successfully.', 'woocommerce-jetpack' );
		}

		$the_tools = '';
		$the_tools .= '<a href="' . add_query_arg( 'wcj_delete_log', '1' ) . '">' . __( 'Delete Log', 'woocommerce-jetpack' ) . '</a>';

		$the_log = '';
		//if ( isset( $_GET['wcj_view_log'] ) ) {
			$the_log .= '<pre>' . get_option( 'wcj_log', '' ) . '</pre>';
		//}

		echo '<p>' . $the_tools . '</p>';

		echo '<p>' . $the_notice . '</p>';

		echo '<p>' . $the_log . '</p>';

	}

    /**
     * add_enabled_option.
     */
    public function add_enabled_option( $settings ) {
        $all_settings = $this->get_settings();
        $settings[] = $all_settings[1];
        return $settings;
    }

    /**
     * get_settings.
     */
    function get_settings() {

        $settings = array(

            array( 'title' => __( 'Admin Tools Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_admin_tools_options' ),

            array(
                'title'    => __( 'Admin Tools', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Debug and log tools for WooCommerce Jetpack.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_admin_tools_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

            array(
                'title'    => __( 'Logging', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                'id'       => 'wcj_logging_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

			array(
                'title'    => __( 'Debug', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                'id'       => 'wcj_debuging_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

			/*array(
                'title'    => __( 'Custom Shortcode', 'woocommerce-jetpack' ),
                'id'       => 'wcj_custom_shortcode_1',
                'default'  => '',
                'type'     => 'textarea',
            ),*/

            array( 'type'  => 'sectionend', 'id' => 'wcj_admin_tools_options' ),
        );

        return $settings;
    }

    /**
     * settings_section.
     */
    function settings_section( $sections ) {
        $sections['admin_tools'] = __( 'Admin Tools', 'woocommerce-jetpack' );
        return $sections;
    }
}

endif;

return new WCJ_Admin_Tools();

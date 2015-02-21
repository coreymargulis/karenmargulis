<?php
/**
 * WooCommerce Jetpack Country by IP
 *
 * The WooCommerce Jetpack Country by IP class.
 *
 * @class    WCJ_Country_By_IP
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Country_By_IP' ) ) :

class WCJ_Country_By_IP {

    /**
     * Constructor.
     */
    public function __construct() {

		// Debug
		add_action( 'woocommerce_after_add_to_cart_button',	array( $this, 'add_debug_info' ),  PHP_INT_MAX, 0 );
		add_action( 'admin_init',							array( $this, 'reinstall_ip_db' ), PHP_INT_MAX, 0 );

		add_action( 'init', 								array( $this, 'init_hooks' ) );

		$this->current_db_file_version = 6;
		$this->customer_country = null;
    }


	/**
	 * reinstall_ip_db.
	 */
	function reinstall_ip_db() {
		//global $current_db_file_version;
		if ( isset( $_GET['wcj-install-ip-db'] ) && '1' == $_GET['wcj-install-ip-db'] ) {
			$this->update_database( $this->current_db_file_version );
		}
	}

	/**
	 * add_debug_info.
	 */
	function add_debug_info() {
		if ( isset( $_GET['wcj-debug'] ) ) {
			//global $current_db_file_version;
			echo '<input type="hidden" name="wcj-get-ip" value="'                      . wcj_get_the_ip() . '" />';
			echo '<input type="hidden" name="wcj-get-country" value="'                 . $this->get_user_country_by_ip_internal() . '" />';
			echo '<input type="hidden" name="wcj-get-country-external" value="'        . $this->get_user_country_by_ip_external() . '" />';
			echo '<input type="hidden" name="wcj-get-country-db-cur-ver" value="'      . $this->current_db_file_version . '" />';
			echo '<input type="hidden" name="wcj-get-country-db-ver" value="'          . get_option( 'wcj_geoipcountry_db_version', 0 ) . '" />';
		}
	}

    /**
     * init_hooks.
     */
	function init_hooks() {
		add_filter( 'wcj_get_ip_db_status_html', array( $this, 'get_ip_db_status_html' ) );
	}

    /**
     * get_ip_db_status_html.
     */
    function get_ip_db_status_html() {

		$installed_db_version = get_option( 'wcj_geoipcountry_db_version', 0 );

		if ( WCJ()->country_by_ip->current_db_file_version != $installed_db_version ) {
			if ( $installed_db_version < 0 )
				$installed_db_version = abs( $installed_db_version ) + 10000;
			return __( 'IP DB not installed', 'woocommerce-jetpack' ) . ' (' . $installed_db_version . ').'
				   . ' ' . '<a href="' . add_query_arg( 'wcj-install-ip-db', '1' ) . '">' . __( 'Fix', 'woocommerce-jetpack' ) . '</a>';
		}
		else
			return __( 'IP DB version: ', 'woocommerce-jetpack' ) . WCJ()->country_by_ip->current_db_file_version;
    }

	/**
	 * convert_ip_to_int.
	 */
	function convert_ip_to_int( $ip ) {
		// Convert IP to int
		$calc = array( 16777216, 65536, 256, 1 );
		$i = 0;
		$result = 0;
		$token = strtok( $ip , '.' );
		while ( $token !== false ) {
			$result += $calc[ $i++ ] * $token;
			$token = strtok( '.' );
		}
		return $result;
	}

	/**
	 * parse_csv_line.
	 */
	function parse_csv_line( $line ) {
		return explode( ',', trim( $line ) );
	}

	/**
	 * check_and_update_database.
	 */
	function update_database( $current_db_file_version ) {
		// Get IPs from file
		// This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com">http://www.maxmind.com</a>.

		// Started
		update_option( 'wcj_geoipcountry_db_version', -1 );

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'woojetpack_country_ip';

		$wpdb->query( "DROP TABLE $table_name;" );

		$sql = "CREATE TABLE $table_name (
			ip_from BIGINT NOT NULL,
			ip_to BIGINT NOT NULL,
			country_code VARCHAR(2) NOT NULL,
			UNIQUE KEY ip_from (ip_from)
		) $charset_collate;";
		$wpdb->query( $sql );

		$the_values = '';
		$max_insert_size = 10000;
		$the_offset = 0;
		$the_predifined_size = 104402;

		//$the_array = file( plugin_dir_path( __FILE__ ) . 'lib/ipdb.csv' );
		$the_array = file( WCJ()->plugin_path() . '/includes/lib/ipdb.csv' );

		// Adding data to table
		while ( $the_offset < $the_predifined_size ) {
			$the_array_slice = array_slice( $the_array, $the_offset, $max_insert_size, true );
			$csv = array_map( array( $this, 'parse_csv_line' ), $the_array_slice );
			if ( ! empty ( $csv ) ) {
				foreach ( $csv as $key => $data ) {
					$the_values .=  "('$data[0]', '$data[1]', '$data[2]'), ";
					//$the_values .= "('" . implode( "', '", $data ) . "'), ";
				}
				$the_values = rtrim( $the_values, ', ' );
				$wpdb->query( "INSERT INTO $table_name (`ip_from`, `ip_to`, `country_code`) VALUES $the_values;" );
				$the_values = '';
			}
			$the_offset += $max_insert_size;
		}

		// Checking if OK
		$count_db_table = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name;" );
		if ( $the_predifined_size != $count_db_table ) {
			// Something went wrong
			update_option( 'wcj_geoipcountry_db_version', -2 );
		}
		else {
			// Finished
			update_option( 'wcj_geoipcountry_db_version', $current_db_file_version );
		}

		// Depreciated - cleaning
		update_option( 'wcj_geoipcountry_db_from', array() );
		update_option( 'wcj_geoipcountry_db_to', array() );
		update_option( 'wcj_geoipcountry_db_country', array() );
	}

	/**
	 * get_country_by_ip_mysql.
	 */
	function get_country_by_ip_mysql( $ip ) {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT `country_code` FROM `{$wpdb->prefix}woojetpack_country_ip` WHERE `ip_from` <= $ip AND `ip_to` >= $ip", OBJECT );
		if ( 1 !== count( $results ) || ! isset( $results[0]->country_code ) ) {
			return get_option( 'woocommerce_default_country', 'GB' );
			//return null;
		}
		return $results[0]->country_code;
	}

	/**
	 * check_and_update_database.
	 */
	function check_and_update_database() {
		//global $current_db_file_version;
		$current_version = get_option( 'wcj_geoipcountry_db_version', 0 );
		if ( $current_version < 0 )
			return;
		if ( $current_version != $this->current_db_file_version ) {
			$this->update_database( $this->current_db_file_version );
		}
	}

	/**
	 * get_user_country_by_ip.
	 */
	function get_user_country_by_ip( $method ) {

//return 'LT';
		// We already know the country - nothing to calculate - return country
//		if ( null != $this->customer_country )
//			return $this->customer_country;
			
		// Debug
		//if ( is_super_admin() && isset( $_GET['wcj-debug-country'] ) && '' != isset( $_GET['wcj-debug-country'] ) )



		//if ( is_super_admin() && isset( $_GET['wcj-debug-country'] ) && '' != $_GET['wcj-debug-country'] ) {
		if ( isset( $_GET['country'] ) && '' != $_GET['country'] && is_super_admin() ) {
		
			//wcj_log( 'wcj-debug-country' );
			return $_GET['country'];
			
		}
			
		// WooCommerce's geolocate_ip()
		if ( 'internal_wc' === $method ) {
			// WooCommerce's geolocate_ip()
			/**/
			//include_once( 'includes/class-wc-geolocation.php' );
			$location = WC_Geolocation::geolocate_ip();
			// Base fallback
			if ( empty( $location['country'] ) ) {
				$location = wc_format_country_state_string( apply_filters( 'woocommerce_customer_default_location', get_option( 'woocommerce_default_country' ) ) );
			}
			//wcj_log( $location );			
			return ( isset( $location['country'] ) ) ? $location['country'] : null; 	
			/**/		
			//wcj_log( $location['country'] );			
			//return wc_get_customer_default_location();
		}		
		
		// External method
		if ( 'external' === $method ) {
			return $this->get_user_country_by_ip_external();
		}

		// Default - internal method
		return $this->get_user_country_by_ip_internal();
	}
	
	/**
	 * get_user_country_by_ip_internal.
	 */
	private function get_user_country_by_ip_internal() {



		// Default value
		//$this->customer_country = WC()->countries->get_base_country();

		// Get user IP
		$customer_ip = wcj_get_the_ip();

		// Convert IP to int
		$ip_as_int = $this->convert_ip_to_int( $customer_ip );

		// Update DB if needed
		$this->check_and_update_database();

		// Search for IP in DB
		return $this->get_country_by_ip_mysql( $ip_as_int );
	}

	/**
	 * get_user_country_by_ip_external.
	 */
	private function get_user_country_by_ip_external() {
		
		ob_start();
		$max_execution_time = ini_get( 'max_execution_time' );
		set_time_limit( 2 );
		
		$country = file_get_contents( 'http://api.hostip.info/country.php?ip=' . wcj_get_the_ip() );
		//$json = file_get_contents( 'http://api.hostip.info/country.php?ip=' . $this->get_the_ip() );//file_get_contents( 'http://ipinfo.io/' . $this->get_the_ip() . '/country' );
		//$country = json_decode( $json );		

		set_time_limit( $max_execution_time );
		ob_end_clean();		
		
		return ( '' != $country ) ? $country : null;
	}
}

endif;

return new WCJ_Country_By_IP();

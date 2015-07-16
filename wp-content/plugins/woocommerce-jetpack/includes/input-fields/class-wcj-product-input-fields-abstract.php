<?php
/**
 * Abstract WooCommerce Jetpack Product Input Fields
 *
 * The WooCommerce Jetpack Product Input Fields abstract class.
 *
 * @class     WCJ_Product_Input_Fields_Abstract
 * @version   2.2.0
 * @category  Class
 * @author    Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Product_Input_Fields_Abstract' ) ) :

class WCJ_Product_Input_Fields_Abstract {

	/** @var string scope. */
	public $scope = '';

    /**
     * Constructor.
     */
    public function __construct() {

    }

    /**
     * get_options.
     */
	public function get_options() {
		$options = array(
			array(
				'id'				=> 'wcj_product_input_fields_enabled_' . $this->scope . '_',
				'title'				=> __( 'Enabled', 'woocommerce-jetpack' ),
				'type'				=> 'checkbox',
				'default'			=> 'no',
			),
			array(
				'id'				=> 'wcj_product_input_fields_type_' . $this->scope . '_',
				'title'				=> __( 'Type', 'woocommerce-jetpack' ),
				'type'				=> 'select',
				'default'			=> 'text',
				'options'           => array(
										'text'     => __( 'Text', 'woocommerce-jetpack' ),
										'textarea' => __( 'Textarea', 'woocommerce-jetpack' ),
										'number'   => __( 'Number', 'woocommerce-jetpack' ),
										'checkbox' => __( 'Checkbox', 'woocommerce-jetpack' ),
				                    ),
			),

			/* array(
				'id'				=> 'wcj_product_input_fields_type_checkbox_' . $this->scope . '_',
				'title'				=> __( 'If checkbox is selected, set possible pairs here.', 'woocommerce-jetpack' ),
				'type'				=> 'select',
				'default'			=> 'yes_no',
				'options'           => array(
										'yes_no' => __( 'Yes / No', 'woocommerce-jetpack' ),
										'on_off' => __( 'On / Off', 'woocommerce-jetpack' ),
				                    ),
			), */
			array(
				'id'				=> 'wcj_product_input_fields_type_checkbox_yes_' . $this->scope . '_',
				'title'				=> __( 'If checkbox is selected, set value for ON here', 'woocommerce-jetpack' ),
				'short_title'		=> __( 'Checkbox: ON', 'woocommerce-jetpack' ),
				'type'				=> 'text',
				'default'			=> __( 'Yes', 'woocommerce-jetpack' ),
			),
			array(
				'id'				=> 'wcj_product_input_fields_type_checkbox_no_' . $this->scope . '_',
				'title'				=> __( 'If checkbox is selected, set value for OFF here', 'woocommerce-jetpack' ),
				'short_title'		=> __( 'Checkbox: OFF', 'woocommerce-jetpack' ),
				'type'				=> 'text',
				'default'			=> __( 'No', 'woocommerce-jetpack' ),
			),

			array(
				'id'				=> 'wcj_product_input_fields_required_' . $this->scope . '_',
				'title'				=> __( 'Required', 'woocommerce-jetpack' ),
				'type'				=> 'checkbox',
				'default'			=> 'no',
			),
			array(
				'id'				=> 'wcj_product_input_fields_title_' . $this->scope . '_',
				'title'				=> __( 'Title', 'woocommerce-jetpack' ),
				'type'				=> 'textarea',
				'default'			=> '',
			),
			array(
				'id'				=> 'wcj_product_input_fields_placeholder_' . $this->scope . '_',
				'title'				=> __( 'Placeholder', 'woocommerce-jetpack' ),
				'type'				=> 'textarea',
				'default'			=> '',
			),
			array(
				'id'				=> 'wcj_product_input_fields_required_message_' . $this->scope . '_',
				'title'				=> __( 'Message on required', 'woocommerce-jetpack' ),
				'type'				=> 'textarea',
				'default'			=> '',
			),
		);
		return $options;
	}

	/**
	 * hide_custom_input_fields_default_output_in_admin_order.
	 * @todo Get actual (max) number of fields in case of local scape.
	 */
	function hide_custom_input_fields_default_output_in_admin_order( $hidden_metas ) {
		$total_number = 0;
		if ( 'global' === $this->scope ) {
			$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', 0, 1 ) );
		} else {
			$max_number_of_fields_for_local = 100;
			$total_number = $max_number_of_fields_for_local; // TODO: not the best solution!
		}

		for ( $i = 1; $i <= $total_number; $i++ ) {
			$hidden_metas[] = '_' . 'wcj_product_input_fields_' . $this->scope . '_' . $i;
		}
		return $hidden_metas;
	}

	/**
	 * output_custom_input_fields_in_admin_order.
	 */
	function output_custom_input_fields_in_admin_order( $item_id, $item, $_product ) {
		echo '<table cellspacing="0" class="display_meta">';
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $_product->id, 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {

			$the_nice_name = $this->get_value( 'wcj_product_input_fields_title_' . $this->scope . '_' . $i, $_product->id, '' );
			if ( '' == $the_nice_name ) $the_nice_name = __( 'Product Input Field', 'woocommerce-jetpack' ) . ' (' . $this->scope . ') #' . $i;

			$the_value = isset( $item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] ) ? $item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] : '';

			if ( '' != $the_value ) {
				echo '<tr><th>' . $the_nice_name . ':</th><td>' . $the_value . '</td></tr>';
			}
		}
		echo '</table>';
	}

	/**
	 * starts_with.
	 *
	function starts_with( $haystack, $needle ) {
		// search backwards starting from haystack length characters from the end
		//return ( '' === $needle ) || ( strpos( $haystack, $needle, strlen( $haystack ) ) !== false );
		return $needle === substr( $haystack, 0, strlen( $needle ) );
		//return substr( $haystack, 0, strlen( $needle ) );
		//return strpos( $haystack, $needle ) !== false;
	}

	/**
	 * change_woocommerce_attribute_label.
	 *
	function change_woocommerce_attribute_label( $label, $name ) {

		if ( $this->starts_with( $label, '_wcj_product_input_fields_global_' ) ) {
			$title_option_id = trim( $label, '_' );
			$title_option_id = str_replace( 'wcj_product_input_fields_global_', 'wcj_product_input_fields_title_global_', $title_option_id );
			//$the_nice_name = $this->get_value( $label, 0, '' );
			$title = get_option( $title_option_id, '' );

			$label = ( '' == $title ) ?
				str_replace(
					'_wcj_product_input_fields_' . $this->scope . '_',
					__( 'Product Input Field', 'woocommerce-jetpack' ) . ' (' . $this->scope . ') #',
					$label ) :
				$title;

		} elseif ( $this->starts_with( $label, '_wcj_product_input_fields_local_' ) ) {

			$title = '';//$label;

			$label = ( '' == $title ) ?
				str_replace(
					'_wcj_product_input_fields_' . $this->scope . '_',
					__( 'Product Input Field', 'woocommerce-jetpack' ) . ' (' . $this->scope . ') #',
					$label ) :
				$title;
		}

		return $label;
	}

	/**
	 * finish_making_nicer_name_for_product_input_fields.
	 *
	public function finish_making_nicer_name_for_product_input_fields( $item_id, $item, $_product ) {
		$buffer = ob_get_clean();
		$the_ugly_name = '_wcj_product_input_fields_' . $this->scope . '_';
		$the_nice_name = $this->get_value( 'wcj_product_input_fields_title_' . $this->scope . '_' . '1', $_product->id, '' );
		if ( '' == $the_nice_name ) $the_nice_name = __( 'Product Input Field', 'woocommerce-jetpack' ) . ' (' . $this->scope . ') #';
		$buffer = str_replace(
			$the_ugly_name,
			$the_nice_name,
			$buffer
		);
		echo $buffer;
	}

	/**
	 * make_nicer_name.
	 *
	public function make_nicer_name( $buffer ) {
		$the_ugly_name = '_wcj_product_input_fields_' . $this->scope . '_';
		$the_nice_name = () ? : __( 'Product Input Field', 'woocommerce-jetpack' ) . ' (' . $this->scope . ') #';
		return str_replace(
			$the_ugly_name,
			$the_nice_name,
			$buffer
		);
	}

	/**
	 * start_making_nicer_name_for_product_input_fields.
	 *
	public function start_making_nicer_name_for_product_input_fields( $item_id, $item, $_product ) {
		ob_start( array( $this, 'make_nicer_name' ) );
	}

	/**
	 * finish_making_nicer_name_for_product_input_fields.
	 *
	public function finish_making_nicer_name_for_product_input_fields( $item_id, $item, $_product ) {
		ob_end_flush();
	}

	/**
	 * get_value.
	 */
	public function get_value( $option_name, $product_id, $default ) {
		return false;
	}

	/**
	 * validate_product_input_fields_on_add_to_cart.
	 */
	public function validate_product_input_fields_on_add_to_cart( $passed, $product_id ) {
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $product_id, 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {

			$is_enabled  = $this->get_value( 'wcj_product_input_fields_enabled_' . $this->scope . '_' . $i, $product_id, 'no' );
			if ( ! $is_enabled ) {
				continue;
			}

			$type = $this->get_value( 'wcj_product_input_fields_type_' . $this->scope . '_' . $i, $product_id, '' );

			if ( 'checkbox' === $type && ! isset( $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] ) ) {
				$_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] = 'off';
			}

			$is_required = $this->get_value( 'wcj_product_input_fields_required_' . $this->scope . '_' . $i, $product_id, 'no' );
			if ( ( 'on' === $is_required  || 'yes' === $is_required )
				&& isset( $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] )
				&& ( '' == $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] || ( 'checkbox' === $type && 'off' === $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] ) )
			) {
				$passed = false;
				//__( 'Fill text box before adding to cart.', 'woocommerce-jetpack' )
				wc_add_notice( $this->get_value( 'wcj_product_input_fields_required_message_' . $this->scope . '_' . $i, $product_id, '' ), 'error' );
			}
		}
		return $passed;
	}

	/**
	 * add_product_input_fields_to_frontend.
	 */
	public function add_product_input_fields_to_frontend() {
		global $product;
		//if ( ! $product )
			//	return;
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $product->id, 1 ) );

		for ( $i = 1; $i <= $total_number; $i++ ) {

			$type        = $this->get_value( 'wcj_product_input_fields_type_' .        $this->scope . '_' . $i, $product->id, 'text' );
			$is_enabled  = $this->get_value( 'wcj_product_input_fields_enabled_' .     $this->scope . '_' . $i, $product->id, 'no' );
			$is_required = $this->get_value( 'wcj_product_input_fields_required_' .    $this->scope . '_' . $i, $product->id, 'no' );
			$title       = $this->get_value( 'wcj_product_input_fields_title_' .       $this->scope . '_' . $i, $product->id, '' );
			$placeholder = $this->get_value( 'wcj_product_input_fields_placeholder_' . $this->scope . '_' . $i, $product->id, '' );

			if ( 'on' === $is_enabled || 'yes' === $is_enabled ) {
				switch ( $type ) {
					case 'number':
					case 'text':
					case 'checkbox':
						echo '<p>' . $title . '<input type="' . $type . '" name="wcj_product_input_fields_' . $this->scope . '_' . $i . '" placeholder="' . $placeholder . '">' . '</p>';
						break;
					case 'textarea':
						echo '<p>' . $title . '<textarea name="wcj_product_input_fields_' . $this->scope . '_' . $i . '" placeholder="' . $placeholder . '">' . '</textarea>' . '</p>';
						break;
				}
			}
		}
	}

	/**
	 * add_product_input_fields_to_cart_item_data.
	 * from $_POST to $cart_item_data
	 */
	public function add_product_input_fields_to_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $product_id, 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( isset( $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] ) )
				$cart_item_data[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] = $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ];
		}
		return $cart_item_data;
	}

	/**
	 * get_cart_item_product_input_fields_from_session.
	 */
	public function get_cart_item_product_input_fields_from_session( $item, $values, $key ) {
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $item['product_id'], 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( array_key_exists( 'wcj_product_input_fields_' . $this->scope . '_' . $i, $values ) )
				$item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] = $values[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ];
		}
		return $item;
	}

	/**
	 * Adds product input values to order details (and emails).
	 */
	public function add_product_input_fields_to_order_item_name( $name, $item ) {

		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $item['product_id'], 1 ) );
		if ( $total_number < 1 )
			return $name;

		$name .= '<dl style="font-size:smaller;">';
		for ( $i = 1; $i <= $total_number; $i++ ) {

			$is_enabled  = $this->get_value( 'wcj_product_input_fields_enabled_' . $this->scope . '_' . $i, $item['product_id'], 'no' );
			if ( ! $is_enabled ) {
				continue;
			}

			$type = $this->get_value( 'wcj_product_input_fields_type_' . $this->scope . '_' . $i, $item['product_id'], '' );

			if ( 'checkbox' === $type && ! array_key_exists( 'wcj_product_input_fields_' . $this->scope . '_' . $i, $item ) ) {
				$item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] = 'off';
			}

			if ( array_key_exists( 'wcj_product_input_fields_' . $this->scope . '_' . $i, $item ) ) {
				$title = $this->get_value( 'wcj_product_input_fields_title_' . $this->scope . '_' . $i, $item['product_id'], '' );

				$value = $item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ];

				$yes_value = $this->get_value( 'wcj_product_input_fields_type_checkbox_yes_' . $this->scope . '_' . $i, $item['product_id'], '' );
				$no_value  = $this->get_value( 'wcj_product_input_fields_type_checkbox_no_'  . $this->scope . '_' . $i, $item['product_id'], '' );
				//$type    = $this->get_value( 'wcj_product_input_fields_type_'              . $this->scope . '_' . $i, $item['product_id'], '' );
				if ( 'checkbox' === $type ) {
					$value = ( 'on' === $value ) ? $yes_value : $no_value;
				}

				$name .= '<dt>'
					  . $title
					  . '</dt>'
					  . '<dd>'
					  . $value
					  . '</dd>';
			}
		}
		$name .= '</dl>';

		return $name;
	}

	/**
	 * Adds product input values to cart item details.
	 */
	public function add_product_input_fields_to_cart_item_name( $name, $cart_item, $cart_item_key  ) {
		return $this->add_product_input_fields_to_order_item_name( $name, $cart_item );
	}

	/**
	 * add_product_input_fields_to_order_item_meta.
	 */
	public function add_product_input_fields_to_order_item_meta(  $item_id, $values, $cart_item_key  ) {
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $values['product_id'], 1 ) );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( array_key_exists( 'wcj_product_input_fields_' . $this->scope . '_' . $i , $values ) )
				wc_add_order_item_meta( $item_id, '_wcj_product_input_fields_' . $this->scope . '_' . $i, $values[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] );
		}
	}
}

endif;

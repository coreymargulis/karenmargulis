<?php
/**
 * WooCommerce Jetpack PDF Invoicing Emails
 *
 * The WooCommerce Jetpack PDF Invoicing Emails class.
 *
 * @version 2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_PDF_Invoicing_Emails' ) ) :

class WCJ_PDF_Invoicing_Emails extends WCJ_Module {

    /**
     * Constructor.
     */
    function __construct() {

		$this->id         = 'pdf_invoicing_emails';
		$this->parent_id  = 'pdf_invoicing';
		$this->short_desc = __( 'Email Options', 'woocommerce-jetpack' );
		$this->desc       = '';
		parent::__construct( 'submodule' );

		//if ( 'yes' === get_option( 'wcj_pdf_invoicing_enabled' ) ) {
		if ( $this->is_enabled() ) {
			add_filter( 'woocommerce_email_attachments', array( $this, 'add_pdf_invoice_email_attachment' ), PHP_INT_MAX, 3 );
		}
    }

	/**
	 * do_attach_for_payment_method.
	 *
	function do_attach_for_payment_method( $payment_method ) {
		return ( 'no' === get_option( 'wcj_gateways_attach_invoice_' . $payment_method, 'yes' ) ) ? false : true;
	}

	/**
	 * add_pdf_invoice_email_attachment.
	 */
	function add_pdf_invoice_email_attachment( $attachments, $status, $order ) {
		$invoice_types_ids = wcj_get_enabled_invoice_types_ids();
		foreach ( $invoice_types_ids as $invoice_type_id ) {
			//if ( 'yes' === apply_filters( 'wcj_get_option_filter', 'no', get_option( 'wcj_invoicing_' . $invoice_type_id . '_attach_to_email_enabled' ) ) ) {
				//if ( isset( $status ) && 'customer_completed_order' === $status && isset( $order ) && true === $this->do_attach_for_payment_method( $order->payment_method ) ) {
				//if ( 'customer_completed_order' === $status ) {
				$send_on_statuses = get_option( 'wcj_invoicing_' . $invoice_type_id . '_attach_to_emails', array() );
				if ( '' == $send_on_statuses ) $send_on_statuses = array();
				if ( in_array( $status, $send_on_statuses ) ) {
					$the_invoice = wcj_get_pdf_invoice( $order->id, $invoice_type_id );
					$file_name = $the_invoice->get_pdf( 'F' );
					if ( '' != $file_name ) {
						$attachments[] = $file_name;
					}
				}
		}
		return $attachments;
	}

    /**
     * get_settings.
     */
    function get_settings() {

		$settings = array();
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {

			$settings[] = array( 'title' => strtoupper( $invoice_type['desc'] ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_emails_options' );

			//$available_emails = apply_filters( 'woocommerce_resend_order_emails_available', array( 'new_order', 'customer_processing_order', 'customer_completed_order', 'customer_invoice' ) );
			$available_emails = array(
				'new_order'                 => __( 'Admin - New Order', 'woocommerce' ),
				'customer_processing_order' => __( 'Customer - Processing Order', 'woocommerce' ),
				'customer_completed_order'  => __( 'Customer - Completed Order', 'woocommerce' ),
				'customer_invoice'          => __( 'Customer - Invoice', 'woocommerce' ),
			);

			$settings[] = array(
				'title' 		=> __( 'Attach PDF to emails', 'woocommerce' ),
				'id'            => 'wcj_invoicing_' . $invoice_type['id'] . '_attach_to_emails',
				'type' 			=> 'multiselect',
				'class'			=> 'chosen_select',
				'css'			=> 'width: 450px;',
				//'default' 		=> 'new_order',
				'default' 		=> '',
				'options'		=> $available_emails,
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select some emails', 'woocommerce' )
				)
			);

			$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_emails_options' );
		}

        return $settings;
    }
}

endif;

return new WCJ_PDF_Invoicing_Emails();
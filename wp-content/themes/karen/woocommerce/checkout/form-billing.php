<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */
?>
<div class="woocommerce-billing-fields">
	
	<h2><?php _e( 'Contact Information', 'woocommerce' ); ?></h2>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<?php
		$mybillingfields=array(
		"billing_first_name",
		"billing_last_name",
		"billing_email",

		
		);
	?>
	<?php foreach ($mybillingfields as $key) : ?>

		<?php woocommerce_form_field( $key, $checkout->checkout_fields['billing'][$key], $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

	<h2><?php _e( 'Billing Address', 'woocommerce' ); ?></h2>

	<?php
		$mybillingfields=array(

		"billing_address_1",
		"billing_address_2",
		"billing_city",
		"billing_state",
		"billing_postcode",
		"billing_country",
		);
	?>
	<?php foreach ($mybillingfields as $key) : ?>

		<?php woocommerce_form_field( $key, $checkout->checkout_fields['billing'][$key], $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

	<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>

		<?php if ( $checkout->enable_guest_checkout ) : ?>

			<p class="form-row form-row-wide create-account">
				<input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( ! empty( $checkout->checkout_fields['account'] ) ) : ?>

			<div class="create-account">

				<p><?php _e( 'Create an account by entering a password below. If you are a returning customer please login !here.', 'woocommerce' ); ?></p>

				<?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>

					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

				<?php endforeach; ?>

				<div class="clear"></div>

			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

	<?php endif; ?>
</div>

<?php
/**
 * Hotelier AJAX Coupons Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Apply coupon.
 */
function hotelier_ajax_action_apply_coupon() {
	if ( isset( $_POST[ 'coupon_nonce' ] ) ) {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST[ 'coupon_nonce' ], 'hotelier-apply-coupon-nonce' ) ) {
			// Invalid nonce
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid nonce.', 'wp-hotelier' )
				)
			);
		}

		$is_removing_coupon = isset( $_POST[ 'is_removing' ] ) && $_POST[ 'is_removing' ] === 'true' ? true : false;

		if ( ! $is_removing_coupon ) {
			// Check coupon
			if ( ! isset( $_POST[ 'coupon_code' ] ) || ! $_POST[ 'coupon_code' ] ) {
				// Empty coupon code
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Please insert a valid coupon code.', 'wp-hotelier' )
					)
				);
			}

			// Get coupon ID
			$coupon_code = trim( sanitize_text_field( $_POST[ 'coupon_code' ] ) );
			$coupon_id   = htl_get_coupon_id_from_code( $coupon_code );

			if ( ! $coupon_id ) {
				// Invalid coupon code
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Invalid coupon code.', 'wp-hotelier' )
					)
				);
			}

			// Check if coupon is valid
			$can_apply_coupon = htl_can_apply_coupon( $coupon_id );

			if ( ! isset( $can_apply_coupon['can_apply'] ) || ! $can_apply_coupon['can_apply'] ) {
				$reason = isset( $can_apply_coupon['reason'] ) ? $can_apply_coupon['reason'] : false;
				$reason = $reason ? $reason : esc_html__( 'This coupon cannot be applied.', 'wp-hotelier' );

				// Can't apply this coupon
				wp_send_json_error(
					array(
						'message' => $reason
					)
				);
			}
		}

		ob_start();

		// Get number of nights
		$checkin  = new DateTime( HTL()->session->get( 'checkin' ) );
		$checkout = new DateTime( HTL()->session->get( 'checkout' ) );
		$nights   = $checkin->diff( $checkout )->days;

		// Set coupon ID
		$coupon_id = $is_removing_coupon ? 0 : $coupon_id;
		HTL()->cart->set_coupon_id( $coupon_id );

		// Refresh totals
		HTL()->cart->calculate_totals();

		htl_get_template( 'booking/reservation-table.php', array(
			'nights' => $nights,
		) );

		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
	} else {
		// Invalid data
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Invalid or empty data.', 'wp-hotelier' )
			)
		);
	}
}

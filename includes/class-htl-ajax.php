<?php
/**
 * AJAX Event Handler.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Ajax' ) ) :

/**
 * HTL_Ajax Class
 */
class HTL_Ajax {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Set HTL AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET[ 'htl-ajax' ] ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			// Turn off display_errors during AJAX events to prevent malformed JSON
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 );
			}
			$GLOBALS[ 'wpdb' ]->hide_errors();
		}
	}

	/**
	 * Check for Ajax request and fire action.
	 */
	public static function do_ajax() {
		global $wp_query;

		if ( ! empty( $_GET[ 'htl-ajax' ] ) ) {
			$wp_query->set( 'htl-ajax', sanitize_text_field( $_GET[ 'htl-ajax' ] ) );
		}

		if ( $action = $wp_query->get( 'htl-ajax' ) ) {
			self::ajax_headers();
			do_action( 'htl_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	/**
	 * Send headers for Ajax Requests
	 * @since 2.5.0
	 */
	private static function ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();
		status_header( 200 );
	}

	/**
	 * Get HTL Ajax Endpoint.
	 * @param  string $request Optional
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters( 'hotelier_ajax_get_endpoint', add_query_arg( 'htl-ajax', $request ), $request ) );
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'get_checkin_dates' => true,
			'apply_coupon'      => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_hotelier_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_hotelier_' . $ajax_event, array( __CLASS__, $ajax_event ) );

				// HTL AJAX can be used for frontend ajax requests
				add_action( 'htl_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Get checkin and checkout dates.
	 */
	public static function get_checkin_dates() {
		$data = array(
			'checkin'  => HTL()->session->get( 'checkin' ),
			'checkout' => HTL()->session->get( 'checkout' )
		);

		wp_send_json( $data );
		die();
	}

	/**
	 * Apply coupon.
	 */
	public static function apply_coupon() {
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
}

endif;

HTL_Ajax::init();

<?php
/**
 * Handle Form Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Form_Functions' ) ) :

/**
 * HTL_Form_Functions Class
 */
class HTL_Form_Functions {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'wp_loaded', array( __CLASS__, 'booking_action' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'reserve_rooms_action' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'add_to_cart_action' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'cancel_reservation' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'datepicker_action' ), 20 );
		add_action( 'wp', array( __CLASS__, 'pay_action' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'remove_room' ), 20 );
	}

	/**
	 * Process the booking form.
	 */
	public static function booking_action() {
		if ( isset( $_POST[ 'hotelier_booking_action' ] ) ) {

			if ( HTL()->cart->is_empty() ) {
				wp_redirect( htl_get_page_permalink( 'listing' ) );
				exit;
			}

			if ( ! defined( 'HOTELIER_BOOKING' ) ) {
				define( 'HOTELIER_BOOKING', true );
			}

			HTL()->booking()->process_booking();
		}
	}

	/**
	 * Process the pay form (this is the form guests are sent to pay for reservations generated by the admin )
	 */
	public static function pay_action() {
		global $wp;

		if ( isset( $_POST[ 'hotelier_pay' ] ) && isset( $_POST[ '_wpnonce' ] ) && wp_verify_nonce( $_POST[ '_wpnonce' ], 'hotelier-pay' ) ) {

			ob_start();

			// Pay for existing reservation
			$reservation_key  = $_GET[ 'key' ];
			$reservation_id   = absint( $wp->query_vars[ 'pay-reservation' ] );
			$reservation      = htl_get_reservation( $reservation_id );

			if ( $reservation->id == $reservation_id && $reservation->reservation_key == $reservation_key ) {

				// Update payment method
				if ( $reservation->needs_payment() ) {
					$payment_method     = isset( $_POST[ 'payment_method' ] ) ? sanitize_text_field( $_POST[ 'payment_method' ] ) : false;
					$available_gateways = HTL()->payment_gateways->get_available_payment_gateways();

					if ( ! $payment_method ) {
						htl_add_notice( esc_html__( 'Invalid payment method.', 'wp-hotelier' ), 'error' );
						return;
					}

					// Terms and conditions
					if ( ! empty( $_POST[ 'has_terms_field' ] ) && empty( $_POST[ 'booking_terms' ] ) ) {
						htl_add_notice( esc_html__( 'You must accept our Terms &amp; Conditions.', 'wp-hotelier' ), 'error' );
						return;
					}

					// Update meta
					update_post_meta( $reservation_id, '_payment_method', $payment_method );

					if ( isset( $available_gateways[ $payment_method ] ) ) {
						$payment_method_title = $available_gateways[ $payment_method ]->get_title();
					} else {
						$payment_method_title = '';
					}

					update_post_meta( $reservation_id, '_payment_method_title', $payment_method_title );

					// Validate
					$available_gateways[ $payment_method ]->validate_fields();

					// Process
					if ( htl_notice_count( 'error' ) == 0 ) {

						$result = $available_gateways[ $payment_method ]->process_payment( $reservation_id );

						// Redirect to success/confirmation/payment page
						if ( 'success' == $result[ 'result' ] ) {
							wp_redirect( $result[ 'redirect' ] );
							exit;
						}
					}

				}
			}

		}
	}

	/**
	 * Process the room_list form.
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool $url (default: false)
	 */
	public static function reserve_rooms_action( $url = false ) {
		if ( isset( $_POST[ 'hotelier_reserve_rooms_button' ] ) ) {
			try {
				if ( empty( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'hotelier_reserve_rooms' ) ) {
					throw new Exception( esc_html__( 'We were unable to process your reservation, please try again.', 'wp-hotelier' ) );
				}

				if ( ! defined( 'HOTELIER_LISTING' ) ) {
					define( 'HOTELIER_LISTING', true );
				}

				// Initialize $items
				$items = array();

				// Check posted data and populate $items if quantity > 0
				if ( isset( $_POST[ 'add_to_cart_room' ] ) && is_array( $_POST[ 'add_to_cart_room' ] ) ) {
					foreach ( $_POST[ 'add_to_cart_room' ] as $key => $value ) {
						$qty = isset( $_POST[ 'quantity' ][ $key ] ) ? $_POST[ 'quantity' ][ $key ] : 0;

						// If quantity > 0
						if ( $qty > 0 ) {

							// And the room_id and rate_id are passed
							if ( isset( $_POST[ 'add_to_cart_room' ][ $key ] ) && isset( $_POST[ 'rate_id' ][ $key ] ) ) {

								// Add room to the $items array
								$items[] = array(
									'room_id'  => absint( $_POST[ 'add_to_cart_room' ][ $key ] ),
									'rate_id'  => absint( $_POST[ 'rate_id' ][ $key ] ),
									'quantity' => absint( $_POST[ 'quantity' ][ $key ] ),
								);
							}
						}
					}
				}

				// If $items is empty (no rooms were added or something went wrong) throw an exception
				if ( empty( $items ) ) {
					throw new Exception( esc_html__( 'Please select at least one room.', 'wp-hotelier' ) );
				}

				// Add room(s) to the cart
				foreach ( $items as $item ) {
					$room_id           = absint( $item[ 'room_id' ] );
					$quantity          = absint( $item[ 'quantity' ] );
					$rate_id           = absint( $item[ 'rate_id' ] );

					$was_added_to_cart = false;
					$was_added_to_cart = self::add_to_cart_from_room_list_handler( $room_id, $quantity, $rate_id );

					if ( ! $was_added_to_cart ) {
						throw new Exception( esc_html__( 'We were unable to process your reservation, please try again.', 'wp-hotelier' ) );
					}
				}

				// If we added the room(s) to the cart we can now optionally do a redirect.
				if ( $was_added_to_cart && htl_notice_count( 'error' ) === 0 ) {
					// If has custom URL redirect there
					if ( $url = apply_filters( 'hotelier_add_to_cart_from_room_list_redirect', $url ) ) {
						wp_safe_redirect( $url );
						exit;
					} else {
						// Redirect to the booking form
						wp_safe_redirect( HTL()->cart->get_booking_form_url() );
						exit;
					}
				}

			} catch ( Exception $e ) {
				if ( ! empty( $e ) ) {
					htl_add_notice( $e->getMessage(), 'error' );
				}
			}
		}
	}

	/**
	 * Add to cart action (single room page)
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool $url (default: false)
	 */
	public static function add_to_cart_action( $url = false ) {
		if ( defined( 'HOTELIER_LISTING' ) ) {
			return;
		}

		if ( empty( $_REQUEST[ 'add_to_cart_room' ] ) || ! is_numeric( $_REQUEST[ 'add_to_cart_room' ] ) ) {
			return;
		}

		$room_id           = absint( $_REQUEST[ 'add_to_cart_room' ] );
		$was_added_to_cart = false;

		$was_added_to_cart = self::add_to_cart_handler( $room_id );

		// If we added the room to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && htl_notice_count( 'error' ) === 0 ) {
			// If has custom URL redirect there
			if ( $url = apply_filters( 'hotelier_add_to_cart_redirect', $url ) ) {
				wp_safe_redirect( $url );
				exit;
			} else {
				// Redirect to the booking form
				wp_safe_redirect( HTL()->cart->get_booking_form_url() );
				exit;
			}
		}
	}

	/**
	 * Handle adding rooms to the cart
	 * @param int $room_id
	 * @return bool success or not
	 */
	private static function add_to_cart_handler( $room_id ) {
		$quantity          = empty( $_REQUEST[ 'quantity' ] ) ? 1 : absint( $_REQUEST[ 'quantity' ] );
		$rate_id           = empty( $_REQUEST[ 'rate_id' ] ) ? 0 : absint( $_REQUEST[ 'rate_id' ] );
		$passed_validation = apply_filters( 'hotelier_add_to_cart_validation', true, $room_id, $quantity, $rate_id );

		if ( $passed_validation && HTL()->cart->add_to_cart( $room_id, $quantity, $rate_id ) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * Handle adding rooms to the cart from room_list
	 * @param int $room_id
	 * @param int $quantity
	 * @param int $rate_id
	 * @return bool success or not
	 */
	private static function add_to_cart_from_room_list_handler( $room_id, $quantity, $rate_id ) {
		$passed_validation = apply_filters( 'hotelier_add_to_cart_validation', true, $room_id, $quantity, $rate_id );

		if ( $passed_validation && HTL()->cart->add_to_cart( $room_id, $quantity, $rate_id ) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * Cancel a pending reservation.
	 */
	public static function cancel_reservation() {
		if ( isset( $_GET[ 'cancel_reservation' ] ) && isset( $_GET[ 'reservation' ] ) && isset( $_GET[ 'reservation_id' ] ) ) {

			$reservation_key        = $_GET[ 'reservation'];
			$reservation_id         = absint( $_GET[ 'reservation_id' ] );
			$reservation            = htl_get_reservation( $reservation_id );
			$reservation_can_cancel = $reservation->has_status( apply_filters( 'hotelier_valid_reservation_statuses_for_cancel', array( 'pending', 'confirmed', 'failed' ) ) );
			$redirect               = $_GET[ 'redirect' ];

			if ( $reservation->has_status( 'cancelled' ) ) {
				// Already cancelled
				htl_add_notice( esc_html__( 'Reservation already cancelled.', 'wp-hotelier' ), 'error' );
			} elseif ( $reservation->has_status( 'refunded' ) ) {
				// Already refunded
				htl_add_notice( esc_html__( 'Reservation already refunded.', 'wp-hotelier' ), 'error' );
			} elseif ( ! $reservation->can_be_cancelled() ) {
				// Reservation contains non-cancellable rooms
				htl_add_notice( esc_html__( 'Your reservation includes a non cancellable and non refundable room and it cannot be cancelled.', 'wp-hotelier' ), 'error' );
			} elseif ( $reservation_can_cancel && $reservation->id == $reservation_id && $reservation->reservation_key == $reservation_key  ) {

				// Cancel the reservation + restore available rooms
				$reservation->cancel_reservation( esc_html__( 'Reservation cancelled by guest.', 'wp-hotelier' ) );

				// Message
				htl_add_notice( apply_filters( 'hotelier_reservation_cancelled_message', esc_html__( 'Your reservation has been cancelled.', 'wp-hotelier' ) ), 'notice' );

				do_action( 'hotelier_cancelled_reservation', $reservation->id );

			} elseif ( ! $reservation_can_cancel ) {
				htl_add_notice( esc_html__( 'Your reservation can no longer be cancelled. Please contact us if you need assistance.', 'wp-hotelier' ), 'error' );
			} else {
				htl_add_notice( esc_html__( 'Invalid reservation.', 'wp-hotelier' ), 'error' );
			}

			if ( $redirect ) {
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}

	/**
	 * Process the datepicker form.
	 */
	public static function datepicker_action() {
		if ( isset( $_POST[ 'hotelier_datepicker_button' ] ) ) {

			$checkin  = sanitize_text_field( $_POST[ 'checkin' ] );
			$checkout = sanitize_text_field( $_POST[ 'checkout' ] );

			if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {
				do_action( 'hotelier_set_cookies', true );

				if ( HTL_Formatting_Helper::is_valid_checkin_checkout( $checkin, $checkout ) ) {

					HTL()->session->set( 'checkin', $checkin );
					HTL()->session->set( 'checkout', $checkout );

				} else {

					$dates = htl_get_default_dates();

					HTL()->session->set( 'checkin', $dates[ 'checkin' ] );
					HTL()->session->set( 'checkout', $dates[ 'checkout' ] );
				}
			}
		}
	}

	/**
	 * Remove room from cart.
	 */
	public static function remove_room() {
		if ( ! isset( $_REQUEST[ 'remove_room' ] ) ) {
			return;
		}

		$nonce_value = isset( $_REQUEST[ '_wpnonce' ] ) ? $_REQUEST[ '_wpnonce' ] : false;

		if ( ! empty( $_GET[ 'remove_room' ] ) && wp_verify_nonce( $nonce_value ) ) {
			$cart_item_key = sanitize_text_field( wp_unslash( $_GET[ 'remove_room' ] ) );
			$cart_item     = HTL()->cart->get_cart_item( $cart_item_key );
			$redirect_page = 'booking';

			if ( $cart_item ) {
				if ( HTL()->cart->remove_cart_item( $cart_item_key ) ) {

					if ( HTL()->cart->is_empty() ){
						$redirect_page = 'listing';
					}

					$_room              = ( $cart_item[ 'data' ] instanceof HTL_Room ) ? $cart_item['data'] : htl_get_room( $cart_item[ 'room_id' ] );
					$item_removed_title = $_room ? sprintf( __( '&ldquo;%s&rdquo;', 'wp-hotelier' ), $_room->get_title() ) : __( 'Item', 'wp-hotelier' );
					$removed_notice     = sprintf( __( '%s removed.', 'wp-hotelier' ), $item_removed_title );

					htl_add_notice( $removed_notice );

					HTL()->cart->calculate_totals();
				}
			}

			$redirect_page_url = htl_get_page_permalink( $redirect_page );

			wp_safe_redirect( $redirect_page_url );

			exit;
		}
	}
}

endif;

HTL_Form_Functions::init();

<?php
/**
 * Handle Form Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.13.1
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
				$empty_cart_redirect = htl_get_option( 'listing_disabled', false ) ? home_url() : htl_get_page_permalink( 'listing' );
				wp_redirect( $empty_cart_redirect );
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
						$qty = isset( $_POST[ 'quantity' ][ $key ] ) ? absint( $_POST[ 'quantity' ][ $key ] ) : 0;

						// If quantity > 0
						if ( $qty > 0 ) {

							// And the room_id and rate_id are passed
							if ( isset( $_POST[ 'add_to_cart_room' ][ $key ] ) && isset( $_POST[ 'rate_id' ][ $key ] ) ) {

								// Item data
								$item_to_add = array(
									'room_id'  => absint( $_POST[ 'add_to_cart_room' ][ $key ] ),
									'rate_id'  => absint( $_POST[ 'rate_id' ][ $key ] ),
									'quantity' => $qty,
									'guests'   => array(),
									'fees'     => array(),
									'extras'   => array(),
								);

								// Calculate guests to add
								$item_to_add[ 'guests' ] = self::calculate_guests_to_add( $item_to_add, $key, $qty );

								if ( isset( $_POST[ 'fees' ][ $key ] ) ) {
									$item_to_add[ 'fees' ] = $_POST[ 'fees' ][ $key ];
								}

								if ( isset( $_POST[ 'extras' ][ $key ] ) ) {
									$item_to_add[ 'extras' ] = $_POST[ 'extras' ][ $key ];
								}

								// Add room to the $items array
								$items[] = $item_to_add;
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
					$room_id  = absint( $item[ 'room_id' ] );
					$quantity = absint( $item[ 'quantity' ] );
					$rate_id  = absint( $item[ 'rate_id' ] );
					$fees     = $item[ 'fees' ];
					$extras   = $item[ 'extras' ];
					$guests   = $item[ 'guests' ];

					$was_added_to_cart = false;
					$was_added_to_cart = self::add_to_cart_from_room_list_handler( $room_id, $quantity, $rate_id, $guests, $fees, $extras );

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
	 * @deprecated
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
	 * @deprecated
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
	 * @param array $fees
	 * @param array $extras
	 * @return bool success or not
	 */
	private static function add_to_cart_from_room_list_handler( $room_id, $quantity, $rate_id, $guests, $fees, $extras ) {
		$passed_validation = apply_filters( 'hotelier_add_to_cart_validation', true, $room_id, $quantity, $rate_id );

		if ( $passed_validation && HTL()->cart->add_to_cart( $room_id, $quantity, $rate_id, $guests, $fees, $extras ) !== false ) {
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
			$is_payment             = isset( $_GET['is_payment'] ) && $_GET['is_payment'] ? true : false;

			if ( $reservation->has_status( 'cancelled' ) ) {
				// Already cancelled
				htl_add_notice( esc_html__( 'Reservation already cancelled.', 'wp-hotelier' ), 'error' );
			} elseif ( $reservation->has_status( 'refunded' ) ) {
				// Already refunded
				htl_add_notice( esc_html__( 'Reservation already refunded.', 'wp-hotelier' ), 'error' );
			} elseif ( ! $reservation->can_be_cancelled() && ! $is_payment ) {
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

			if ( htl_get_option( 'listing_disabled', false ) ) {
				$redirect_page_url = home_url();
			} else {
				$redirect_page_url = htl_get_page_permalink( $redirect_page );
			}

			wp_safe_redirect( $redirect_page_url );

			exit;
		}
	}


	/**
	 * Add a room to the cart via AJAX.
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 */
	public static function add_to_cart_from_ajax( $room_id, $checkin, $checkout, $quantity, $form_data ) {
		try {
			// Initialize $item
			$item = array();

			$room_id = absint( $room_id );

			// Clear notices
			htl_clear_notices();

			// Check posted data
			if ( is_array( $form_data ) ) {
				$quantity = $quantity ? absint( $quantity ) : 0;

				if ( $quantity > 0 ) {
					if ( $room_id ) {
						$rate_id = isset( $form_data['rate'] ) ? absint( $form_data['rate'] ) : 0;

						// Item data
						$item = array(
							'room_id'  => ( $room_id ),
							'rate_id'  => ( $rate_id ),
							'quantity' => $quantity,
							'guests'   => array(),
							'fees'     => array(),
							'extras'   => array(),
						);

						// Generate key
						$cart_item_key = htl_generate_item_key( $room_id, $rate_id );

						// Add adults/children to form data (with item key)
						$form_data['adults']   = array( $cart_item_key => $form_data['adults'] );
						$form_data['children'] = array( $cart_item_key => $form_data['children'] );

						// Calculate guests to add
						$item[ 'guests' ] = self::calculate_guests_to_add( $item, $cart_item_key, $quantity, $form_data );

						if ( isset( $form_data[ 'fees' ][ $cart_item_key ] ) ) {
							$item[ 'fees' ] = $form_data[ 'fees' ][ $cart_item_key ];
						}

						if ( isset( $form_data[ 'extras' ][ $cart_item_key ] ) ) {
							$item[ 'extras' ] = $form_data[ 'extras' ][ $cart_item_key ];
						}
					}
				}
			}

			// If $item is empty (no rooms were added or something went wrong) throw an exception
			if ( empty( $item ) ) {
				throw new Exception( esc_html__( 'Sorry, something went wrong during the calculation of the totals.', 'wp-hotelier' ) );
			}

			// Add room to the cart
			$was_added_to_cart = false;
			$was_added_to_cart = self::add_to_cart_from_room_list_handler( $item[ 'room_id' ], $item[ 'quantity' ], $item[ 'rate_id' ], $item[ 'guests' ], $item[ 'fees' ], $item[ 'extras' ] );

			if ( ! $was_added_to_cart ) {
				throw new Exception( esc_html__( 'We were unable to process your reservation, please try again.', 'wp-hotelier' ) );
			}

			// Check notices
			if ( htl_notice_count( 'error' ) > 0 ) {
			    $notices = HTL()->session->get( 'htl_notices', array() );
			    throw new Exception( $notices['error'][0] );
			}

			// If we added the room to the cart, then redirect.
			if ( $was_added_to_cart ) {
				$url = apply_filters( 'hotelier_add_to_cart_from_ajax_room_booking_redirect', HTL()->cart->get_booking_form_url() );
				$url = wp_sanitize_redirect( $url );
				$url = wp_validate_redirect( $url, apply_filters( 'wp_safe_redirect_fallback', home_url(), 302 ) );

				$added_to_cart = array(
					'added_to_cart' => true,
					'redirect_url'  => $url,
				);

				return $added_to_cart;
			}

		} catch ( Exception $e ) {
			if ( ! empty( $e ) ) {
				$added_to_cart = array(
					'added_to_cart' => false,
					'error'         => $e->getMessage(),
				);

				return $added_to_cart;
			}
		}
	}

	/**
	 * Calculate guests to add
	 */
	private static function calculate_guests_to_add( $item_to_add, $key, $qty, $post_data = false ) {
		$post_data    = is_array( $post_data ) ? $post_data : $_POST;
		$_room        = htl_get_room( $item_to_add['room_id'] );
		$max_guests   = $_room->get_max_guests();
		$max_children = $_room->get_max_children();

		$guests = array();

		for ( $i = 0; $i < $qty; $i++ ) {
			$guests[$i] = array(
				'adults'   => $max_guests,
				'children' => 0,
			);
		}

		if ( function_exists( 'hotelier_aps_room_has_extra_guests_enabled' ) && hotelier_aps_room_has_extra_guests_enabled( $_room ) ) {
			if ( isset( $post_data['fees'][ $key ] ) ) {
				if ( hotelier_aps_room_has_extra_adults( $_room ) ) {
					$adults_included_in_rate = absint( get_post_meta( $_room->id, '_seasons_extra_person_fees_adults_included', true ) );
					$adults_to_add           = $adults_included_in_rate;
					$extra_adults            = isset( $post_data['fees'][$key]['adults'] ) ? absint( $post_data['fees'][$key]['adults'] ) : 0;
					$adults_to_add           += $extra_adults;
					$adults_to_add           = $adults_to_add > $max_guests ? $max_guests : $adults_to_add;

					for ( $i = 0; $i < $qty; $i++ ) {
						$guests[$i]['adults'] = $adults_to_add;
					}
				}

				if ( hotelier_aps_room_has_extra_children( $_room ) ) {
					$children_included_in_rate = absint( get_post_meta( $_room->id, '_seasons_extra_person_fees_children_included', true ) );
					$children_to_add           = $children_included_in_rate;
					$extra_children            = isset( $post_data['fees'][$key]['children'] ) ? absint( $post_data['fees'][$key]['children'] ) : 0;
					$children_to_add           += $extra_children;
					$children_to_add           = $children_to_add > $max_children ? $max_children : $children_to_add;

					for ( $i = 0; $i < $qty; $i++ ) {
						$guests[$i]['children'] = $children_to_add;
					}
				}
			}
		} else {
			if ( isset( $post_data['adults'][ $key ] ) ) {
				$adults_to_add    = absint( $post_data[ 'adults' ][ $key ] );
				$adults_to_add    = $adults_to_add > $max_guests ? $max_guests : $adults_to_add;

				for ( $i = 0; $i < $qty; $i++ ) {
					$guests[$i]['adults'] = $adults_to_add;
				}
			}

			if ( isset( $post_data['children'][ $key ] ) ) {
				$children_to_add    = absint( $post_data[ 'children' ][ $key ] );
				$children_to_add    = $children_to_add > $max_children ? $max_children : $children_to_add;

				for ( $i = 0; $i < $qty; $i++ ) {
					$guests[$i]['children'] = $children_to_add;
				}
			}
		}

		return $guests;
	}
}

endif;

HTL_Form_Functions::init();

<?php
/**
 * Hotelier New Reservation Page.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_New_Reservation' ) ) :

/**
 * HTL_Admin_New_Reservation Class
 */
class HTL_Admin_New_Reservation {

	/**
	 * Array of posted form data.
	 *
	 * @var array
	 */
	protected static $form_data = array();

	/**
	 * Array of rooms.
	 *
	 * @var array
	 */
	protected static $rooms = array();

	/**
	 * Checkin date.
	 *
	 * @var string
	 */
	protected static $checkin;

	/**
	 * Coupon ID.
	 *
	 * @var string
	 */
	protected static $coupon_id;

	/**
	 * Checkout date.
	 *
	 * @var string
	 */
	protected static $checkout;

	/**
	 * Force booking.
	 *
	 * @var bool
	 */
	protected static $force_booking = false;

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'new_reservation_action' ), 20 );
	}

	/**
	 * Show the new reservation page
	 */
	public static function output() {
		include_once( 'views/html-admin-new-reservation.php' );
	}

	/**
	 * Process the booking form.
	 */
	public static function new_reservation_action() {
		if ( isset( $_POST[ 'hotelier_admin_add_new_reservation' ] ) ) {

			ob_start();

			try {
				if ( empty( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'hotelier_admin_process_new_reservation' ) ) {
					throw new Exception( esc_html__( 'We were unable to process your reservation, please try again.', 'wp-hotelier' ) );
				}

				// Prevent timeout
				@set_time_limit( 0 );

				// Validate fields
				foreach ( $_POST as $key => $value ) {
					self::$form_data[ $key ] = isset( $_POST[ $key ] ) ? ( is_array( $_POST[ $key ] ) ? array_map( 'sanitize_text_field', $_POST[ $key ] ) : sanitize_text_field( $_POST[ $key ] ) ) : '';

					if ( $key == 'room' ) {

						foreach ( $_POST[ $key ] as $index => $room_value ) {
							$room_id_index = explode( '-', $room_value );

							$qty = absint( $_POST[ 'room_qty' ][ $index ] );

							$item_to_add = array(
								'room_id'  => $room_id_index[ 0 ],
								'rate_id'  => $room_id_index[ 1 ],
								'qty'      => $qty,
								'guests'   => array(),
								'fees'     => array(),
								'extras'   => array(),
							);

							// Calculate guests to add
							$item_to_add['guests'] = self::calculate_guests_to_add( $item_to_add, $index, $qty );

							if ( isset( $_POST[ 'fees' ][ $index ] ) && isset( $_POST[ 'fees' ][ $index ][ $room_id_index[ 0 ] ] ) ) {
								$item_to_add[ 'fees' ] = $_POST[ 'fees' ][ $index ][ $room_id_index[ 0 ] ];
							}

							if ( isset( $_POST[ 'extras' ][ $index ] ) && isset( $_POST[ 'extras' ][ $index ][ $room_id_index[ 0 ] ] ) ) {
								$item_to_add[ 'extras' ] = $_POST[ 'extras' ][ $index ][ $room_id_index[ 0 ] ];
							}

							self::$rooms[] = $item_to_add;
						}

					} elseif ( $key == 'from' ) {

						self::$checkin = sanitize_text_field( $_POST[ 'from' ] );
						add_filter( 'hotelier_advanced_extras_get_checkin_date', array( __CLASS__, 'apply_checkin_to_advanced_extras' ) );

					} elseif ( $key == 'to' ) {

						self::$checkout = sanitize_text_field( $_POST[ 'to' ] );
						add_filter( 'hotelier_advanced_extras_get_checkout_date', array( __CLASS__, 'apply_checkout_to_advanced_extras' ) );

					} elseif ( $key == 'coupon_id' ) {

						self::$coupon_id = $_POST[ 'coupon_id' ];
					}
				}

				foreach ( HTL_Meta_Box_Reservation_Data::get_guest_details_fields() as $key => $field ) {
					// Validation: Required fields
					if ( isset( $field[ 'required' ] ) && $field[ 'required' ] && empty( self::$form_data[ $key ] ) ) {
						throw new Exception( sprintf( esc_html__( 'Please fill the required fields.', 'wp-hotelier' ) ) );
					}
				}

				// Force booking?
				self::$force_booking = isset( $_POST[ 'force_booking' ] ) && $_POST[ 'force_booking' ] ? true : false;

				if ( self::$force_booking ) {
					add_filter( 'hotelier_booking_minimum_nights', '__return_true' );
					add_filter( 'hotelier_booking_maximum_nights', '__return_zero' );
					add_filter( 'hotelier_check_min_nights_passed', '__return_true' );
					add_filter( 'hotelier_check_max_nights_passed', '__return_true' );
				}

				// Check checkin and checkout dates
				if ( ! HTL_Formatting_Helper::is_valid_checkin_checkout( self::$checkin, self::$checkout, self::$force_booking ) ) {
					throw new Exception( esc_html__( 'Sorry, this room is not available on the given dates.', 'wp-hotelier' ) );
				}

				// Init HTL_Cart_Totals()
				$cart_totals = new HTL_Cart_Totals( self::$checkin, self::$checkout, self::$coupon_id );

				// Add rooms to the reservation
				foreach ( self::$rooms as $room ) {
					$added_to_cart = $cart_totals->add_to_cart( $room[ 'room_id' ], $room[ 'qty' ], $room[ 'rate_id' ], $room[ 'guests' ], $room[ 'fees' ], $room[ 'extras' ], self::$force_booking );

					if ( is_array( $added_to_cart ) && isset( $added_to_cart[ 'error' ] ) ) {
						$error = $added_to_cart[ 'message' ] ? esc_html( $added_to_cart[ 'message' ] ) : esc_html__( 'Sorry, this room is not available.', 'wp-hotelier' );

						throw new Exception( $error );
					}
				}

				do_action( 'hotelier_save_manual_reservation_before_calculate_totals' );

				// Calculate totals
				$cart_totals->calculate_totals();

				// Create the reservation
				$reservation_id = self::create_reservation( $cart_totals );

				if ( is_wp_error( $reservation_id ) ) {
					throw new Exception( $reservation_id->get_error_message() );
				}

				$reservation = htl_get_reservation( $reservation_id );

				// Mark reservation as confirmed if the option is enabled
				if ( ! ( $cart_totals->required_deposit > 0 ) && htl_get_option( 'booking_admin_reservation_confirmed', false ) ) {
					$reservation->payment_complete();
				}

				if ( self::$force_booking ) {
					remove_filter( 'hotelier_booking_minimum_nights', '__return_true' );
					remove_filter( 'hotelier_booking_maximum_nights', '__return_zero' );
					remove_filter( 'hotelier_check_min_nights_passed', '__return_true' );
					remove_filter( 'hotelier_check_max_nights_passed', '__return_true' );
				}

				echo '<div class="htl-ui-notice htl-ui-notice--success htl-ui-notice--new-reservation-message" style="display:none;"><p class="htl-ui-notice__text htl-ui-notice__text--success">' . esc_html__( 'Reservation created' ) . '</p></div>';

			} catch ( Exception $e ) {
				if ( ! empty( $e ) ) {
					echo '<div class="htl-ui-notice htl-ui-notice--error htl-ui-notice--new-reservation-message" style="display:none;"><p class="htl-ui-notice__text htl-ui-notice__text--error">' . esc_html( $e->getMessage() ) . '</p></div>';
				}
			}
		}
	}

	/**
	 * Create the reservation.
	 *
	 * Error codes:
	 * 		400 - Cannot insert reservation into the database (reservations_items table)
	 * 		401 - Cannot insert booking into the database (bookings table)
	 * 		402 - Cannot populate room_bookings
	 * 		403 - Cannot add item to reservation
	 *
	 * @access public
	 * @throws Exception
	 * @return int|WP_ERROR
	 */
	public static function create_reservation( $cart_totals ) {
		global $wpdb;

		try {
			// Start transaction if available
			$wpdb->query( 'START TRANSACTION' );

			$reservation_data = array(
				'status'           => 'pending',
				'guest_name'       => self::get_formatted_guest_full_name(),
				'email'            => self::get_form_data_field( 'email' ),
				'special_requests' => self::get_form_data_field( 'special_requests' ),
				'created_via'      => 'admin'
			);

			$reservation = htl_create_reservation( $reservation_data );

			if ( is_wp_error( $reservation ) ) {
				throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 400 ) );
			} else {
				$reservation_id = $reservation->id;
				$booking_id = htl_add_booking( $reservation_id, self::$checkin, self::$checkout, 'pending', self::$force_booking );

				if ( ! $booking_id ) {
					throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 401 ) );
				}
			}

			// Guest address
			$guest_address = array();

			foreach ( HTL_Meta_Box_Reservation_Data::get_guest_details_fields() as $address_key => $address_value ) {
				$guest_address[ $address_key ] = self::get_form_data_field( $address_key );
			}

			foreach ( $cart_totals->cart_contents as $cart_item_key => $values ) {
				for ( $i = 0; $i < $values[ 'quantity' ]; $i++ ) {
					$rooms_bookings_id = htl_populate_rooms_bookings( $reservation_id, $values[ 'room_id' ] );

					if ( ! $rooms_bookings_id ) {
						throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 402 ) );
					}
				}

				// Since the version 1.2.0, the calculated deposit is saved into
				// the reservation meta (as well as the percent deposit)
				$deposit = round( ( $values[ 'price' ] * $values[ 'deposit' ] ) / 100 );
				$deposit = array(
					'deposit'         => $deposit,
					'percent_deposit' => $values[ 'deposit' ],
				);
				$deposit = apply_filters( 'hotelier_get_item_deposit_for_reservation', $deposit, $values );

				// Default adults/children for this room
				$adults   = $values[ 'max_guests' ];
				$children = 0;

				if ( isset( $values['guests'] ) && is_array( $values['guests'] ) ) {
					$guests   = $values['guests'];
					$adults   = array();
					$children = array();

					for ( $i = 0; $i < $values[ 'quantity' ]; $i++ ) {
						// Default fallback values
						$adults[$i]   = $values[ 'max_guests' ];
						$children[$i] = 0;

						if ( isset( $guests[$i] ) && isset( $guests[$i]['adults'] ) ) {
							$adults[$i] = $guests[$i]['adults'];
						}

						if ( isset( $guests[$i] ) && isset( $guests[$i]['children'] ) ) {
							$children[$i] = $guests[$i]['children'];
						}
					}
				}

				// Fees
				$values[ 'fees' ] = isset( $values[ 'fees' ] ) && is_array( $values[ 'fees' ] ) ? $values[ 'fees' ] : array();

				// Extras
				$values[ 'extras' ] = isset( $values[ 'extras' ] ) && is_array( $values[ 'extras' ] ) ? $values[ 'extras' ] : array();

				$item_id = $reservation->add_item(
					$values[ 'data' ],
					$values[ 'quantity' ],
					array(
						'rate_name'       => $values[ 'rate_name' ],
						'rate_id'         => $values[ 'rate_id' ],
						'max_guests'      => $values[ 'max_guests' ],
						'price'           => $values[ 'price' ],
						'price_without_extras' => $values[ 'price_without_extras' ],
						'total_without_extras' => $values[ 'total_without_extras' ],
						'percent_deposit' => $deposit[ 'percent_deposit' ],
						'deposit'         => $deposit[ 'deposit' ],
						'is_cancellable'  => $values[ 'is_cancellable' ],
						'adults'          => $adults,
						'children'        => $children,
						'fees'            => $values[ 'fees' ],
						'extras'               => $values[ 'extras' ],

					)
				);

				if ( ! $item_id ) {
					throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 403 ) );
				}
			}

			$reservation->set_checkin( self::$checkin );
			$reservation->set_checkout( self::$checkout );
			$reservation->set_address( $guest_address );
			$reservation->set_arrival_time( '-1' );
			$reservation->set_booking_method( 'manual-booking' );
			$reservation->set_subtotal( $cart_totals->subtotal );
			$reservation->set_tax_total( $cart_totals->tax_total );
			$reservation->set_total( $cart_totals->total );
			$reservation->set_deposit( $cart_totals->required_deposit );

			// Save coupon data
			if ( htl_coupons_enabled() ) {
				$coupon_id = $cart_totals->coupon_id;

				if ( absint( $coupon_id ) > 0 ) {
					$coupon      = htl_get_coupon( $coupon_id );
					$coupon_code = $coupon->get_code();

					// Check if coupon is valid
					$can_apply_coupon = htl_can_apply_coupon( $coupon_id, self::$force_booking );

					if ( isset( $can_apply_coupon['can_apply'] ) && $can_apply_coupon['can_apply'] ) {
						$reservation->set_discount_total( $cart_totals->discount_total );
						$reservation->set_coupon_id( $coupon_id );
						$reservation->set_coupon_code( $coupon_code );
					} else {
						$reason = isset( $can_apply_coupon['reason'] ) ? $can_apply_coupon['reason'] : false;
						$reason = $reason ? $reason : esc_html__( 'This coupon cannot be applied.', 'wp-hotelier' );

						throw new Exception( $reason );
					}
				}
			}

			// Add a note to the reservation
			$reservation->add_reservation_note( esc_html__( 'Reservation manually created by admin.', 'wp-hotelier' ) );

			// If we got here, the reservation was created without problems!
			$wpdb->query( 'COMMIT' );

		} catch ( Exception $e ) {

			// There was an error adding reservation data
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error( 'booking-error', $e->getMessage() );
		}

		return $reservation_id;
	}

	/**
	 * Get a form field value after sanitization and validation.
	 * @param string $field
	 * @return string
	 */
	public static function get_form_data_field( $field ) {
		return isset( self::$form_data[ $field ] ) ? self::$form_data[ $field ] : '';
	}

	/**
	 * Get a formatted guest full name.
	 *
	 * @return string
	 */
	public static function get_formatted_guest_full_name() {
		return sprintf( '%1$s %2$s', self::get_form_data_field( 'first_name' ), self::get_form_data_field( 'last_name' ) );
	}

	/**
	 * Calculate guests to add
	 */
	private static function calculate_guests_to_add( $item_to_add, $key, $qty ) {
		$room_id      = $item_to_add['room_id'];
		$_room        = htl_get_room( $room_id );
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
			if ( isset( $_POST['fees'][ $key ] ) ) {
				if ( hotelier_aps_room_has_extra_adults( $_room ) ) {
					$adults_included_in_rate = absint( get_post_meta( $_room->id, '_seasons_extra_person_fees_adults_included', true ) );
					$adults_to_add           = $adults_included_in_rate;
					$extra_adults            = isset( $_POST['fees'][$key][$room_id] ) && isset( $_POST['fees'][$key][$room_id]['adults'] ) ? absint( $_POST['fees'][$key][$room_id]['adults'] ) : 0;
					$adults_to_add           += $extra_adults;
					$adults_to_add           = $adults_to_add > $max_guests ? $max_guests : $adults_to_add;

					for ( $i = 0; $i < $qty; $i++ ) {
						$guests[$i]['adults'] = $adults_to_add;
					}
				}

				if ( hotelier_aps_room_has_extra_children( $_room ) ) {
					$children_included_in_rate = absint( get_post_meta( $_room->id, '_seasons_extra_person_fees_children_included', true ) );
					$children_to_add           = $children_included_in_rate;
					$extra_children            = isset( $_POST['fees'][$key][$room_id] ) && isset( $_POST['fees'][$key][$room_id]['children'] ) ? absint( $_POST['fees'][$key][$room_id]['children'] ) : 0;
					$children_to_add           += $extra_children;
					$children_to_add           = $children_to_add > $max_guests ? $max_guests : $children_to_add;

					for ( $i = 0; $i < $qty; $i++ ) {
						$guests[$i]['children'] = $children_to_add;
					}
				}
			}
		} else {
			if ( isset( $_POST['room_adults'][$key] ) ) {
				$adults_to_add    = absint( $_POST['room_adults'][$key] );
				$adults_to_add    = $adults_to_add > $max_guests ? $max_guests : $adults_to_add;

				for ( $i = 0; $i < $qty; $i++ ) {
					$guests[$i]['adults'] = $adults_to_add;
				}
			}

			if ( isset( $_POST['room_children'][ $key ] ) ) {
				$children_to_add    = absint( $_POST['room_children'][$key] );
				$children_to_add    = $children_to_add > $max_children ? $max_children : $children_to_add;

				for ( $i = 0; $i < $qty; $i++ ) {
					$guests[$i]['children'] = $children_to_add;
				}
			}
		}

		return $guests;
	}

	/**
	 * Pass checkin to Advanced Extras extension.
	 *
	 * @return string
	 */
	public static function apply_checkin_to_advanced_extras() {
		return self::$checkin;
	}

	/**
	 * Pass checkout to Advanced Extras extension.
	 *
	 * @return string
	 */
	public static function apply_checkout_to_advanced_extras() {
		return self::$checkout;
	}
}

endif;

HTL_Admin_New_Reservation::init();

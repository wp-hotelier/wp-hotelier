<?php
/**
 * Hotelier New Reservation Page.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.2.0
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
	 * Checkout date.
	 *
	 * @var string
	 */
	protected static $checkout;

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

							$item_to_add = array(
								'room_id' => $room_id_index[ 0 ],
								'rate_id' => $room_id_index[ 1 ],
								'qty'     => absint( $_POST[ 'room_qty' ][ $index ] ),
								'fees'    => array(),
							);

							if ( isset( $_POST[ 'fees' ][ $index ] ) && isset( $_POST[ 'fees' ][ $index ][ $room_id_index[ 0 ] ] ) ) {
								$item_to_add[ 'fees' ] = $_POST[ 'fees' ][ $index ][ $room_id_index[ 0 ] ];
							}

							self::$rooms[] = $item_to_add;
						}

					} elseif ( $key == 'from' ) {

						self::$checkin = sanitize_text_field( $_POST[ 'from' ] );

					} elseif ( $key == 'to' ) {

						self::$checkout = sanitize_text_field( $_POST[ 'to' ] );
					}
				}

				foreach ( HTL_Meta_Box_Reservation_Data::get_guest_details_fields() as $key => $field ) {
					// Validation: Required fields
					if ( isset( $field[ 'required' ] ) && $field[ 'required' ] && empty( self::$form_data[ $key ] ) ) {
						throw new Exception( sprintf( esc_html__( 'Please fill the required fields.', 'wp-hotelier' ) ) );
					}
				}

				// Check checkin and checkout dates
				if ( ! HTL_Formatting_Helper::is_valid_checkin_checkout( self::$checkin, self::$checkout ) ) {
					throw new Exception( esc_html__( 'Sorry, this room is not available on the given dates.', 'wp-hotelier' ) );
				}

				// Init HTL_Cart_Totals()
				$cart_totals = new HTL_Cart_Totals( self::$checkin, self::$checkout );

				// Add rooms to the reservation
				foreach ( self::$rooms as $room ) {
					$added_to_cart = $cart_totals->add_to_cart( $room[ 'room_id' ], $room[ 'qty' ], $room[ 'rate_id' ], $room[ 'fees' ] );

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
				$booking_id = htl_add_booking( $reservation_id, self::$checkin, self::$checkout, 'pending' );

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

				// Fees
				$values[ 'fees' ] = isset( $values[ 'fees' ] ) && is_array( $values[ 'fees' ] ) ? $values[ 'fees' ] : array();

				$item_id = $reservation->add_item(
					$values[ 'data' ],
					$values[ 'quantity' ],
					array(
						'rate_name'       => $values[ 'rate_name' ],
						'rate_id'         => $values[ 'rate_id' ],
						'max_guests'      => $values[ 'max_guests' ],
						'price'           => $values[ 'price' ],
						'total'           => $values[ 'total' ],
						'percent_deposit' => $deposit[ 'percent_deposit' ],
						'deposit'         => $deposit[ 'deposit' ],
						'is_cancellable'  => $values[ 'is_cancellable' ],
						'fees'            => $values[ 'fees' ],
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
}

endif;

HTL_Admin_New_Reservation::init();

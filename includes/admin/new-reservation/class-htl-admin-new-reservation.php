<?php
/**
 * Hotelier New Reservation Page.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  1.7.0
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
	 * Contains an array of reservation items.
	 *
	 * @var array
	 */
	protected static $reservation_contents = array();

	/** @var int The total cost of the reservation. */
	protected static  $reservation_contents_total;

	/** @var int The required deposit. */
	protected static  $required_deposit;

	/** @var int Tax total. */
	protected static  $tax_total;

	/** @var int Subtotal. */
	protected static  $subtotal;

	/** @var int Grand total. */
	protected static  $total;

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
							self::$rooms[] = array( 'room_id' => $room_id_index[ 0 ], 'rate_id' => $room_id_index[ 1 ], 'qty' => absint( $_POST[ 'room_qty' ][ $index ] ) );
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

				// Add rooms to the reservation
				foreach ( self::$rooms as $room ) {
					self::add_to_reservation( $room );
				}

				// Calculate totals
				self::calculate_totals();

				// Create the reservation
				$reservation_id = self::create_reservation();

				if ( is_wp_error( $reservation_id ) ) {
					throw new Exception( $reservation_id->get_error_message() );
				}

				echo '<div class="updated"><p>' . esc_html__( 'Reservation created' ) . '</p></div>';

			} catch ( Exception $e ) {
				if ( ! empty( $e ) ) {
					echo '<div class="error"><p>' . esc_html( $e->getMessage() ) . '</p></div>';
				}
			}
		}
	}

	/**
	 * Add a room to the reservation.
	 */
	public static function add_to_reservation( $room ) {
		$room_id  = absint( $room[ 'room_id' ] );
		$rate_id  = absint( $room[ 'rate_id' ] );
		$quantity = absint( $room[ 'qty' ] );

		// Get the room
		$_room = htl_get_room( $room_id );

		if ( ! $_room->exists() ) {
			// Oops, check failed so throw an error (this this room does not exists)
			throw new Exception( esc_html__( 'Sorry, this room does not exists.', 'wp-hotelier' ) );
		}

		// Sanitity check
		if ( $quantity <= 0 || ! $_room || 'publish' !== $_room->post->post_status ) {
			throw new Exception();
		}

		// Check room is_available on the given dates
		if ( ! $_room->is_available( self::$checkin, self::$checkout, $quantity ) ) {
			throw new Exception( esc_html__( 'Sorry, this room is not available on the given dates.', 'wp-hotelier' ) );
		}

		// If a $rate_id > 0 is passed, then this is (technically) a variable room
		if ( $rate_id > 0 ) {

			if ( ! $_room->is_variable_room() ) {
				// Oops, check failed so throw an error (this is not a variable room)
				throw new Exception( esc_html__( 'Sorry, this room does not exists.', 'wp-hotelier' ) );
			}

			// Check if the room has this rate and get it (we need the slug)
			$rate_name = $_room->get_rate_name( $rate_id );

			// Final check - Check if the rate exists in the room_rate taxonomy
			// We need to make this check because the rate_name (term slug) is stored in a meta box (and we do not know if it still exists).
			if ( $rate_name && $_room->rate_term_exists( $rate_name ) ) {

				// Ok, we can load the variation
				$_variation = $_room->get_room_variation( $rate_id );

				// Deposit
				$deposit = $_variation->get_deposit();

				// Check if it is cancellable
				$is_cancellable = $_variation->is_cancellable();
			} else {

				// Oops, check failed so throw an error (rate does not exist in the room_rate taxonomy)
				throw new Exception( esc_html__( 'Sorry, this room does not exists.', 'wp-hotelier' ) );
			}
		} elseif ( $rate_id === 0 && $_room->is_variable_room() ) {
			// Oops, check failed so throw an error (passed rate_id = 0 but this is a variable room)
			throw new Exception( esc_html__( 'Sorry, this room does not exists.', 'wp-hotelier' ) );
		} else {
			// This is a standard room
			$rate_name  = false;
			$_variation = false;

			// Deposit
			$deposit = $_room->get_deposit();

			// Check if it is cancellable
			$is_cancellable = $_room->is_cancellable();
		}

		// Generate an ID based on room ID and rate ID - this also avoid duplicates
		$reservation_item_key = htl_generate_item_key( $room_id, $rate_id );

		self::$reservation_contents[ $reservation_item_key ] = array(
			'data'           => $_room,
			'room_id'        => $_room->id,
			'quantity'       => $quantity,
			'rate_id'        => $rate_id,
			'rate_name'      => $rate_name,
			'max_guests'     => $_room->get_max_guests(),
			'deposit'        => $deposit,
			'is_cancellable' => $is_cancellable,
		);

		return $reservation_item_key;
	}

	/**
	 * Calculate totals for the items in the reservation.
	 */
	public static function calculate_totals() {
		foreach ( self::$reservation_contents as $reservation_item_key => $values ) {
			$_room   = $values[ 'data' ];
			$rate_id = $values[ 'rate_id' ];
			$qty     = $values[ 'quantity' ];

			// Price for variable room - We already know that if we pass a $rate_id is a variable room ( in self::add_to_reservation() )
			if ( $rate_id ) {
				$_variation   = $_room->get_room_variation( $rate_id );
				$line_price   = $_variation->get_price( self::$checkin, self::$checkout );
				$line_deposit = $_variation->get_deposit();

			} else {
				// Price for standard room
				$line_price   = $_room->get_price( self::$checkin, self::$checkout );
				$line_deposit = $_room->get_deposit();
			}

			if ( ! $line_price ) {
				// Remove room from reservation if has not price and throw an error
				unset( self::$reservation_contents[ $reservation_item_key ] );
				throw new Exception( esc_html__( 'Sorry, this room cannot be reserved.', 'wp-hotelier' ) );
			}

			// The total price of the room
			$line_total  = $line_price * $qty;

			// The total required deposit of the room
			$line_to_pay = ( ( $line_price * $line_deposit ) / 100 );
			$line_to_pay = round( $line_to_pay ) * $qty;

			// This is the total deposit required to confirm a reservation
			// Deposits are per line (room)
			self::$required_deposit += $line_to_pay;

			// This is the total cost of the reservation (deposit included)
			self::$reservation_contents_total += $line_total;

			// Set prices
			self::$reservation_contents[ $reservation_item_key ][ 'price' ] = $line_price;
			self::$reservation_contents[ $reservation_item_key ][ 'total' ] = $line_total;
		}

		// Subtotal
		self::$subtotal  = self::$reservation_contents_total;

		// Calculate taxes
		self::$tax_total = htl_is_tax_enabled() ? htl_calculate_tax( self::$reservation_contents_total ) : 0;

		$total           = self::$reservation_contents_total + htl_calculate_tax( self::$reservation_contents_total );
		self::$total     = $total;
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
	public static function create_reservation() {
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

			foreach ( self::$reservation_contents as $reservation_item_key => $values ) {
				for ( $i = 0; $i < $values[ 'quantity' ]; $i++ ) {
					$rooms_bookings_id = htl_populate_rooms_bookings( $reservation_id, $values[ 'room_id' ] );

					if ( ! $rooms_bookings_id ) {
						throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 402 ) );
					}
				}

				$item_id = $reservation->add_item(
					$values[ 'data' ],
					$values[ 'quantity' ],
					array(
						'rate_name'  => $values[ 'rate_name' ],
						'max_guests' => $values[ 'max_guests' ],
						'price'      => $values[ 'price' ],
						'total'      => $values[ 'total' ],
						'deposit'    => $values[ 'deposit' ],
						// 'is_cancellable' => $values[ 'is_cancellable' ],
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
			$reservation->set_subtotal( self::$subtotal );
			$reservation->set_tax_total( self::$tax_total );
			$reservation->set_total( self::$total );
			$reservation->set_deposit( self::$required_deposit );

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

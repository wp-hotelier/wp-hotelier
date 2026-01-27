<?php
/**
 * Hotelier New Reservation Page.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.17.0
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
	 * Uses the htl_create_reservation_from_cart() utility function to create
	 * a reservation from calculated cart totals.
	 *
	 * @since 2.18.0 Refactored to use htl_create_reservation_from_cart().
	 *
	 * @access public
	 * @param HTL_Cart_Totals $cart_totals Calculated cart totals.
	 * @return int|WP_Error Reservation ID on success, WP_Error on failure.
	 */
	public static function create_reservation( $cart_totals ) {
		$current_user = wp_get_current_user();

		// Build guest address from form data.
		$guest_address = array();
		foreach ( HTL_Meta_Box_Reservation_Data::get_guest_details_fields() as $key => $val ) {
			$guest_address[ $key ] = self::get_form_data_field( $key );
		}

		$args = array(
			'checkin'          => self::$checkin,
			'checkout'         => self::$checkout,
			'guest_address'    => $guest_address,
			'special_requests' => self::get_form_data_field( 'special_requests' ),
			'arrival_time'     => -1,
			'created_via'      => 'admin',
			'admin_creator'    => $current_user->ID,
			'force_booking'    => self::$force_booking,
			'status'           => 'pending',
		);

		return htl_create_reservation_from_cart( $cart_totals, $args );
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

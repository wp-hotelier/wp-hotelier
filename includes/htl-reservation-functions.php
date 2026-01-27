<?php
/**
 * Hotelier Reservation Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.18.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning reservations.
 *
 * @param  mixed $the_reservation Post object or post ID of the reservation.
 * @return HTL_Reservation
 */
function htl_get_reservation( $the_reservation = false ) {
	return new HTL_Reservation( $the_reservation );
}

/**
 * Create a new reservation programmatically
 *
 * Returns a new reservation object on success which can then be used to add additional data.
 *
 * @return HTL_Reservation on success, WP_Error on failure
 */
function htl_create_reservation( $args = array() ) {
	$default_args = array(
		'status'           => '',
		'guest_name'       => '',
		'email'            => '',
		'special_requests' => null,
		'reservation_id'   => 0,
		'created_via'      => '',
		'admin_creator'    => '',
		'parent'           => 0
	);

	$args             = wp_parse_args( $args, $default_args );
	$reservation_data = array();

	if ( $args[ 'reservation_id' ] > 0 ) {
		$updating                 = true;
		$reservation_data[ 'ID' ] = $args[ 'reservation_id' ];
	} else {
		$updating                            = false;
		$reservation_data[ 'post_type' ]     = 'room_reservation';
		$reservation_data[ 'post_author' ]   = 1;
		$reservation_data[ 'post_status' ]   = 'htl-pending';
		$reservation_data[ 'ping_status' ]   = 'closed';
		$reservation_data[ 'post_title' ]    = wp_strip_all_tags( $args[ 'guest_name' ] );
		$reservation_data[ 'post_parent' ]   = absint( $args[ 'parent' ] );
	}

	if ( $args[ 'status' ] ) {
		if ( ! in_array( 'htl-' . $args[ 'status' ], array_keys( htl_get_reservation_statuses() ) ) ) {
			return new WP_Error( 'hotelier_invalid_reservation_status', esc_html__( 'Invalid reservation status', 'wp-hotelier' ) );
		}

		$reservation_data[ 'post_status' ]  = 'htl-' . $args[ 'status' ];
	}

	if ( ! is_null( $args[ 'special_requests' ] ) ) {
		$reservation_data[ 'post_excerpt' ] = $args[ 'special_requests' ];
	}

	if ( $updating ) {
		$reservation_id = wp_update_post( $reservation_data );
	} else {
		$reservation_id = wp_insert_post( apply_filters( 'hotelier_new_reservation_data', $reservation_data ), true );

		// insert the reservation ID before the title ( #123 - John Doe )
		$reservation_title = '#' . $reservation_id . ' - ' . get_the_title( $reservation_id );
		$reservation_id = wp_update_post( array( 'ID' => $reservation_id, 'post_title' => apply_filters( 'hotelier_new_reservation_title', sanitize_text_field( $reservation_title ) ) ) );
	}

	if ( is_wp_error( $reservation_id ) ) {
		return $reservation_id;
	}

	if ( ! $updating ) {
		update_post_meta( $reservation_id, '_reservation_key', 'htl_' . apply_filters( 'hotelier_generate_reservation_key', uniqid( 'reservation_' ) ) );
		update_post_meta( $reservation_id, '_reservation_guest_ip_address', htl_get_ip() );
		update_post_meta( $reservation_id, '_reservation_currency', htl_get_currency() );
		update_post_meta( $reservation_id, '_created_via', sanitize_text_field( $args[ 'created_via' ] ) );

		if ( isset( $args[ 'admin_creator' ] ) ) {
			update_post_meta( $reservation_id, '_admin_creator', sanitize_text_field( $args[ 'admin_creator' ] ) );
		}
	}

	return htl_get_reservation( $reservation_id );
}

/**
 * Update a reservation. Uses htl_create_reservation.
 *
 * @param  array $args
 * @return string | HTL_Reservation
 */
function htl_update_reservation( $args ) {
	if ( ! $args[ 'reservation_id' ] ) {
		return new WP_Error( esc_html__( 'Invalid reservation ID', 'wp-hotelier' ) );
	}

	return htl_create_reservation( $args );
}

/**
 * Get all reservation statuses
 *
 * @return array
 */
function htl_get_reservation_statuses() {
	$reservation_statuses = array(
		'htl-completed'  => esc_html_x( 'Completed', 'Reservation status', 'wp-hotelier' ),
		'htl-confirmed'  => esc_html_x( 'Confirmed', 'Reservation status', 'wp-hotelier' ),
		'htl-pending'    => esc_html_x( 'Pending', 'Reservation status', 'wp-hotelier' ),
		'htl-on-hold'    => esc_html_x( 'On Hold', 'Reservation status', 'wp-hotelier' ),
		'htl-cancelled'  => esc_html_x( 'Cancelled', 'Reservation status', 'wp-hotelier' ),
		'htl-refunded'   => esc_html_x( 'Refunded', 'Reservation status', 'wp-hotelier' ),
		'htl-failed'     => esc_html_x( 'Failed', 'Reservation status', 'wp-hotelier' )
	);

	return apply_filters( 'hotelier_reservation_statuses', $reservation_statuses );
}

/**
 * Adds reservation data to the bookings table.
 *
 * @access public
 * @param int $reservation_id
 * @param string $checkin
 * @param string $checkout
 * @param string $status
 * @param bool $force
 * @return mixed
 */
function htl_add_booking( $reservation_id, $checkin, $checkout, $status, $force = false ) {
	global $wpdb;

	$reservation_id = absint( $reservation_id );

	if ( ! $reservation_id || ! $checkin || ! $checkout ) {
		return false;
	}

	// Check dates
	if ( ! HTL_Formatting_Helper::is_valid_checkin_checkout( $checkin, $checkout, $force ) ) {
		return false;
	}

	// Check status
	if ( ! in_array( 'htl-' . $status, array_keys( htl_get_reservation_statuses() ) ) ) {
		$status = 'pending';
	}

	$wpdb->insert(
		$wpdb->prefix . "hotelier_bookings",
		array(
			'reservation_id' => $reservation_id,
			'checkin'        => $checkin,
			'checkout'       => $checkout,
			'status'         => $status,
		),
		array(
			'%s', '%s', '%s', '%s'
		)
	);

	$row_id = absint( $wpdb->insert_id );

	do_action( 'hotelier_new_booking_row', $row_id, $reservation_id, $checkin, $checkout );

	return $row_id;
}

/**
 * Populate rooms_bookings table.
 *
 * @access public
 * @param int $reservation_id
 * @param int $room_id
 * @return mixed
 */
function htl_populate_rooms_bookings( $reservation_id, $room_id ) {
	global $wpdb;

	$reservation_id = absint( $reservation_id );
	$room_id        = absint( $room_id );

	if ( ! $reservation_id || ! $room_id ) {
		return false;
	}

	$wpdb->insert(
		$wpdb->prefix . "hotelier_rooms_bookings",
		array(
			'reservation_id' => $reservation_id,
			'room_id'        => $room_id
		),
		array(
			'%d', '%d'
		)
	);

	$row_id = absint( $wpdb->insert_id );

	do_action( 'hotelier_new_rooms_bookings_row', $row_id, $reservation_id, $room_id );

	return $row_id;
}

/**
 * Add a room to a reservation.
 *
 * @access public
 * @param int $reservation_id
 * @return mixed
 */
function htl_add_reservation_item( $reservation_id, $item ) {
	global $wpdb;

	$reservation_id = absint( $reservation_id );

	if ( ! $reservation_id ) {
		return false;
	}

	$defaults = array(
		'reservation_item_name' => ''
	);

	$item = wp_parse_args( $item, $defaults );

	$wpdb->insert(
		$wpdb->prefix . "hotelier_reservation_items",
		array(
			'reservation_item_name' => $item[ 'reservation_item_name' ],
			'reservation_id'        => $reservation_id
		),
		array(
			'%s', '%d'
		)
	);

	$item_id = absint( $wpdb->insert_id );

	do_action( 'hotelier_new_reservation_item', $item_id, $item, $reservation_id );

	return $item_id;
}

/**
 * Add reservation item meta
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return bool
 */
function htl_add_reservation_item_meta( $item_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'reservation_item', $item_id, $meta_key, $meta_value, $unique );
}

/**
 * Update reservation item meta
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @return bool
 */
function htl_update_reservation_item_meta( $item_id, $meta_key, $meta_value ) {
	return update_metadata( 'reservation_item', $item_id, $meta_key, $meta_value );
}

/**
 * Get reservation item meta
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $meta_key
 * @param bool $single (default: false)
 * @return bool
 */
function htl_get_reservation_item_meta( $item_id, $key, $single = false ) {
	return get_metadata( 'reservation_item', $item_id, $key, $single );
}

/**
 * Get reservation item ID(s) from reservation items table
 */
function htl_get_reservation_items_id( $reservation_id ) {
	global $wpdb;

	$items         = array();
	$get_items_sql = $wpdb->prepare( "SELECT reservation_item_id FROM {$wpdb->prefix}hotelier_reservation_items WHERE reservation_id = %d ", $reservation_id );
	$line_items    = $wpdb->get_results( $get_items_sql );

	foreach ( $line_items as $item ) {
		$items[] = absint( $item->reservation_item_id );
	}

	return $items;
}

/**
 * Get room ID from reservation item
 */
function htl_get_room_id_from_reservation_item( $reservation_item ) {
	global $wpdb;

	$items         = array();
	$get_items_sql = $wpdb->prepare( "SELECT reservation_item_id FROM {$wpdb->prefix}hotelier_reservation_items WHERE reservation_id = %d ", $reservation_id );
	$line_items    = $wpdb->get_results( $get_items_sql );

	foreach ( $line_items as $item ) {
		$items[] = absint( $item->reservation_item_id );
	}

	return $items;
}

/**
 * Set table name
 */
function htl_taxonomy_metadata_wpdbfix() {
	global $wpdb;
	$itemmeta_name = 'hotelier_reservation_itemmeta';

	$wpdb->reservation_itemmeta = $wpdb->prefix . $itemmeta_name;

	$wpdb->tables[] = 'hotelier_reservation_itemmeta';
}
add_action( 'init', 'htl_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'htl_taxonomy_metadata_wpdbfix', 0 );

/**
 * Get the nice name for a reservation status
 *
 * @param  string $status
 * @return string
 */
function htl_get_reservation_status_name( $status ) {
	$statuses = htl_get_reservation_statuses();
	$status   = 'htl-' === substr( $status, 0, 4 ) ? substr( $status, 4 ) : $status;
	$status   = isset( $statuses[ 'htl-' . $status ] ) ? $statuses[ 'htl-' . $status ] : $status;

	return $status;
}

/**
 * Finds a Reservation ID based on an reservation key.
 *
 * @access public
 * @param string $reservation_key An reservation key has generated by
 * @return int The ID of an reservation, or 0 if the reservation could not be found
 */
function htl_get_reservation_id_by_reservation_key( $reservation_key ) {
	global $wpdb;

	// Faster than get_posts()
	$reservation_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_reservation_key' AND meta_value = %s", $reservation_key ) );

	return $reservation_id;
}

/**
 * Cancel all pending reservations after hold minutes.
 * Please note: for "pending" we mean "unpaid reservations" (pending and failed) in this case.
 *
 * @access public
 */
function htl_cancel_pending_reservations() {
	global $wpdb;

	$hold_minutes = htl_get_option( 'booking_hold_minutes', '60' );

	if ( $hold_minutes < 1 ) {
		return;
	}

	$date = date( "Y-m-d H:i:s", strtotime( '-' . absint( $hold_minutes ) . ' MINUTES', current_time( 'timestamp' ) ) );

	$pending_reservations = $wpdb->get_col( $wpdb->prepare( "
		SELECT posts.ID
		FROM   {$wpdb->posts} AS posts
		WHERE  posts.post_type   IN ('room_reservation')
		AND    posts.post_status IN ('htl-pending', 'htl-failed')
		AND    posts.post_modified < %s
	", $date ) );

	if ( $pending_reservations ) {
		foreach ( $pending_reservations as $pending_reservation ) {
			$reservation = htl_get_reservation( $pending_reservation );

			if ( apply_filters( 'hotelier_cancel_pending_reservation', 'booking' === get_post_meta( $pending_reservation, '_created_via', true ), $reservation ) ) {
				$reservation->update_status( 'cancelled', esc_html__( 'Time limit reached.', 'wp-hotelier' ) );
			}
		}
	}

	wp_clear_scheduled_hook( 'hotelier_cancel_pending_reservations' );
	wp_schedule_single_event( time() + ( absint( $hold_minutes ) * 60 ), 'hotelier_cancel_pending_reservations' );
}
add_action( 'hotelier_cancel_pending_reservations', 'htl_cancel_pending_reservations' );

/**
 * Process reservations after checkout date.
 *
 * @access public
 */
function htl_process_completed_reservations() {
	global $wpdb;

	$date = date( 'Y-m-d' );

	$completed_reservations = $wpdb->get_col( $wpdb->prepare( "
		SELECT reservations.reservation_id
		FROM {$wpdb->prefix}hotelier_bookings AS reservations
		WHERE 	reservations.checkout < %s
	", $date ) );

	if ( $completed_reservations ) {
		foreach ( $completed_reservations as $completed_reservation ) {
			$reservation = htl_get_reservation( $completed_reservation );

			// Skip already completed or cancelled/refunded reservations
			if ( $reservation->get_status() == 'completed' || $reservation->get_status() == 'cancelled' || $reservation->get_status() == 'refunded' ) {
				continue;
			}

			if ( $reservation->get_status() == 'confirmed' ) {
				$reservation->update_status( 'completed', esc_html__( 'Check-out date reached.', 'wp-hotelier' ) );
			} else {
				$reservation->update_status( 'cancelled', esc_html__( 'Check-out date reached.', 'wp-hotelier' ) );
			}
		}
	}

}
add_action( 'hotelier_process_completed_reservations', 'htl_process_completed_reservations' );

/**
 * Function that returns an array containing all reservations made on a specific date range.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @return array (reservation_id, checkin, checkout and status)
 */
function htl_get_all_reservations( $checkin, $checkout ) {
	$reservations = array();
	$all_rooms = apply_filters( 'hotelier_get_room_ids_for_reservations', htl_get_room_ids() );

	foreach ( $all_rooms as $room_id ) {
		$room_reservations        = htl_get_room_reservations( $room_id, $checkin, $checkout );
		$reservations[ $room_id ] = $room_reservations;
	}

	return $reservations;
}

/**
 * Create a reservation from calculated cart totals.
 *
 * @param HTL_Cart_Totals $cart_totals Calculated cart totals (after calculate_totals()).
 * @param array $args Reservation arguments.
 * @return int|WP_Error Reservation ID on success, WP_Error on failure.
 */
function htl_create_reservation_from_cart( $cart_totals, $args = array() ) {
	global $wpdb;

	$defaults = array(
		'checkin'              => '',
		'checkout'             => '',
		'guest_address'        => array(),
		'special_requests'     => '',
		'arrival_time'         => -1,
		'created_via'          => 'api',
		'admin_creator'        => 0,
		'force_booking'        => false,
		'status'               => 'pending',
		'payment_method'       => '',
		'payment_method_title' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	// Validate required dates
	if ( empty( $args['checkin'] ) || empty( $args['checkout'] ) ) {
		return new WP_Error( 'hotelier_reservation_error', esc_html__( 'Check-in and check-out dates are required.', 'wp-hotelier' ) );
	}

	// Validate guest address
	$guest_address = $args['guest_address'];
	if ( empty( $guest_address['first_name'] ) || empty( $guest_address['last_name'] ) || empty( $guest_address['email'] ) ) {
		return new WP_Error( 'hotelier_reservation_error', esc_html__( 'Guest first name, last name, and email are required.', 'wp-hotelier' ) );
	}

	$guest_full_name = sprintf( '%s %s', $guest_address['first_name'], $guest_address['last_name'] );

	// Start transaction if available
	$wpdb->query( 'START TRANSACTION' );

	try {
		// Create reservation
		$reservation_data = array(
			'status'           => $args['status'],
			'guest_name'       => $guest_full_name,
			'email'            => $guest_address['email'],
			'special_requests' => $args['special_requests'],
			'created_via'      => $args['created_via'],
			'admin_creator'    => $args['admin_creator'],
		);

		$reservation = htl_create_reservation( $reservation_data );

		if ( is_wp_error( $reservation ) ) {
			throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 400 ) );
		}

		$reservation_id = $reservation->id;

		// Add booking
		$booking_id = htl_add_booking(
			$reservation_id,
			$args['checkin'],
			$args['checkout'],
			$args['status'],
			$args['force_booking']
		);

		if ( ! $booking_id ) {
			throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 401 ) );
		}

		// Process each cart item
		foreach ( $cart_totals->cart_contents as $cart_item_key => $values ) {
			// Populate rooms_bookings for each unit
			for ( $i = 0; $i < $values['quantity']; $i++ ) {
				$rooms_bookings_id = htl_populate_rooms_bookings(
					$reservation_id,
					$values['room_id']
				);

				if ( ! $rooms_bookings_id ) {
					throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 402 ) );
				}
			}

			// Calculate the deposit for this item
			$deposit = round( ( $values['price'] * $values['deposit'] ) / 100 );
			$deposit = array(
				'deposit'         => $deposit,
				'percent_deposit' => $values['deposit'],
			);
			$deposit = apply_filters( 'hotelier_get_item_deposit_for_reservation', $deposit, $values );

			// Default adults/children for this room
			$adults   = $values['max_guests'];
			$children = 0;

			if ( isset( $values['guests'] ) && is_array( $values['guests'] ) ) {
				$guests   = $values['guests'];
				$adults   = array();
				$children = array();

				for ( $i = 0; $i < $values['quantity']; $i++ ) {
					$adults[ $i ]   = $values['max_guests'];
					$children[ $i ] = 0;

					if ( isset( $guests[ $i ] ) && isset( $guests[ $i ]['adults'] ) ) {
						$adults[ $i ] = $guests[ $i ]['adults'];
					}

					if ( isset( $guests[ $i ] ) && isset( $guests[ $i ]['children'] ) ) {
						$children[ $i ] = $guests[ $i ]['children'];
					}
				}
			}

			// Fees
			$values['fees'] = isset( $values['fees'] ) && is_array( $values['fees'] ) ? $values['fees'] : array();

			// Extras
			$values['extras'] = isset( $values['extras'] ) && is_array( $values['extras'] ) ? $values['extras'] : array();

			// Add item
			$item_id = $reservation->add_item(
				$values['data'],
				$values['quantity'],
				array(
					'rate_name'            => $values['rate_name'],
					'rate_id'              => $values['rate_id'],
					'max_guests'           => $values['max_guests'],
					'price'                => $values['price'],
					'price_without_extras' => $values['price_without_extras'],
					'total_without_extras' => $values['total_without_extras'],
					'percent_deposit'      => $deposit['percent_deposit'],
					'deposit'              => $deposit['deposit'],
					'is_cancellable'       => $values['is_cancellable'],
					'adults'               => $adults,
					'children'             => $children,
					'fees'                 => $values['fees'],
					'extras'               => $values['extras'],
				)
			);

			if ( ! $item_id ) {
				throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-hotelier' ), 403 ) );
			}
		}

		// Set reservation meta
		$reservation->set_checkin( $args['checkin'] );
		$reservation->set_checkout( $args['checkout'] );
		$reservation->set_address( $guest_address );
		$reservation->set_arrival_time( $args['arrival_time'] );
		$reservation->set_subtotal( $cart_totals->subtotal );
		$reservation->set_tax_total( $cart_totals->tax_total );
		$reservation->set_total( $cart_totals->total );
		$reservation->set_deposit( $cart_totals->required_deposit );

		// Set booking method
		$booking_method = 'manual-booking';
		if ( $args['created_via'] === 'api' ) {
			$booking_method = 'api-booking';
		} elseif ( $args['created_via'] === 'booking' ) {
			$booking_method = 'online-booking';
		}
		$reservation->set_booking_method( $booking_method );

		// Set payment method
		if ( ! empty( $args['payment_method'] ) ) {
			$reservation->set_payment_method( $args['payment_method'], $args['payment_method_title'] );
		}

		// Save coupon data
		if ( htl_coupons_enabled() ) {
			$coupon_id = $cart_totals->coupon_id;

			if ( absint( $coupon_id ) > 0 ) {
				$coupon      = htl_get_coupon( $coupon_id );
				$coupon_code = $coupon->get_code();

				// Check if coupon is valid
				$can_apply_coupon = htl_can_apply_coupon( $coupon_id, $args['force_booking'] );

				if ( isset( $can_apply_coupon['can_apply'] ) && $can_apply_coupon['can_apply'] ) {
					$reservation->set_discount_total( $cart_totals->discount_total );
					$reservation->set_coupon_id( $coupon_id );
					$reservation->set_coupon_code( $coupon_code );
				} else {
					$reason = isset( $can_apply_coupon['reason'] ) ? $can_apply_coupon['reason'] : esc_html__( 'This coupon cannot be applied.', 'wp-hotelier' );
					throw new Exception( $reason );
				}
			}
		}

		// Add a note to the reservation
		$note = '';
		if ( $args['created_via'] === 'admin' && $args['admin_creator'] ) {
			$admin_user = get_userdata( $args['admin_creator'] );
			$admin_name = $admin_user ? $admin_user->display_name : esc_html__( 'admin', 'wp-hotelier' );
			$note       = sprintf( esc_html__( 'Reservation manually created by %s.', 'wp-hotelier' ), $admin_name );
		} elseif ( $args['created_via'] === 'api' ) {
			$note = esc_html__( 'Reservation created via REST API.', 'wp-hotelier' );
		}

		if ( $note ) {
			$reservation->add_reservation_note( $note );
		}

		$wpdb->query( 'COMMIT' );

		do_action( 'hotelier_reservation_created_from_cart_totals', $reservation, $cart_totals, $args );

		return $reservation_id;

	} catch ( Exception $e ) {
		$wpdb->query( 'ROLLBACK' );
		return new WP_Error( 'hotelier_reservation_error', $e->getMessage() );
	}
}

/**
 * Get reservations by guest email.
 *
 * @since 1.6.0
 * @param array $email
 * @return Array of reservation objects
 */
function htl_get_reservations_by_email( $email ) {
	$reservations_objects = array();

	if ( ! $email ) {
		return $reservations_objects;
	}

	$query_args = array(
		'post_type'           => 'room_reservation',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'meta_query'          => array(
			array(
				'key'   => '_guest_email',
				'value' => $email,
			),
		),
	);

	$reservations = new WP_Query( $query_args );

	if ( $reservations->have_posts() ) {

		while ( $reservations->have_posts() ) {
			$reservations->the_post();
			global $post;

			$reservations_objects[] = htl_get_reservation( $post->ID );
		}

		wp_reset_postdata();
	}

	return $reservations_objects;
}

<?php
/**
 * Reservation Class.
 *
 * @author   Lollum
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Reservation' ) ) :

/**
 * HTL_Reservation Class
 */
class HTL_Reservation {
	/**
	 * The Reservation (post) ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * $post Stores post data
	 *
	 * @var $post WP_Post
	 */
	public $post = null;

	/** @public string Reservation Date */
	public $reservation_date = '';

	/** @public string Customer Message (excerpt) */
	public $guest_special_requests = '';

	/** @public string Reservation Status */
	public $post_status = '';

	/** @protected string Formatted address. Accessed via get_formatted_guest_address() */
	protected $formatted_guest_address = '';

	/**
	 * Get things going
	 */
	public function __construct( $reservation ) {
		if ( is_numeric( $reservation ) ) {
			$this->id   = absint( $reservation );
			$this->post = get_post( $this->id );
			$this->get_reservation( $this->id );
		} elseif ( $reservation instanceof HTL_Reservation ) {
			$this->id   = absint( $reservation->id );
			$this->post = $reservation->post;
			$this->get_reservation( $this->id );
		} elseif ( isset( $reservation->ID ) ) {
			$this->id   = absint( $reservation->ID );
			$this->post = $reservation;
			$this->get_reservation( $this->id );
		}
	}

	/**
	 * __get function.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		// Get values or default if not set
		if ( 'status' === $key ) {
			$value = $this->get_status();
		} else {
			$value = get_post_meta( $this->id, '_' . $key, true );
		}

		return $value;
	}

	/**
	 * Gets the reservation number for display (by default, reservation ID)
	 *
	 * @return string
	 */
	public function get_reservation_number() {
		return apply_filters( 'hotelier_reservation_number', $this->id, $this );
	}

	/**
	 * Set the booking method
	 *
	 * @param array $booking_method Booking method
	 */
	public function set_booking_method( $booking_method ) {
		update_post_meta( $this->id, '_booking_method', $booking_method );
	}

	/**
	 * Set the payment method for the reservation
	 *
	 * @param HTL_Payment_Gateway $payment_method
	 */
	public function set_payment_method( $payment_method ) {

		if ( is_object( $payment_method ) ) {
			update_post_meta( $this->id, '_payment_method', $payment_method->id );
			update_post_meta( $this->id, '_payment_method_title', $payment_method->get_title() );
		}
	}

	/**
	 * Get the payment method used for the reservation
	 */
	public function get_payment_method() {
		return get_post_meta( $this->id, '_payment_method', true );
	}

	/**
	 * Get the title of the payment method used for the reservation
	 */
	public function get_payment_method_title() {
		return get_post_meta( $this->id, '_payment_method_title', true );
	}

	/**
	 * Set the guest address
	 *
	 * @param array $address Address data
	 */
	public function set_address( $address ) {

		foreach ( $address as $key => $value ) {
			update_post_meta( $this->id, '_guest_' . $key, $value );
		}
	}

	/**
	 * Returns the requested address in raw
	 * @return array The stored address after filter
	 */
	public function get_address() {

		$address = array(
			'first_name' => $this->guest_first_name,
			'last_name'  => $this->guest_last_name,
			'email'      => $this->guest_email,
			'telephone'  => $this->guest_telephone,
			'country'    => $this->guest_country,
			'address1'   => $this->guest_address1,
			'address2'   => $this->guest_address2,
			'city'       => $this->guest_city,
			'state'      => $this->guest_state,
			'postcode'   => $this->guest_postcode
		);

		return apply_filters( 'hotelier_get_guest_address', $address, $this );
	}

	/**
	 * Get a formatted guest full name.
	 *
	 * @return string
	 */
	public function get_formatted_guest_full_name() {
		return sprintf( ( '%1$s %2$s' ),  $this->guest_first_name, $this->guest_last_name );
	}

	/**
	 * Get formatted guest address for the reservation.
	 *
	 * @return string
	 */
	public function get_formatted_guest_address() {
		$address  = $this->guest_first_name . ' ' . $this->guest_last_name . '<br>';

		if ( $this->guest_address1 ) {
			$address .= $this->guest_address1 . '<br>';
		}

		if ( $this->guest_address2 ) {
			$address .= $this->guest_address2 . '<br>';
		}

		if ( $this->guest_postcode ) {
			$address .= $this->guest_postcode . '<br>';
		}

		if ( $this->guest_city ) {
			$address .= $this->guest_city . '<br>';
		}

		if ( $this->guest_state ) {
			$address .= $this->guest_state . '<br>';
		}

		if ( $this->guest_country ) {
			$country_list = htl_get_country_codes();
			$country      = isset( $country_list[ $this->guest_country ] ) ? $country_list[ $this->guest_country ] : $this->guest_country;
			$address     .= $country;
		}

		return apply_filters( 'hotelier_get_formatted_guest_address', $address, $this );
	}

	/**
	 * Sets the guest arrival time
	 *
	 * @param array $time Arrival time
	 */
	public function set_arrival_time( $time ) {
		update_post_meta( $this->id, '_guest_arrival_time', sanitize_text_field( $time ) );
	}

	/**
	 * Gets the guest arrival time
	 */
	public function get_arrival_time() {
		return apply_filters( 'hotelier_get_guest_arrival_time', intval( $this->guest_arrival_time ), $this );
	}

	/**
	 * Gets the guest arrival time - formatted for display.
	 */
	public function get_formatted_arrival_time() {
		$time           = intval( $this->guest_arrival_time );
		$formatted_time = esc_html__( 'I don\'t know', 'wp-hotelier' );

		if ( $time >= 1 && $time <= 23 ) {
			$hour           = sprintf( '%02d', $time );
			$formatted_time = $hour . ':00 - ' . ( ( $hour + 1 ) % 24 ) . ':00';
		}

		return apply_filters( 'hotelier_get_guest_formatted_arrival_time', $formatted_time, $this );
	}

	/**
	 * Return the guest special requests.
	 *
	 * @return string
	 */
	public function get_guest_special_requests() {
		$message = $this->guest_special_requests;

		return apply_filters( 'hotelier_get_guest_special_requests', $message, $this );
	}

	/**
	 * Updates guest special requests (post excerpt)
	 *
	 * @param string $new_requests
	 */
	public function update_guest_special_requests( $new_requests ) {
		if ( ! $this->id ) {
			return;
		}

		$old_requests = $this->get_guest_special_requests();

		// Only update if they differ
		if ( $new_requests !== $old_requests ) {

			// Temporarily remove reservation save action to
			// avoid infinite loops
			remove_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Data::save', 20, 2 );

			// Update the reservation
			wp_update_post( array( 'ID' => $this->id, 'post_excerpt' => $new_requests ) );
			$this->guest_special_requests = $new_requests;

			// Re-enable save action
			add_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Data::save', 20, 2 );
		}
	}

	/**
	 * Gets a reservation from the db.
	 *
	 * @param int $id (default: 0)
	 * @return bool
	 */
	public function get_reservation( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $result = get_post( $id ) ) {
			$this->populate( $result );
			return true;
		}

		return false;
	}

	/**
	 * Populates a reservation from the loaded post data.
	 *
	 * @param mixed $result
	 */
	public function populate( $result ) {

		// Standard post data
		$this->id                     = $result->ID;
		$this->reservation_date       = $result->post_date;
		// $this->modified_date       = $result->post_modified;
		$this->guest_special_requests = $result->post_excerpt;
		$this->post_status            = $result->post_status;
	}

	/**
	 * Return the reservation statuses without htl- internal prefix.
	 *
	 * Queries get_post_status() directly to avoid having out of date statuses, if updated elsewhere.
	 *
	 * @return string
	 */
	public function get_status() {
		$this->post_status = get_post_status( $this->id );

		return apply_filters( 'hotelier_reservation_get_status', 'htl-' === substr( $this->post_status, 0, 4 ) ? substr( $this->post_status, 4 ) : $this->post_status, $this );
	}

	/**
	 * Checks the reservation status against a passed in status.
	 *
	 * @return bool
	 */
	public function has_status( $status ) {
		return apply_filters( 'hotelier_reservation_has_status', ( is_array( $status ) && in_array( $this->get_status(), $status ) ) || $this->get_status() === $status ? true : false, $this, $status );
	}

	/**
	 * Adds a note (comment) to the reservation
	 *
	 * @param string $note Note to add
	 * @return int Comment ID
	 */
	public function add_reservation_note( $note ) {
		$comment_author_email = strtolower( esc_html__( 'Hotelier', 'wp-hotelier' ) ) . '@';
		$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) : 'noreply.com';
		$comment_author_email = sanitize_email( $comment_author_email );

		$commentdata = apply_filters( 'hotelier_new_reservation_note_data', array(
			'comment_post_ID' => $this->id,
			'comment_author' => esc_html__( 'Hotelier', 'wp-hotelier' ),
			'comment_author_email' => $comment_author_email,
			'comment_author_url' => '',
			'comment_content' => esc_html( $note ),
			'comment_agent' => 'Hotelier',
			'comment_type' => 'reservation_note',
			'comment_parent' => 0,
			'comment_approved' => 1
		) );

		$comment_id = wp_insert_comment( $commentdata );

		return $comment_id;
	}

	/**
	 * Updates status of reservation
	 *
	 * @param string $new_status Status to change the reservation to. No internal htl- prefix is required.
	 * @param string $note (default: '') Optional note to add
	 * @param string $manual (default: '') Changed by admin or not
	 */
	public function update_status( $new_status, $note = '',  $manual = false ) {
		if ( ! $this->id ) {
			return;
		}

		// Standardise status names.
		$new_status = 'htl-' === substr( $new_status, 0, 4 ) ? substr( $new_status, 4 ) : $new_status;
		$old_status = $this->get_status();

		// Only update if they differ - and ensure post_status is a 'htl' status.
		if ( $new_status !== $old_status || ! in_array( $this->post_status, array_keys( htl_get_reservation_statuses() ) ) ) {

			// Refunded reservations cannot be restored
			if ( $old_status == 'refunded' ) {

				$this->add_reservation_note( trim( $note . ' ' . sprintf( esc_html__( 'Error: Trying to change the status from %1$s to %2$s. %1$s reservations cannot be restored or modified.', 'wp-hotelier' ), htl_get_reservation_status_name( $old_status ), htl_get_reservation_status_name( $new_status ) ) ) );

			} else {
				// We need to ensure that the rooms are still available on the given dates.
				// If not, change the status to 'cancelled'.
				if ( $old_status == 'cancelled' || $old_status == 'completed' ) {
					$cart_contents_quantity = array();
					$checkin                = $this->get_checkin();
					$checkout               = $this->get_checkout();
					$items                  = $this->get_items();

					$ret = true;

					foreach ( $items as $item ) {
						$_room = $this->get_room_from_item( $item );
						$qty   = $item[ 'qty' ];

						// Check the real quantity (rates have the same ID and stock)
						if ( isset( $cart_contents_quantity[ $_room->id ] ) ) {
							$real_qty = $cart_contents_quantity[ $_room->id ] + $qty;
						} else {
							$real_qty = $qty;
						}

						if ( ! $_room || ! $_room->exists() || $_room->post->post_status == 'trash' ) {
							$ret = false;
						}

						if ( ! $_room->is_available( $checkin, $checkout, $real_qty ) ) {
							$ret = false;
						}
					}

					$cart_contents_quantity[ $_room->id ] = $real_qty;

					if ( ! $ret ) {
						$this->add_reservation_note( trim( $note . ' ' . esc_html__( 'This reservation cannot be restored because one or more rooms are no longer available.', 'wp-hotelier' ) ) );

						return false;
					}
				}

				// Temporarily remove reservation save action to
				// avoid email triggering twice and other actions
				remove_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Save::save', 30, 2 );

				// Update the reservation
				wp_update_post( array( 'ID' => $this->id, 'post_status' => 'htl-' . $new_status ) );

				// Re-enable save action
				add_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Save::save', 30, 2 );

				$this->post_status = 'htl-' . $new_status;

				// Update the reservation table
				$this->update_table_status( $new_status );

				$this->add_reservation_note( trim( $note . ' ' . sprintf( esc_html__( 'Reservation status changed from %s to %s.', 'wp-hotelier' ), htl_get_reservation_status_name( $old_status ), htl_get_reservation_status_name( $new_status ) ) ) );

				// Status was changed
				do_action( 'hotelier_reservation_status_' . $new_status, $this->id );

				if ( $manual ) {
					do_action( 'hotelier_reservation_status_' . $old_status . '_to_' . $new_status . '_manual', $this->id );
				} else {
					do_action( 'hotelier_reservation_status_' . $old_status . '_to_' . $new_status, $this->id );
				}

				do_action( 'hotelier_reservation_status_changed', $this->id, $old_status, $new_status );
			}
		}
	}

	/**
	 * Update reservation status in table 'hotelier_bookings'.
	 *
	 * @param string $status Status to change the reservation to.
	 */
	public function update_table_status( $status ) {
		global $wpdb;

		if ( ! in_array( 'htl-' . $status, array_keys( htl_get_reservation_statuses() ) ) ) {
			$status = 'pending';
		}

		$wpdb->update(
			$wpdb->prefix . "hotelier_bookings",
			array(
				'status' => $status,
			),
			array(
				'reservation_id' => $this->id,
			),
			array(
				'%s',
			),
			array( '%d' )
		);
	}

	/**
	 * Update reservation dates in table 'hotelier_bookings'.
	 */
	public function update_table_reservation_dates( $checkin, $checkout ) {
		global $wpdb;

		$wpdb->update(
			$wpdb->prefix . "hotelier_bookings",
			array(
				'checkin'  => $checkin,
				'checkout' => $checkout,
			),
			array(
				'reservation_id' => $this->id,
			),
			array(
				'%s',
				'%s',
			),
			array( '%d' )
		);
	}

	/**
	 * Update modified date.
	 */
	public function update_last_modified() {
		global $wpdb;

		$post_modified     = current_time( 'mysql' );
		$post_modified_gmt = current_time( 'mysql', 1 );

		$wpdb->update(
			$wpdb->posts,
			array(
				'post_modified'     => $post_modified,
				'post_modified_gmt' => $post_modified_gmt,
			),
			array(
				'ID' => $this->id,
			),
			array(
				'%s',
				'%s',
			),
			array( '%d' )
		);
	}

	/**
	 * Get transaction id for the reservation.
	 *
	 * @return string
	 */
	public function get_transaction_id() {
		return get_post_meta( $this->id, '_transaction_id', true );
	}

	/**
	 * Add an item (room) to the reservation.
	 *
	 * @param HTL_Room $room
	 * @param int $qty Room quantity
	 * @param array $args
	 * @return int|bool Item ID or false
	 */
	public function add_item( $room, $qty = 1, $args = array() ) {
		$default_args = array(
			'rate_name'            => false,
			'rate_id'              => 0,
			'max_guests'           => 0,
			'price'                => 0,
			'price_without_extras' => 0,
			'total'                => 0,
			'total_without_extras' => 0,
			'deposit'              => 0,
			'percent_deposit'      => 0,
			'is_cancellable'       => true,
			'adults'               => false,
			'children'             => false,
			'fees'                 => array(),
			'extras'               => array(),
		);

		$args    = wp_parse_args( $args, $default_args );
		$item_id = htl_add_reservation_item( $this->id, array(
			'reservation_item_name' => $room->get_title()
		) );

		if ( ! $item_id ) {
			return false;
		}

		htl_add_reservation_item_meta( $item_id, '_qty', absint( $qty ) );
		htl_add_reservation_item_meta( $item_id, '_room_id', absint( $room->id ) );

		if ( $args[ 'rate_name' ] ) {
			htl_add_reservation_item_meta( $item_id, '_rate_name', $args[ 'rate_name' ] );
			htl_add_reservation_item_meta( $item_id, '_rate_id', absint( $args[ 'rate_id' ] ) );
		}

		htl_add_reservation_item_meta( $item_id, '_max_guests', absint( $args[ 'max_guests' ] ? $args[ 'max_guests' ] : 0 ) );
		htl_add_reservation_item_meta( $item_id, '_price', absint( $args[ 'price' ] ? $args[ 'price' ] : 0 ) );
		htl_add_reservation_item_meta( $item_id, '_price_without_extras', absint( $args[ 'price_without_extras' ] ? $args[ 'price_without_extras' ] : 0 ) );
		htl_add_reservation_item_meta( $item_id, '_total', absint( $args[ 'total' ] ? $args[ 'total' ] : 0 ) );
		htl_add_reservation_item_meta( $item_id, '_total_without_extras', absint( $args[ 'total_without_extras' ] ? $args[ 'total_without_extras' ] : 0 ) );
		htl_add_reservation_item_meta( $item_id, '_percent_deposit', absint( $args[ 'percent_deposit' ] ? $args[ 'percent_deposit' ] : 0 ) );
		htl_add_reservation_item_meta( $item_id, '_deposit', absint( $args[ 'deposit' ] ? $args[ 'deposit' ] : 0 ) );
		htl_add_reservation_item_meta( $item_id, '_is_cancellable', absint( $args[ 'is_cancellable' ] ? $args[ 'is_cancellable' ] : false ) );

		$adults = $args[ 'adults' ] && is_array( $args[ 'adults' ] ) ? array_map( 'absint', $args[ 'adults' ] ) : false;
		htl_add_reservation_item_meta( $item_id, '_adults', $adults );

		$children = $args[ 'children' ] && is_array( $args[ 'children' ] ) ? array_map( 'absint', $args[ 'children' ] ) : false;
		htl_add_reservation_item_meta( $item_id, '_children', $children );

		$fees = $args[ 'fees' ] && is_array( $args[ 'fees' ] ) ? $args[ 'fees' ] : false;
		htl_add_reservation_item_meta( $item_id, '_fees', $fees );

		$extras = $args[ 'extras' ] && is_array( $args[ 'extras' ] ) ? $args[ 'extras' ] : false;
		htl_add_reservation_item_meta( $item_id, '_extras', $extras );

		do_action( 'hotelier_reservation_add_item', $this->id, $item_id, $room, $qty, $args );

		return $item_id;
	}

	/**
	 * Return an array of items (rooms) within this reservation.
	 *
	 * @return array
	 */
	public function get_items() {
		global $wpdb;

		$items          = array();
		$get_items_sql  = $wpdb->prepare( "SELECT reservation_item_id, reservation_item_name FROM {$wpdb->prefix}hotelier_reservation_items WHERE reservation_id = %d ", $this->id );
		$line_items     = $wpdb->get_results( $get_items_sql );

		// Loop items
		foreach ( $line_items as $item ) {
			$items[ $item->reservation_item_id ][ 'name' ]      = $item->reservation_item_name;
			$items[ $item->reservation_item_id ][ 'item_meta' ] = $this->get_item_meta( $item->reservation_item_id );
			$items[ $item->reservation_item_id ]                = $this->expand_item_meta( $items[ $item->reservation_item_id ] );
		}

		return apply_filters( 'hotelier_reservation_get_items', $items, $this );
	}

	/**
	 * Expand item meta into the $item array.
	 * @param array $item before expansion
	 * @return array
	 */
	public function expand_item_meta( $item ) {
		// Reserved meta keys
		$reserved_item_meta_keys = array(
			'name',
			'item_meta',
			'qty',
			'room_id',
			'rate_name',
			'price',
			'total',
			'deposit'
		);

		// Expand item meta if set
		if ( ! empty( $item[ 'item_meta' ] ) ) {
			foreach ( $item[ 'item_meta' ] as $name => $value ) {
				if ( in_array( $name, $reserved_item_meta_keys ) ) {
					continue;
				}
				if ( '_' === substr( $name, 0, 1 ) ) {
					$item[ substr( $name, 1 ) ] = $value[ 0 ];
				}
			}
		}
		return $item;
	}

	/**
	 * Get reservation item meta.
	 *
	 * @param mixed $reservation_item_id
	 * @param string $key (default: '')
	 * @param bool $single (default: false)
	 * @return array|string
	 */
	public function get_item_meta( $reservation_item_id, $key = '', $single = false ) {
		return get_metadata( 'reservation_item', $reservation_item_id, $key, $single );
	}

	/**
	 * has_meta function for reservation items.
	 *
	 * @param string $reservation_item_id
	 * @return array of meta data
	 */
	public function has_meta( $reservation_item_id ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value, meta_id, reservation_item_id
			FROM {$wpdb->prefix}hotelier_reservation_itemmeta WHERE reservation_item_id = %d
			ORDER BY meta_id", absint( $reservation_item_id ) ), ARRAY_A );
	}

	/**
	 * Get a room.
	 *
	 * @param mixed $item
	 * @return HTL_Room
	 */
	public function get_room_from_item( $item ) {
		if ( ! empty( $item[ 'room_id' ]  ) ) {
			$_room = htl_get_room( $item[ 'room_id' ] );
		} else {
			$_room = false;
		}

		return apply_filters( 'hotelier_get_room_from_item', $_room, $item, $this );
	}

	/**
	 * Remove all line items from the reservation.
	 */
	public function remove_reservation_items() {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}hotelier_reservation_itemmeta itemmeta INNER JOIN {$wpdb->prefix}hotelier_reservation_items items WHERE itemmeta.reservation_item_id = items.reservation_item_id and items.reservation_id = %d", $this->id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}hotelier_reservation_items WHERE reservation_id = %d", $this->id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}hotelier_rooms_bookings WHERE reservation_id = %d", $this->id ) );
		// $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}hotelier_bookings WHERE reservation_id = %d", $this->id ) );
	}

	/**
	 * Gets line total - formatted for display.
	 *
	 * @param array  $item
	 * @return string
	 */
	public function get_formatted_line_total( $item ) {
		if ( ! isset( $item[ 'total' ] ) || ! isset( $item[ 'total' ] ) ) {
			return '';
		}

		$total           = $item[ 'total' ];
		$item_has_extras = false;

		if ( isset( $item[ 'extras' ] ) ) {
			$extras = maybe_unserialize( $item[ 'extras' ] );

			if ( is_array( $extras ) && count( $extras ) > 0 ) {
				$item_has_extras = true;
			}
		}

		if ( $item_has_extras && isset( $item[ 'total_without_extras' ] ) ) {
			$total = $item[ 'total_without_extras' ];
		}

		$total = htl_price( htl_convert_to_cents( $total ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_reservation_formatted_line_total', $total, $item, $this );
	}

	/**
	 * Get item calculated deposit.
	 *
	 * @param mixed $item
	 * @return int
	 */
	public function get_item_deposit( $item ) {
		$amount = round( ( $item[ 'price' ] * $item[ 'deposit' ] ) / 100 );

		return apply_filters( 'hotelier_get_item_deposit', $amount, $this );
	}

	/**
	 * Set a reservation total
	 *
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function set_total( $amount ) {
		update_post_meta( $this->id, '_reservation_total', absint( $amount ) );

		return true;
	}

	/**
	 * Gets reservation total.
	 *
	 * @return int
	 */
	public function get_total() {
		return absint( apply_filters( 'hotelier_reservation_amount_total', $this->reservation_total, $this ) );
	}

	/**
	 * Gets reservation total - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_total() {
		$amount = htl_price( htl_convert_to_cents( $this->get_total() ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_get_formatted_reservation_total', $amount, $this );
	}

	/**
	 * Mark a reservation as paid
	 *
	 * @return bool
	 */
	public function mark_as_paid() {
		update_post_meta( $this->id, '_reservation_marked_as_paid', true );
		$this->add_reservation_note( esc_html__( 'Reservation marked as paid.', 'wp-hotelier' ) );

		return true;
	}

	/**
	 * Mark a reservation as unpaid
	 *
	 * @return bool
	 */
	public function mark_as_unpaid() {
		delete_post_meta( $this->id, '_reservation_marked_as_paid', false );
		$this->add_reservation_note( esc_html__( 'Reservation marked as unpaid.', 'wp-hotelier' ) );

		return true;
	}

	/**
	 * Check if a reservation is marked as paid
	 *
	 * @return bool
	 */
	public function is_marked_as_paid() {
		return get_post_meta( $this->id, '_reservation_marked_as_paid', false );
	}

	/**
	 * Set a reservation deposit
	 *
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function set_deposit( $amount ) {
		update_post_meta( $this->id, '_reservation_deposit', absint( $amount ) );

		return true;
	}

	/**
	 * Gets reservation deposit.
	 *
	 * @return int
	 */
	public function get_deposit() {
		return absint( apply_filters( 'hotelier_reservation_amount_deposit', $this->reservation_deposit, $this ) );
	}

	/**
	 * Gets reservation deposit - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_deposit() {
		$amount = htl_price( htl_convert_to_cents( $this->get_deposit() ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_get_formatted_reservation_deposit', $amount, $this );
	}

	/**
	 * Set reservation paid deposit.
	 *
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function set_paid_deposit( $amount ) {
		update_post_meta( $this->id, '_reservation_paid_deposit', absint( $amount ) );

		return true;
	}

	/**
	 * Gets reservation paid deposit.
	 *
	 * @return int
	 */
	public function get_paid_deposit() {
		return absint( apply_filters( 'hotelier_reservation_paid_deposit', $this->reservation_paid_deposit, $this ) );
	}

	/**
	 * Gets reservation paid deposit - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_paid_deposit() {
		$amount = htl_price( htl_convert_to_cents( $this->get_paid_deposit() ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_get_formatted_reservation_paid_deposit', $amount, $this );
	}

	/**
	 * Set reservation manual charge.
	 *
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function set_remain_deposit_charge( $amount ) {
		update_post_meta( $this->id, '_reservation_remain_deposit_charge_amount', absint( $amount ) );
		update_post_meta( $this->id, '_reservation_remain_deposit_charge_date', current_time( 'timestamp' ) );
		return true;
	}

	/**
	 * Gets reservation remain deposit charge date.
	 *
	 * @return int
	 */
	public function get_remain_deposit_charge_date() {
		$date = $this->reservation_remain_deposit_charge_date ? $this->reservation_remain_deposit_charge_date : null;

		return $date;
	}

	/**
	 * Gets reservation remain deposit charge.
	 *
	 * @return int
	 */
	public function get_remain_deposit_charge() {
		$remain_deposit_charge = $this->reservation_remain_deposit_charge_amount ? $this->reservation_remain_deposit_charge_amount : 0;

		return apply_filters( 'hotelier_reservation_remain_deposit_charge', $remain_deposit_charge, $this );
	}

	/**
	 * Gets reservation remain deposit charge - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_remain_deposit_charge() {
		$amount = htl_price( htl_convert_to_cents( $this->get_remain_deposit_charge() ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_get_formatted_reservation_remain_deposit_charge', $amount, $this );
	}

	/**
	 * Set reservation subtotal
	 *
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function set_subtotal( $amount ) {
		update_post_meta( $this->id, '_reservation_subtotal', absint( $amount ) );

		return true;
	}

	/**
	 * Gets reservation subtotal.
	 *
	 * @return int
	 */
	public function get_subtotal() {
		return absint( apply_filters( 'hotelier_reservation_amount_subtotal', $this->reservation_subtotal, $this ) );
	}

	/**
	 * Gets reservation subtotal - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_subtotal() {
		$amount = htl_price( htl_convert_to_cents( $this->get_subtotal() ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_get_formatted_reservation_subtotal', $amount, $this );
	}

	/**
	 * Set reservation tax total
	 *
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function set_tax_total( $amount ) {
		update_post_meta( $this->id, '_reservation_tax_total', absint( $amount ) );

		return true;
	}

	/**
	 * Gets reservation tax total.
	 *
	 * @return int
	 */
	public function get_tax_total() {
		return absint( apply_filters( 'hotelier_reservation_amount_tax_total', $this->reservation_tax_total, $this ) );
	}

	/**
	 * Gets reservation tax total - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_tax_total() {
		$amount = htl_price( htl_convert_to_cents( $this->get_tax_total() ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_get_formatted_reservation_tax_total', $amount, $this );
	}

	/**
	 * Gets reservation balance due.
	 *
	 * @return int
	 */
	public function get_balance_due() {
		$amount = $this->is_marked_as_paid() ? 0 : $this->get_total() - $this->get_paid_deposit() - $this->get_remain_deposit_charge();

		return apply_filters( 'hotelier_reservation_balance_due', $amount, $this );
	}

	/**
	 * Gets reservation balance due - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_balance_due() {
		if ( $this->is_marked_as_paid() ) {
			$amount = '<del>' . htl_price( htl_convert_to_cents( $this->get_total() - $this->get_paid_deposit() - $this->get_remain_deposit_charge() ), $this->get_reservation_currency() ) . '</del> <ins>' . htl_price( htl_convert_to_cents( $this->get_balance_due() ), $this->get_reservation_currency() ) . '</ins>';
		} else {
			$amount = htl_price( htl_convert_to_cents( $this->get_balance_due() ), $this->get_reservation_currency() );
		}


		return apply_filters( 'hotelier_get_formatted_reservation_balance_due', $amount, $this );
	}

	/**
	 * Get totals for display on pages and in emails.
	 *
	 * @return array
	 */
	public function get_reservation_totals( $is_email = false ) {
		$total_rows = array();
		$discount_printed = false;

		if ( $this->has_tax() ) {

			$total_rows[ 'subtotal' ] = array(
				'label' => esc_html__( 'Subtotal:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_subtotal()
			);

			if ( $this->get_discount_total() > 0  ) {
				$discount_printed = true;

				$total_rows[ 'discount_total' ] = array(
					'label' => esc_html__( 'Discount:', 'wp-hotelier' ),
					'value'	=> $is_email ? $this->get_formatted_discount_total() : $this->get_formatted_discount_total() . ' <small class="reservation-table__coupon-code">' . $this->get_coupon_code() . '</small>',
					'extra'	=> $this->get_coupon_code()
				);
			}

			$total_rows[ 'tax_total' ] = array(
				'label' => esc_html__( 'Tax total:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_tax_total()
			);
		}

		if ( ! $discount_printed && $this->get_discount_total() > 0 ) {
			$total_rows[ 'subtotal' ] = array(
				'label' => esc_html__( 'Subtotal:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_subtotal()
			);

			$total_rows[ 'discount_total' ] = array(
				'label' => esc_html__( 'Discount:', 'wp-hotelier' ),
				'value'	=> $is_email ? $this->get_formatted_discount_total() : $this->get_formatted_discount_total() . ' <small class="reservation-table__coupon-code">' . $this->get_coupon_code() . '</small>',
				'extra'	=> $this->get_coupon_code()
			);
		}

		if ( $this->has_room_with_deposit() ) {

			if ( $this->get_formatted_paid_deposit() > 0 || $this->requires_capture() ) {

				$total_rows[ 'paid_deposit' ] = array(
					'label' => esc_html__( 'Paid deposit:', 'wp-hotelier' ),
					'value'	=> $this->requires_capture() ? $this->get_formatted_deposit() : $this->get_formatted_paid_deposit()
				);

			} else {

				$total_rows[ 'required_deposit' ] = array(
					'label' => esc_html__( 'Required deposit:', 'wp-hotelier' ),
					'value'	=> $this->get_formatted_deposit()
				);
			}

			if ( ( $this->get_paid_deposit() > 0 || $this->requires_capture() ) && $this->payment_method_title ) {
				$total_rows[ 'payment_method' ] = array(
					'label' => esc_html__( 'Payment method:', 'wp-hotelier' ),
					'value' => $this->payment_method_title
				);
			}

		}

		$total_rows[ 'total' ] = array(
			'label' => $this->requires_capture() ? esc_html__( 'Total:', 'wp-hotelier' ) : esc_html__( 'Total due:', 'wp-hotelier' ),
			'value'	=> $this->get_formatted_balance_due()
		);

		return apply_filters( 'hotelier_get_reservation_totals', $total_rows, $this );
	}

	/**
	 * Get totals before payment/booking for display on pages.
	 *
	 * @return array
	 */
	public function get_totals_before_booking( $is_email = false ) {
		$total_rows = array();
		$discount_printed = false;

		if ( htl_is_tax_enabled() && htl_get_tax_rate() > 0 ) {

			$total_rows[ 'subtotal' ] = array(
				'label' => esc_html__( 'Subtotal:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_subtotal()
			);

			if ( $this->get_discount_total() > 0  ) {
				$discount_printed = true;

				$total_rows[ 'discount_total' ] = array(
					'label' => esc_html__( 'Discount:', 'wp-hotelier' ),
					'value'	=> $is_email ? $this->get_formatted_discount_total() : $this->get_formatted_discount_total() . ' <small class="reservation-table__coupon-code">' . $this->get_coupon_code() . '</small>',
					'extra'	=> $this->get_coupon_code()
				);
			}

			$total_rows[ 'tax_total' ] = array(
				'label' => esc_html__( 'Tax total:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_tax_total()
			);
		}

		if ( ! $discount_printed && $this->get_discount_total() > 0 ) {
			$total_rows[ 'subtotal' ] = array(
				'label' => esc_html__( 'Subtotal:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_subtotal()
			);

			$total_rows[ 'discount_total' ] = array(
				'label' => esc_html__( 'Discount:', 'wp-hotelier' ),
				'value'	=> $is_email ? $this->get_formatted_discount_total() : $this->get_formatted_discount_total() . ' <small class="reservation-table__coupon-code">' . $this->get_coupon_code() . '</small>',
				'extra'	=> $this->get_coupon_code()
			);
		}

		if ( $this->has_room_with_deposit() ) {

			$total_rows[ 'total' ] = array(
				'label' => esc_html__( 'Total:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_total()
			);

			$total_rows[ 'deposit_due' ] = array(
				'label' => esc_html__( 'Deposit Due Now:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_deposit()
			);

		} else {

			$total_rows[ 'total' ] = array(
				'label' => esc_html__( 'Total:', 'wp-hotelier' ),
				'value'	=> $this->get_formatted_total()
			);
		}

		return apply_filters( 'hotelier_get_totals_before_booking', $total_rows, $this );
	}

	/**
	 * Checks if at least one room requires a deposit.
	 *
	 * @return bool
	 */
	public function has_room_with_deposit() {
		$ret = false;

		foreach ( $this->get_items() as $item ) {
			if ( $item[ 'deposit' ] > 0 ) {
				$ret = true;

				return $ret;
			}
		}

		return $ret;
	}

	/**
	 * Checks if the reservations includes tax.
	 *
	 * @return bool
	 */
	public function has_tax() {
		return $this->get_tax_total() > 0 ? true : false;
	}

	/**
	 * Return the number of nights of the reservation.
	 *
	 * @return int
	 * @todo perhaps store the number of nights in post meta?
	 */
	public function get_nights() {
		$checkin  = new DateTime( $this->guest_checkin );
		$checkout = new DateTime( $this->guest_checkout );
		$nights   = $checkin->diff( $checkout )->days;

		return apply_filters( 'hotelier_get_nights', $nights, $this );
	}

	/**
	 * Set the checkin date of the reservation.
	 *
	 * @param string $checkin Checkin time
	 */
	public function set_checkin( $checkin ) {
		update_post_meta( $this->id, '_guest_checkin', $checkin );
	}

	/**
	 * Return the checkin date of the reservation.
	 *
	 * @return string
	 */
	public function get_checkin() {
		return apply_filters( 'hotelier_get_checkin', $this->guest_checkin, $this );
	}

	/**
	 * Return the checkin date of the reservation - formatted for display.
	 *
	 * @return string
	 */
	public function get_formatted_checkin() {
		$date = date_i18n( get_option( 'date_format' ), strtotime( $this->guest_checkin ) );

		return apply_filters( 'hotelier_get_checkin', $date, $this );
	}

	/**
	 * Set the checkout date of the reservation.
	 *
	 * @param string $checkout Checkout time
	 */
	public function set_checkout( $checkout ) {
		update_post_meta( $this->id, '_guest_checkout', $checkout );
	}

	/**
	 * Return the checkout date of the reservation.
	 *
	 * @return string
	 */
	public function get_checkout() {
		return apply_filters( 'hotelier_get_checkout', $this->guest_checkout, $this );
	}

	/**
	 * Return the checkout date of the reservation - formatted for display.
	 *
	 * @return string
	 */
	public function get_formatted_checkout() {
		$date = date_i18n( get_option( 'date_format' ), strtotime( $this->guest_checkout ) );

		return apply_filters( 'hotelier_get_checkout', $date, $this );
	}

	/**
	 * Set reservation discount
	 *
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function set_discount_total( $amount ) {
		update_post_meta( $this->id, '_reservation_discount_total', absint( $amount ) );

		return true;
	}

	/**
	 * Gets reservation discount.
	 *
	 * @return int
	 */
	public function get_discount_total() {
		return absint( apply_filters( 'hotelier_get_reservation_discount_total', $this->reservation_discount_total, $this ) );
	}

	/**
	 * Gets reservation discount - formatted for display.
	 *
	 * @return int
	 */
	public function get_formatted_discount_total() {
		$amount = '<span class="discount-separator">-</span>' . htl_price( htl_convert_to_cents( $this->get_discount_total() ), $this->get_reservation_currency() );

		return apply_filters( 'hotelier_get_formatted_reservation_discount_total', $amount, $this );
	}

	/**
	 * Set coupon ID
	 *
	 * @param int $coupon_id
	 *
	 * @return bool
	 */
	public function set_coupon_id( $coupon_id ) {
		update_post_meta( $this->id, '_reservation_coupon_id', absint( $coupon_id ) );

		return true;
	}

	/**
	 * Gets coupon ID.
	 *
	 * @return int
	 */
	public function get_coupon_id() {
		return absint( apply_filters( 'hotelier_get_reservation_coupon_id', $this->reservation_coupon_id, $this ) );
	}

	/**
	 * Set coupon ID
	 *
	 * @param int $coupon_code
	 *
	 * @return bool
	 */
	public function set_coupon_code( $coupon_code ) {
		update_post_meta( $this->id, '_reservation_coupon_code', trim( $coupon_code ) );

		return true;
	}

	/**
	 * Gets coupon code.
	 *
	 * @return int
	 */
	public function get_coupon_code() {
		return apply_filters( 'hotelier_get_reservation_coupon_code', $this->reservation_coupon_code, $this );
	}

	/**
	 * Gets reservation currency
	 *
	 * @return string
	 */
	public function get_reservation_currency() {
		return apply_filters( 'hotelier_get_reservation_currency', $this->reservation_currency, $this );
	}

	/**
	 * Generates a URL so that a customer can pay for their (unpaid - pending) reservation. Pass 'true' for the booking version which doesn't offer gateway choices.
	 *
	 * @param  boolean $on_booking
	 * @return string
	 */
	public function get_booking_payment_url( $on_booking = false ) {

		$pay_url = htl_get_endpoint_url( 'pay-reservation', $this->id, htl_get_page_permalink( 'booking' ) );

		if ( htl_get_option( 'enforce_ssl_booking' ) || is_ssl() ) {
			$pay_url = str_replace( 'http:', 'https:', $pay_url );
		}

		if ( $on_booking ) {
			$pay_url = add_query_arg( 'key', $this->reservation_key, $pay_url );
		} else {
			$pay_url = add_query_arg( array( 'pay_for_reservation' => 'true', 'key' => $this->reservation_key ), $pay_url );
		}

		return apply_filters( 'hotelier_get_booking_payment_url', $pay_url, $this );
	}

	/**
	 * Generates a URL for the reservation received page
	 *
	 * @return string
	 */
	public function get_booking_received_url() {
		$booking_received_url = htl_get_endpoint_url( 'reservation-received', $this->id, htl_get_page_permalink( 'booking' ) );

		if ( htl_get_option( 'enforce_ssl_booking' ) || is_ssl() ) {
			$booking_received_url = str_replace( 'http:', 'https:', $booking_received_url );
		}

		$booking_received_url = add_query_arg( 'key', $this->reservation_key, $booking_received_url );

		return apply_filters( 'hotelier_get_booking_received_url', $booking_received_url, $this );
	}

	/**
	 * Generates a URL so that a customer can cancel their (unpaid - pending) reservation.
	 * Also confirmed reservations can be cancelled if they don't contain non-cancellable rooms.
	 *
	 * @param string $redirect
	 *
	 * @return string
	 */
	public function get_booking_cancel_url( $redirect = '' ) {

		// Get cancel endpoint
		$cancel_endpoint = $this->get_cancel_endpoint();

		return apply_filters( 'hotelier_get_booking_cancel_url', wp_nonce_url( add_query_arg( array(
			'cancel_reservation' => 'true',
			'reservation'        => $this->reservation_key,
			'reservation_id'     => $this->id,
			'redirect'           => $redirect
		), $cancel_endpoint ), 'hotelier-cancel_reservation' ) );
	}

	/**
	 * Generates a raw (unescaped) URL so that a customer can cancel their (unpaid - pending) reservation.
	 * Also confirmed reservations can be cancelled if they don't contain non-cancellable rooms.
	 *
	 * @param string $redirect
	 *
	 * @return string
	 */
	public function get_booking_cancel_url_raw( $is_payment = false, $redirect = '' ) {

		// Get cancel endpoint
		$cancel_endpoint = $this->get_cancel_endpoint();

		return apply_filters( 'hotelier_get_booking_cancel_url_raw', add_query_arg( array(
			'cancel_reservation' => 'true',
			'reservation'        => $this->reservation_key,
			'reservation_id'     => $this->id,
			'is_payment'         => $is_payment,
			'redirect'           => $redirect
		), $cancel_endpoint ) );
	}

	/**
	 * Helper method to return the cancel endpoint.
	 *
	 * @return string the cancel endpoint; either the listing page or the home page.
	 */
	public function get_cancel_endpoint() {
		$listing_page_enabled = htl_get_option( 'listing_disabled', false ) ? false : true;
		$cancel_endpoint      = $listing_page_enabled ? htl_get_page_permalink( 'listing' ) : home_url();

		if ( ! $cancel_endpoint && $listing_page_enabled ) {
			$cancel_endpoint = home_url();
		}

		if ( false === strpos( $cancel_endpoint, '?' ) ) {
			$cancel_endpoint = trailingslashit( $cancel_endpoint );
		}

		return $cancel_endpoint;
	}

	/**
	 * When a payment is complete this function is called
	 *
	 * Most of the time this should mark a reservation as 'confirmed'
	 * so the admin needs to take no action.
	 * Finally, record the date of payment
	 *
	 * @param $transaction_id string Optional transaction id to store in post meta
	 */
	public function payment_complete( $transaction_id = '' ) {
		do_action( 'hotelier_pre_payment_complete', $this->id );

		if ( null !== HTL()->session ) {
			HTL()->session->set( 'reservation_awaiting_payment', null );
		}

		$valid_reservation_statuses = apply_filters( 'hotelier_valid_reservation_statuses_for_payment_complete', array( 'on-hold', 'pending', 'failed' ), $this );

		if ( $this->id && $this->has_status( $valid_reservation_statuses ) ) {

			$this->set_paid_deposit( $this->get_deposit() );

			$this->update_status( apply_filters( 'hotelier_payment_complete_reservation_status', 'confirmed', $this->id ) );

			add_post_meta( $this->id, '_paid_date', current_time( 'mysql' ), true );

			if ( ! empty( $transaction_id ) ) {
				add_post_meta( $this->id, '_transaction_id', $transaction_id, true );
			}

			wp_update_post( array(
				'ID'            => $this->id,
				'post_date'     => current_time( 'mysql', 0 ),
				'post_date_gmt' => current_time( 'mysql', 1 )
			) );

			do_action( 'hotelier_payment_complete', $this->id );
		} else {
			do_action( 'hotelier_payment_complete_reservation_status_' . $this->get_status(), $this->id );
		}
	}

	/**
	 * Charge remain deposit
	 *
	 * Most of the time this should mark a reservation as 'paid'
	 *
	 * @param $transaction_id string Optional transaction id to store in post meta
	 */
	public function charge_remain_deposit( $transaction_id = '' ) {
		// Set charge
		$balance_due = $this->get_balance_due();
		$this->set_remain_deposit_charge( $balance_due );

		// Save transaction id
		if ( ! empty( $transaction_id ) ) {
			add_post_meta( $this->id, '_reservation_remain_deposit_charge_transaction_id', $transaction_id, true );
		}

		// Mark reservation as paid
		$this->mark_as_paid();
	}

	/**
	 * Cancel the reservation
	 *
	 * @param string $note (default: '') Optional note to add
	 */
	public function cancel_reservation( $note = '' ) {
		HTL()->session->set( 'reservation_awaiting_payment', null );
		$this->update_status( 'cancelled', $note );
	}

	/**
	 * Send request when the booking mode is set to 'manual'
	 */
	public function send_request() {
		if ( ! $this->id ) {
			return;
		}

		$status = $this->get_status();

		// Check if status is 'pending'
		if ( 'pending' == $status ) {

			// Trigger emails
			do_action( 'hotelier_new_booking_request', $this->id );
		}
	}

	/**
	 * Checks if a reservation needs payment, based on status and reservation paid_deposit
	 *
	 * @return bool
	 */
	public function needs_payment() {
		$valid_reservation_statuses = apply_filters( 'hotelier_valid_reservation_statuses_for_payment', array( 'pending', 'failed' ), $this );

		if ( $this->has_status( $valid_reservation_statuses ) && ! $this->get_paid_deposit() > 0 && $this->get_deposit() > 0 ) {
			$needs_payment = true;
		} else {
			$needs_payment = false;
		}

		return apply_filters( 'hotelier_reservation_needs_payment', $needs_payment, $this, $valid_reservation_statuses );
	}

	/**
	 * Output items for display in html emails.
	 *
	 * @param bool plain text
	 * @return string
	 */
	public function email_reservation_items_table( $plain_text = false ) {

		ob_start();

		$template = $plain_text ? 'emails/plain/email-reservation-items.php' : 'emails/email-reservation-items.php';

		htl_get_template( $template, array(
			'reservation'           => $this,
			'items'                 => $this->get_items()
		) );

		return apply_filters( 'hotelier_email_reservation_items_table', ob_get_clean(), $this );
	}

	/**
	 * Checks if a reservation can be manually charged.
	 * A reservation can be manually charged when has a previous paid deposit
	 * and when the payment method supports this feature
	 *
	 * @return bool
	 */
	public function can_be_charged() {
		if ( HTL()->payment_gateways() ) {
			$payment_gateways = HTL()->payment_gateways->get_available_payment_gateways();
		} else {
			$payment_gateways = array();
		}

		$payment_method = $this->get_payment_method() ? $this->get_payment_method() : '';

		if (
			$payment_method &&
			isset( $payment_gateways[ $payment_method ] ) &&
			$payment_gateways[ $payment_method ]->supports( 'manual_charge' ) &&
			$payment_gateways[ $payment_method ]->can_do_manual_charge( $this->id ) &&
			$this->get_balance_due() > 0 &&
			! $this->is_marked_as_paid() ) {
				return true;
		}

		return false;
	}

	/**
	 * Checks if a reservation can be cancelled.
	 * A reservation can be cancelled if they don't contain a non-cancellable room.
	 *
	 * @return bool
	 */
	public function can_be_cancelled() {
		$ret = true;

		foreach ( $this->get_items() as $item ) {
			if ( isset( $item[ 'is_cancellable' ] ) && ! $item[ 'is_cancellable' ] ) {
				$ret = false;

				return $ret;
			}
		}

		return $ret;
	}

	/**
	 * Checks if a reservation supports captures for auth charges.
	 * A reservation can be manually charged when has an authorized payment
	 * and when the payment method supports this feature
	 *
	 * @return bool
	 */
	public function can_be_captured() {
		if ( HTL()->payment_gateways() ) {
			$payment_gateways = HTL()->payment_gateways->get_available_payment_gateways();
		} else {
			$payment_gateways = array();
		}

		$payment_method = $this->get_payment_method() ? $this->get_payment_method() : '';

		if (
			$payment_method &&
			isset( $payment_gateways[ $payment_method ] ) &&
			$payment_gateways[ $payment_method ]->supports( 'capture' ) &&
			$payment_gateways[ $payment_method ]->can_do_capture( $this->id ) &&
			$this->get_deposit() > 0 &&
			! $this->is_marked_as_paid() ) {
				return true;
		}

		return false;
	}

	/**
	 * Checks if a reservation requires a capture
	 *
	 * @return bool
	 */
	public function requires_capture() {
		$requires_capture = $this->requires_capture ? true : false;

		return ( apply_filters( 'hotelier_reservation_requires_capture', $requires_capture, $this ) );
	}

	/**
	 * Checks if a reservation supports a refund.
	 * A reservation can be manually refunded when has a paid deposit
	 * and when the payment method supports this feature
	 *
	 * @return bool
	 */
	public function can_be_refunded() {
		if ( HTL()->payment_gateways() ) {
			$payment_gateways = HTL()->payment_gateways->get_available_payment_gateways();
		} else {
			$payment_gateways = array();
		}

		$payment_method = $this->get_payment_method() ? $this->get_payment_method() : '';

		if (
			$payment_method &&
			isset( $payment_gateways[ $payment_method ] ) &&
			$payment_gateways[ $payment_method ]->supports( 'refund' ) &&
			$payment_gateways[ $payment_method ]->can_do_refund( $this->id ) &&
			$this->get_paid_deposit() > 0 ) {
				return true;
		}

		return false;
	}
}

endif;

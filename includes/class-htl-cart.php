<?php
/**
 * Cart Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Cart' ) ) :

/**
 * HTL_Cart Class
 */
class HTL_Cart {

	/**
	 * The arrival date of the guest.
	 *
	 * @var string
	 */
	public $checkin;

	/**
	 * The departure date of the guest.
	 *
	 * @var string
	 */
	public $checkout;

	/**
	 * Contains an array of cart items and the quantity.
	 * When we check if an item is available we use the ID
	 * of the room. Rates belong to the same room (ID) so we
	 * need to sum the quantity of each rate to check the
	 * availability.
	 *
	 * e.g. The stock of a room is 10 and the room has two
	 * rates ('rate one' and 'rate two'). If a guest selects
	 * 7 rooms of 'rate one', the stock available for the rate
	 * 'rate two' is 3, not 10.
	 *
	 * @var array
	 */
	public $cart_contents_quantity = array();

	/**
	 * Contains an array of cart items.
	 *
	 * @var array
	 */
	public $cart_contents = array();

	/**
	 * The total cost of the cart items.
	 *
	 * @var int
	 */
	public $cart_contents_total;

	/**
	 * The required deposit.
	 *
	 * @var int
	 */
	public $required_deposit;

	/**
	 * Total cart tax.
	 *
	 * @var int
	 */
	public $tax_total;

	/**
	 * Total cart without tax.
	 *
	 * @var int
	 */
	public $subtotal;

	/**
	 * Cart grand total.
	 *
	 * @var int
	 */
	public $total;

	/**
	 * Cart discount.
	 *
	 * @var int
	 */
	public $discount_total;

	/**
	 * Applied coupon.
	 *
	 * @var int
	 */
	public $coupon_id;

	/**
	 * Array of data the cart calculates and stores in the session with defaults.
	 *
	 * @var array
	 */
	public $cart_session_data = array(
		'cart_contents_total' => 0,
		'required_deposit'    => 0,
		'subtotal'            => 0,
		'tax_total'           => 0,
		'discount_total'      => 0,
		'coupon_id'           => 0,
		'total'               => 0
	);

	/**
	 * Hold HTL_Cart_Totals() class
	 *
	 * @var object
	 */
	public $cart_totals;

	/**
	 * Constructor for the cart class. Loads options and hooks in the init method.
	 */
	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'init' ) );
		add_action( 'hotelier_add_to_cart', array( $this, 'calculate_totals' ), 20, 0 );
	}

	/**
	 * Loads the cart data from the PHP session during WordPress init and hooks in other methods.
	 */
	public function init() {
		$this->get_cart_from_session();
		$this->checkin  = HTL()->session->get( 'checkin' );
		$this->checkout = HTL()->session->get( 'checkout' );

		// Init HTL_Cart_Totals()
		$this->cart_totals = new HTL_Cart_Totals( $this->checkin, $this->checkout, $this->coupon_id );

		add_action( 'hotelier_booking_check_rooms_availability', array( $this, 'check_cart_items' ), 1 );
	}

	/**
	 * Get the contents of the cart
	 *
	 * @return array
	 */
	public function get_cart() {
		if ( ! did_action( 'wp_loaded' ) ) {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Get cart should not be called before the wp_loaded action.', 'wp-hotelier' ), '1.0.0' );
		}

		if ( ! did_action( 'hotelier_cart_loaded_from_session' ) ) {
			$this->get_cart_from_session();
		}

		return array_filter( (array) $this->cart_contents );
	}

	/**
	 * Returns the contents of the cart in an array without the 'data' element (room object).
	 *
	 * @return array contents of the cart
	 */
	public function get_cart_for_session() {
		$cart_session = array();

		if ( $this->get_cart() ) {
			foreach ( $this->get_cart() as $key => $values ) {
				$cart_session[ $key ] = $values;
				unset( $cart_session[ $key ][ 'data' ] ); // Unset room object
			}
		}

		return $cart_session;
	}

	/**
	 * Get the cart data from the PHP session and store it in class variables.
	 */
	public function get_cart_from_session() {
		// Load cart session data from session
		foreach ( $this->cart_session_data as $key => $default ) {
			$this->$key = HTL()->session->get( $key, $default );
		}

		$update_cart_session = false;

		// Load the cart object
		$cart = HTL()->session->get( 'cart', null );

		// Load checkin and checkout data
		$this->checkin  = HTL()->session->get( 'checkin' );
		$this->checkout = HTL()->session->get( 'checkout' );

		// Load cart_contents_quantity
		$this->cart_contents_quantity = HTL()->session->get( 'cart_contents_quantity' );

		if ( is_null( $cart ) ) {
			$cart = array();
		}

		if ( is_array( $cart ) ) {
			foreach ( $cart as $key => $values ) {
				$_room = htl_get_room( $values[ 'room_id' ] );

				if ( ! empty( $_room ) && $_room->exists() && $values[ 'quantity' ] > 0 ) {

					// Check room is_available on the given dates
					if ( ! $_room->is_available( $this->checkin, $this->checkout, $values[ 'quantity' ] ) ) {

						// Flag to indicate the stored cart should be update
						$update_cart_session = true;

						htl_add_notice( sprintf( esc_html__( '%s has been removed from your cart because it can no longer be reserved. Please contact us if you need assistance.', 'wp-hotelier' ), $_room->get_title() ), 'error' );
						do_action( 'hotelier_remove_cart_item_from_session', $key, $values );

					} else {

						// Put session data into array. Run through filter so other plugins can load their own session data
						$session_data = array_merge( $values, array( 'data' => $_room ) );
						$this->cart_contents[ $key ] = apply_filters( 'hotelier_get_cart_item_from_session', $session_data, $values, $key );

					}
				}
			}

		}

		// Trigger action
		do_action( 'hotelier_cart_loaded_from_session', $this );

		if ( $update_cart_session ) {
			HTL()->session->set( 'cart', $this->get_cart_for_session() );
		}

		// Queue re-calc if total is not set
		if ( ( ! $this->total && ! $this->is_empty() ) || $update_cart_session ) {
			$this->calculate_totals();
		}
	}

	/**
	 * Sets the php session data for the cart.
	 */
	public function set_session() {
		// Set cart session data
		$cart_session = $this->get_cart_for_session();

		HTL()->session->set( 'cart', $cart_session );
		HTL()->session->set( 'cart_contents_quantity', $this->cart_contents_quantity );

		foreach ( $this->cart_session_data as $key => $default ) {
			HTL()->session->set( $key, $this->$key );
		}

		do_action( 'hotelier_cart_updated' );
	}

	/**
	 * Empties the Cart.
	 *
	 * @return void
	 */
	public function empty_cart() {
		// Remove cart contents
		$this->cart_contents = array();
		$this->reset();

		HTL()->session->set( 'cart', null );
		HTL()->session->set( 'reservation_awaiting_payment', null );
		HTL()->session->set( 'cart_contents_quantity', null );
		HTL()->session->set( 'coupon_id', null );

		do_action( 'hotelier_cart_emptied' );
	}

	/**
	* Checks if the cart is empty.
	*
	* @return bool
	*/
	public function is_empty() {
		return 0 === sizeof( $this->get_cart() );
	}

	/**
	 * Add a room to the cart.
	 *
	 * @param integer $room_id contains the id of the room to add to the cart
	 * @param integer $quantity contains the quantity of the item to add
	 * @param integer $rate_id
	 * @param array $guests
	 * @param array $fees
	 * @param array $extras
	 * @return string $cart_item_key
	 */
	public function add_to_cart( $room_id = 0, $quantity = 1, $rate_id = 0, $guests = array(), $fees = false, $extras = array() ) {
		// Wrap in try catch so plugins can throw an exception to prevent adding to cart
		try {
			$room_id                      = absint( $room_id );
			$quantity                     = absint( $quantity );
			$rate_id                      = absint( $rate_id );
			$cart_item_key                = $this->cart_totals->add_to_cart( $room_id, $quantity, $rate_id, $guests, $fees, $extras );
			$this->cart_contents          = $this->cart_totals->cart_contents;
			$this->cart_contents_quantity = $this->cart_totals->cart_contents_quantity;

			do_action( 'hotelier_add_to_cart', $cart_item_key, $room_id, $quantity, $rate_id );

			return $cart_item_key;

		} catch ( Exception $e ) {
			if ( $e->getMessage() ) {
				htl_add_notice( $e->getMessage(), 'error' );
			}
			return false;
		}
	}

	/**
	 * Calculate totals for the items in the cart.
	 */
	public function calculate_totals() {
		$this->reset();

		do_action( 'hotelier_before_calculate_totals', $this );

		if ( $this->is_empty() ) {
			$this->set_session();
			return;
		}

		$cart = $this->get_cart();

		try {
			$cart_totals = new HTL_Cart_Totals( $this->checkin, $this->checkout, $this->coupon_id );
			$cart_totals->calculate_totals( $cart );

			$this->cart_contents    = $cart_totals->cart_contents;
			$this->subtotal         = $cart_totals->subtotal;
			$this->tax_total        = $cart_totals->tax_total;
			$this->required_deposit = $cart_totals->required_deposit;
			$this->discount_total   = $cart_totals->discount_total;
			$this->total            = $cart_totals->total;

			$this->set_session();

		} catch ( Exception $e ) {
			if ( $e->getMessage() ) {
				htl_add_notice( $e->getMessage(), 'error' );
			}

			return;
		}
	}

	/**
	 * Looks at the totals to see if payment is actually required.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		return apply_filters( 'hotelier_cart_needs_payment', $this->required_deposit > 0, $this );
	}

	/**
	 * Generate a unique ID for the cart item being added.
	 *
	 * @param int $room_id - id of the room the key is being generated for
	 * @param int $rate_id of the room
	 * @return string cart item key
	 */
	public function generate_cart_item_key( $room_id, $rate_id ) {
		return htl_generate_item_key( $room_id, $rate_id );
	}

	/**
	 * Reset cart totals to the defaults. Useful before running calculations.
	 *
	 * @access private
	 */
	private function reset() {
		foreach ( $this->cart_session_data as $key => $default ) {
			if ( $key === 'coupon_id' ) {
				continue;
			}

			$this->$key = $default;
			HTL()->session->set( $key, $default );
		}

		do_action( 'hotelier_cart_reset', $this );
	}

	/**
	 * Check all cart items for errors.
	 */
	public function check_cart_items() {
		// Result
		$return = true;

		// Check cart item validity
		$result = $this->check_cart_item_validity();

		if ( is_wp_error( $result ) ) {
			htl_add_notice( $result->get_error_message(), 'error' );
			$return = false;
		}

		// Check item stock
		$result = $this->check_cart_item_availability();

		if ( is_wp_error( $result ) ) {
			htl_add_notice( $result->get_error_message(), 'error' );
			$return = false;
		}

		// Let extensions add their own checks
		$result = $this->check_cart_item_for_extensions();

		if ( is_wp_error( $result ) ) {
			htl_add_notice( $result->get_error_message(), 'error' );
			$return = false;
		}

		return $return;
	}

	/**
	 * Looks through cart items and checks the posts are not trashed or deleted.
	 *
	 * @return bool|WP_Error
	 */
	public function check_cart_item_validity() {
		foreach ( $this->get_cart() as $cart_item_key => $values ) {

			$_room = $values[ 'data' ];

			if ( ! $_room || ! $_room->exists() || $_room->post->post_status == 'trash' ) {
				$this->set_quantity( $cart_item_key, 0 );

				return new WP_Error( 'invalid', sprintf( esc_html__( '%s has been removed from your cart because it can no longer be reserved. Please contact us if you need assistance.', 'wp-hotelier' ), $_room->get_title() ) );
			}
		}

		return true;
	}

	/**
	 * Looks through the cart to check each room is available. If not, add an error.
	 *
	 * @return bool|WP_Error
	 */
	public function check_cart_item_availability() {
		$error = new WP_Error();

		foreach ( $this->get_cart() as $cart_item_key => $values ) {
			$_room = $values[ 'data' ];
			$qty   = $this->cart_contents_quantity[ $_room->id ];

			// Check room is_available on the given dates
			if ( ! $_room->is_available( $this->checkin, $this->checkout, $qty ) ) {
				$error->add( 'room-not-available', sprintf( esc_html__( 'Sorry, we do not have enough "%s" available to fulfill your reservation. Please select another room or reduce the quantity and try again. We apologise for any inconvenience caused.', 'wp-hotelier' ), $_room->get_title() ) );

				return $error;
			}
		}

		return true;
	}

	/**
	 * Looks through cart items and let extensions add their own checks.
	 *
	 * @return bool|WP_Error
	 */
	public function check_cart_item_for_extensions() {
		foreach ( $this->get_cart() as $cart_item_key => $values ) {

			$_room = $values[ 'data' ];
			$qty   = $this->cart_contents_quantity[ $_room->id ];

			if ( ! apply_filters( 'hotelier_check_cart_item_for_extensions', true, $_room, $this->checkin, $this->checkout, $qty, $values ) ) {
				return new WP_Error( 'invalid', esc_html( apply_filters( 'hotelier_check_cart_item_for_extensions_error', __( 'Sorry, this room cannot be reserved.', 'wp-hotelier' ), $_room, $this->checkin, $this->checkout, $qty, $values ) ) );
			}
		}

		return true;
	}

	/**
	 * Set the quantity for an item in the cart.
	 *
	 * @param string	cart_item_key	contains the id of the cart item
	 * @param string	quantity		contains the quantity of the item
	 *
	 * @return bool
	 */
	public function set_quantity( $cart_item_key, $quantity = 1 ) {
		if ( $quantity == 0 || $quantity < 0 ) {
			do_action( 'hotelier_before_cart_item_quantity_zero', $cart_item_key );

			unset( $this->cart_contents[ $cart_item_key ] );
		} else {
			$old_quantity = $this->cart_contents[ $cart_item_key ]['quantity'];
			$this->cart_contents[ $cart_item_key ]['quantity'] = $quantity;

			do_action( 'hotelier_after_cart_item_quantity_update', $cart_item_key, $quantity, $old_quantity );
		}

		return true;
	}

	/**
	 * Gets total (after calculation).
	 *
	 * @return int price
	 */
	public function get_total() {
		return apply_filters( 'hotelier_cart_total', $this->total );
	}

	/**
	 * Gets subtotal (after calculation).
	 *
	 * @return int price
	 */
	public function get_subtotal() {
		return apply_filters( 'hotelier_cart_subtotal', $this->subtotal );
	}

	/**
	 * Gets tax total.
	 *
	 * @return int price
	 */
	public function get_tax_total() {
		return apply_filters( 'hotelier_cart_tax_total', $this->tax_total );
	}

	/**
	 * Gets the required deposit (after calculation).
	 *
	 * @return int price
	 */
	public function get_required_deposit() {
		return apply_filters( 'hotelier_cart_required_deposit', $this->required_deposit );
	}

	/**
	 * Sets coupon ID.
	 *
	 * @return int price
	 */
	public function set_coupon_id( $coupon_id ) {
		$this->coupon_id = $coupon_id;
		HTL()->session->set( 'coupon_id', $coupon_id );
	}

	/**
	 * Gets coupon ID.
	 *
	 * @return int price
	 */
	public function get_coupon_id() {
		return apply_filters( 'hotelier_cart_coupon_id', $this->coupon_id );
	}

	/**
	 * Gets discount.
	 *
	 * @return int price
	 */
	public function get_discount_total() {
		return apply_filters( 'hotelier_cart_discount_total', $this->discount_total );
	}

	/**
	 * Get the formatted room price.
	 *
	 * @param $total
	 * @return string formatted price
	 */
	public function get_room_price( $total ) {
		$total = htl_price( htl_convert_to_cents( $total ) );

		return apply_filters( 'hotelier_cart_room_price', $total );
	}

	/**
	 * Get booking form page URL.
	 *
	 * @return string
	 */
	public function get_booking_form_url() {
		$booking_form_url = htl_get_page_permalink( 'booking' );
		if ( $booking_form_url ) {
			// Force SSL if needed
			if ( is_ssl() || htl_get_option( 'enforce_ssl_booking' ) ) {
				$booking_form_url = str_replace( 'http:', 'https:', $booking_form_url );
			}
		}
		return apply_filters( 'hotelier_get_booking_form_url', $booking_form_url );
	}

	/**
	 * Get room_list form page URL.
	 *
	 * @return string
	 */
	public function get_room_list_form_url( $room_id = false ) {
		$room_list_form_url = htl_get_option( 'listing_disabled', false ) ? home_url() : htl_get_page_permalink( 'listing' );

		if ( $room_id ) {
			$room_id = absint( $room_id );

			$room_list_form_url = add_query_arg( array(
				'room-id' => $room_id
				),
				$room_list_form_url
			);
		}

		return apply_filters( 'hotelier_get_room_list_form_url', $room_list_form_url );
	}

	/**
	 * Looks at the items to see if there are non cancellable rooms.
	 *
	 * @return bool
	 */
	public function is_cancellable() {
		$is_cancellable = true;

		foreach ( $this->get_cart() as $cart_item_key => $values ) {

			if ( ! $values[ 'is_cancellable' ] ) {
				$is_cancellable = false;

				return $is_cancellable;
			}
		}

		return $is_cancellable;
	}

	/**
	 * Returns a specific item in the cart.
	 *
	 * @param string $item_key Cart item key.
	 * @return array Item data
	 */
	public function get_cart_item( $item_key ) {
		return isset( $this->cart_contents[ $item_key ] ) ? $this->cart_contents[ $item_key ] : array();
	}

	/**
	 * Remove a cart item.
	 *
	 * @param  string $cart_item_key Cart item key to remove from the cart.
	 * @return bool
	 */
	public function remove_cart_item( $cart_item_key ) {
		if ( isset( $this->cart_contents[ $cart_item_key ] ) ) {
			unset( $this->cart_contents[ $cart_item_key ] );

			return true;
		}

		return false;
	}
}

endif;

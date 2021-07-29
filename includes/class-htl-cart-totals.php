<?php
/**
 * Helper class that calculates cart totals.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Cart_Totals' ) ) :

/**
 * HTL_Cart_Totals Class
 */
class HTL_Cart_Totals {

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
	 * Get things going.
	 */
	public function __construct( $checkin, $checkout, $coupon_id = 0 ) {
		$this->checkin   = $checkin;
		$this->checkout  = $checkout;
		$this->coupon_id = $coupon_id;
	}

	/**
	 * Add a room to the cart.
	 *
	 * @param integer $room_id contains the id of the room to add to the cart
	 * @param integer $quantity contains the quantity of the item to add
	 * @param integer $rate_id
	 * @param array $guests array of guests
	 * @param array $fees array of fees
	 * @param array $extras array of fees
	 * @param bool $force checks only if checkout > checkin
	 * @param array $exclude reservation IDs to exclude when checking the available rooms
	 * @return string $cart_item_key
	 */
	public function add_to_cart( $room_id = 0, $quantity = 1, $rate_id = 0, $guests = array(), $fees = false, $extras = false, $force = false, $exclude = array() ) {
		// Wrap in try catch so plugins can throw an exception to prevent adding to cart
		try {
			$room_id  = absint( $room_id );
			$quantity = absint( $quantity );
			$rate_id  = absint( $rate_id );

			if ( ! HTL_Formatting_Helper::is_valid_checkin_checkout( $this->checkin, $this->checkout, $force ) ) {
				throw new Exception( esc_html__( 'Sorry, this room is not available on the given dates.', 'wp-hotelier' ) );
			}

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

			// Check the real quantity (rates have the same ID and stock)
			if ( isset( $this->cart_contents_quantity[ $_room->id ] ) ) {
				$real_qty = $this->cart_contents_quantity[ $_room->id ] + $quantity;
			} else {
				$real_qty = $quantity;
			}

			// Check room is_available on the given dates
			if ( ! $_room->is_available( $this->checkin, $this->checkout, $real_qty, $exclude, $force ) ) {
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

			// Fees
			$fees = $fees && is_array( $fees ) ? $fees : array();

			// Extras
			$extras = $extras && is_array( $extras ) ? $extras : array();

			// Generate an ID based on room ID and rate ID - this also avoid duplicates
			$cart_item_key = htl_generate_item_key( $room_id, $rate_id );

			// Hook to allow plugins to modify cart item
			$this->cart_contents[ $cart_item_key ] = apply_filters( 'hotelier_add_cart_item', array(
				'data'           => $_room,
				'room_id'        => $_room->id,
				'quantity'       => $quantity,
				'rate_id'        => $rate_id,
				'rate_name'      => $rate_name,
				'max_guests'     => $_room->get_max_guests(),
				'deposit'        => $deposit,
				'is_cancellable' => $is_cancellable,
				'guests'         => $guests,
				'fees'           => $fees,
				'extras'         => $extras,
			) );

			// Set the quantity
			$this->cart_contents_quantity[ $_room->id ] = $real_qty;

			return $cart_item_key;

		} catch ( Exception $e ) {
			if ( $e->getMessage() ) {
				if ( function_exists( 'htl_add_notice' ) ) {
					htl_add_notice( $e->getMessage(), 'error' );
				} else {
					// We are on admin. Return an array with the error message
					return array(
						'error'   => true,
						'message' => $e->getMessage()
					);
				}
			}

			return false;
		}
	}

	/**
	 * Calculate totals for the items in the reservation.
	 */
	public function calculate_totals( $cart_contents = false) {
		$this->cart_contents = $cart_contents ? $cart_contents : $this->cart_contents;

		foreach ( $this->cart_contents as $cart_item_key => $values ) {
			$_room     = $values[ 'data' ];
			$rate_id   = $values[ 'rate_id' ];
			$qty       = $values[ 'quantity' ];
			$room_type = 'standard';

			// Price for variable room - We already know that if we pass a $rate_id is a variable room ( in $this->add_to_cart() )
			if ( $rate_id ) {
				$_variation                = $_room->get_room_variation( $rate_id );
				$line_price                = $_variation->get_price( $this->checkin, $this->checkout );
				$line_price_without_fees   = $line_price;
				$line_price                = $this->calculate_fees( $line_price, $values[ 'fees' ], $_room );
				$line_price_without_extras = $line_price;
				$line_extras               = $this->get_line_extras( $line_price, $values[ 'extras' ], $values, $_room );
				$line_price                = $this->calculate_extras( $line_price, $line_extras );
				$line_deposit              = $_variation->get_deposit();
				$room_type                 = 'variation';

			} else {
				// Price for standard room
				$line_price                = $_room->get_price( $this->checkin, $this->checkout );
				$line_price_without_fees   = $line_price;
				$line_price                = $this->calculate_fees( $line_price, $values[ 'fees' ], $_room );
				$line_price_without_extras = $line_price;
				$line_extras               = $this->get_line_extras( $line_price, $values[ 'extras' ], $values, $_room );
				$line_price                = $this->calculate_extras( $line_price, $line_extras );
				$line_deposit              = $_room->get_deposit();
			}

			if ( ! $line_price_without_fees ) {
				// Remove room from reservation if has not price and throw an error
				unset( $this->cart_contents[ $cart_item_key ] );
				throw new Exception( esc_html__( 'Sorry, this room cannot be reserved.', 'wp-hotelier' ) );
			}

			// The total price of the room
			$line_total  = $line_price * $qty;

			// The total required deposit of the room
			$line_to_pay = ( ( $line_price * $line_deposit ) / 100 );
			$line_to_pay = round( $line_to_pay ) * $qty;

			// Hold room details so we can pass them to the filter
			$room_data = $room_type == 'standard' ? $_room : $_variation;

			// Allow plugins to filter the deposit
			$line_to_pay = apply_filters( 'hotelier_line_to_pay', $line_to_pay, $line_price, $line_deposit, $qty, $room_type, $room_data );

			// This is the total deposit required to confirm a reservation
			// Deposits are per line (room)
			$this->required_deposit += $line_to_pay;

			// This is the total cost of the reservation (deposit included)
			$this->cart_contents_total += $line_total;

			// Set prices
			$this->cart_contents[ $cart_item_key ][ 'price' ]                = $line_price;
			$this->cart_contents[ $cart_item_key ][ 'price_without_extras' ] = $line_price_without_extras;
			$this->cart_contents[ $cart_item_key ][ 'total' ]                = $line_total;
			$this->cart_contents[ $cart_item_key ][ 'extras' ]               = $line_extras ;
			$this->cart_contents[ $cart_item_key ][ 'total_without_extras' ] = $line_price_without_extras * $qty;
		}

		// Subtotal
		$this->subtotal  = apply_filters( 'hotelier_calculated_subtotal', $this->cart_contents_total, $this );

		// Calculate coupons
		if ( htl_coupons_enabled() && $this->coupon_id > 0 ) {
			$this->discount_total = htl_calculate_coupon( $this->cart_contents_total, $this->coupon_id );

			if ( $this->discount_total > 0 ) {
				$this->cart_contents_total = $this->discount_total > $this->cart_contents_total ? 0 : $this->cart_contents_total - $this->discount_total;
			}
		} else {
			$this->discount_total = 0;
		}

		// Ensure deposit is never > cart totals
		$this->required_deposit = $this->required_deposit > $this->cart_contents_total ? $this->cart_contents_total : $this->required_deposit;

		// Calculate taxes
		$this->tax_total = htl_is_tax_enabled() ? htl_calculate_tax( $this->cart_contents_total ) : 0;

		// Taxes on deposits
		if ( htl_is_deposit_tax_enabled() ) {
			$this->required_deposit = $this->required_deposit + htl_calculate_tax( $this->required_deposit );
		}

		// Allow plugins to hook and alter totals before final total is calculated
		do_action( 'hotelier_calculate_totals', $this );

		$total       = $this->cart_contents_total + htl_calculate_tax( $this->cart_contents_total );
		$this->total = apply_filters( 'hotelier_calculated_total', $total, $this );

		do_action( 'hotelier_after_calculate_totals' );
	}

	/**
	 * Calculate fees.
	 */
	public function calculate_fees( $line_price, $fees, $room, $rate_id = 0 ) {
		foreach ( $fees as $key => $value ) {
			$fee_to_add = htl_calculate_fee( $key, $value, $line_price, $this->checkin, $this->checkout, $room, $rate_id );
			$line_price += $fee_to_add;
		}

		return $line_price;
	}

	/**
	 * Get line extras.
	 */
	public function get_line_extras( $line_price, $extras, $values, $room ) {
		$line_extras = htl_get_room_extras( $line_price, $extras, $values, $room, $this->checkin, $this->checkout );

		return $line_extras;
	}

	/**
	 * Calculate extras.
	 */
	public function calculate_extras( $line_price, $line_extras ) {
		foreach ( $line_extras as $line_extra_id => $line_extra ) {
			if ( isset( $line_extra['price'] ) ) {
				$qty        = isset( $line_extra['qty'] ) ? $line_extra['qty'] : 1;
				$line_price += $line_extra['price']  * $qty;
			}
		}

		return $line_price;
	}
}

endif;

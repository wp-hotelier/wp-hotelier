<?php
/**
 * Generates requests to send to PayPal.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes/Payment
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Gateway_Paypal_Request' ) ) :

/**
 * HTL_Gateway_Paypal_Request Class
 */
class HTL_Gateway_Paypal_Request {

	/**
	 * Stores line items to send to PayPal
	 * @var array
	 */
	protected $line_items = array();

	/**
	 * Pointer to gateway making the request
	 * @var HTL_Gateway_Paypal
	 */
	protected $gateway;

	/**
	 * Endpoint for requests from PayPal
	 * @var string
	 */
	protected $notify_url;

	/**
	 * Constructor
	 * @param HTL_Gateway_Paypal $gateway
	 */
	public function __construct( $gateway ) {
		$this->gateway    = $gateway;
		$this->notify_url = HTL()->api_request_url( 'HTL_Gateway_Paypal' );
	}

	/**
	 * Get the PayPal request URL for an reservation
	 * @param  HTL_Reservation  $reservation
	 * @param  boolean $sandbox
	 * @return string
	 */
	public function get_request_url( $reservation, $sandbox = false ) {
		$paypal_args = http_build_query( $this->get_paypal_args( $reservation ), '', '&' );

		if ( $this->gateway->testmode ) {
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&' . $paypal_args;
		} else {
			return 'https://www.paypal.com/cgi-bin/webscr?' . $paypal_args;
		}
	}

	/**
	 * Get PayPal Args for passing to Paypal
	 *
	 * @param HTL_Reservation $reservation
	 * @return array
	 */
	protected function get_paypal_args( $reservation ) {
		HTL_Gateway_Paypal::log( 'Generating payment for reservation ' . $reservation->get_reservation_number() . '. Notify URL: ' . $this->notify_url );

		return apply_filters( 'hotelier_paypal_args', array_merge(
			array(
				'cmd'           => '_cart',
				'business'      => $this->gateway->email,
				'no_note'       => 1,
				'currency_code' => htl_get_currency(),
				'charset'       => 'utf-8',
				'rm'            => is_ssl() ? 2 : 1,
				'upload'        => 1,
				'return'        => esc_url_raw( add_query_arg( 'utm_nooverride', '1', $this->gateway->get_return_url( $reservation ) ) ),
				'cancel_return' => esc_url_raw( $reservation->get_booking_cancel_url_raw( true ) ),
				'page_style'    => htl_get_option( 'paypal_page_style' ),
				'bn'            => 'Hotelier_Cart',
				'invoice'       => $reservation->get_reservation_number(),
				'custom'        => json_encode( array( 'reservation_id' => $reservation->id, 'reservation_key' => $reservation->reservation_key ) ),
				'notify_url'    => $this->notify_url,
				'first_name'    => $reservation->guest_first_name,
				'last_name'     => $reservation->guest_last_name,
				'address1'      => $reservation->guest_address1,
				'address2'      => $reservation->guest_address2,
				'city'          => $reservation->guest_city,
				'state'         => $reservation->guest_state,
				'zip'           => $reservation->guest_postcode,
				'country'       => $reservation->guest_country,
				'email'         => $reservation->guest_email
			),
			$this->get_line_item_args( $reservation )
		), $reservation );
	}

	/**
	 * Get line item args for paypal request
	 * @param  HTL_Reservation $reservation
	 * @return array
	 */
	protected function get_line_item_args( $reservation ) {

		/**
		 * Try passing a line item per room if supported
		 */
		if ( $this->prepare_line_items( $reservation ) ) {
			$line_item_args = $this->get_line_items();

		/**
		 * Send as a single item
		 */
		} else {
			$this->delete_line_items();
			$all_items_name = $this->get_reservation_item_names( $reservation );
			$this->add_line_item( $all_items_name ? $all_items_name : esc_html__( 'Reservation', 'wp-hotelier' ), 1, $reservation->get_deposit(), $reservation->get_reservation_currency() );
			$line_item_args = $this->get_line_items();

		}

		return $line_item_args;
	}

	/**
	 * Return all line items
	 */
	protected function get_line_items() {
		return $this->line_items;
	}

	/**
	 * Remove all line items
	 */
	protected function delete_line_items() {
		$this->line_items = array();
	}

	/**
	 * Get line items to send to paypal
	 *
	 * @param  HTL_Reservation $reservation
	 * @return bool
	 */
	protected function prepare_line_items( $reservation ) {
		$this->delete_line_items();
		$calculated_deposit = 0;

		// Rooms
		foreach ( $reservation->get_items() as $item ) {
			if ( $item[ 'deposit' ] > 0 ) {
				$item_line_deposit  = $reservation->get_item_deposit( $item );
				$line_item          = $this->add_line_item( $this->get_reservation_item_name( $item ), $item[ 'qty' ], $item_line_deposit, $reservation->get_reservation_currency() );
				$calculated_deposit += $item_line_deposit * $item[ 'qty' ];

				if ( ! $line_item ) {
					return false;
				}
			}
		}

		// Check for mismatched totals
		if ( $calculated_deposit != $reservation->get_deposit() ) {
			return false;
		}

		return true;
	}

	/**
	 * Add PayPal Line Item
	 * @param string  $item_name
	 * @param integer $quantity
	 * @param integer $amount
	 * @return bool successfully added or not
	 */
	protected function add_line_item( $item_name, $quantity = 1, $amount = 0, $currency = '' ) {
		$index = ( sizeof( $this->line_items ) / 3 ) + 1;

		if ( $amount < 0 || $index > 9 ) {
			return false;
		}

		$amount = $this->number_format( htl_convert_to_cents( $amount ), $currency );

		$this->line_items[ 'item_name_' . $index ]   = html_entity_decode( HTL_Formatting_Helper::trim_string( $item_name, 127 ), ENT_NOQUOTES, 'UTF-8' );
		$this->line_items[ 'quantity_' . $index ]    = $quantity;
		$this->line_items[ 'amount_' . $index ]      = $amount;

		return true;
	}

	/**
	 * Get reservation item names as a string
	 * @param  array $item
	 * @return string
	 */
	protected function get_reservation_item_name( $item ) {
		$item_name = $item[ 'name' ];
		$rate      = isset( $item[ 'rate_name' ] ) ? $item[ 'rate_name' ] : false;

		if ( $rate ) {
			$item_name .= ' ( ' . htl_get_formatted_room_rate( $rate ) . ' )';
		}

		return $item_name;
	}

	/**
	 * Get reservation item names as a string
	 * @param  HTL_Reservation $reservation
	 * @return string
	 */
	protected function get_reservation_item_names( $reservation ) {
		$item_names = array();

		foreach ( $reservation->get_items() as $item ) {
			$item_names[] = $item[ 'name' ] . ' x ' . $item[ 'qty' ];
		}

		return implode( ', ', $item_names );
	}

	/**
	 * Check if currency has decimals
	 *
	 * @param  string $currency
	 *
	 * @return bool
	 */
	protected function currency_has_decimals( $currency ) {
		if ( in_array( $currency, array( 'HUF', 'JPY', 'TWD' ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Format prices
	 *
	 * @param  int $price
	 * @param  HTL_Reservation $reservation
	 *
	 * @return float|int
	 */
	protected function number_format( $price, $currency ) {
		$decimals = 2;

		if ( ! $this->currency_has_decimals( $currency ) ) {
			$decimals = 0;
		}

		return number_format( $price, $decimals, '.', '' );
	}
}

endif;

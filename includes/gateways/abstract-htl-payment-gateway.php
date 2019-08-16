<?php
/**
 * Payment Gateway class
 *
 * Plugins should extend this class to add their own gateway settings
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes/Payment
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class HTL_Payment_Gateway {
	/**
	 * Payment method title
	 * @var string
	 */
	public $title;

	/**
	 * Payment method description
	 * @var string
	 */
	public $description;

	/**
	 * Check if selected
	 * @var bool
	 */
	public $selected;

	/**
	 * True if the gateway shows fields on the checkout
	 * @var bool
	 */
	public $has_fields;

	/**
	 * Icon for the gateway
	 * @var string
	 */
	public $icon;

	/**
	 * Optional URL to view a transaction
	 * @var string
	 */
	public $view_transaction_url = '';

	/**
	 * Supported features
	 * @var array
	 */
	public $supports = array();

	/**
	 * Get the return url (reservation received page)
	 *
	 * @param HTL_Reservation $reservation
	 * @return string
	 */
	public function get_return_url( $reservation = null ) {

		if ( $reservation ) {
			$return_url = $reservation->get_booking_received_url();
		} else {
			$return_url = htl_get_endpoint_url( 'reservation-received', '', htl_get_page_permalink( 'booking' ) );
		}

		if ( is_ssl() || htl_get_option( 'enforce_ssl_booking' ) ) {
			$return_url = str_replace( 'http:', 'https:', $return_url );
		}

		return apply_filters( 'hotelier_get_return_url', $return_url, $reservation );
	}

	/**
	 * Get a link to the transaction on the 3rd party gateway size (if applicable)
	 *
	 * @param  HTL_Reservation $reservation the reservation object
	 * @return string transaction URL, or empty string
	 */
	public function get_transaction_url( $reservation ) {

		$return_url = '';
		$transaction_id = $reservation->get_transaction_id();

		if ( ! empty( $this->view_transaction_url ) && ! empty( $transaction_id ) ) {
			$return_url = sprintf( $this->view_transaction_url, $transaction_id );
		}

		return apply_filters( 'hotelier_get_transaction_url', $return_url, $reservation, $this );
	}

	/**
	 * Get the required reservation deposit in booking form and pay_reservation page.
	 *
	 * @return int
	 */
	protected function get_reservation_deposit() {

		$total = 0;
		$reservation_id = absint( get_query_var( 'pay-reservation' ) );

		// Gets required deposit from "pay reservation" page.
		if ( 0 < $reservation_id ) {
			$reservation = htl_get_reservation( $reservation_id );
			$total = $reservation->get_deposit();

		// Gets reservation total from booking form.
		} elseif ( 0 < HTL()->cart->get_required_deposit() ) {
			$total = HTL()->cart->get_required_deposit();
		}

		return $total;
	}

	/**
	 * Return the gateway's title
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'hotelier_gateway_title', $this->title, $this->id );
	}

	/**
	 * Return the gateway's description
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'hotelier_gateway_description', $this->description, $this->id );
	}

	/**
	 * get_icon function.
	 *
	 * @return string
	 */
	public function get_icon() {
		$icon = $this->icon ? '<img src="' . HTL_HTTPS::force_https_url( $this->icon ) . '" alt="' . esc_attr( $this->get_title() ) . '" />' : '';

		return apply_filters( 'hotelier_gateway_icon', $icon, $this->id );
	}

	/**
	 * Check if the gateway is available.
	 *
	 * @return bool
	 */
	public function is_available() {
		$enabled = htl_get_option( 'payment_gateways', false );
		$is_available = false;

		if ( isset( $enabled[ $this->id ] ) && $enabled[ $this->id ] == 1 ) {
			$is_available = true;
		}

		return $is_available;
	}

	/**
	 * Set this as the selected gateway.
	 */
	public function set_selected() {
		$this->selected = true;
	}

	/**
	 * Process Payment
	 *
	 * Process the payment. Override this in your gateway. When implemented, this should
	 * return the success and redirect in an array. e.g.
	 *
	 *        return array(
	 *            'result'   => 'success',
	 *            'redirect' => $this->get_return_url( $reservation )
	 *        );
	 *
	 * @param int $reservation_id
	 * @return array
	 */
	public function process_payment( $reservation_id ) {
		return array();
	}

	/**
	 * Validate frontend fields
	 *
	 * Validate payment fields on the frontend.
	 */
	public function validate_fields() {
		return true;
	}

	/**
	 * has_fields function.
	 *
	 * @return bool
	 */
	public function has_fields() {
		return $this->has_fields ? true : false;
	}

	/**
	 * Override this in your gateway if you have some.
	 */
	public function payment_fields() {
		return;
	}

	/**
	 * Checks if a gateway supports a given feature.
	 *
	 * Gateways should override this to declare support (or lack of support) for a feature.
	 *
	 * @param string $feature string The name of a feature to test support for.
	 * @return bool True if the gateway supports the feature, false otherwise.
	 */
	public function supports( $feature ) {
		return apply_filters( 'hotelier_payment_gateway_supports', in_array( $feature, $this->supports ) ? true : false, $feature, $this );
	}

	/**
	 * Checks if a gateway can charge the guest later. A gateway can charge later if it
	 * supports a manual charge and if the customer data was stored in the reservation.
	 *
	 * DEVELOPERS: the gateway must check the existence of the mentioned customer data!
	 * And obviously, the gateway must store this data in some way during the payment
	 * of the deposit (at the time of booking).
	 *
	 * 1. Declare the support for 'manual_charge'
	 * 2. Register the customer data (or whatever the gateway requires)
	 *    in the reservation during the first payment (the booking)
	 * 3. Check in this method if the customer data exists
	 *
	 * e.g. https://stripe.com/docs/charges#saving-credit-card-details-for-later
	 *
	 * @param int $reservation_id
	 * @return bool
	 */
	public function can_do_manual_charge( $reservation_id ) {
		return false;
	}

	/**
	 * Process manual charge. Override this in your gateway.
	 *
	 * @param int $reservation_id
	 */
	public function process_manual_charge( $reservation_id ) {
		return false;
	}

	/**
	 * Checks if a gateway can capture a previous authorized payment.
	 *
	 * @param int $reservation_id
	 * @return bool
	 */
	public function can_do_capture( $reservation_id ) {
		return false;
	}

	/**
	 * Checks if a gateway can refund a payment from the admin.
	 *
	 * @param int $reservation_id
	 * @return bool
	 */
	public function can_do_refund( $reservation_id ) {
		return false;
	}
}

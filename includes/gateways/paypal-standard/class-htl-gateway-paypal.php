<?php
/**
 * PayPal Standard Payment Gateway
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes/Payment
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class HTL_Gateway_Paypal extends HTL_Payment_Gateway {

	/** @var boolean Whether or not logging is enabled */
	public static $log_enabled = false;

	/** @var HTL_Log Logger instance */
	public static $log = false;

	/**
	 * Get things going.
	 */
	public function __construct() {
		$this->id          = 'paypal';
		$this->title       = esc_html__( 'PayPal', 'wp-hotelier' );
		$this->description = htl_get_option( 'paypal_message' );
		$this->icon        = HTL()->plugin_url() . '/includes/gateways/paypal-standard/assets/images/paypal.svg';
		$this->testmode    = htl_get_option( 'paypal_sandbox' ) ? true : false;
		$this->debug       = htl_get_option( 'paypal_log' ) ? true : false;
		$this->email       = $this->get_email();

		self::$log_enabled = $this->debug;

		include_once( 'includes/class-htl-gateway-paypal-response.php' );
		new HTL_Gateway_Paypal_Response( $this->testmode, $this->email );

		add_filter( 'hotelier_settings_payment', array( $this, 'settings_fields' ), 0 );
	}

	/**
	 * Add settings fields in admin.
	 */
	public function settings_fields( $fields ) {
		$gateway_fields = array(
			'paypal_settings' => array(
				'id'    => 'paypal_settings',
				'name'  => '<strong>' . esc_html__( 'PayPal settings', 'wp-hotelier' ) . '</strong>',
				'type'  => 'header',
				'class' => 'htl-ui-row--section-description'
			),
			'paypal_description' => array(
				'id'   => 'paypal_description',
				'desc' => sprintf( wp_kses( __( 'PayPal standard sends customers to PayPal to enter their payment information. PayPal IPN requires <a href="%s">fsockopen/cURL</a> support to update bookings after payment.', 'wp-hotelier' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( '?page=hotelier-settings&tab=tools' ) ),
				'type' => 'description'
			),
			'paypal_message' => array(
				'id'   => 'paypal_message',
				'name' => esc_html__( 'PayPal description', 'wp-hotelier' ),
				'desc' => esc_html__( 'The description the user sees during the booking.', 'wp-hotelier' ),
				'std'  => esc_html__( 'Pay with PayPal - The safer, easier way to pay online!', 'wp-hotelier' ),
				'type' => 'textarea'
			),
			'paypal_sandbox' => array(
				'id'      => 'paypal_sandbox',
				'name'    => esc_html__( 'PayPal sandbox', 'wp-hotelier' ),
				'desc'    => esc_html__( 'Enable test mode.', 'wp-hotelier' ),
				'subdesc' => esc_html__( 'While in test mode no live transactions are processed. To fully use test mode, you must have a PayPal sandbox (test) account.', 'wp-hotelier' ),
				'type'    => 'checkbox',
				'toggle'  => true,
			),
			'paypal_log' => array(
				'id'                => 'paypal_log',
				'name'              => esc_html__( 'Debug log', 'wp-hotelier' ),
				'desc'              => esc_html__( 'Enable logging.', 'wp-hotelier' ),
				'subdesc'           => sprintf( __( 'Log PayPal events, such as IPN requests, inside <code>%s</code>. Please note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'wp-hotelier' ), htl_get_log_file_path( 'paypal' ) ),
				'type'    => 'checkbox',
				'toggle'  => true,
			),
			'paypal_email' => array(
				'id'   => 'paypal_email',
				'name' => esc_html__( 'PayPal email', 'wp-hotelier' ),
				'desc' => esc_html__( 'Enter your PayPal account\'s email.', 'wp-hotelier' ),
				'type' => 'text',
				'std'  => ''
			),
			'paypal_page_style' => array(
				'id'   => 'paypal_page_style',
				'name' => esc_html__( 'PayPal page style', 'wp-hotelier' ),
				'desc' => esc_html__( 'Enter the name of the page style to use, or leave blank for default. These are defined within your PayPal account.', 'wp-hotelier' ),
				'type' => 'text',
				'std'  => ''
			),
		);

		return array_merge( $fields, $gateway_fields );
	}

	/**
	 * Logging method
	 * @param  string $message
	 */
	public static function log( $message ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = new HTL_Log();
			}

			self::$log->add( 'paypal', $message );
		}
	}

	/**
	 * Get valid PayPal email.
	 *
	 * @return string
	 */
	public function get_email() {
		$email = htl_get_option( 'paypal_email' );

		if ( is_email( $email ) ) {
			return $email;
		} else {
			return '';
		}
	}

	/**
	 * Get the transaction URL.
	 *
	 * @param  HTL_Reservation $reservation
	 *
	 * @return string
	 */
	public function get_transaction_url( $reservation ) {
		if ( $this->testmode ) {
			$this->view_transaction_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		} else {
			$this->view_transaction_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		}

		return parent::get_transaction_url( $reservation );
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $reservation_id
	 * @return array
	 */
	public function process_payment( $reservation_id ) {
		include_once( 'includes/class-htl-gateway-paypal-request.php' );

		$reservation    = htl_get_reservation( $reservation_id );
		$paypal_request = new HTL_Gateway_Paypal_Request( $this );

		// Remove cart
		HTL()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $paypal_request->get_request_url( $reservation, $this->testmode )
		);
	}
}

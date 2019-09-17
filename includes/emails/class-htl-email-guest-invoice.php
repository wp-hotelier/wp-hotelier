<?php
/**
 * Guest Invoice Email (sent to guest).
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes/Emails
 * @version  2.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Email_Guest_Invoice' ) ) :

/**
 * HTL_Email_Guest_Invoice Class
 */
class HTL_Email_Guest_Invoice extends HTL_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id               = 'guest_invoice';
		$this->title            = esc_html__( 'Guest invoice', 'wp-hotelier' );

		$this->heading          = htl_get_option( 'emails_guest_invoice_heading', __( 'Invoice for reservation #{reservation_number}', 'wp-hotelier' ) );
		$this->subject          = htl_get_option( 'emails_guest_invoice_subject', __( '{site_title} - Invoice for reservation #{reservation_number}', 'wp-hotelier' ) );

		$this->template_html    = 'emails/guest-invoice.php';
		$this->template_plain   = 'emails/plain/guest-invoice.php';
		$this->enabled          = htl_get_option( 'emails_guest_invoice_enabled', true );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 */
	function trigger( $reservation_id ) {

		if ( $reservation_id ) {
			$this->object                          = htl_get_reservation( $reservation_id );
			$this->find[ 'reservation-number' ]    = '{reservation_number}';
			$this->replace[ 'reservation-number' ] = $this->object->get_reservation_number();
			$this->recipient                       = $this->object->guest_email;
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		htl_get_template( $this->template_html, array(
			'reservation'   => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => false
		) );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		htl_get_template( $this->template_plain, array(
			'reservation'   => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => true
		) );
		return ob_get_clean();
	}
}

endif;

return new HTL_Email_Guest_Invoice();

<?php
/**
 * Cancelled Reservation Email (sent to guest).
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes/Emails
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Email_Guest_Cancelled_Reservation' ) ) :

/**
 * HTL_Email_Guest_Cancelled_Reservation Class
 */
class HTL_Email_Guest_Cancelled_Reservation extends HTL_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id               = 'guest_cancelled_reservation';
		$this->title            = esc_html__( 'Cancelled reservation', 'wp-hotelier' );

		$this->heading          = htl_get_option( 'emails_guest_cancelled_reservation_heading', __( 'Cancelled reservation', 'wp-hotelier' ) );
		$this->subject          = htl_get_option( 'emails_guest_cancelled_reservation_subject', __( 'Your reservation for {site_title}', 'wp-hotelier' ) );

		$this->template_html    = 'emails/guest-cancelled-reservation.php';
		$this->template_plain   = 'emails/plain/guest-cancelled-reservation.php';
		$this->enabled          = htl_get_option( 'emails_guest_cancelled_reservation_enabled', true );

		// Triggers for this email
		add_action( 'hotelier_reservation_status_pending_to_cancelled_notification', array( $this, 'trigger' ) );
		add_action( 'hotelier_reservation_status_on-hold_to_cancelled_notification', array( $this, 'trigger' ) );
		add_action( 'hotelier_reservation_status_confirmed_to_cancelled_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 */
	function trigger( $reservation_id ) {

		if ( $reservation_id ) {
			$this->object                          = htl_get_reservation( $reservation_id );
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

return new HTL_Email_Guest_Cancelled_Reservation();

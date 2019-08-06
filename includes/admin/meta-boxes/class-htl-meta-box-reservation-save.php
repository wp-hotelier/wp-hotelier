<?php
/**
 * Reservation Save Button.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Reservation_Save' ) ) :

/**
 * HTL_Meta_Box_Reservation_Save Class
 */
class HTL_Meta_Box_Reservation_Save {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $post, $thereservation;

		if ( ! is_object( $thereservation ) ) {
			$thereservation = htl_get_reservation( $post->ID );
		}

		$reservation = $thereservation;
		?>
		<div class="submitbox">

			<div id="reservation-actions">

				<p><?php esc_html_e( 'Resend reservation emails.', 'wp-hotelier' ); ?></p>

				<select name="hotelier_emails_action" id="emails-action">
					<option value=""><?php esc_html_e( 'Emails', 'wp-hotelier' ); ?></option>

					<?php
					$mailer           = HTL()->mailer();
					$available_emails = apply_filters( 'hotelier_resend_reservation_emails_available', array( 'guest_request_received', 'guest_confirmed_reservation', 'guest_cancelled_reservation', 'guest_invoice' ) );
					$mails            = $mailer->get_emails();

					if ( ! empty( $mails ) ) {
						foreach ( $mails as $mail ) {
							if ( in_array( $mail->id, $available_emails ) ) {
								echo '<option value="send_email_'. esc_attr( $mail->id ) .'">' . esc_html( $mail->title ) . '</option>';
							}
						}
					}
					?>
				</select>

				<button class="button" title="<?php esc_attr_e( 'Send email', 'wp-hotelier' ); ?>"><?php esc_html_e( 'Send email', 'wp-hotelier' ); ?></button>

			</div>

			<div id="delete-action"><?php
				if ( current_user_can( 'delete_post', $post->ID ) ) {

					if ( ! EMPTY_TRASH_DAYS ) {
						$delete_text = esc_html__( 'Delete Permanently', 'wp-hotelier' );
					} else {
						$delete_text = esc_html__( 'Move to Trash', 'wp-hotelier' );
					}
					?><a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo $delete_text; ?></a>
				<?php }
				?>
			</div>

			<input type="submit" class="button save-reservation button-primary" name="save" value="<?php esc_html_e( 'Save reservation', 'wp-hotelier' ); ?>" />

		</div>

		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		$reservation = htl_get_reservation( $post_id );

		// Handle emails action
		if ( ! empty( $_POST[ 'hotelier_emails_action' ] ) ) {
			$action = sanitize_text_field( $_POST[ 'hotelier_emails_action' ] );

			if ( strstr( $action, 'send_email_' ) ) {

				do_action( 'hotelier_before_resend_reservation_emails', $reservation );

				// Ensure gateways are loaded in case they need to insert data into the emails
				HTL()->payment_gateways();

				// Load mailer
				$mailer = HTL()->mailer();

				$email_to_send = str_replace( 'send_email_', '', $action );

				$mails = $mailer->get_emails();

				if ( ! empty( $mails ) ) {
					foreach ( $mails as $mail ) {
						if ( $mail->id == $email_to_send ) {
							$mail->trigger( $reservation->id );
							$reservation->add_reservation_note( sprintf( esc_html__( '%s email notification manually sent.', 'wp-hotelier' ), $mail->title ), false, true );
						}
					}
				}

				do_action( 'hotelier_after_resend_reservation_emails', $reservation );

				// Change the post saved message
				add_filter( 'redirect_post_location', array( __CLASS__, 'set_email_sent_message' ) );
			}
		}

		// Handle mark as paid/unpaid action
		if ( ! empty( $_POST[ 'hotelier_mark_as_paid_action' ] ) ) {
			if ( 'paid' == $_POST[ 'hotelier_mark_as_paid_action' ] ) {
				$reservation->mark_as_paid();
			} else if ( 'unpaid' == $_POST[ 'hotelier_mark_as_paid_action' ] ) {
				$reservation->mark_as_unpaid();
			}
		}

		// Handle manual charge
		if ( ! empty( $_POST[ 'hotelier_charge_remain_deposit' ] ) ) {
			// Do a final check here.
			// This ensures that:
			//      1. The reservation can be charged
			//      2. The payment method used in this reservation
			//         exists and supports manual charges
			if ( $reservation->can_be_charged() ) {
				$available_gateways = HTL()->payment_gateways->get_available_payment_gateways();
				$available_gateways[ $reservation->get_payment_method() ]->process_manual_charge( $reservation->id );
			}
		}
	/**
	 * Set save error for later
	 */
	public static function set_save_error( $message ) {
		update_option( 'hotelier_save_reservation_error', $message );
		add_filter( 'redirect_post_location', array( __CLASS__, 'add_error_query_var' ), 99 );
	}

	/**
	 * Add query_var for error notices
	 *
	 * @static
	 * @param $location
	 * @return string
	 */
	public static function add_error_query_var( $location ) {
		remove_filter( 'redirect_post_location', array( __CLASS__, 'add_error_query_var' ), 99 );

		$location = add_query_arg( array( 'save-error-reservation' => true ), $location );

		return $location;
	}

	/**
	 * Set the correct message ID
	 *
	 * @static
	 * @param $location
	 * @return string
	 */
	public static function print_notices() {
		$error = get_option( 'hotelier_save_reservation_error', false );

		// Delete notice
		delete_option( 'hotelier_save_reservation_error' );

		if ( ! isset( $_GET[ 'save-error-reservation' ] ) ) {
			return;
		}

		if ( ! $error ) {
			return;
		}

		echo '<div class="error"><p>' . wp_kses_post( $error ) . '</p></div>';
	}

	/**
	 * Set the correct message ID
	 *
	 * @static
	 * @param $location
	 * @return string
	 */
	public static function set_email_sent_message( $location ) {
		return add_query_arg( 'message', 11, $location );
	}
}

endif;

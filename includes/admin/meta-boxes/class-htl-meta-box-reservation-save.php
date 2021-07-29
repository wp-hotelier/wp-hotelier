<?php
/**
 * Reservation Save Actions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.6.0
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
	 * Checkin date.
	 *
	 * @var string
	 */
	protected static $checkin;

	/**
	 * Checkout date.
	 *
	 * @var string
	 */
	protected static $checkout;

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

		// Handle capture action
		if ( ! empty( $_POST[ 'hotelier_capture_deposit' ] ) ) {
			$amount = isset( $_POST[ 'hotelier_capture_deposit_amount' ] ) ? $_POST[ 'hotelier_capture_deposit_amount' ] : 0;
			self::capture_deposit( $reservation, $amount );
		}

		// Handle refund action
		if ( ! empty( $_POST[ 'hotelier_refund_deposit' ] ) ) {
			$amount = isset( $_POST[ 'hotelier_refund_amount' ] ) ? $_POST[ 'hotelier_refund_amount' ] : 0;
			self::refund_deposit( $reservation, $amount );
		}

		// Handle new checkin/checkout dates or new coupon
		if ( ! empty( $_POST[ 'reservation_checkin' ] ) && ! empty( $_POST[ 'reservation_checkout' ] ) ) {
			self::change_reservation_totals( $reservation, $_POST[ 'reservation_checkin' ], $_POST[ 'reservation_checkout' ] );
		}
	}

	/**
	 * Handle captures
	 */
	protected static function capture_deposit( $reservation, $amount ) {
		$amount = HTL_Formatting_Helper::sanitize_amount( $amount );

		if ( ! $amount ) {
			self::set_save_error( esc_html__( 'Please set a correct amount.', 'wp-hotelier' ) );

			return false;
		}

		if ( $amount > $reservation->get_deposit() ) {
			self::set_save_error( sprintf( __( 'Cannot capture this payment. The max amount capturable for this reservation is %s.', 'wp-hotelier' ), '<strong>' . htl_price( htl_convert_to_cents( $reservation->get_deposit() ), $reservation->get_reservation_currency() ) . '</strong>' ) );

			return false;
		}

		// Do a final check here.
		// This ensures that:
		//      1. The reservation can be captured
		//      2. The payment method used in this reservation
		//         exists and supports captures
		if ( $reservation->can_be_captured() ) {
			$available_gateways = HTL()->payment_gateways->get_available_payment_gateways();
			$success = $available_gateways[ $reservation->get_payment_method() ]->process_capture( $reservation->id, $amount );

			if ( ! $success ) {
				self::set_save_error( sprintf( __( 'Cannot capture this payment. Please check the <a href="%s">logs</a>.', 'wp-hotelier' ), admin_url( 'admin.php?page=hotelier-logs' ) ) );

				return false;
			}
		}

		// Change the post saved message
		add_filter( 'redirect_post_location', array( __CLASS__, 'set_needs_reload_message' ) );
	}

	/**
	 * Handle refunds
	 */
	protected static function refund_deposit( $reservation, $amount ) {
		$amount = HTL_Formatting_Helper::sanitize_amount( $amount );

		if ( ! $amount ) {
			self::set_save_error( esc_html__( 'Please set a correct amount.', 'wp-hotelier' ) );

			return false;
		}

		if ( $amount > $reservation->get_paid_deposit() ) {
			self::set_save_error( sprintf( __( 'Cannot refund this payment. The max amount refundable for this reservation is %s.', 'wp-hotelier' ), '<strong>' . htl_price( htl_convert_to_cents( $reservation->get_paid_deposit() ), $reservation->get_reservation_currency() ) . '</strong>' ) );

			return false;
		}

		// Do a final check here.
		// This ensures that:
		//      1. The reservation can be refunded
		//      2. The payment method used in this reservation
		//         exists and supports refunds
		if ( $reservation->can_be_refunded() ) {
			$available_gateways = HTL()->payment_gateways->get_available_payment_gateways();
			$success = $available_gateways[ $reservation->get_payment_method() ]->process_refund( $reservation->id, $amount );

			if ( ! $success ) {
				self::set_save_error( sprintf( __( 'Cannot refund this payment. Please check the <a href="%s">logs</a>.', 'wp-hotelier' ), admin_url( 'admin.php?page=hotelier-logs' ) ) );

				return false;
			}
		}

		// Change the post saved message
		add_filter( 'redirect_post_location', array( __CLASS__, 'set_needs_reload_message' ) );
	}

	/**
	 * Handle new reservation totals
	 */
	protected static function change_reservation_totals( $reservation, $checkin, $checkout ) {
		$needs_recalculation = false;
		$checkin             = sanitize_text_field( $checkin );
		$checkout            = sanitize_text_field( $checkout );
		$coupon_id           = 0;

		self::$checkin = $checkin;
		add_filter( 'hotelier_advanced_extras_get_checkin_date', array( __CLASS__, 'apply_checkin_to_advanced_extras' ) );
		self::$checkout = $checkout;
		add_filter( 'hotelier_advanced_extras_get_checkout_date', array( __CLASS__, 'apply_checkout_to_advanced_extras' ) );

		// Check coupon
		if ( isset( $_POST[ 'coupon_id' ] ) ) {
			switch ( $_POST[ 'coupon_id' ] ) {
				case '-1':
					if ( $reservation->get_coupon_id() > 0 ) {
						$coupon_id = $reservation->get_coupon_id();
					}
					break;

				case '0':
					if ( $reservation->get_coupon_id() > 0 ) {
						$needs_recalculation = true;
						// $remove_coupon     = true;
					}

					break;

				default:
					$current_coupon_id = $reservation->get_coupon_id();
					$coupon_id         = absint( $_POST[ 'coupon_id' ] ) ;

					if ( $current_coupon_id !== $coupon_id ) {
						$needs_recalculation = true;
					}
					break;
			}
		}

		if ( $checkin !== $reservation->get_checkin() || $checkout !== $reservation->get_checkout() ) {
			$needs_recalculation = true;
		}

		// Change totals only if dates are different or if there is a new coupon to apply/remove
		if ( $needs_recalculation ) {
			$new_post_status = sanitize_text_field( $_POST[ 'reservation_status' ] );
			$new_post_status = 'htl-' === substr( $new_post_status, 0, 4 ) ? substr( $new_post_status, 4 ) : $new_post_status;
			$old_post_status = $reservation->get_status();
			$to_occupancy    = false;

			$status_with_occupancy = array(
				'confirmed',
				'pending',
				'on-hold',
				'failed'
			);

			$status_without_occupancy = array(
				'completed',
				'cancelled',
			);

			if ( in_array( $new_post_status, $status_with_occupancy ) && in_array( $old_post_status, $status_without_occupancy ) ) {
				$to_occupancy = true;
			}

			try {
				// Return early if the reservation has been paid
				// or has an auhtorized payment
				if ( $reservation->get_paid_deposit() > 0 || $reservation->requires_capture() ) {
					throw new Exception( esc_html__( 'Sorry, you cannnot edit paid reservations.', 'wp-hotelier' ) );
				}

				// When changing the status and the dates of a reservation at the same time
				// throw an error if the new status changes the occupancy. Just in case.
				if ( $new_post_status !== $old_post_status && $to_occupancy ) {
					throw new Exception( esc_html__( 'Sorry, you cannnot change the status and the dates at the same time.', 'wp-hotelier' ) );
				}

				// Init HTL_Cart_Totals()
				$cart_totals = new HTL_Cart_Totals( $checkin, $checkout, $coupon_id );
				$line_items  = $reservation->get_items();

				foreach ( $line_items as $item_id => $item ) {
					$room      = $reservation->get_room_from_item( $item );
					$quantity  = absint( $item[ 'qty' ] );
					$rate_name = isset( $item[ 'rate_name' ] ) ? $item[ 'rate_name' ] : false;
					$rate_id   = isset( $item[ 'rate_id' ] ) ? $item[ 'rate_id' ] : 0;

					if ( ! $room->exists() || ! $room || 'publish' !== $room->post->post_status ) {
						// Oops, check failed so throw an error (this this room does not exists)
						throw new Exception( esc_html__( 'Sorry, this room does not exists.', 'wp-hotelier' ) );
					}

					// Check existence of room rate
					if ( $room->is_variable_room() && $rate_name ) {
						if ( ! $rate_id ) {
							// Fallback for old reservations that don't have
							// the rate_id meta (WP Hotelier 2.1.0 +)
							$rate_id = htl_get_room_rate_id_from_rate_name( $room, $rate_name );
						}

						if ( ! ( $rate_id > 0 ) ) {
							throw new Exception( esc_html__( 'Sorry, this room rate does not exists anymore.', 'wp-hotelier' ) );
						}
					}

					// Guests
					$guests = array();

					$adults     = isset( $item['adults'] ) ? maybe_unserialize( $item[ 'adults' ] ) : false;
					$children   = isset( $item['children'] ) ? maybe_unserialize( $item[ 'children' ] ) : false;
					$max_guests = $room->get_max_guests();

					$has_adults_in_meta   = is_array( $adults ) && count( $adults ) === $quantity ? true : false;
					$has_children_in_meta = is_array( $children ) && count( $children ) === $quantity ? true : false;

					// Populate guests
					for ( $i = 0; $i < $quantity; $i++ ) {
						if ( $has_adults_in_meta && isset( $adults[$i] ) ) {
							// Get guests from meta
							$guests[$i]['adults'] = $adults[$i];
						} else {
							// Add default adults if missing from meta
							$guests[$i]['adults'] = $max_guests;
						}

						if ( $has_children_in_meta && isset( $children[$i] ) ) {
							// Get guests from meta
							$guests[$i]['children'] = $children[$i];
						} else {
							// Add default children if missing from meta
							$guests[$i]['children'] = 0;
						}
					}

					// Extras
					$extras = array();

					if ( isset( $item[ 'extras' ] ) ) {
						$extras = maybe_unserialize( $item[ 'extras' ] );
					}

					$extras = is_array( $extras ) ? $extras : array();

					// Fees
					$fees = array();

					if ( isset( $item[ 'fees' ] ) ) {
						$fees = maybe_unserialize( $item[ 'fees' ] );
					}

					$fees = is_array( $fees ) ? $fees : array();

					$added_to_cart = $cart_totals->add_to_cart( $room->id, $quantity, $rate_id, $guests, $fees, $extras, true, array( $reservation->id ) );

					if ( is_array( $added_to_cart ) && isset( $added_to_cart[ 'error' ] ) ) {
						$error = $added_to_cart[ 'message' ] ? esc_html( $added_to_cart[ 'message' ] ) : esc_html__( 'Sorry, this room is not available.', 'wp-hotelier' );

						throw new Exception( $error );
					}
				}

				do_action( 'hotelier_change_reservation_dates_before_calculate_totals' );

				// Calculate totals
				$cart_totals->calculate_totals();

				// If we got here, the dates of this reservation can be changed
				// We need to update the totals of the reservations as well
				$reservation_items = htl_get_reservation_items_id( $reservation->id );

				foreach ( $reservation_items as $reservation_item ) {
					$reservation_item_room_id   = absint( htl_get_reservation_item_meta( $reservation_item, '_room_id', true ) );
					$reservation_item_rate_name = htl_get_reservation_item_meta( $reservation_item, '_rate_name', true );
					$reservation_item_rate_id   = htl_get_reservation_item_meta( $reservation_item, '_rate_id', true );

					if ( $reservation_item_rate_name ) {
						if ( ! $reservation_item_rate_id ) {
							// Fallback for old reservations that don't have
							// the rate_id meta (WP Hotelier 2.1.0 +)
							$_reservation_item_room   = htl_get_room( $reservation_item_room_id );
							$reservation_item_rate_id = htl_get_room_rate_id_from_rate_name( $_reservation_item_room, $reservation_item_rate_name );
						}
					}

					$reservation_item_rate_id = $reservation_item_rate_id ? $reservation_item_rate_id : 0;
					$reservation_item_key     = htl_generate_item_key( $reservation_item_room_id, $reservation_item_rate_id );

					if ( ! isset( $cart_totals->cart_contents[ $reservation_item_key ] ) ) {
						throw new Exception( esc_html__( 'Sorry, something went wrong during the calculation of the totals.', 'wp-hotelier' ) );
					}

					$reservation_cart_item    = $cart_totals->cart_contents[ $reservation_item_key ];
					$new_max_guests           = isset( $reservation_cart_item[ 'max_guests' ] ) ? $reservation_cart_item[ 'max_guests' ] : 0;
					$new_price                = isset( $reservation_cart_item[ 'price' ] ) ? $reservation_cart_item[ 'price' ] : 0;
					$new_price_without_extras = isset( $reservation_cart_item[ 'price_without_extras' ] ) ? $reservation_cart_item[ 'price_without_extras' ] : 0;
					$new_total                = isset( $reservation_cart_item[ 'total' ] ) ? $reservation_cart_item[ 'total' ] : 0;
					$new_total_without_extras = isset( $reservation_cart_item[ 'total_without_extras' ] ) ? $reservation_cart_item[ 'total_without_extras' ] : 0;
					$new_percent_deposit      = isset( $reservation_cart_item[ 'percent_deposit' ] ) ? $reservation_cart_item[ 'percent_deposit' ] : 0;
					$new_deposit              = isset( $reservation_cart_item[ 'deposit' ] ) ? $reservation_cart_item[ 'deposit' ] : 0;
					$new_is_cancellable       = isset( $reservation_cart_item[ 'is_cancellable' ] ) ? $reservation_cart_item[ 'is_cancellable' ] : false;
					$new_extras               = isset( $reservation_cart_item[ 'extras' ] ) ? $reservation_cart_item[ 'extras' ] : array();

					htl_update_reservation_item_meta( $reservation_item, '_max_guests', $new_max_guests );
					htl_update_reservation_item_meta( $reservation_item, '_price', $new_price );
					htl_update_reservation_item_meta( $reservation_item, '_price_without_extras', $new_price_without_extras );
					htl_update_reservation_item_meta( $reservation_item, '_total', $new_total );
					htl_update_reservation_item_meta( $reservation_item, '_total_without_extras', $new_total_without_extras );
					htl_update_reservation_item_meta( $reservation_item, '_percent_deposit', $new_percent_deposit );
					htl_update_reservation_item_meta( $reservation_item, '_deposit', $new_deposit );
					htl_update_reservation_item_meta( $reservation_item, '_is_cancellable', $new_is_cancellable );
					htl_update_reservation_item_meta( $reservation_item, '_extras', $new_extras );
				}

				$reservation->set_checkin( $checkin );
				$reservation->set_checkout( $checkout );
				$reservation->set_subtotal( $cart_totals->subtotal );
				$reservation->set_tax_total( $cart_totals->tax_total );
				$reservation->set_total( $cart_totals->total );
				$reservation->set_deposit( $cart_totals->required_deposit );

				// Save coupon data
				if ( htl_coupons_enabled() ) {
					$coupon_id = $cart_totals->coupon_id;

					if ( absint( $coupon_id ) > 0 ) {
						$coupon      = htl_get_coupon( $coupon_id );
						$coupon_code = $coupon->get_code();

						// Check if coupon is valid
						$can_apply_coupon = htl_can_apply_coupon( $coupon_id, true );

						if ( isset( $can_apply_coupon['can_apply'] ) && $can_apply_coupon['can_apply'] ) {
							$reservation->set_discount_total( $cart_totals->discount_total );
							$reservation->set_coupon_id( $coupon_id );
							$reservation->set_coupon_code( $coupon_code );
						} else {
							$reason = isset( $can_apply_coupon['reason'] ) ? $can_apply_coupon['reason'] : false;
							$reason = $reason ? $reason : esc_html__( 'This coupon cannot be applied.', 'wp-hotelier' );

							throw new Exception( $reason );
						}
					}
				}

				$reservation->update_table_reservation_dates( $checkin, $checkout );
				$reservation->add_reservation_note( esc_html__( 'Reservation dates updated. Totals have been recalculated.', 'wp-hotelier' ) );

				do_action( 'hotelier_reservation_dates_changed' , $reservation->id, $checkin, $checkout );

				// Change reservation modified date
				$reservation->update_last_modified();

			} catch ( Exception $e ) {
				if ( ! empty( $e ) ) {
					self::set_save_error( esc_html( $e->getMessage() ) );
				}

				return false;
			}
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
	 */
	public static function add_error_query_var( $location ) {
		remove_filter( 'redirect_post_location', array( __CLASS__, 'add_error_query_var' ), 99 );

		$location = add_query_arg( array( 'save-error-reservation' => true ), $location );

		return $location;
	}

	/**
	 * Set the correct message ID
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
	 * Set message ID for email sent action
	 */
	public static function set_email_sent_message( $location ) {
		return add_query_arg( 'message', 11, $location );
	}

	/**
	 * Set message ID when page needs reload
	 */
	public static function set_needs_reload_message( $location ) {
		return add_query_arg( 'message', 12, $location );
	}

	/**
	 * Pass checkin to Advanced Extras extension.
	 *
	 * @return string
	 */
	public static function apply_checkin_to_advanced_extras() {
		return self::$checkin;
	}

	/**
	 * Pass checkout to Advanced Extras extension.
	 *
	 * @return string
	 */
	public static function apply_checkout_to_advanced_extras() {
		return self::$checkout;
	}
}

endif;

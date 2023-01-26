<?php
/**
 * Reservation Data Meta Boxes.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Reservation_Data' ) ) :

/**
 * HTL_Meta_Box_Reservation_Data Class
 */
class HTL_Meta_Box_Reservation_Data {

	/**
	 * Guest details
	 *
	 * @var array
	 */
	protected static $guest_details = array();

	/**
	 * Guest info
	 *
	 * @var array
	 */
	protected static $guest_info = array();

	/**
	 * Init guest details fields
	 */
	public static function init_guest_details() {

		self::$guest_details = apply_filters( 'hotelier_admin_guest_details_fields', array(
			'first_name' => array(
				'label'       => esc_html__( 'First name', 'wp-hotelier' ),
				'required'    => true,
				'description' => esc_html__( 'Guest\'s first name', 'wp-hotelier' ),
			),
			'last_name' => array(
				'label'         => esc_html__( 'Last name', 'wp-hotelier' ),
				'wrapper_class' => 'form-field-last',
				'required'      => true,
				'description'   => esc_html__( 'Guest\'s last name', 'wp-hotelier' ),
			),
			'email' => array(
				'label'       => esc_html__( 'Email address', 'wp-hotelier' ),
				'type'        => 'email',
				'required'    => true,
				'description' => esc_html__( 'Guest\'s email address', 'wp-hotelier' ),
			),
			'telephone' => array(
				'label'         => esc_html__( 'Telephone', 'wp-hotelier' ),
				'wrapper_class' => 'form-field-last',
				'description'   => esc_html__( 'Guest\'s phone number', 'wp-hotelier' ),
			),
			'address1' => array(
				'label'         => esc_html__( 'Address 1', 'wp-hotelier' ),
				'wrapper_class' => 'form-field-wide',
				'description'   => esc_html__( 'Guest\'s address', 'wp-hotelier' ),
			),
			'address2' => array(
				'label'         => esc_html__( 'Address 2', 'wp-hotelier' ),
				'wrapper_class' => 'form-field-wide',
				'description'   => esc_html__( 'Guest\'s additional address', 'wp-hotelier' ),
			),
			'city' => array(
				'label' => esc_html__( 'Town / City', 'wp-hotelier' ),
				'description'   => esc_html__( 'Guest\'s city', 'wp-hotelier' ),
			),
			'postcode' => array(
				'label'         => esc_html__( 'Postcode / Zip', 'wp-hotelier' ),
				'wrapper_class' => 'form-field-last',
				'description'   => esc_html__( 'Guest\'s postcode', 'wp-hotelier' ),
			),
			'state' => array(
				'label'       => esc_html__( 'State / County', 'wp-hotelier' ),
				'description' => esc_html__( 'Guest\'s state', 'wp-hotelier' ),
			),
			'country' => array(
				'label'         => esc_html__( 'Country', 'wp-hotelier' ),
				'type'          => 'select',
				'options'       => htl_get_country_codes(),
				'required'      => true,
				'wrapper_class' => 'form-field-last',
				'description'   => esc_html__( 'Guest\'s country', 'wp-hotelier' ),
			)
		) );
	}

	/**
	 * Init guest info fields
	 */
	public static function init_guest_info() {

		self::$guest_info = apply_filters( 'hotelier_admin_guest_info_fields', array(
			'guest_arrival_time' => array(
				'id'      => 'guest_arrival_time',
				'label'   => esc_html__( 'Estimated arrival time', 'wp-hotelier' ),
				'type'    => 'select',
				'options' => array(
					'-1' => esc_html__( 'I don\'t know', 'wp-hotelier' ),
					'0'  => '00:00 - 01:00',
					'1'  => '01:00 - 02:00',
					'2'  => '02:00 - 03:00',
					'3'  => '03:00 - 04:00',
					'4'  => '04:00 - 05:00',
					'5'  => '05:00 - 06:00',
					'6'  => '06:00 - 07:00',
					'7'  => '07:00 - 08:00',
					'8'  => '08:00 - 09:00',
					'9'  => '09:00 - 10:00',
					'10' => '10:00 - 11:00',
					'11' => '11:00 - 12:00',
					'12' => '12:00 - 13:00',
					'13' => '13:00 - 14:00',
					'14' => '14:00 - 15:00',
					'15' => '15:00 - 16:00',
					'16' => '16:00 - 17:00',
					'17' => '17:00 - 18:00',
					'18' => '18:00 - 19:00',
					'19' => '19:00 - 20:00',
					'20' => '20:00 - 21:00',
					'21' => '21:00 - 22:00',
					'22' => '22:00 - 23:00',
					'23' => '23:00 - 00:00'
				)
			)
		) );
	}

	/**
	 * Get guest details fields
	 */
	public static function get_guest_details_fields() {
		self::init_guest_details();

		return self::$guest_details;
	}

	/**
	 * Get guest info fields
	 */
	public static function get_guest_info_fields() {
		self::init_guest_info();

		return self::$guest_info;
	}

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $thereservation;

		if ( ! is_object( $thereservation ) ) {
			$thereservation = htl_get_reservation( $post->ID );
		}

		$reservation = $thereservation;
		if ( HTL()->payment_gateways() ) {
			$payment_gateways = HTL()->payment_gateways->get_available_payment_gateways();
		} else {
			$payment_gateways = array();
		}

		$payment_method = $reservation->get_payment_method() ? $reservation->get_payment_method() : '';

		$booking_method = $reservation->booking_method;

		self::init_guest_details();
		self::init_guest_info();

		wp_nonce_field( 'hotelier_save_data', 'hotelier_meta_nonce' );
		?>

		<style type="text/css">
			#post-body-content, #titlediv, .misc-pub-section.misc-pub-post-status, #visibility, #minor-publishing-actions { display:none }
		</style>

		<div class="htl-ui-scope edit-reservation-page">
			<input name="post_title" type="hidden" value="<?php echo empty( $post->post_title ) ? esc_attr__( 'Reservation', 'wp-hotelier' ) : esc_attr( $post->post_title ); ?>" />
			<input name="post_status" type="hidden" value="<?php echo esc_attr( $post->post_status ); ?>" />

			<h2 class="htl-ui-heading edit-reservation-page__title"><?php echo sprintf( esc_html__( 'Reservation #%d details', 'wp-hotelier' ), $reservation->id ); ?></h2>

			<div class="edit-reservation-page__booking-details booking-details">
				<span class="booking-details__booking-mode"><?php echo esc_html__( 'Booking mode', 'wp-hotelier' ) . ': ' . esc_html( ucfirst( str_replace( '-', ' ', $booking_method ) ) ); ?></span>

				<?php if ( ( get_post_meta( $post->ID, '_created_via', true ) == 'admin' ) ) : ?>
					<span class="booking-details__created-by-admin"><?php esc_html_e( '(created by admin)', 'wp-hotelier' ); ?></span>
				<?php endif; ?>

				<?php if ( $payment_method ) : ?>
					<span class="booking-details__payment-method"><?php echo sprintf( esc_html__( 'Payment via %s', 'wp-hotelier' ), $reservation->get_payment_method_title() ); ?></span>

					<?php if ( $transaction_id = $reservation->get_transaction_id() ) {
						if ( isset( $payment_gateways[ $payment_method ] ) && ( $url = $payment_gateways[ $payment_method ]->get_transaction_url( $reservation ) ) ) {
							?>
							<span class="booking-details__transaction-id"><?php esc_html_e( 'Transaction ID:', 'wp-hotelier' ); ?> <a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( $transaction_id ); ?></a></span>
							<?php
						}
					} ?>
				<?php endif; ?>

				<?php do_action( 'hotelier_reservation_after_booking_details', $reservation ); ?>
			</div>


			<div class="edit-reservation-page__general-details">

				<h3 class="htl-ui-heading htl-ui-heading--section-header"><?php esc_html_e( 'General details', 'wp-hotelier' ); ?></h3>

				<?php
				$can_be_modified = $reservation->get_paid_deposit() > 0 || $reservation->requires_capture() ? false : true;

				HTL_Meta_Boxes_Helper::select_input(
					array(
						'name'    => 'reservation_status',
						'value'   => 'htl-' . $reservation->get_status(),
						'label'   => esc_html__( 'Reservation status:', 'wp-hotelier' ),
						'options' => htl_get_reservation_statuses(),
					)
				);
				?>

				<?php
				if ( ! $can_be_modified ) {
					HTL_Meta_Boxes_Helper::plain(
						array(
							'label'       => esc_html__( 'Check-in:', 'wp-hotelier' ),
							'description' => $reservation->get_formatted_checkin(),
						)
					);
				} else {
					HTL_Meta_Boxes_Helper::datepicker(
						array(
							'name'        => 'reservation_checkin',
							'label'       => esc_html__( 'Check-in:', 'wp-hotelier' ),
							'value'       => $reservation->get_checkin(),
							'description' => esc_html__( 'Check-in date.', 'wp-hotelier' ),
							'class'       => 'htl-ui-input--start-date'
						)
					);
				}
				?>

				<?php
				if ( ! $can_be_modified ) {
					HTL_Meta_Boxes_Helper::plain(
						array(
							'label'       => esc_html__( 'Check-out:', 'wp-hotelier' ),
							'description' => $reservation->get_formatted_checkout(),
						)
					);
				} else {
					HTL_Meta_Boxes_Helper::datepicker(
						array(
							'name'        => 'reservation_checkout',
							'label'       => esc_html__( 'Check-out:', 'wp-hotelier' ),
							'value'       => $reservation->get_checkout(),
							'description' => esc_html__( 'Check-out date.', 'wp-hotelier' ),
							'class'       => 'htl-ui-input--end-date'
						)
					);
				}
				?>

				<?php
				if ( htl_coupons_enabled() && $can_be_modified ) {
					$all_coupons   = htl_get_all_coupons();
					$coupon_values = array(
						'-1' => '--- ' . esc_html__( "Don't apply a new coupon (leave current one if any)", "wp-hotelier" )
					);

					if ( $reservation->get_coupon_id() ) {
						$coupon_values['0'] = '--- ' . esc_html__( 'Remove existing coupon', 'wp-hotelier' );
					}

					if ( is_array( $all_coupons ) ) {
						foreach ( $all_coupons as $coupon_id => $coupon_value ) {
							if ( isset( $coupon_value['code'] ) ) {
								$coupon_code = $coupon_value['code'];

								if ( isset( $coupon_value['title'] ) ) {
									$coupon_code .= ' (' . $coupon_value['title'] . ')';
								}

								$coupon_values[$coupon_id] = $coupon_code;
							}
						}
					}

					HTL_Meta_Boxes_Helper::select_input(
						array(
							'name'        => 'coupon_id',
							'label'       => esc_html__( 'Apply a coupon:', 'wp-hotelier' ),
							'description' => esc_html__( 'Select the coupon you want to apply.', 'wp-hotelier' ),
							'options'     => $coupon_values,
							'std'         => $reservation->get_coupon_id(),
						)
					);
				}
				?>

				<?php
				HTL_Meta_Boxes_Helper::plain(
					array(
						'label'       => esc_html__( 'Number of nights:', 'wp-hotelier' ),
						'description' => sprintf( esc_html__( '%d-night stay', 'wp-hotelier' ), $reservation->get_nights() ),
					)
				);
				?>
			</div>

			<div class="edit-reservation-page__guest-details">

				<h3 class="htl-ui-heading htl-ui-heading--section-header"><?php esc_html_e( 'Guest details', 'wp-hotelier' ); ?></h3>

				<?php do_action( 'hotelier_reservation_guest_data' ); ?>

				<?php do_action( 'hotelier_reservation_before_guest_details' ); ?>

				<?php
				foreach ( self::$guest_details as $key => $field ) {
					if ( ! isset( $field[ 'id' ] ) ){
						$field[ 'id' ] = '_guest_' . $key;
					}

					$field_id         = isset( $field[ 'id' ] ) ? $field[ 'id' ] : '_guest_' . $key;
					$field[ 'value' ] = get_post_meta( $post->ID, $field_id, true );

					// Backward compatibility for country field
					if ( $field_id === '_guest_country' ) {
						$country_list = htl_get_country_codes();

						if ( ! isset( $country_list[ $field[ 'value' ] ] ) ) {
							HTL_Meta_Boxes_Helper::text_input( $field );

							continue;
						}
					}

					if ( isset( $field[ 'type' ] ) && method_exists( 'HTL_Meta_Boxes_Helper', $field[ 'type' ] . '_input' ) ) {
						call_user_func( array( 'HTL_Meta_Boxes_Helper', $field[ 'type' ] . '_input' ), $field );
					} else {
						HTL_Meta_Boxes_Helper::text_input( $field );
					}
				}
				?>

				<?php do_action( 'hotelier_reservation_after_guest_details' ); ?>

				<h3 class="htl-ui-heading htl-ui-heading--section-header"><?php esc_html_e( 'Guest notes', 'wp-hotelier' ); ?></h3>

				<?php do_action( 'hotelier_reservation_before_guest_arrival_time' ); ?>

				<?php
				HTL_Meta_Boxes_Helper::select_input(
					array(
						'name'    => 'guest_arrival_time',
						'value'   => $reservation->get_arrival_time(),
						'std'     => $reservation->get_arrival_time(),
						'label'   => esc_html__( 'Estimated arrival time:', 'wp-hotelier' ),
						'options' => array(
							'-1' => esc_html__( 'I don\'t know', 'wp-hotelier' ),
							'0'  => '00:00 - 01:00',
							'1'  => '01:00 - 02:00',
							'2'  => '02:00 - 03:00',
							'3'  => '03:00 - 04:00',
							'4'  => '04:00 - 05:00',
							'5'  => '05:00 - 06:00',
							'6'  => '06:00 - 07:00',
							'7'  => '07:00 - 08:00',
							'8'  => '08:00 - 09:00',
							'9'  => '09:00 - 10:00',
							'10' => '10:00 - 11:00',
							'11' => '11:00 - 12:00',
							'12' => '12:00 - 13:00',
							'13' => '13:00 - 14:00',
							'14' => '14:00 - 15:00',
							'15' => '15:00 - 16:00',
							'16' => '16:00 - 17:00',
							'17' => '17:00 - 18:00',
							'18' => '18:00 - 19:00',
							'19' => '19:00 - 20:00',
							'20' => '20:00 - 21:00',
							'21' => '21:00 - 22:00',
							'22' => '22:00 - 23:00',
							'23' => '23:00 - 00:00'
						),
					)
				);
				?>

				<?php do_action( 'hotelier_reservation_after_guest_arrival_time' ); ?>

				<?php do_action( 'hotelier_reservation_before_guest_special_requets' ); ?>

				<?php
				HTL_Meta_Boxes_Helper::textarea_input(
					array(
						'name'        => 'guest_special_requests',
						'value'       => $reservation->get_guest_special_requests(),
						'label'       => esc_html__( 'Special requests:', 'wp-hotelier' ),
					)
				);
				?>

				<?php do_action( 'hotelier_reservation_after_guest_special_requets' ); ?>

			</div>
		</div>
		<?php
	}

	/**
	 * Save reservation data
	 */
	public static function save( $post_id, $post ) {
		$reservation = htl_get_reservation( $post_id );

		// Reservation status
		$reservation->update_status( sanitize_text_field( $_POST[ 'reservation_status' ] ), '', true );

		// Guest special requests
		$reservation->update_guest_special_requests( sanitize_text_field( $_POST[ 'guest_special_requests' ] ) );

		// Guest estimated arrival time
		$reservation->set_arrival_time( sanitize_text_field( $_POST[ 'guest_arrival_time' ] ) );
	}
}

endif;

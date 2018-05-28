<?php
/**
 * Personal data erasers.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Privacy_Erasers' ) ) :

/**
 * HTL_Privacy_Erasers Class
 */
class HTL_Privacy_Erasers {

	/**
	 * Finds and erases data associated with an email address.
	 *
	 * @param string $email The guest email address.
	 * @return array An array of personal data in name value pairs
	 */
	public static function reservation_data_eraser( $email ) {
		$erasure_enabled = htl_get_option( 'privacy_remove_reservation_data_on_erasure_request', false );
		$response        = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		$reservations = htl_get_reservations_by_email( $email );

		foreach ( $reservations as $reservation ) {
			if ( apply_filters( 'hotelier_privacy_erase_reservation_personal_data', $erasure_enabled, $reservation ) ) {
				self::remove_reservation_personal_data( $reservation );

				$response[ 'messages' ][]    = sprintf( __( 'Removed personal data from reservation %s.', 'wp-hotelier' ), $reservation->get_reservation_number() );
				$response[ 'items_removed' ] = true;
			} else {
				$response[ 'messages' ][]     = sprintf( __( 'Personal data within reservation %s has been retained.', 'wp-hotelier' ), $reservation->get_reservation_number() );
				$response[ 'items_retained' ] = true;
			}
		}

		return $response;
	}

	/**
	 * Remove personal data from a reservation object.
	 *
	 * @param HTL_Reservation $reservation Reservation object.
	 * @return array
	 */
	public static function remove_reservation_personal_data( $reservation ) {
		/**
		 * Allow extensions to remove their own personal data for this reservation
		 *
		 * @param HTL_Reservation $reservation A reservation object.
		 */
		do_action( 'hotelier_privacy_before_remove_reservation_personal_data', $reservation );

		/**
		 * Expose props and data types we'll be anonymizing.
		 */
		$props_to_remove = apply_filters( 'hotelier_privacy_remove_reservation_personal_data_props', array(
			'reservation_guest_ip_address'                     => 'ip',
			'guest_first_name'                                 => 'text',
			'guest_last_name'                                  => 'text',
			'guest_address1'                                   => 'text',
			'guest_address2'                                   => 'text',
			'guest_city'                                       => 'text',
			'guest_postcode'                                   => 'text',
			'guest_state'                                      => 'text',
			'guest_country'                                    => 'text',
			'guest_telephone'                                  => 'phone',
			'guest_email'                                      => 'email',
			'transaction_id'                                   => 'numeric_id',
			'reservation_remain_deposit_charge_transaction_id' => 'numeric_id',
		), $reservation );

		if ( ! empty( $props_to_remove ) && is_array( $props_to_remove ) ) {
			foreach ( $props_to_remove as $prop => $data_type ) {
				$value = $reservation->{$prop};

				// Skip empty values
				if ( empty( $value ) || empty( $data_type ) ) {
					continue;
				}

				$anon_value = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( $data_type, $value ) : '';

				$anon_value = apply_filters( 'hotelier_privacy_remove_reservation_personal_data_prop_value', $anon_value, $prop, $value, $data_type, $reservation );

				update_post_meta( $reservation->id, '_' . $prop, $anon_value );
			}
		}

		// Remove meta data.
		$meta_to_remove = apply_filters( 'hotelier_privacy_remove_reservation_personal_data_meta', array(
			'Payer first name'     => 'text',
			'Payer last name'      => 'text',
			'Payer PayPal address' => 'email',
		) );

		if ( ! empty( $meta_to_remove ) && is_array( $meta_to_remove ) ) {
			foreach ( $meta_to_remove as $meta_key => $data_type ) {
				$value = get_post_meta( $reservation->id, $meta_key, true );

				// If the value is empty, it does not need to be anonymized.
				if ( empty( $value ) || empty( $data_type ) ) {
					continue;
				}

				$anon_value = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( $data_type, $value ) : '';

				$anon_value = apply_filters( 'hotelier_privacy_remove_reservation_personal_data_meta_value', $anon_value, $meta_key, $value, $data_type, $order );

				if ( $anon_value ) {
					update_post_meta( $reservation->id, $meta_key, $anon_value );
				} else {
					delete_post_meta( $reservation->id, $meta_key );
				}
			}
		}

		// Update title
		wp_update_post( array( 'ID' => $reservation->id, 'post_title' => '#' . sanitize_text_field( $reservation->id ) . ' - [deleted]' ) );

		update_post_meta( $reservation->id, '_anonymized', 'yes' );

		// Add reservation note
		$reservation->add_reservation_note( esc_html__( 'Personal data removed.', 'wp-hotelier' ) );

		/**
		 * Allow extensions to remove their own personal data for this reservation.
		 */
		do_action( 'hotelier_privacy_remove_reservation_personal_data', $reservation );
	}
}

endif;

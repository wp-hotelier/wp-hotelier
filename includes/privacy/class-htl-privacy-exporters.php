<?php
/**
 * Personal data exporters.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Privacy_Exporters' ) ) :

/**
 * HTL_Privacy_Exporters Class
 */
class HTL_Privacy_Exporters {

	/**
	 * Finds and exports data associated with an email address.
	 *
	 * @param string $email The guest email address.
	 * @return array An array of personal data in name value pairs
	 */
	public static function reservation_data_exporter( $email ) {
		$data_to_export = array();
		$reservations   = htl_get_reservations_by_email( $email );

		foreach ( $reservations as $reservation ) {
			$data_to_export[] = array(
				'group_id'    => 'hotelier_reservations',
				'group_label' => __( 'Reservations', 'wp-hotelier' ),
				'item_id'     => 'reservation-' . $reservation->id,
				'data'        => self::get_reservation_personal_data( $reservation ),
			);
		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * Get personal data (key/value pairs) for a reservation object.
	 *
	 * @param HTL_Reservation $reservation Reservation object.
	 * @return array
	 */
	protected static function get_reservation_personal_data( $reservation ) {
		$personal_data   = array();
		$props_to_export = apply_filters( 'hotelier_privacy_export_reservation_personal_data_props', array(
			'reservation_number'      => __( 'Resevation Number', 'wp-hotelier' ),
			'date_created'            => __( 'Resevation Date', 'wp-hotelier' ),
			'guest_ip_address'        => __( 'IP Address', 'wp-hotelier' ),
			'formatted_guest_address' => __( 'Guest Address', 'wp-hotelier' ),
			'guest_telephone'         => __( 'Guest Phone Number', 'wp-hotelier' ),
			'guest_email'             => __( 'Email Address', 'wp-hotelier' ),
		), $reservation );

		foreach ( $props_to_export as $prop => $name ) {
			$value = '';

			switch ( $prop ) {
				case 'reservation_number':
					$value = $reservation->id;
					break;
				case 'date_created':
					$value = $reservation->reservation_date;
					break;
				case 'guest_ip_address':
					$value = $reservation->reservation_guest_ip_address;
					break;
				case 'formatted_guest_address':
					$value = preg_replace( '#<br\s*/?>#i', ', ', $reservation->get_formatted_guest_address() );
					break;
				case 'guest_telephone':
					$value = $reservation->guest_telephone;
					break;
				case 'guest_email':
					$value = $reservation->guest_email;
					break;
			}

			$value = apply_filters( 'hotelier_privacy_export_reservation_personal_data_prop', $value, $prop, $reservation );

			if ( $value ) {
				$personal_data[] = array(
					'name'  => $name,
					'value' => $value,
				);
			}
		}

		/**
		 * Allow extensions to register their own personal data for this reservation for the export.
		 *
		 * @param array    $personal_data Array of name value pairs to expose in the export.
		 * @param HTL_Reservation $reservation A reservation object.
		 */
		$personal_data = apply_filters( 'hotelier_privacy_export_reservation_personal_data', $personal_data, $reservation );

		return $personal_data;
	}
}

endif;

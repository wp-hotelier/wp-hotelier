<?php
/**
 * Hotelier Booking Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generate unique key
 *
 * Returns the md5 hash of a string
 *
 * @param string $rate_name Room rate
 * @param string $rate_id Room rate ID
 * @return string
 */
function htl_generate_item_key( $room_id, $rate_id ) {
	return md5( $room_id . '_' . $rate_id );
}

/**
 * Get default adults selection
 */
function htl_get_reservation_table_guests_default_adults_selection( $adults, $item_key, $index ) {
	if ( isset( $_POST['form_data'] ) ) {
		$form_data = array();
		parse_str( $_POST['form_data'], $form_data );

		if ( isset( $form_data['adults'] ) ) {
			$adults_data = $form_data['adults'];

			if ( is_array( $adults_data ) && isset( $adults_data[$item_key] ) ) {
				$adults_line = $adults_data[$item_key];

				if ( isset( $adults_line[$index] ) ) {
					$adults = $adults_line[$index];
				}
			}
		}
	}

	return $adults;
}

/**
 * Get default children selection
 */
function htl_get_reservation_table_guests_default_children_selection( $children, $item_key, $index ) {
	if ( isset( $_POST['form_data'] ) ) {
		$form_data = array();
		parse_str( $_POST['form_data'], $form_data );

		if ( isset( $form_data['children'] ) ) {
			$children_data = $form_data['children'];

			if ( is_array( $children_data ) && isset( $children_data[$item_key] ) ) {
				$children_line = $children_data[$item_key];

				if ( isset( $children_line[$index] ) ) {
					$children = $children_line[$index];
				}
			}
		}
	}

	return $children;
}

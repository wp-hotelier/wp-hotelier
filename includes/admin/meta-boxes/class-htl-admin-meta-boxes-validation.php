<?php
/**
 * Hotelier Meta Boxes validation class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Meta_Boxes_Validation' ) ) :

/**
 * HTL_Admin_Meta_Boxes_Validation Class
 */
class HTL_Admin_Meta_Boxes_Validation {

	/**
	 * Sanitize text input
	 */
	public static function sanitize_text( $data ) {
		return sanitize_text_field( $data );
    }

    /**
	 * Sanitize textarea input
	 */
	public static function sanitize_textarea( $data ) {
		return sanitize_text_field( $data );
	}

	/**
	 * Sanitize number
	 */
	public static function sanitize_number( $number ) {
		return absint( $number );
	}

	/**
	 * Sanitize select input
	 */
	public static function sanitize_select( $data ) {
		return sanitize_key( $data );
	}

	/**
	 * Sanitize multiselect input
	 */
	public static function sanitize_multiselect( $data ) {
		if ( is_array( $data ) ) {
			$data = array_map( 'sanitize_key', $data );
		} else {
			$data = array();
		}

		return $data;
	}

	/**
	 * Sanitize checkbox input
	 */
	public static function sanitize_checkbox( $data ) {
		return $data ? true : false;
	}

	/**
	 * Sanitize price amount
	 */
	public static function sanitize_price( $price ) {
		if ( empty( $price ) ) {
			return;
		}

		return HTL_Formatting_Helper::sanitize_amount( $price );
	}

	/**
	 * Sanitize price per day
	 */
	public static function sanitize_price_per_day( $prices ) {
		if ( is_array( $prices ) ) {
			foreach ( $prices as $key => $value ) {
				if ( empty( $prices[ $key ] ) ) {
					return;
				}
				$prices[ $key ] = HTL_Formatting_Helper::sanitize_amount( $value );
			}
		}

		return $prices;
	}

	/**
	 * Sanitize switch input
	 */
	public static function sanitize_switch( $data ) {
		return sanitize_text_field( $data );
	}

	/**
	 * Sanitize seasonal price
	 */
	public static function sanitize_seasonal_price( $prices ) {
		if ( is_array( $prices ) ) {
			foreach ( $prices as $key => $value ) {
				$prices[ $key ] = HTL_Formatting_Helper::sanitize_amount( $value );
			}
		}

		return $prices;
	}

	/**
	 * Sanitize multi text field
	 */
	public static function sanitize_multi_text( $values ) {
		if ( is_array( $values ) ) {
			$count = count( $values );

			// ensures values are correctly mapped to an array starting with an index of 1
			uasort( $values, function( $a, $b ) {
				return $a[ 'index' ] - $b[ 'index' ];
			});

			$values = array_combine( range( 1, count( $values ) ), array_values( $values ) );

			foreach ( $values as $id => $value ) {
				if ( empty( $value[ 'name' ] ) && ( $count > 1 ) ) {
					unset( $values[ $id ] );
					$count --;
					continue;
				}

				$values[ $id ][ 'name' ] = self::sanitize_text( $value[ 'name' ] );
			}

		}

		return array_combine( range( 1, count( $values ) ), array_values( $values ) );
	}

	/**
	 * Sanitize variations
	 */
	public static function sanitize_room_variations( $variations ) {
		global $post;

		// Store an array of rated IDs (terms of the taxonomy 'room_rate')
		$term_ids = array();

		// First check if we are saving a variable room
		if ( $_POST[ '_room_type' ] === 'variable_room' ) {

			if ( is_array( $variations ) ) {

				// Don't save placeholder variation
				if ( array_key_exists( '99999', $variations ) ) {
					unset( $variations[ 99999 ] );
				}

				// Ensures variations are correctly mapped to an array starting with an index of 1
				uasort( $variations, function( $a, $b ) {
					return $a[ 'index' ] - $b[ 'index' ];
				});

				$variations = array_combine( range( 1, count( $variations ) ), array_values( $variations ) );

				foreach ( $variations as $id => $variation ) {
					$keys = apply_filters( 'hotelier_room_variation_keys', array(
						'room_rate'           => 'select',
						'price_type'          => 'switch',
						'regular_price'       => 'price',
						'sale_price'          => 'price',
						'price_day'           => 'price_per_day',
						'sale_price_day'      => 'price_per_day',
						'seasonal_base_price' => 'price',
						'seasonal_price'      => 'seasonal_price',
						'require_deposit'     => 'checkbox',
						'deposit_amount'      => 'select',
						'non_cancellable'     => 'checkbox',
						'room_conditions'     => 'multi_text',
					) );

					foreach ( $keys as $key => $validation ) {
						if ( isset( $variations[ $id ][ $key ] ) ) {
							if ( method_exists( __CLASS__, 'sanitize_' . $validation ) && is_callable( array( __CLASS__, 'sanitize_' . $validation ) ) ) {
								$variations[ $id ][ $key ] = call_user_func( array( __CLASS__, 'sanitize_' . $validation ), $variations[ $id ][ $key ] );
							} else {
								// Each field is passed to a custom filter that validates the input
								$value = $variations[ $id ][ $key ];
								$variations[ $id ][ $key ] = apply_filters( 'hotelier_meta_box_save_variation_field_' . $key, $value );
							}
						}
					}

					// Get room_rate ID ('term_id')
					$term = term_exists( $variations[ $id ][ 'room_rate' ], 'room_rate' );
					$term = absint( $term[ 'term_id' ] );

					if ( $term !== 0 && $term !== null ) {
						$term_ids[] = $term;
					}
				}
			}

		} else {
			$variations = array();
		}

		// Assign rate terms to the post (empty if it is standard room)
		wp_set_object_terms( $post->ID, $term_ids, 'room_rate' );

		return $variations;
	}

	/**
	 * Sanitize date input
	 */
	public static function sanitize_date( $data ) {
		if ( HTL_Formatting_Helper::is_valid_date( $data ) ) {
			return $data;
		}

		return '';
	}
}

endif;

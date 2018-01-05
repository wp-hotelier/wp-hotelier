<?php
/**
 * Hotelier Meta Boxes Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Boxes_Helper' ) ) :

/**
 * HTL_Meta_Boxes_Helper Class
 */
class HTL_Meta_Boxes_Helper {
	/**
	 * Output a text input box.
	 */
	public static function text_input( $field ) {
		global $thepostid, $post;

		$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;

		$field[ 'placeholder' ]   = isset( $field[ 'placeholder' ] ) ? $field[ 'placeholder' ] : '';
		$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
		$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
		$field[ 'value' ]         = isset( $field[ 'value' ] ) ? $field[ 'value' ] : get_post_meta( $thepostid, $field[ 'id' ], true );
		$field[ 'name' ]          = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
		$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';
		$field[ 'type' ]          = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';
		$field[ 'min' ]           = isset( $field[ 'min' ] ) ? $field[ 'min' ] : false;
		$field[ 'max' ]           = isset( $field[ 'max' ] ) ? $field[ 'max' ] : false;
		$data_type                = empty( $field[ 'data_type' ] ) ? '' : $field[ 'data_type' ];

		$field_id = '';

		if ( isset( $field[ 'show_id' ] ) && false !== $field[ 'show_id' ] ) {
			$field_id = 'id="' . esc_attr( $field[ 'id' ] ) . '"';
		}

		if ( get_post_meta( $thepostid, $field[ 'id' ], true ) ) {
			$field[ 'value' ] = get_post_meta( $thepostid, $field[ 'id' ], true );

			if ( isset( $field[ 'depth' ] ) ) {
				$field[ 'value' ] = isset ( $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ] ) ? $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ] : '';
			}
		}

		if ( $data_type == 'price' ) {
			$field[ 'class' ] .= ' htl-input-price';
			$field[ 'value' ]  = HTL_Formatting_Helper::localized_amount( $field[ 'value' ] );
		}

		$field_min_max = '';

		if ( $field[ 'type' ] == 'number' ) {
			if ( $field[ 'min' ] ) {
				$field_min_max .= ' min="' . $field[ 'min' ] . '"';
			}

			if ( $field[ 'max' ] ) {
				$field_min_max .= ' max="' . $field[ 'max' ] . '"';
			}
		}

		echo '<p class="form-field ' . esc_attr( $field[ 'wrapper_class' ] ) . '"><label><span>' . wp_kses_post( $field[ 'label' ] ) . '</span><input type="' . esc_attr( $field[ 'type' ] ) . '" class="' . esc_attr( $field[ 'class' ] ) . '" name="' . esc_attr( $field[ 'name' ] ) . '" value="' . esc_attr( $field[ 'value' ] ) . '" placeholder="' . esc_attr( $field[ 'placeholder' ] ) . '" ' . $field_id . $field_min_max . ' /></label>';

		if ( isset( $field[ 'after_input' ] ) ) {
			echo '<span class="after-input">' . wp_kses_post( $field[ 'after_input' ] ) . '</span>';
		}

		if ( ! empty( $field[ 'description' ] ) ) {
			if ( isset( $field[ 'desc_tip' ] ) && false !== $field[ 'desc_tip' ] ) {
				echo '<span title="' . esc_attr( $field[ 'description' ] ) . '" class="hastip"><i class="htl-icon htl-help-circled"></i></span>';
			} else {
				echo '<span class="description">' . wp_kses_post( $field[ 'description' ] ) . '</span>';
			}
		}

		echo '</p>';
	}

	/**
	 * Output a textarea input box.
	 */
	public static function textarea_input( $field ) {
		global $thepostid, $post;

		$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;

		$field[ 'placeholder' ]   = isset( $field[ 'placeholder' ] ) ? $field[ 'placeholder' ] : '';
		$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
		$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
		$field[ 'value' ]         = isset( $field[ 'value' ] ) ? $field[ 'value' ] : get_post_meta( $thepostid, $field[ 'id' ], true );
		$field[ 'name' ]          = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
		$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';
		$field_id = '';

		if ( isset( $field[ 'show_id' ] ) && false !== $field[ 'show_id' ] ) {
			$field_id = 'id="' . esc_attr( $field[ 'id' ] ) . '"';
		}

		if ( get_post_meta( $thepostid, $field[ 'id' ], true ) ) {
			$field[ 'value' ] = get_post_meta( $thepostid, $field[ 'id' ], true );

			if ( isset( $field[ 'depth' ] ) ) {
				$field[ 'value' ] = isset ( $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ] ) ? $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ] : '';
			}
		}

		echo '<p class="form-field ' . esc_attr( $field[ 'wrapper_class' ] ) . '"><label><span>' . wp_kses_post( $field[ 'label' ] ) . '</span><textarea cols="40" rows="5" class="' . esc_attr( $field[ 'class' ] ) . '" name="' . esc_attr( $field[ 'name' ] ) . '" placeholder="' . esc_attr( $field[ 'placeholder' ] ) . '" ' . $field_id . '>' . esc_attr( $field[ 'value' ] ) . '</textarea></label>';

		if ( ! empty( $field[ 'description' ] ) ) {
			if ( isset( $field[ 'desc_tip' ] ) && false !== $field[ 'desc_tip' ] ) {
				echo '<span title="' . esc_attr( $field[ 'description' ] ) . '" class="hastip"><i class="htl-icon htl-help-circled"></i></span>';
			} else {
				echo '<span class="description">' . wp_kses_post( $field[ 'description' ] ) . '</span>';
			}
		}

		echo '</p>';
	}

	/**
	 * Output a select input box.
	 */
	public static function select_input( $field ) {
		global $thepostid, $post;

		$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;

		$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
		$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
		$field[ 'value' ]         = isset( $field[ 'value' ] ) ? $field[ 'value' ] : '';
		$field[ 'name' ]          = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
		$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';

		$field_id = '';

		if ( isset( $field[ 'show_id' ] ) && false !== $field[ 'show_id' ] ) {
			$field_id = 'id="' . esc_attr( $field[ 'id' ] ) . '"';
		}

		if ( get_post_meta( $thepostid, $field[ 'id' ], true ) ) {
			$field[ 'value' ] = get_post_meta( $thepostid, $field[ 'id' ], true );

			if ( isset( $field[ 'depth' ] ) ) {
				$field[ 'value' ] = isset ( $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ] ) ? $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ] : '';
			}
		}

		echo '<p class="form-field ' . esc_attr( $field[ 'wrapper_class' ] ) . '"><label><span>' . wp_kses_post( $field[ 'label' ] ) . '</span><select class="' . esc_attr( $field[ 'class' ] ) . '" name="' . esc_attr( $field[ 'name' ] ) . '" ' . $field_id . '>';

		foreach ( $field[ 'options' ] as $key => $value ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field[ 'value' ] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
		}

		echo '</select></label>';

		if ( isset( $field[ 'after_input' ] ) ) {
			echo '<span class="after-input">' . wp_kses_post( $field[ 'after_input' ] ) . '</span>';
		}

		if ( ! empty( $field[ 'description' ] ) ) {
			if ( isset( $field[ 'desc_tip' ] ) && false !== $field[ 'desc_tip' ] ) {
				echo '<span title="' . esc_attr( $field[ 'description' ] ) . '" class="hastip"><i class="htl-icon htl-help-circled"></i></span>';
			} else {
				echo '<span class="description">' . wp_kses_post( $field[ 'description' ] ) . '</span>';
			}
		}

		echo '</p>';
	}

	/**
	 * Output a checkbox input box.
	 */
	public static function checkbox_input( $field ) {
		global $thepostid, $post;

		$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;

		$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
		$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
		$field[ 'value' ]         = get_post_meta( $thepostid, $field[ 'id' ], true );
		$field[ 'name' ]          = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
		$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';

		$field_id = '';

		if ( isset( $field[ 'show_id' ] ) && false !== $field[ 'show_id' ] ) {
			$field_id = 'id="' . esc_attr( $field[ 'id' ] ) . '"';
		}

		if ( isset( $field[ 'depth' ] ) ) {
			if ( isset( $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ] ) ) {
				$checked = checked( 1, $field[ 'value' ][ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ], false );
			} else {
				$checked = '';
			}
		} else {
			$checked = checked( 1, $field[ 'value' ], false );
		}

		echo '<p class="form-field ' . esc_attr( $field[ 'wrapper_class' ] ) . '"><label><span>' . wp_kses_post( $field[ 'label' ] ) . '</span><input type="checkbox" class="' . esc_attr( $field[ 'class' ] ) . '" name="' . esc_attr( $field[ 'name' ] ) . '" value="1" ' . $checked . ' ' . $field_id . ' /></label>';

		if ( isset( $field[ 'after_input' ] ) ) {
			echo '<span class="after-input">' . wp_kses_post( $field[ 'after_input' ] ) . '</span>';
		}

		if ( ! empty( $field[ 'description' ] ) ) {
			if ( isset( $field[ 'desc_tip' ] ) && false !== $field['desc_tip'] ) {
				echo '<span title="' . esc_attr( $field[ 'description' ] ) . '" class="hastip"><i class="htl-icon htl-help-circled"></i></span>';
			} else {
				echo '<span class="description checkbox-description">' . wp_kses_post( $field[ 'description' ] ) . '</span>';
			}
		}

		echo '</p>';

	}

	/**
	 * Output price per day input box.
	 */
	public static function price_per_day( $field ) {
		global $wp_locale, $thepostid, $post;

		$thepostid        = empty( $thepostid ) ? $post->ID : $thepostid;

		$field[ 'id' ]    = isset( $field[ 'id' ] ) ? $field[ 'id' ] : '';
		$field[ 'label' ] = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';
		$field[ 'name' ]  = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];

		echo '<div class="price-per-day-row">';

		echo '<span class="label-text">' . wp_kses_post( $field[ 'label' ] ) . '</span>';

		echo '<p class="form-field price-per-day-fields">';

		$locale_start_of_week = get_option( 'start_of_week' );

		for ( $i = 0; $i < 7; $i ++ ) {
			$day_index = ( $locale_start_of_week + $i ) % 7;
			$day = $wp_locale->get_weekday( $day_index );
			$day_initial = $wp_locale->get_weekday_initial( $day );

			// check if the post meta exists and if it is an array
			if ( get_post_meta( $thepostid, $field[ 'id' ], true ) && ( is_array( get_post_meta( $thepostid, $field[ 'id' ], true ) ) ) ) {
				$meta = get_post_meta( $thepostid, $field[ 'id' ], true );

				if ( isset( $field[ 'depth' ] ) ) {
					$meta = $meta[ $field[ 'depth' ][ 0 ] ][ $field[ 'depth' ][ 1 ] ];
				}

				$value = 'value="' .  HTL_Formatting_Helper::localized_amount( $meta[ $day_index ] ) . '"';
			} else {
				$value = '';
			}

			echo '<span class="single-day-field"><label><span>' . $day_initial . '</span><input type="text" name="' . $field[ 'name' ] . '[' . $day_index . ']" ' . $value . ' placeholder="' . HTL_Meta_Box_Room_Settings::get_price_placeholder() . '" class="htl-input-price" /></label></span>';
		}

		if ( ! empty( $field[ 'description' ] ) ) {
			if ( isset( $field[ 'desc_tip' ] ) && false !== $field[ 'desc_tip' ] ) {
				echo '<span title="' . esc_attr( $field[ 'description' ] ) . '" class="hastip"><i class="htl-icon htl-help-circled"></i></span>';
			} else {
				echo '<span class="description">' . wp_kses_post( $field[ 'description' ] ) . '</span>';
			}
		}

		echo '</p>';

		echo '</div>';
	}
}

endif;

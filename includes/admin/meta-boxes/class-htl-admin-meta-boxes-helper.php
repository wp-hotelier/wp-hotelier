<?php
/**
 * Hotelier Meta Boxes Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.5.0
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
	 * Output a text input field.
	 */
	public static function text_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-text.php';
	}

	/**
	 * Output a number input field.
	 */
	public static function number_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-number.php';
	}

	/**
	 * Output a textarea input field.
	 */
	public static function textarea_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-textarea.php';
	}

	/**
	 * Output a select input field.
	 */
	public static function select_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-select.php';
	}

	/**
	 * Output a multiselect input field.
	 */
	public static function multiselect_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-multiselect.php';
	}

	/**
	 * Output a checkbox input field.
	 */
	public static function checkbox_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-checkbox.php';
	}

	/**
	 * Output a price input field.
	 */
	public static function price_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-price.php';
	}

	/**
	 * Output price per day input field.
	 */
	public static function price_per_day( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-price-per-day.php';
	}

	/**
	 * Output a switch input field.
	 */
	public static function switch_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-switch.php';
	}

	/**
	 * Output room conditions field.
	 */
	public static function multi_text( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-multi-text.php';
	}

	/**
	 * Output a button input field.
	 */
	public static function button_input( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-button.php';
	}

	/**
	 * Output plain text.
	 */
	public static function plain( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-plain.php';
	}

	/**
	 * Output a datepicker input.
	 */
	public static function datepicker( $field ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fields/html-meta-box-field-datepicker.php';
	}

	/**
	 * Get the value of a field inside a variation.
	 */
	public static function get_variation_field_value( $variations, $field_id, $index, $default = null ) {
		$default = $default !== null ? $default : '';

		if ( ! is_array( $variations ) || empty( $variations ) ) {
			return $default;
		}

		if ( isset( $variations[ $index ][ $field_id ] ) ) {
			return $variations[ $index ][ $field_id ];
		}

		return $default;
	}
}

endif;

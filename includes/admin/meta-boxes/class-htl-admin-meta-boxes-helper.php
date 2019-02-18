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
}

endif;

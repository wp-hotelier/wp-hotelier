<?php
/**
 * Extra Class.
 *
 * @author   Lollum
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Extra' ) ) :

/**
 * HTL_Extra Class
 */
class HTL_Extra {
	/**
	 * The Extra (post) ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * $post Stores post data
	 *
	 * @var $post WP_Post
	 */
	public $post = null;

	/**
	 * Get things going
	 */
	public function __construct( $coupon ) {
		if ( is_numeric( $coupon ) ) {
			$this->id   = absint( $coupon );
			$this->post = get_post( $this->id );
		} elseif ( $coupon instanceof HTL_Extra ) {
			$this->id   = absint( $coupon->id );
			$this->post = $coupon->post;
		} elseif ( isset( $coupon->ID ) ) {
			$this->id   = absint( $coupon->ID );
			$this->post = $coupon;
		}
	}

	/**
	 * __get function.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		$value = get_post_meta( $this->id, '_' . $key, true );

		return $value;
	}

	/**
	 * Returns whether or not the extra post exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return empty( $this->post ) ? false : true;
	}

	/**
	 * Checks if the extra is enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$enabled = $this->extra_enabled;
		$enabled = $enabled === 'enabled' ? true : false;

		return apply_filters( 'hotelier_is_extra_enabled', $enabled, $this->id, $this );
	}

	/**
	 * Gets the extra name
	 *
	 * @return string
	 */
	public function get_name() {
		$extra_name = $this->extra_name;

		return apply_filters( 'hotelier_get_extra_name', $extra_name, $this->id, $this );
	}

	/**
	 * Gets the extra description
	 *
	 * @return string
	 */
	public function get_description() {
		$extra_description = $this->extra_description;

		return apply_filters( 'hotelier_get_extra_description', $extra_description, $this->id, $this );
	}

	/**
	 * Gets the extra amount type
	 *
	 * @return string
	 */
	public function get_amount_type() {
		$extra_amount_type = $this->extra_amount_type;
		$extra_amount_type = $extra_amount_type === 'fixed' ? 'fixed' : 'percentage';

		return apply_filters( 'hotelier_get_extra_amount_type', $extra_amount_type, $this->id, $this );
	}

	/**
	 * Gets the extra amount
	 *
	 * @return int
	 */
	public function get_amount() {
		$amount = 0;

		if ( $this->get_amount_type() === 'fixed' ) {
			$amount = $this->extra_amount_fixed;
		} else {
			$amount = $this->extra_amount_percentage;
		}

		return apply_filters( 'hotelier_get_extra_amount', absint( $amount ), $this->id, $this );
	}

	/**
	 * Gets the extra type
	 *
	 * @return string
	 */
	public function get_type() {
		$extra_type = $this->extra_type;
		$extra_type = $extra_type === 'per_room' ? 'per_room' : 'per_person';

		return apply_filters( 'hotelier_get_extra_type', $extra_type, $this->id, $this );
	}

	/**
	 * Check if the cost should be multiplied for the nights of staying
	 *
	 * @return bool
	 */
	public function calculate_per_night() {
		$calculate_per_night = $this->extra_calculate_per_night;

		return apply_filters( 'hotelier_get_extra_calculate_per_night', $calculate_per_night, $this->id, $this );
	}

	/**
	 * Gets max cost of extra when calculation per night is enabled
	 *
	 * @return int
	 */
	public function get_max_cost() {
		$max_cost = $this->extra_max_cost;

		return apply_filters( 'hotelier_get_extra_max_cost', absint( $max_cost ), $this->id, $this );
	}

	/**
	 * Gets allowed guest type
	 *
	 * @return string
	 */
	public function get_allowed_guest_type() {
		$extra_guest_type = $this->extra_guest_type;

		return apply_filters( 'hotelier_get_extra_allowed_guest_type', $extra_guest_type, $this->id, $this );
	}

	/**
	 * Checks if the extra is optional
	 *
	 * @return bool
	 */
	public function is_optional() {
		$is_optional = $this->extra_make_optional;
		$is_optional = $is_optional ? true : false;

		return apply_filters( 'hotelier_get_extra_is_optional', $is_optional, $this->id, $this );
	}

	/**
	 * Checks if the quantity can be selected
	 *
	 * @return bool
	 */
	public function can_select_quantity() {
		$can_select_quantity = $this->extra_selectable_qty;
		$can_select_quantity = $can_select_quantity ? true : false;

		return apply_filters( 'hotelier_get_extra_can_select_quantity', $can_select_quantity, $this->id, $this );
	}

	/**
	 * Get max quantity that can be selected
	 *
	 * @return bool
	 */
	public function get_max_quantity() {
		$max_quantity = absint( $this->extra_selectable_qty_max );

		return apply_filters( 'hotelier_get_extra_max_quantity', $max_quantity, $this->id, $this );
	}
}

endif;

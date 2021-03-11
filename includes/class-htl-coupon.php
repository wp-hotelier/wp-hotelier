<?php
/**
 * Coupon Class.
 *
 * @author   Lollum
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Coupon' ) ) :

/**
 * HTL_Coupon Class
 */
class HTL_Coupon {
	/**
	 * The Coupon (post) ID.
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
		} elseif ( $coupon instanceof HTL_Coupon ) {
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
	 * Gets the coupon code
	 *
	 * @return string
	 */
	public function get_code() {
		$coupon_code = $this->coupon_code;
		$coupon_code = str_replace(' ', '', $coupon_code);

		return apply_filters( 'hotelier_get_coupon_code', $coupon_code, $this->id, $this );
	}

	/**
	 * Gets the coupon type
	 *
	 * @return string
	 */
	public function get_type() {
		$coupon_type = $this->coupon_type;
		$coupon_type = $coupon_type === 'fixed' ? 'fixed' : 'percentage';

		return apply_filters( 'hotelier_get_coupon_type', $coupon_type, $this->id, $this );
	}

	/**
	 * Gets the coupon amount
	 *
	 * @return int
	 */
	public function get_amount() {
		$amount = 0;

		if ( $this->get_type() === 'fixed' ) {
			$amount = $this->coupon_amount_fixed;
		} else {
			$amount = $this->coupon_amount_percentage;
		}

		return apply_filters( 'hotelier_get_coupon_amount', $amount, $this->id, $this );
	}

	/**
	 * Gets the coupon expiration date (if any)
	 *
	 * @return string
	 */
	public function expiration_date() {
		$expiration_date = $this->coupon_expiration_date;
		$expiration_date = $expiration_date ? $expiration_date : false;

		return apply_filters( 'hotelier_get_coupon_expiration_date', $expiration_date, $this->id, $this );
	}

	/**
	 * Checks if the coupon is active
	 *
	 * @return string
	 */
	public function is_active() {
		$active = true;

		if ( $this->is_enabled() ) {
			if ( $expiration_date = $this->expiration_date() ) {
				$curdate         = new DateTime( current_time( 'Y-m-d' ) );
				$expiration_date = new DateTime( $expiration_date );

				if ( $curdate > $expiration_date ) {
					$active = false;
				}
			}
		} else {
			$active = false;
		}

		return apply_filters( 'hotelier_is_coupon_active', $active, $this->id, $this );
	}

	/**
	 * Checks if the coupon is enabled
	 *
	 * @return string
	 */
	public function is_enabled() {
		$enabled = $this->coupon_enabled;
		$enabled = $enabled === 'enabled' ? true : false;

		return apply_filters( 'hotelier_is_coupon_enabled', $enabled, $this->id, $this );
	}

	/**
	 * Gets the coupon has usage limit
	 *
	 * @return int
	 */
	public function get_usage_limit() {
		$usage_limit = absint( $this->coupon_usage_limit );

		return apply_filters( 'hotelier_get_coupon_usage_limit', $usage_limit, $this->id, $this );
	}

	/**
	 * Gets coupon usage
	 *
	 * @return int
	 */
	public function get_usage_count() {
		$usage_count = absint( $this->usage_count );

		return apply_filters( 'hotelier_get_coupon_usage', $usage_count, $this->id, $this );
	}

	/**
	 * Checks if the coupon has exceeded its usage limit
	 *
	 * @return bool
	 */
	public function is_coupon_usage_exceeded() {
		$usage_exceeded = false;
		$usage_limit    = $this->get_usage_limit();
		$usage_count    = $this->get_usage_count();

		if ( $usage_limit > 0 && $usage_count > 0 && $usage_count >= $usage_limit ) {
			$usage_exceeded = true;
		}

		return apply_filters( 'hotelier_is_coupon_usage_exceeded', $usage_exceeded, $this->id, $this );
	}
}

endif;

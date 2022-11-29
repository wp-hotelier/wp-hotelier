<?php
/**
 * Load admin assets.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Frontend_Scripts' ) ) :

/**
 * HTL_Frontend_Scripts Class
 */
class HTL_Frontend_Scripts {
	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
	}

	/**
	 * Enqueue styles
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_styles() {
		$default_style    = apply_filters( 'hotelier_enqueue_styles', true );
		$lightbox_enabled = htl_get_option( 'room_lightbox', true );

		if ( $lightbox_enabled && ( is_listing() || ( is_room() && ! htl_get_option( 'room_hide_gallery' ) ) ) ) {
			wp_register_style( 'photoswipe', HTL_PLUGIN_URL . 'assets/css/frontend/photoswipe/photoswipe.css', array(), '4.1.1' );
			wp_enqueue_style( 'photoswipe-default-skin', HTL_PLUGIN_URL . 'assets/css/frontend/photoswipe/default-skin/default-skin.css', array( 'photoswipe' ), '4.1.1' );
		}

		if ( $default_style ) {
			wp_enqueue_style( 'hotelier-css', HTL_PLUGIN_URL . 'assets/css/frontend/hotelier.css', array(), HTL_VERSION );
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$lightbox_enabled = htl_get_option( 'room_lightbox', true );

		// Enqueue the main Hotelier script
		wp_enqueue_script( 'hotelier-js', HTL_PLUGIN_URL . 'assets/js/frontend/hotelier' . $suffix . '.js', array( 'jquery' ), HTL_VERSION, true );

		// Datepicker params
		$hotelier_params = array(
			'book_now_redirect_to_booking_page' => htl_get_option( 'book_now_redirect_to_booking_page', 0 ),
			'book_now_allow_quantity_selection' => htl_get_option( 'book_now_allow_quantity_selection', 0 ),
			'apply_coupon_nonce'                => wp_create_nonce( 'hotelier-apply-coupon-nonce' ),
			'apply_coupon_i18n'                 => array(
				'empty_coupon' => esc_html__( 'Please insert a valid coupon code.', 'wp-hotelier' ),
			),
			'ajax_url'                          => HTL()->ajax_url(),
			'enable_debug'                      => defined('WP_DEBUG') && true === WP_DEBUG ? true : false
		);

		wp_localize_script( 'hotelier-js', 'hotelier_params', $hotelier_params );

		// Get first day of week (can be sunday or monday)
		$start_of_week = 'monday';
		if ( get_option( 'start_of_week' ) === '0' ) {
			$start_of_week = 'sunday';
		}

		// Start date
		$arrival_date = htl_get_option( 'booking_arrival_date', 0 );
		$start_date = new DateTime( current_time( 'Y-m-d' ) );
		$start_date->modify( "+$arrival_date days" );
		$start_date = $start_date->format( 'Y-m-d' );

		// End date
		$end_date = false;

		// Check if months_advance is set.
		$months_advance = htl_get_option( 'booking_months_advance', 0 );

		if ( $months_advance ) {
			$end_date = new DateTime( current_time( 'Y-m-d' ) );
			$end_date->modify( "+$months_advance months" );
			$end_date = $end_date->format( 'Y-m-d' );
		}

		// Create array of weekday names
		$timestamp       = strtotime('next Sunday');
		$day_names       = array();
		$day_names_short = array();

		for ( $i = 0; $i < 7; $i++ ) {
			$day_names[] = date_i18n( 'l', $timestamp );
			$day_names_short[] = date_i18n( 'D', $timestamp );
			$timestamp = strtotime('+1 day', $timestamp);
		}

		// Create array of month names (full textual and short)
		$month_names = array();
		$month_names_short = array();
		$timestamp = strtotime('2016-01-01');

		for ( $i = 0; $i < 12; $i++ ) {
			$month_names[] = date_i18n( 'F', $timestamp );
			$month_names_short[] = date_i18n( 'M', $timestamp );
			$timestamp = strtotime('+1 month', $timestamp);
		}

		// Datepicker params
		$datepicker_params = array(
			'ajax_url'           => HTL()->ajax_url(),
			'htl_ajax_url'       => HTL_AJAX::get_endpoint( 'get_checkin_dates' ),
			'start_of_week'      => $start_of_week,
			'start_date'         => $start_date,
			'end_date'           => $end_date,
			'move_both_months'   => htl_get_option( 'datepicker_move_both_months', false ),
			'autoclose'          => htl_get_option( 'datepicker_autoclose', false ),
			'min_nights'         => apply_filters( 'hotelier_datepicker_min_nights', htl_get_option( 'booking_minimum_nights', 1 ) ),
			'max_nights'         => apply_filters( 'hotelier_datepicker_max_nights', htl_get_option( 'booking_maximum_nights', 0 ) ),
			'datepicker_format'  => apply_filters( 'hotelier_datepicker_format', 'D MMM YYYY' ),
			'disabled_dates'     => apply_filters( 'hotelier_datepicker_disabled_dates', array() ),
			'enable_checkout'    => apply_filters( 'hotelier_datepicker_enable_checkout', true ),
			'disabled_days_of_week' => apply_filters( 'hotelier_datepicker_disabled_days_of_week', array() ),
			'inline'             => apply_filters( 'hotelier_datepicker_inline', false ),
			'topbar_position'    => apply_filters( 'hotelier_datepicker_topbar_position', 'top' ),
			'submit_button_name' => 'hotelier_datepicker_button',
			'i18n'               => array(
				'selected'          => esc_html_x( 'Your stay:', 'datepicker_selected', 'wp-hotelier' ),
				'night'             => esc_html_x( 'Night', 'datepicker_night', 'wp-hotelier' ),
				'nights'            => esc_html_x( 'Nights', 'datepicker_nights', 'wp-hotelier' ),
				'button'            => esc_html_x( 'Close', 'datepicker_apply', 'wp-hotelier' ),
    			'clearButton'       => esc_html_x( 'Clear', 'datepicker_apply', 'wp-hotelier' ),
    			'submitButton'      => esc_html_x( 'Check', 'datepicker_apply', 'wp-hotelier' ),
				'checkin-disabled'  => esc_html_x( 'Check-in disabled', 'datepicker_checkin_disabled', 'wp-hotelier' ),
				'checkout-disabled' => esc_html_x( 'Check-out disabled', 'datepicker_checkout_disabled', 'wp-hotelier' ),
				'day-names'         => $day_names,
				'day-names-short'   => $day_names_short,
				'month-names'       => $month_names,
				'month-names-short' => $month_names_short,
				'error-more'        => esc_html_x( 'Date range should not be more than 1 night', 'datepicker_error_more', 'wp-hotelier' ),
				'error-more-plural' => esc_html_x( 'Date range should not be more than %d nights', 'datepicker_error_more_plural', 'wp-hotelier' ),
				'error-less'        => esc_html_x( 'Date range should not be less than 1 night', 'datepicker_error_less', 'wp-hotelier' ),
				'error-less-plural' => esc_html_x( 'Date range should not be less than %d nights', 'datepicker_error_less_plural', 'wp-hotelier' ),
				'info-more'         => esc_html_x( 'Please select a date range longer than 1 night', 'datepicker_info_more', 'wp-hotelier' ),
				'info-more-plural'  => esc_html_x( 'Please select a date range longer than %d nights', 'datepicker_info_more_plural', 'wp-hotelier' ),
				'info-range'        => esc_html_x( 'Please select a date range between %d and %d nights', 'datepicker_info_range', 'wp-hotelier' ),
				'info-range-equal'        => esc_html_x( 'Please select a date range of %d nights', 'datepicker_info_range', 'wp-hotelier' ),
				'info-default'      => esc_html_x( 'Please select a date range', 'datepicker_info_default', 'wp-hotelier' ),
				'aria-application'      => esc_html_x( 'Calendar', 'datepicker_aria_application', 'wp-hotelier' ),
				'aria-selected-checkin'      => esc_html_x( 'Selected as check-in date, %s', 'datepicker_aria_selected_checkin', 'wp-hotelier' ),
				'aria-selected-checkout'      => esc_html_x( 'Selected as check-out date, %s', 'datepicker_aria_selected_checkout', 'wp-hotelier' ),
				'aria-selected'      => esc_html_x( 'Selected, %s', 'datepicker_aria_selected', 'wp-hotelier' ),
				'aria-disabled'      => esc_html_x( 'Not available, %s', 'datepicker_aria_disabled', 'wp-hotelier' ),
				'aria-choose-checkin'      => esc_html_x( 'Choose %s as your check-in date', 'datepicker_aria_choose_checkin', 'wp-hotelier' ),
				'aria-choose-checkout'      => esc_html_x( 'Choose %s as your check-out date', 'datepicker_aria_choose_checkout', 'wp-hotelier' ),
				'aria-prev-month'      => esc_html_x( 'Move backward to switch to the previous month', 'datepicker_aria_prev_month', 'wp-hotelier' ),
				'aria-next-month'      => esc_html_x( 'Move forward to switch to the next month', 'datepicker_aria_next_month', 'wp-hotelier' ),
				'aria-close-button'      => esc_html_x( 'Close the datepicker', 'datepicker_aria_close_button', 'wp-hotelier' ),
				'aria-clear-button'      => esc_html_x( 'Clear the selected dates', 'datepicker_aria_clear_button', 'wp-hotelier' ),
				'aria-submit-button'      => esc_html_x( 'Submit the form', 'datepicker_aria_submit_button', 'wp-hotelier' ),
			)
		);

		// Localize and enqueue the datepicker scripts
		wp_register_script( 'fecha', HTL_PLUGIN_URL . 'assets/js/lib/fecha/fecha.min.js', array(), '4.2.1', true );
		wp_register_script( 'hotel-datepicker', HTL_PLUGIN_URL . 'assets/js/lib/hotel-datepicker/hotel-datepicker.min.js', array( 'fecha' ), '4.4.0', true );

		wp_register_script( 'hotelier-init-datepicker', HTL_PLUGIN_URL . 'assets/js/frontend/hotelier-init-datepicker' . $suffix . '.js', array( 'jquery', 'hotel-datepicker' ), HTL_VERSION, true );

		wp_localize_script( 'hotelier-init-datepicker', 'datepicker_params', $datepicker_params );

		if ( is_listing() || is_room() ) {
			wp_enqueue_script( 'hotelier-init-datepicker' );
		}

		// Lightbox scripts
		if ( $lightbox_enabled && ( is_listing() || ( is_room() && ! htl_get_option( 'room_hide_gallery' ) ) ) ) {

			// PhotoSwipe
			wp_enqueue_script( 'photoswipe', HTL_PLUGIN_URL . 'assets/js/lib/photoswipe/photoswipe.min.js', array(), '4.1.1', true );
			wp_enqueue_script( 'photoswipe-ui', HTL_PLUGIN_URL . 'assets/js/lib/photoswipe/photoswipe-ui-default.min.js', array( 'photoswipe' ), '4.1.1', true );
			wp_enqueue_script( 'photoswipe-init', HTL_PLUGIN_URL . 'assets/js/frontend/photoswipe.init' . $suffix . '.js', array( 'jquery', 'photoswipe-ui' ), HTL_VERSION, true );
		}

		if ( is_room() ) {
			// AJAX Room Booking script
			$hotelier_ajax_room_booking_params = array(
				'nonce'        => wp_create_nonce( 'hotelier-ajax-room-booking-nonce' ),
				'ajax_url'     => HTL()->ajax_url(),
				'locale'     => array(
					'default_button_text'  => esc_html__( 'Check availability', 'wp-hotelier' ),
					'checking_button_text' => esc_html__( 'Checking availability...', 'wp-hotelier' ),
					'booking_button_text'  => esc_html__( 'Booking room...', 'wp-hotelier' ),
					'book_button_text'     => esc_html__( 'Book now', 'wp-hotelier' ),
				),
				'enable_debug' => defined('WP_DEBUG') && true === WP_DEBUG ? true : false
			);

			wp_register_script( 'hotelier-ajax-room-booking', HTL_PLUGIN_URL . 'assets/js/frontend/hotelier-ajax-room-booking' . $suffix . '.js', array( 'jquery' ), HTL_VERSION, true );

			wp_localize_script( 'hotelier-ajax-room-booking', 'hotelier_ajax_room_booking_params', $hotelier_ajax_room_booking_params );
		}
	}
}

endif;

return new HTL_Frontend_Scripts();

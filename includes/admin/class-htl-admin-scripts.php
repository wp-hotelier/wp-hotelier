<?php
/**
 * Load admin assets.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Scripts' ) ) :

/**
 * HTL_Admin_Scripts Class
 */
class HTL_Admin_Scripts {
	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles
	 *
	 * @access public
	 * @return void
	 */
	public function admin_styles() {
		$screen = get_current_screen();
		$prefix = HTL_Admin_Functions::get_prefix_screen_id();

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Menu icon
		wp_enqueue_style( 'hotelier_menu_styles', HTL_PLUGIN_URL . 'assets/css/admin/menu.css', array(), HTL_VERSION );

		// Font Awesome
		wp_register_style( 'fontawesome', HTL_PLUGIN_URL . 'assets/fonts/fontawesome/css/all' . $suffix . '.css', array(), '5.8.1' );

		// Admin styles for all Hotelier pages
		if ( in_array( $screen->id, HTL_Admin_Functions::get_screen_ids() ) ) {
			wp_enqueue_style( 'hotelier_admin_styles', HTL_PLUGIN_URL . 'assets/css/admin/admin.css', array(), HTL_VERSION );
			wp_enqueue_style( 'fontawesome' );
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @access public
	 * @return void
	 */
	public function admin_scripts() {
		$screen = get_current_screen();
		$prefix = HTL_Admin_Functions::get_prefix_screen_id();

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts
		wp_register_script( 'htl-admin', HTL_PLUGIN_URL . 'assets/js/admin/admin' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		wp_register_script( 'htl-admin-settings', HTL_PLUGIN_URL . 'assets/js/admin/settings' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		wp_register_script( 'htl-admin-fields', HTL_PLUGIN_URL . 'assets/js/admin/fields' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), HTL_VERSION );

		// Localize
		$admin_params = array(
			'decimal_error' => sprintf( esc_html__( 'Please enter in decimal (%s) format without thousand separators.', 'wp-hotelier' ), htl_get_price_decimal_separator() ),
			'decimal_point' => htl_get_price_decimal_separator()
		);
		wp_localize_script( 'htl-admin', 'AdminParameters', $admin_params );

		// All hotelier pages
		if ( in_array( $screen->id, HTL_Admin_Functions::get_screen_ids() ) ) {
			wp_enqueue_script( 'htl-admin' );
		}

		// Admin settings
		if ( $screen->id == 'toplevel_page_hotelier-settings' ) {
			wp_enqueue_media();
			wp_enqueue_script( 'htl-admin-settings' );
		}

		// Room meta boxes
		if ( in_array( $screen->id, array( 'room', 'edit-room' ) ) ) {
			wp_register_script( 'htl-admin-room-meta-boxes', HTL_PLUGIN_URL . 'assets/js/admin/meta-boxes-room' . $suffix . '.js', array( 'jquery-ui-sortable' ), HTL_VERSION );
			wp_enqueue_script( 'htl-admin-room-meta-boxes' );
		}

		// Admin settings and room meta boxes
		if ( $screen->id == 'toplevel_page_hotelier-settings' || in_array( $screen->id, array( 'room', 'edit-room', 'coupon', 'extra' ) ) ) {
			wp_enqueue_script( 'htl-admin-fields' );
		}

		// Reservation meta boxes
		if ( in_array( $screen->id, array( 'room_reservation', 'edit-room_reservation' ) ) ) {
			$reservation_params = array(
				'i18n_do_remain_deposit_charge' => esc_html__( 'Are you sure you wish to proceed with this charge? This action cannot be undone.', 'wp-hotelier' )
			);

			wp_enqueue_script( 'htl-admin-reservation-meta-boxes', HTL_PLUGIN_URL . 'assets/js/admin/meta-boxes-reservation' . $suffix . '.js', array(), HTL_VERSION );

			wp_localize_script( 'htl-admin-reservation-meta-boxes', 'reservation_meta_params', $reservation_params );
		}

		// New reservation
		if ( $screen->id == $prefix . '_hotelier-add-reservation' ) {
			wp_enqueue_script( 'htl-admin-add-reservation', HTL_PLUGIN_URL . 'assets/js/admin/new-reservation' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		}

		// Calendar script
		if ( $screen->id == $prefix . '_hotelier-calendar' ) {
			wp_enqueue_script( 'htl-admin-calendar', HTL_PLUGIN_URL . 'assets/js/admin/calendar' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		}

		// Coupons
		if ( in_array( $screen->id, array( 'coupon', 'edit-coupon' ) ) ) {
			wp_enqueue_script( 'htl-admin-coupon-meta-boxes', HTL_PLUGIN_URL . 'assets/js/admin/meta-boxes-coupon' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		}

		// Admin settings, room page, new reservation, calendar page, reservation page
		if ( $screen->id == 'toplevel_page_hotelier-settings' || in_array( $screen->id, array( 'room', 'edit-room' ) ) || $screen->id == $prefix . '_hotelier-add-reservation' || $screen->id == $prefix . '_hotelier-calendar' || in_array( $screen->id, array( 'room_reservation', 'edit-room_reservation' ) ) || in_array( $screen->id, array( 'coupon', 'edit-coupon' ) ) ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}
	}

}

endif;

return new HTL_Admin_Scripts();

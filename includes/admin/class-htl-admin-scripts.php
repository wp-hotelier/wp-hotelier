<?php
/**
 * Load admin assets.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  1.0.0
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

		// Menu icon
		wp_enqueue_style( 'hotelier_menu_styles', HTL_PLUGIN_URL . 'assets/css/menu.css', array(), HTL_VERSION );

		// jquery-ui
		wp_register_style( 'jquery-ui-css', HTL_PLUGIN_URL . 'assets/css/jquery-ui.css', array(), HTL_VERSION );

		if ( in_array( $screen->id, HTL_Admin_Functions::get_screen_ids() ) ) {

			if ( $screen->id != 'hotelier_page_hotelier-calendar' ) {
				// Admin styles for Hotelier pages only
				wp_enqueue_style( 'hotelier_admin_styles', HTL_PLUGIN_URL . 'assets/css/admin.css', array(), HTL_VERSION );
			}


		}

		// Settings, new reservation and calendar pages only
		if ( $screen->id == 'toplevel_page_hotelier-settings' || $screen->id == 'hotelier_page_hotelier-calendar' || $screen->id == 'hotelier_page_hotelier-add-reservation' ) {
			wp_enqueue_style( 'jquery-ui-css' );
		}

		// Booking calendar style
		if ( $screen->id == 'hotelier_page_hotelier-calendar' ) {
			wp_enqueue_style( 'hotelier_calendar_styles', HTL_PLUGIN_URL . 'assets/css/calendar.css', array(), HTL_VERSION );
		}

		// Addons page style
		if ( $screen->id == 'hotelier_page_hotelier-addons' ) {
			wp_enqueue_style( 'hotelier_addons_styles', HTL_PLUGIN_URL . 'assets/css/addons.css', array(), HTL_VERSION );
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

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts

		wp_register_script( 'htl-admin-settings', HTL_PLUGIN_URL . 'assets/js/admin/settings' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		wp_register_script( 'htl-admin-meta-boxes', HTL_PLUGIN_URL . 'assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-tipsy' ), HTL_VERSION );
		wp_register_script( 'jquery-tipsy', HTL_PLUGIN_URL . 'assets/js/lib/jquery-tipsy/jquery-tipsy' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );

		// Admin settings
		if ( $screen->id == 'toplevel_page_hotelier-settings' ) {
			wp_enqueue_media();
			wp_enqueue_script( 'htl-admin-settings' );
		}

		// Admin, new reservation and calendar
		if ( $screen->id == 'toplevel_page_hotelier-settings' || $screen->id == 'hotelier_page_hotelier-calendar' || $screen->id == 'hotelier_page_hotelier-add-reservation' ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}

		// Room meta boxes
		if ( in_array( $screen->id, array( 'room', 'edit-room' ) ) ) {
			wp_register_script( 'accounting', HTL_PLUGIN_URL . 'assets/js/lib/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2' );

			$room_params = array(
				'decimal_error'                => sprintf( esc_html__( 'Please enter in decimal (%s) format without thousand separators.', 'wp-hotelier' ), htl_get_price_decimal_separator() ),
				'sale_less_than_regular_error'  => esc_html__( 'Please enter in a value less than the regular price.', 'wp-hotelier' ),
				'decimal_point'                     => htl_get_price_decimal_separator()
			);

			wp_register_script( 'htl-admin-room-meta-boxes', HTL_PLUGIN_URL . 'assets/js/admin/meta-boxes-room' . $suffix . '.js', array( 'htl-admin-meta-boxes', 'accounting' ), HTL_VERSION );

			wp_localize_script( 'htl-admin-room-meta-boxes', 'room_params', $room_params );

			wp_enqueue_script( 'htl-admin-room-meta-boxes' );
		}

		// Reservation meta boxes
		if ( in_array( $screen->id, array( 'room_reservation', 'edit-room_reservation' ) ) ) {
			$reservation_params = array(
				'i18n_do_remain_deposit_charge' => esc_html__( 'Are you sure you wish to proceed with this charge? This action cannot be undone.', 'wp-hotelier' )
			);

			wp_enqueue_script( 'htl-admin-reservation-meta-boxes', HTL_PLUGIN_URL . 'assets/js/admin/meta-boxes-reservation' . $suffix . '.js', array( 'htl-admin-meta-boxes' ), HTL_VERSION );

			wp_localize_script( 'htl-admin-reservation-meta-boxes', 'reservation_meta_params', $reservation_params );
		}

		// New reservation
		if ( $screen->id == 'hotelier_page_hotelier-add-reservation' ) {
			wp_enqueue_script( 'htl-admin-add-reservation', HTL_PLUGIN_URL . 'assets/js/admin/new-reservation' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		}

		// Calendar script
		if ( $screen->id == 'hotelier_page_hotelier-calendar' ) {
			wp_enqueue_script( 'htl-admin-calendar', HTL_PLUGIN_URL . 'assets/js/admin/calendar' . $suffix . '.js', array( 'jquery' ), HTL_VERSION );
		}
	}

}

endif;

return new HTL_Admin_Scripts();

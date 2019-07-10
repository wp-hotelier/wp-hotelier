<?php
/**
 * Setup menus in WP admin.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Menus' ) ) :

/**
 * HTL_Admin_Menus Class
 *
 * Creates the admin menu pages.
 */
class HTL_Admin_Menus {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add Hotelier pages to WP menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'admin_calendar' ), 30 );
		add_action( 'admin_menu', array( $this, 'admin_logs' ), 50 );
	}

	/**
	 * Add settings page
	 */
	public function admin_menu() {
		add_submenu_page( 'hotelier-settings', esc_html__( 'Add New Reservation', 'wp-hotelier' ),  esc_html__( 'Add Reservation', 'wp-hotelier' ) , 'manage_hotelier', 'hotelier-add-reservation', array( $this, 'add_new_reservation_page' ) );
	}

	/**
	 * Add calendar page
	 */
	public function admin_calendar() {
		add_submenu_page( 'hotelier-settings', esc_html__( 'Calendar', 'wp-hotelier' ),  esc_html__( 'Calendar', 'wp-hotelier' ) , 'manage_hotelier', 'hotelier-calendar', array( $this, 'calendar_page' ) );
	}

	/**
	 * Add logs page
	 */
	public function admin_logs() {
		add_submenu_page( 'hotelier-settings', esc_html__( 'Logs', 'wp-hotelier' ),  esc_html__( 'Logs', 'wp-hotelier' ) , 'manage_hotelier', 'hotelier-logs', array( $this, 'log_page' ) );
	}

	/**
	 * Init the 'Log' page
	 */
	public function log_page() {
		HTL_Admin_Logs::output();
	}

	/**
	 * Init the 'Add New Reservation' page
	 */
	public function add_new_reservation_page() {
		HTL_Admin_New_Reservation::output();
	}

	/**
	 * Init the 'Calendar' page
	 */
	public function calendar_page() {
		HTL_Admin_Calendar::output();
	}
}

endif;

return new HTL_Admin_Menus();

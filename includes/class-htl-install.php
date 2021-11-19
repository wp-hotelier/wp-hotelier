<?php
/**
 * Install Function
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Install' ) ) :

/**
 * HTL_Install Class
 */
class HTL_Install {

	/**
	 * Init functions.
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_filter( 'plugin_action_links_' . HTL_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Check HTL version.
	 *
	 * @access public
	 * @return void
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'hotelier_version' ) != HTL_VERSION ) ) {
			self::install();
			do_action( 'hotelier_updated' );
		}
	}

	/**
	 * Install HTL
	 */
	public static function install() {
		// Ensure needed classes are loaded
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-roles.php';

		// Create tables
		self::create_tables();

		// Create log files
		self::create_files();

		// Cron jobs
		self::create_cron_jobs();

		// Create HTL hotel roles
		$roles = new HTL_Roles;
		$roles->add_roles();
		$roles->add_caps();

		// Update version
		delete_option( 'hotelier_version' );
		add_option( 'hotelier_version', HTL_VERSION );

		// Register endpoints
		HTL()->query->init_query_vars();
		HTL()->query->add_endpoints();

		// Clear the permalinks
		flush_rewrite_rules( false );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET[ 'activate-multi' ] ) ) {
			return;
		}

		// Trigger action
		do_action( 'hotelier_installed' );
	}

	/**
	 * Create files/directories
	 */
	private static function create_files() {
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir = wp_upload_dir();

		$files = array(
			array(
				'base' 		=> HTL_LOG_DIR,
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> HTL_LOG_DIR,
				'file' 		=> 'index.html',
				'content' 	=> ''
			)
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file[ 'base' ] ) && ! file_exists( trailingslashit( $file[ 'base' ] ) . $file[ 'file' ] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file[ 'base' ] ) . $file[ 'file' ], 'w' ) ) {
					fwrite( $file_handle, $file[ 'content' ] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * Set up the database tables which Hotelier needs to function.
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::get_schema() );
	}

	/**
	 * Get Table schema
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}hotelier_reservation_items (
			reservation_item_id bigint(20) NOT NULL auto_increment,
			reservation_item_name longtext NOT NULL,
			reservation_id bigint(20) NOT NULL,
			PRIMARY KEY  (reservation_item_id),
			KEY reservation_id (reservation_id)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}hotelier_reservation_itemmeta (
			meta_id bigint(20) NOT NULL auto_increment,
			reservation_item_id bigint(20) NOT NULL,
			meta_key varchar(255) NULL,
			meta_value longtext NULL,
			PRIMARY KEY  (meta_id),
			KEY reservation_item_id (reservation_item_id),
			KEY meta_key (meta_key)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}hotelier_bookings (
			id bigint(20) NOT NULL auto_increment,
			reservation_id bigint(20) NOT NULL,
			checkin date NOT NULL,
			checkout date NOT NULL,
			status varchar(255) NOT NULL,
			PRIMARY KEY  (id),
			KEY reservation_id (reservation_id)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}hotelier_rooms_bookings (
			id bigint(20) NOT NULL auto_increment,
			reservation_id bigint(20) NOT NULL,
			room_id bigint(20) NOT NULL,
			PRIMARY KEY  (id),
			KEY reservation_id (reservation_id),
			KEY room_id (room_id)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}hotelier_sessions (
			session_id bigint(20) NOT NULL AUTO_INCREMENT,
			session_key char(32) NOT NULL,
			session_value longtext NOT NULL,
			session_expiry bigint(20) NOT NULL,
			UNIQUE KEY session_id (session_id),
			PRIMARY KEY  (session_key)
		) $charset_collate;
		";

		return $sql;

	}

	/**
	 * Create Hotelier pages, storing page id's in variables.
	 */
	public static function create_pages() {
		$pages = array(
			'listing' => array(
				'name'    => esc_html_x( 'available-rooms', 'Page slug', 'wp-hotelier' ),
				'title'   => esc_html_x( 'Available rooms', 'Page title', 'wp-hotelier' ),
				'content' => '[' . apply_filters( 'hotelier_listing_shortcode_tag', 'hotelier_listing' ) . ']'
			),
			'booking' => array(
				'name'    => esc_html_x( 'booking', 'Page slug', 'wp-hotelier' ),
				'title'   => esc_html_x( 'Booking', 'Page title', 'wp-hotelier' ),
				'content' => '[' . apply_filters( 'hotelier_booking_shortcode_tag', 'hotelier_booking' ) . ']'
			)
		);

		if ( htl_get_option( 'listing_disabled', false ) ) {
			unset( $pages['listing'] );
		}

		$pages = apply_filters( 'hotelier_create_pages', $pages );

		foreach ( $pages as $key => $page ) {
			HTL_Admin_Functions::create_page( $key, esc_sql( $page['name'] ), 'hotelier_' . $key . '_page_id', $page[ 'title' ], $page[ 'content' ], ! empty( $page[ 'parent' ] ) ? htl_get_page_id( $page[ 'parent' ] ) : '' );
		}

		delete_transient( 'hotelier_cache_excluded_uris' );
	}

	/**
	 * Create cron jobs (clear them first).
	 */
	private static function create_cron_jobs() {
		wp_clear_scheduled_hook( 'hotelier_cancel_pending_reservations' );
		wp_clear_scheduled_hook( 'hotelier_process_completed_reservations' );
		wp_clear_scheduled_hook( 'hotelier_cleanup_sessions' );
		wp_clear_scheduled_hook( 'hotelier_check_license_cron' );

		$hold_minutes = htl_get_option( 'booking_hold_minutes', '60' );

		if ( $hold_minutes != 0 ) {
			wp_schedule_single_event( time() + ( absint( $hold_minutes ) * 60 ), 'hotelier_cancel_pending_reservations' );
		}

		wp_schedule_event( time(), 'daily', 'hotelier_process_completed_reservations' );
		wp_schedule_event( time(), 'twicedaily', 'hotelier_cleanup_sessions' );
		wp_schedule_event( time(), 'weekly', 'hotelier_check_license_cron' );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=hotelier-settings' ) . '" title="' . esc_attr__( 'View Hotelier settings', 'wp-hotelier' ) . '">' . esc_html__( 'Settings', 'wp-hotelier' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file == HTL_PLUGIN_BASENAME ) {
			$row_meta = array(
				'docs'    => '<a href="http://docs.wphotelier.com/" title="' . esc_attr__( 'View WP Hotelier documentation', 'wp-hotelier' ) . '">' . esc_html__( 'Docs', 'wp-hotelier' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}

endif;

HTL_Install::init();

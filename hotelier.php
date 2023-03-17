<?php
/**
 * Plugin Name:       WP Hotelier
 * Plugin URI:        https://wphotelier.com/?utm_source=wpadmin&utm_medium=plugin&utm_campaign=wphotelierplugin
 * Description:       Hotel booking plugin for WordPress.
 * Version:           2.9.0
 * Author:            WP Hotelier
 * Author URI:        https://wphotelier.com/
 * Requires at least: 4.0
 * Tested up to:      5.9
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wp-hotelier
 * Domain Path:       languages
 *
 * @package  Hotelier
 * @category Core
 * @author   Benito Lopez <hello@lopezb.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Hotelier' ) ) :

/**
 * Main Hotelier Class
 */
final class Hotelier {

	/**
	 * @var string
	 */
	public $version = '2.9.0';

	/**
	 * @var Hotelier The single instance of the class
	 */
	private static $_instance = null;

	/**
	 * HTL Session Object
	 *
	 * @var object
	 */
	public $session = null;

	/**
	 * HTL Query Object
	 *
	 * @var object
	 */
	public $query = null;

	/**
	 * HTL Roles Object
	 *
	 * @var object
	 */
	public $roles = null;

	/**
	 * HTL Cart Object
	 *
	 * @var object
	 */
	public $cart = null;

	/**
	 * Main Hotelier Instance
	 *
	 * Insures that only one instance of Hotelier exists in memory at any one time.
	 *
	 * @static
	 * @see HTL()
	 * @return Hotelier - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-hotelier' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-hotelier' ), '1.0.0' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'mailer', 'booking' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * Hotelier Constructor.
	 */
	public function __construct() {
		$this->setup_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'hotelier_loaded' );
	}

	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'HTL_Install', 'install' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_thumbnails' ) );
		add_action( 'after_setup_theme', array( $this, 'template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'HTL_Emails', 'init_transactional_emails' ) );

		if ( $this->is_request( 'frontend' ) ) {
			add_action( 'init', array( 'HTL_Shortcodes', 'init' ) );
		}
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @return void
	 */
	private function setup_constants() {
		$upload_dir = wp_upload_dir();

		// Plugin version
		if ( ! defined( 'HTL_VERSION' ) ) {
			define( 'HTL_VERSION', $this->version );
		}

		// Plugin Folder Path
		if ( ! defined( 'HTL_PLUGIN_DIR' ) ) {
			define( 'HTL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'HTL_PLUGIN_URL' ) ) {
			define( 'HTL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'HTL_PLUGIN_FILE' ) ) {
			define( 'HTL_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Basename
		if ( ! defined( 'HTL_PLUGIN_BASENAME' ) ) {
			define( 'HTL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}

		// Make sure CAL_GREGORIAN is defined
		if ( ! defined( 'CAL_GREGORIAN' ) ) {
			define( 'CAL_GREGORIAN', 1 );
		}

		// Log File Folder
		if ( ! defined( 'HTL_LOG_DIR' ) ) {
			define( 'HTL_LOG_DIR', $upload_dir[ 'basedir' ] . '/htl-logs/' );
		}

		// Log File Folder
		if ( ! defined( 'HTL_SESSION_CACHE_GROUP' ) ) {
			define( 'HTL_SESSION_CACHE_GROUP', 'htl_session_id' );
		}
	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required files used in admin and on the frontend.
	 *
	 * @access private
	 * @return void
	 */
	private function includes() {
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-install.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-formatting-helper.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-cart-totals.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-room.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-room-variation.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-reservation.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-coupon.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-extra.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-comments.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-booking.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-log.php';
		include_once HTL_PLUGIN_DIR . 'includes/gateways/abstract-htl-payment-gateway.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-payment-gateways.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-emails.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-ajax.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-core-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-tax-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-widget-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-booking-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/privacy/class-htl-privacy.php';

		if ( is_admin() ) {
			include_once HTL_PLUGIN_DIR . 'includes/admin/class-htl-admin.php';
			include_once HTL_PLUGIN_DIR . 'includes/admin/license-manager/class-htl-admin-license-manager.php';
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) || defined( 'HOTELIER_SHORTCODE_PREVIEW' ) ) {
			include_once HTL_PLUGIN_DIR . 'includes/class-htl-session.php';
		}

		$this->api   = include( 'includes/class-htl-api.php' );
		$this->query = include( 'includes/class-htl-query.php' );

		include_once HTL_PLUGIN_DIR . 'includes/class-htl-post-types.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-misc-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-country-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-page-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-info.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-cache.php';
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once HTL_PLUGIN_DIR . 'includes/htl-session-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-cart-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-notice-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/htl-template-hooks.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-template-loader.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-frontend-scripts.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-form-functions.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-cart.php';
		include_once HTL_PLUGIN_DIR . 'includes/shortcodes/class-htl-shortcodes.php';
		include_once HTL_PLUGIN_DIR . 'includes/class-htl-https.php';
		include_once HTL_PLUGIN_DIR . 'includes/theme-support/htl-theme-support-functions.php';
	}

	/**
	 * Include Template Functions.
	 */
	public function template_functions() {
		include_once HTL_PLUGIN_DIR . 'includes/htl-template-functions.php';
	}

	/**
	 * Init Hotelier when WordPress initialises.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		// Before init action
		do_action( 'before_hotelier_init' );

		// Set up localisation
		$this->load_textdomain();

		// Classes/actions loaded for the frontend and for ajax requests
		if ( $this->is_request( 'frontend' ) ) {
			$this->cart = new HTL_Cart();
		}

		// Session class, handles session data for users
		if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) || defined( 'HOTELIER_SHORTCODE_PREVIEW' ) ) {
			$this->session  = new HTL_Session();
		}

		// Init action
		do_action( 'hotelier_init' );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @return void
	 */
	public function load_textdomain() {
		// Set filter for plugin's languages directory
		$hotelier_lang_dir = dirname( HTL_PLUGIN_BASENAME ) . '/languages/';
		$hotelier_lang_dir = apply_filters( 'hotelier_languages_directory', $hotelier_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-hotelier' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'wp-hotelier', $locale );

		// Setup paths to current locale file
		$mofile_local  = $hotelier_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/wp-hotelier/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/wp-hotelier folder
			load_textdomain( 'wp-hotelier', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/wp-hotelier/languages/ folder
			load_textdomain( 'wp-hotelier', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'wp-hotelier', false, $hotelier_lang_dir );
		}
	}

	/**
	 * Setup image sizes.
	 */
	public function setup_thumbnails() {
		$this->add_thumbnail_support();
		$this->add_image_sizes();
	}

	/**
	 * Ensure post thumbnail support is turned on
	 */
	private function add_thumbnail_support() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		add_post_type_support( 'room', 'thumbnail' );
	}

	/**
	 * Add HTL Image sizes to WP
	 */
	private function add_image_sizes() {
		$room_thumbnail = htl_get_image_size( 'room_thumbnail' );
		$room_catalog   = htl_get_image_size( 'room_catalog' );
		$room_single    = htl_get_image_size( 'room_single' );

		add_image_size( 'room_thumbnail', $room_thumbnail[ 'width' ], $room_thumbnail[ 'height' ], $room_thumbnail[ 'crop' ] );
		add_image_size( 'room_catalog', $room_catalog[ 'width' ], $room_catalog[ 'height' ], $room_catalog[ 'crop' ] );
		add_image_size( 'room_single', $room_single[ 'width' ], $room_single[ 'height' ], $room_single[ 'crop' ] );
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'hotelier_template_path', 'hotelier/' );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Return the HTL API URL for a given request (used by gateways)
	 *
	 * @param string $request
	 * @param mixed $ssl (default: null)
	 * @return string
	 */
	public function api_request_url( $request, $ssl = null ) {
		if ( is_null( $ssl ) ) {
			$scheme = parse_url( home_url(), PHP_URL_SCHEME );
		} elseif ( $ssl ) {
			$scheme = 'https';
		} else {
			$scheme = 'http';
		}

		$api_request_url = add_query_arg( 'htl-api', $request, trailingslashit( home_url( '', $scheme ) ) );

		return esc_url_raw( $api_request_url );
	}

	/**
	 * Get Booking Class.
	 * @return HTL_Booking
	 */
	public function booking() {
		return HTL_Booking::instance();
	}

	/**
	 * Get gateways class
	 * @return HTL_Payment_Gateways
	 */
	public function payment_gateways() {
		return HTL_Payment_Gateways::instance();
	}

	/**
	 * Email Class.
	 * @return HTL_Emails
	 */
	public function mailer() {
		return HTL_Emails::instance();
	}
}

endif;

if ( ! function_exists( 'HTL' ) ) :
	/**
	 * Returns the main instance of HTL to prevent the need to use globals.
	 *
	 * @return Hotelier
	 */
	function HTL() {
		return Hotelier::instance();
	}
endif;

// Get HTL Running
HTL();

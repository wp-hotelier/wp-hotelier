<?php
/**
 * Creates and validates the settings fields.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Settings_Fields' ) ) :

/**
 * HTL_Admin_Settings_Fields Class
 */
class HTL_Admin_Settings_Fields {

	/**
    * Holds the values to be used in the fields callbacks
    */
   private $options = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->options = get_option( 'hotelier_settings' );
		$this->pages   = $this->get_hotelier_pages();

		// Fields callback (HTML)
		add_filter( 'hotelier_settings_header_callback', array( $this, 'print_header' ), 10, 2 );
		add_filter( 'hotelier_settings_description_callback', array( $this, 'print_description' ), 10, 2 );
		add_filter( 'hotelier_settings_text_callback', array( $this, 'print_text' ), 10, 2 );
		add_filter( 'hotelier_settings_textarea_callback', array( $this, 'print_textarea' ), 10, 2 );
		add_filter( 'hotelier_settings_email_callback', array( $this, 'print_email' ), 10, 2 );
		add_filter( 'hotelier_settings_upload_callback', array( $this, 'print_upload' ), 10, 2 );
		add_filter( 'hotelier_settings_number_callback', array( $this, 'print_number' ), 10, 2 );
		add_filter( 'hotelier_settings_select_callback', array( $this, 'print_select' ), 10, 2 );
		add_filter( 'hotelier_settings_checkbox_callback', array( $this, 'print_checkbox' ), 10, 2 );
		add_filter( 'hotelier_settings_multi_checkbox_callback', array( $this, 'print_multi_checkbox' ), 10, 2 );
		add_filter( 'hotelier_settings_radio_callback', array( $this, 'print_radio' ), 10, 2 );
		add_filter( 'hotelier_settings_switch_callback', array( $this, 'print_switch' ), 10, 2 );
		add_filter( 'hotelier_settings_tool_button_callback', array( $this, 'print_tool_button' ), 10, 2 );
		add_filter( 'hotelier_settings_card_icons_callback', array( $this, 'print_card_icons' ), 10, 2 );
		add_filter( 'hotelier_settings_image_size_callback', array( $this, 'print_image_size' ), 10, 2 );
		add_filter( 'hotelier_settings_from_to_callback', array( $this, 'print_from_to' ), 10, 2 );
		add_filter( 'hotelier_settings_info_callback', array( $this, 'print_info' ), 10, 2 );
		add_filter( 'hotelier_settings_seasonal_prices_table_callback', array( $this, 'print_seasonal_prices_table' ), 10, 2 );
		add_filter( 'hotelier_settings_license_key_callback', array( $this, 'print_license_key' ), 10, 2 );
		add_filter( 'hotelier_settings_percentage_callback', array( $this, 'print_percentage' ), 10, 2 );

		// Fields validation
		add_filter( 'hotelier_settings_sanitize_text', array( $this, 'sanitize_text' ) );
		add_filter( 'hotelier_settings_sanitize_textarea', array( $this, 'sanitize_text' ) );
		add_filter( 'hotelier_settings_sanitize_email', array( $this, 'sanitize_email' ) );
		add_filter( 'hotelier_settings_sanitize_switch', array( $this, 'sanitize_switch' ) );
		add_filter( 'hotelier_settings_sanitize_upload', array( $this, 'sanitize_upload' ) );
		add_filter( 'hotelier_settings_sanitize_number', array( $this, 'sanitize_number' ), 10, 2 );
		add_filter( 'hotelier_settings_sanitize_select', array( $this, 'sanitize_select' ), 10, 2 );
		add_filter( 'hotelier_settings_sanitize_booking_minimum_nights', array( $this, 'sanitize_booking_minimum_nights' ) );
		add_filter( 'hotelier_settings_sanitize_booking_hold_minutes', array( $this, 'sanitize_booking_hold_minutes' ) );
		add_filter( 'hotelier_settings_sanitize_image_size', array( $this, 'sanitize_image_size' ) );
		add_filter( 'hotelier_settings_sanitize_seasonal_prices_table', array( $this, 'sanitize_seasonal_prices_table' ) );
		add_filter( 'hotelier_settings_sanitize_percentage', array( $this, 'sanitize_percentage' ) );

		// Actions
		add_action( 'hotelier_settings_hook_install_pages', array( $this, 'install_pages' ) );
		add_action( 'hotelier_settings_hook_send_test_email', array( $this, 'send_test_email' ) );
		add_action( 'hotelier_settings_hook_clear_sessions', array( $this, 'clear_sessions' ) );
		add_action( 'hotelier_settings_hook_delete_completed_bookings', array( $this, 'delete_completed_bookings' ) );
		add_action( 'hotelier_settings_info_hotelier_version', array( $this, 'print_hotelier_version' ) );
		add_action( 'hotelier_settings_info_theme_name', array( $this, 'print_theme_name' ) );
		add_action( 'hotelier_settings_info_theme_version', array( $this, 'print_theme_version' ) );
		add_action( 'hotelier_settings_info_parent_theme_name', array( $this, 'print_parent_theme_name' ) );
		add_action( 'hotelier_settings_info_parent_theme_version', array( $this, 'print_parent_theme_version' ) );
		add_action( 'hotelier_settings_info_listing_page_info', array( $this, 'print_listing_page_info' ) );
		add_action( 'hotelier_settings_info_booking_page_info', array( $this, 'print_booking_page_info' ) );
		add_action( 'hotelier_settings_info_server_info', array( $this, 'print_server_info' ) );
		add_action( 'hotelier_settings_info_php_version', array( $this, 'print_php_version' ) );
		add_action( 'hotelier_settings_info_wp_memory_limit', array( $this, 'print_wp_memory_limit' ) );
		add_action( 'hotelier_settings_info_wp_debug', array( $this, 'print_wp_debug' ) );
		add_action( 'hotelier_settings_info_php_post_max_size', array( $this, 'print_php_post_max_size' ) );
		add_action( 'hotelier_settings_info_php_post_max_upload_size', array( $this, 'print_php_post_max_upload_size' ) );
		add_action( 'hotelier_settings_info_php_time_limit', array( $this, 'print_php_time_limit' ) );
		add_action( 'hotelier_settings_info_php_max_input_vars', array( $this, 'print_php_max_input_vars' ) );
		add_action( 'hotelier_settings_info_fsockopen_cURL', array( $this, 'print_fsockopen_cURL' ) );
		add_action( 'hotelier_settings_info_domdocument', array( $this, 'print_domdocument' ) );
		add_action( 'hotelier_settings_info_mbstring', array( $this, 'print_mbstring' ) );
		add_action( 'hotelier_settings_info_log_directory_writable', array( $this, 'print_log_directory_writable' ) );
	}

	/**
	 * Get required Hotelier pages
	 */
	public function get_hotelier_pages() {
		$required_pages = array(
			'listing' => array(
				'option'    => 'hotelier_listing_page_id',
				'shortcode' => '[' . apply_filters( 'hotelier_listing_shortcode_tag', 'hotelier_listing' ) . ']',
			),
			'booking' => array(
				'option'    => 'hotelier_booking_page_id',
				'shortcode' => '[' . apply_filters( 'hotelier_booking_shortcode_tag', 'hotelier_booking' ) . ']',
			),
		);

		$pages = array();

		foreach ( $required_pages as $page_key => $page ) {
			$errors        = array();
			$page_id       = get_option( $page[ 'option' ] );
			$page_set      = false;
			$page_exists   = false;
			$page_visible  = false;
			$has_shortcode = false;

			// Check if the page is set
			if ( $page_id ) {
				$page_set = true;
			}

			// Check if the page exists
			if ( get_post( $page_id ) ) {
				$page_exists = true;
			}

			// Check if the page is visible
			if ( 'publish' === get_post_status( $page_id ) ) {
				$page_visible = true;
			}

			// Check if the page has the required shortcode
			if ( get_post( $page_id ) ) {
				$_page = get_post( $page_id );

				if ( strstr( $_page->post_content, $page[ 'shortcode' ] ) ) {
					$has_shortcode = true;
				}
			}

			$pages[ $page_key ] = array(
				'page_id'            => $page_id,
				'page_set'           => $page_set,
				'page_exists'        => $page_exists,
				'page_visible'       => $page_visible,
				'shortcode'          => $page[ 'shortcode' ],
				'has_shortcode'      => $has_shortcode
			);
		}

		return $pages;
	}

	/**
	 * Print header section
	 */
	public function print_header( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-header.php';
	}

	/**
	 * Print description section
	 */
	public function print_description( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-description.php';
	}

	/**
	 * Print text input
	 */
	public function print_text( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-text.php';
	}

	/**
	 * Print textarea input
	 */
	public function print_textarea( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-textarea.php';
	}

	/**
	 * Print email input
	 */
	public function print_email( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-email.php';
	}

	/**
	 * Print upload input
	 */
	public function print_upload( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-upload.php';
	}

	/**
	 * Print text-number input
	 */
	public function print_number( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-number.php';
	}

	/**
	 * Print select input
	 */
	public function print_select( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-select.php';
	}

	/**
	 * Print checkbox input
	 */
	public function print_checkbox( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-checkbox.php';
	}

	/**
	 * Print multi_checkbox input
	 */
	public function print_multi_checkbox( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-multicheckbox.php';
	}

	/**
	 * Print card_icons input
	 */
	public function print_card_icons( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-card-icons.php';
	}

	/**
	 * Print radio input
	 */
	public function print_radio( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-radio.php';
	}

	/**
	 * Print switch input
	 */
	public function print_switch( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-switch.php';
	}

	/**
	 * Print image_size input
	 */
	public function print_image_size( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-image-size.php';
	}

	/**
	 * Print from_to input
	 */
	public function print_from_to( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-from-to.php';
	}

	/**
	 * Print tool button
	 */
	public function print_tool_button( $html, $args ) {
		switch ( $args[ 'id' ] ) {
			case 'install_pages':
				$url   = wp_nonce_url( admin_url( 'admin.php?page=hotelier-settings&tab=tools&action=install_pages' ), 'tools_action' );
				$label = esc_html__( 'Install pages', 'wp-hotelier' );
				break;

			case 'send_test_email':
				$url   = wp_nonce_url( admin_url( 'admin.php?page=hotelier-settings&tab=tools&action=send_test_email' ), 'tools_action' );
				$label = esc_html__( 'Send email', 'wp-hotelier' );
				break;

			case 'clear_sessions':
				$url   = wp_nonce_url( admin_url( 'admin.php?page=hotelier-settings&tab=tools&action=clear_sessions' ), 'tools_action' );
				$label = esc_html__( 'Clear sessions', 'wp-hotelier' );
				break;

			case 'delete_completed_bookings':
				$url   = wp_nonce_url( admin_url( 'admin.php?page=hotelier-settings&tab=tools&action=delete_completed_bookings' ), 'tools_action' );
				$label = esc_html__( 'Delete bookings', 'wp-hotelier' );
				break;

			default:
				break;
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-tool-button.php';
	}

	/**
	 * Print seasonal prices table
	 */
	public function print_seasonal_prices_table( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-table-seasonal-prices.php';
	}

	/**
	 * Print license key input
	 */
	public function print_license_key( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-license-keys.php';
	}

	/**
	 * Print percentage input
	 */
	public function print_percentage( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-input-percentage.php';
	}

	/**
	 * Print info
	 */
	public function print_info( $html, $args ) {
		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info-wrapper.php';
	}

	/**
	 * Print hotelier_version
	 */
	public function print_hotelier_version() {
		$info = HTL()->version;

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print theme_name
	 */
	public function print_theme_name() {
		$active_theme = wp_get_theme();
		$info         = $active_theme->Name;

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print theme_version
	 */
	public function print_theme_version() {
		$active_theme = wp_get_theme();
		$info         = $active_theme->Version;

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print parent_theme_name
	 */
	public function print_parent_theme_name() {
		$info = '-';

		if ( is_child_theme() ) {
			$active_theme  = wp_get_theme();
			$parent_theme = wp_get_theme( $active_theme->Template );
			$info = $parent_theme->Name;
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print parent_theme_version
	 */
	public function print_parent_theme_version() {
		$info = '-';

		if ( is_child_theme() ) {
			$active_theme  = wp_get_theme();
			$parent_theme = wp_get_theme( $active_theme->Template );
			$info = $parent_theme->Version;
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}


	/**
	 * Print listing_page_info
	 */
	public function print_listing_page_info() {
		$pages        = $this->pages;
		$listing_page = $pages[ 'listing' ];
		$class        = 'error';

		if ( htl_get_option( 'listing_disabled', false ) ) {
			$class = '';
			$info = __( 'Listing page disabled', 'wp-hotelier' ) ;;
		} elseif ( ! $listing_page[ 'page_set' ] ) {
			$info = __( 'Page not set', 'wp-hotelier' ) . '</mark>';
		} elseif ( ! $listing_page[ 'page_exists' ] ) {
			$info = __( 'The page is set, but it does not exist', 'wp-hotelier' );
		} elseif ( ! $listing_page[ 'page_visible' ] ) {
			$info = __( 'The page is set, but it is not public', 'wp-hotelier' ) ;
		} elseif ( ! $listing_page[ 'has_shortcode' ] ) {
			$info = sprintf( __( 'The page requires this shortcode: %s', 'wp-hotelier' ), '<code>' . $listing_page[ 'shortcode' ] . '</code>' );
		} else {
			$class = 'success';
			$info = '(ID = ' . absint( $listing_page[ 'page_id' ] ) . ') ' . get_permalink( $listing_page[ 'page_id' ] );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print booking_page_info
	 */
	public function print_booking_page_info() {
		$pages        = $this->pages;
		$booking_page = $pages[ 'booking' ];
		$class        = 'error';

		if ( ! $booking_page[ 'page_set' ] ) {
			$info = __( 'Page not set', 'wp-hotelier' );
		} elseif ( ! $booking_page[ 'page_exists' ] ) {
			$info = __( 'The page is set, but it does not exist', 'wp-hotelier' );
		} elseif ( ! $booking_page[ 'page_visible' ] ) {
			$info = __( 'The page is set, but it is not public', 'wp-hotelier' );
		} elseif ( ! $booking_page[ 'has_shortcode' ] ) {
			$info = sprintf( __( 'The page requires this shortcode: %s', 'wp-hotelier' ), '<code>' . $booking_page[ 'shortcode' ] . '</code>' );
		} else {
			$class = 'success';
			$info = '(ID = ' . absint( $booking_page[ 'page_id' ] ) . ') ' . get_permalink( $booking_page[ 'page_id' ] );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print server_info
	 */
	public function print_server_info() {
		$info = esc_html__( 'Not available', 'wp-hotelier' );

		if ( isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) && ! empty( $_SERVER[ 'SERVER_SOFTWARE' ] ) ) {
			$info = $_SERVER[ 'SERVER_SOFTWARE' ];
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print php_version
	 */
	public function print_php_version() {
		$info = esc_html__( 'Not available', 'wp-hotelier' );

		if ( function_exists( 'phpversion' ) ) {
			if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
				$class = 'error';
				$info = sprintf( esc_html__( '%s - WP Hotelier requires at least PHP 5.6.0. Please update your PHP version.', 'wp-hotelier' ), phpversion() );
			} else {
				$class = 'success';
				$info = phpversion();
			}
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print wp_memory_limit
	 */
	public function print_wp_memory_limit() {
		$info   = esc_html__( 'Not available', 'wp-hotelier' );
		$memory = HTL_Formatting_Helper::notation_to_int( WP_MEMORY_LIMIT );

		if ( $memory < 67108864 ) {
			$class = 'error';
			$info = sprintf( esc_html__( '%s - We recommend setting memory to at least 64MB.', 'wp-hotelier' ), size_format( $memory ) );
		} else {
			$class = 'success';
			$info = size_format( $memory );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print wp_debug
	 */
	public function print_wp_debug() {
		if ( defined('WP_DEBUG') && true === WP_DEBUG ) {
			$info = esc_html__( 'Enabled', 'wp-hotelier' );
		} else {
			$info = esc_html__( 'Disabled', 'wp-hotelier' );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print php_post_max_size
	 */
	public function print_php_post_max_size() {
		$info = esc_html__( 'Not available', 'wp-hotelier' );

		if ( function_exists( 'ini_get' ) ) {
			$info = size_format( HTL_Formatting_Helper::notation_to_int( ini_get( 'post_max_size' ) ) );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print php_post_max_upload_size
	 */
	public function print_php_post_max_upload_size() {
		$info = size_format( wp_max_upload_size() );

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print php_time_limit
	 */
	public function print_php_time_limit() {
		$info = esc_html__( 'Not available', 'wp-hotelier' );

		if ( function_exists( 'ini_get' ) ) {
			$info = ini_get( 'max_execution_time' );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print php_max_input_vars
	 */
	public function print_php_max_input_vars() {
		$info = esc_html__( 'Not available', 'wp-hotelier' );

		if ( function_exists( 'ini_get' ) ) {
			$info = ini_get( 'max_input_vars' );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print fsockopen_cURL
	 */
	public function print_fsockopen_cURL() {
		if ( function_exists( 'fsockopen' ) && function_exists( 'curl_init' ) ) {
			$class = 'success';
			$info = esc_html__( 'Enabled', 'wp-hotelier' );
		} else {
			$class = 'error';
			$info = esc_html__( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'wp-hotelier' );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print DOMDocument
	 */
	public function print_domdocument() {
		if ( class_exists( 'DOMDocument' ) ) {
			$class = 'success';
			$info = esc_html__( 'Enabled', 'wp-hotelier' );
		} else {
			$class = 'error';
			$info = esc_html__( 'Your server does not have the DOMDocument class enabled - Some extensions may not work without DOMDocument', 'wp-hotelier' );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print log_directory_writable
	 */
	public function print_log_directory_writable() {
		if ( @fopen( HTL_LOG_DIR . 'test-log.log', 'a' ) ) {
			$class = 'success';
			$info = HTL_LOG_DIR ;
		} else {
			$class = 'error';
			$info = sprintf( wp_kses( __( 'To allow logging, make <code>%s</code> writable or define a custom (writebale) <code>HTL_LOG_DIR</code>.', 'wp-hotelier' ), array( 'code' => array() ) ), HTL_LOG_DIR );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Print the mbstring check field
	 */
	public function print_mbstring() {
		$info  = esc_html__( 'Enabled', 'wp-hotelier' );
		$class = 'success';

		if ( ! function_exists( 'mb_detect_encoding' ) ) {
			$class = 'error';
			$info  = esc_html__( 'Your server does not have the mbstring extension enabled - Some extensions (Stripe) may require this module to work. Contact your hosting provider.', 'wp-hotelier' );
		}

		include HTL_PLUGIN_DIR . 'includes/admin/settings/views/fields/html-settings-field-server-info.php';
	}

	/**
	 * Sanitize text input
	 */
	public function sanitize_text( $input ) {
		return sanitize_text_field( $input );
	}

	/**
	 * Sanitize select input
	 */
	public function sanitize_select( $input, $key ) {
		// Save hotelier pages in a separate option
		if ( $key == 'listing_page' ) {
			update_option( 'hotelier_listing_page_id', absint( $input ) );

			// clear transient
			delete_transient( 'hotelier_cache_excluded_uris' );
		} else if ( $key == 'booking_page' ) {
			update_option( 'hotelier_booking_page_id', absint( $input ) );

			// clear transient
			delete_transient( 'hotelier_cache_excluded_uris' );
		}  else if ( $key == 'terms_page' ) {
			update_option( 'hotelier_terms_page_id', absint( $input ) );
		}

		return sanitize_text_field( $input );
	}

	/**
	 * Sanitize upload input
	 */
	public function sanitize_upload( $input ) {
		return esc_url( $input );
	}

	/**
	 * Sanitize switch input
	 */
	public function sanitize_switch( $input ) {
		return sanitize_text_field( $input );
	}

	/**
	 * Sanitize email input
	 */
	public function sanitize_email( $input ) {
		$input = explode( ',', $input );

		if ( is_array( $input ) ) {
			foreach ( $input as $key => $email ) {
				if ( ! is_email( $email ) ) {
					$input[ $key ] = '';
				}
			}

			$input = implode( ',', $input );
		}

		return $input;
	}

	/**
	 * Sanitize text-number input
	 */
	public function sanitize_number( $input, $key ) {
		return absint( $input );
	}

	/**
	 * Sanitize minimum nights field
	 */
	public function sanitize_booking_minimum_nights( $input ) {
		// Min nights option must be at least 1
		if ( $input == 0 ) {
			return 1;
		}

		return absint( $input );
	}

	/**
	 * Sanitize booking_hold_minutes (text-number) input
	 */
	public function sanitize_booking_hold_minutes( $input ) {
		$input = absint( $input );

		wp_clear_scheduled_hook( 'hotelier_cancel_pending_reservations' );

		if ( $input > 0 ) {
			wp_schedule_single_event( time() + ( absint( $input ) * 60 ), 'hotelier_cancel_pending_reservations' );
		}

		return $input;
	}

	/**
	 * Sanitize image_size input
	 */
	public function sanitize_image_size( $input ) {
		return array_map( 'absint', $input );
	}

	/**
	 * Sanitize seasonal_prices_table input
	 */
	public function sanitize_seasonal_prices_table( $rules ) {
		if ( is_array( $rules ) ) {
			// ensures rules are correctly mapped to an array starting with an index of 1
			uasort( $rules, function( $a, $b ) {
				return $a[ 'index' ] - $b[ 'index' ];
			});

			$rules = array_combine( range( 1, count( $rules ) ), array_values( $rules ) );

			foreach ( $rules as $key => $rule ) {
				// Check date range
				if ( ! HTL_Formatting_Helper::is_valid_date_range( $rule[ 'from' ], $rule[ 'to' ] ) ) {
					unset( $rules[ $key ] );
					continue;
				}

				// We can't accept repeated rules that are greater than one year
				$from       = new DateTime( $rule[ 'from' ] );
				$to         = new DateTime( $rule[ 'to' ] );
				$interval   = $from->diff( $to );
				$years_diff = $interval->y;

				if ( $years_diff > 0 ) {
					$rules[ $key ] = array(
						'from'        => $rule[ 'from' ],
						'to'          => $rule[ 'to' ],
						'index'       => $rule[ 'index' ],
						'season_name' => isset( $rule[ 'season_name' ] ) ? sanitize_text_field( $rule[ 'season_name' ] ) : '',
					);
				}
			}

			if ( count( $rules ) > 0 ) {
				return array_combine( range( 1, count( $rules ) ), array_values( $rules ) );
			}
		}

		return array();
	}

	/**
	 * Sanitize percentage input
	 */
	public function sanitize_percentage( $input ) {
		$input = number_format( (double) abs( $input ), 4, '.', '' );
		return $input;
	}

/**
	 * Install pages
	 */
	public function install_pages() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'install_pages' ) {

				HTL_Install::create_pages();
				echo '<div class="updated"><p>' . esc_html__( 'All missing Hotelier pages was installed successfully.', 'wp-hotelier' ) . '</p></div>';
			}
		}
	}

	/**
	 * Send test email
	 */
	public function send_test_email() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'send_test_email' ) {

				$to      = get_option( 'admin_email' );
				$subject = sprintf( esc_html__( 'Test email from %s', 'wp-hotelier'), get_bloginfo( 'name', 'display' ) );
				$message = sprintf( esc_html__( "This test email proves that your WordPress installation at %s can send emails.\n\nSent: %s", "hotelier" ), esc_url( get_bloginfo( "url" ) ), date( "r" ) );
				$headers = 'Content-Type: text/plain';
				wp_mail( $to, $subject, $message, $headers );

				echo '<div class="updated"><p>' . sprintf( wp_kses( __( 'Email sent. This does not mean it has been delivered. See %s in the Codex for more information.', 'wp-hotelier' ), array( 'a' => array( 'href' => array() ) ) ), '<a href="http://codex.wordpress.org/Function_Reference/wp_mail">wp_mail</a>' ) . '</p></div>';
			}
		}
	}

	/**
	 * Clear sessions
	 */
	public function clear_sessions() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'clear_sessions' ) {

				global $wpdb;

				$wpdb->query( "DELETE FROM {$wpdb->prefix}hotelier_sessions" );

				wp_cache_flush();

				echo '<div class="updated"><p>' . esc_html__( 'Sessions successfully cleared.', 'wp-hotelier' ) . '</p></div>';
			}
		}
	}

	/**
	 * Delete completed bookings
	 */
	public function delete_completed_bookings() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'delete_completed_bookings' ) {

				global $wpdb;

				$date        = date( 'Y-m-d' );
				$booking_ids = $wpdb->get_col( $wpdb->prepare( "SELECT reservation_id FROM {$wpdb->prefix}hotelier_bookings WHERE checkout < %s", $date ) );

				if ( $booking_ids ) {

					foreach ( $booking_ids as $booking_id) {

						// Delete bookings from custom tables
						$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}hotelier_reservation_itemmeta itemmeta INNER JOIN {$wpdb->prefix}hotelier_reservation_items items WHERE itemmeta.reservation_item_id = items.reservation_item_id and items.reservation_id = %d", $booking_id ) );
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}hotelier_reservation_items WHERE reservation_id = %d", $booking_id ) );
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}hotelier_rooms_bookings WHERE reservation_id = %d", $booking_id ) );
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}hotelier_bookings WHERE reservation_id = %d", $booking_id ) );

						// Delete post
						wp_delete_post( $booking_id, true );
					}
				}

				echo '<div class="updated"><p>' . esc_html__( 'Bookings successfully deleted.', 'wp-hotelier' ) . '</p></div>';
			}
		}
	}
}

endif;

return new HTL_Admin_Settings_Fields();

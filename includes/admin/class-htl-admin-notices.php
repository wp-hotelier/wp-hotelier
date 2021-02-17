<?php
/**
 * Admin Notices Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Notices' ) ) :

/**
 * HTL_Admin_Notices Class
 */
class HTL_Admin_Notices {

	/**
	 * Get things going.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'init', array( $this, 'show_notices_for_extensions' ), 100 );
	}

	/**
	 * Show notices.
	 */
	public function show_notices() {
		$notices = array(
			'updated'	=> array(),
			'error'		=> array()
		);

		settings_errors( 'hotelier-notices' );
	}

	/**
	 * Show notices in admin for extensions.
	 */
	public function show_notices_for_extensions() {

		// Disabled Dates
		if ( defined( 'HTL_DISABLE_DATES_VERSION' ) && version_compare( HTL_DISABLE_DATES_VERSION, '1.1.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_disabled_dates' ) );
		}

		// Stripe
		if ( defined( 'HTL_STRIPE_VERSION' ) && version_compare( HTL_STRIPE_VERSION, '1.2.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_stripe' ) );
		}

		// iCalendar
		if ( defined( 'HTL_ICS_VERSION' ) && version_compare( HTL_ICS_VERSION, '1.2.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_icalendar' ) );
		}

		// Flat Deposit
		if ( defined( 'HTL_FD_VERSION' ) && version_compare( HTL_FD_VERSION, '1.1.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_flat_deposit' ) );
		}

		// Bank Transfer
		if ( defined( 'HTL_BANK_TRANSFER_VERSION' ) && version_compare( HTL_BANK_TRANSFER_VERSION, '1.1.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_bank_transfer' ) );
		}

		// Eurobank
		if ( defined( 'HTL_EUROBANK_VERSION' ) && version_compare( HTL_EUROBANK_VERSION, '1.1.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_eurobank' ) );
		}

		// Week Bookings
		if ( defined( 'HTL_WEEK_RANGE_BOOKINGS_VERSION' ) && version_compare( HTL_WEEK_RANGE_BOOKINGS_VERSION, '1.1.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_week_bookings' ) );
		}

		// Min/Max Nights
		if ( defined( 'HTL_MIN_MAX_NIGHTS_VERSION' ) && version_compare( HTL_MIN_MAX_NIGHTS_VERSION, '1.1.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_min_max_nights' ) );
		}

		// MailChimp
		if ( defined( 'HTL_MAILCHIMP_VERSION' ) && version_compare( HTL_MAILCHIMP_VERSION, '1.2.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_mailchimp' ) );
		}

		// Hotelier Multilingual
		if ( defined( 'HTL_WPML_VERSION' ) && version_compare( HTL_WPML_VERSION, '1.3.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_ext_wpml' ) );
		}

		// Uncode integration
		if ( defined( 'UNCODE_VERSION' ) && ! defined( 'HTL_UNCODE_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice_for_uncode_integration' ) );
		}
	}

	/**
	 * Get notice text for old extensions.
	 */
	public function get_notice_for_old_extension( $plugin_name ) {
		$text = sprintf( wp_kses( __( 'You are using an old version of <strong>"%1$s"</strong> that is not supported by the current version of <strong>"WP Hotelier"</strong>. Please update <strong>"%1$s"</strong> to the last version to ensure the correct functionality of your installation.', 'wp-hotelier' ), array( 'strong' => array() ) ), $plugin_name );

		return $text;
	}

	/**
	 * Notice for old extensions.
	 */
	public function show_notice_for_ext_disabled_dates() {
		$plugin_name = esc_html__( 'Disabled Dates', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension Stripe.
	 */
	public function show_notice_for_ext_stripe() {
		$plugin_name = esc_html__( 'Gateway Stripe', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension iCalendar.
	 */
	public function show_notice_for_ext_icalendar() {
		$plugin_name = esc_html__( 'iCalendar', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension Flat Deposit.
	 */
	public function show_notice_for_ext_flat_deposit() {
		$plugin_name = esc_html__( 'Flat Deposit', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension Bank Transfer.
	 */
	public function show_notice_for_ext_bank_transfer() {
		$plugin_name = esc_html__( 'Gateway Bank Transfer', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension Eurobank.
	 */
	public function show_notice_for_ext_eurobank() {
		$plugin_name = esc_html__( 'Gateway Eurobank', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension Week Bookings.
	 */
	public function show_notice_for_ext_week_bookings() {
		$plugin_name = esc_html__( 'Week Bookings', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension Min/Max Nights.
	 */
	public function show_notice_for_ext_min_max_nights() {
		$plugin_name = esc_html__( 'Min/Max Nights', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension MailChimp.
	 */
	public function show_notice_for_ext_mailchimp() {
		$plugin_name = esc_html__( 'MailChimp', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension Hotelier Multilingual.
	 */
	public function show_notice_for_ext_wpml() {
		$plugin_name = esc_html__( 'Hotelier Multilingual', 'wp-hotelier' );

		echo '<div class="error"><p>' . $this->get_notice_for_old_extension( $plugin_name ) . '</p></div>';
	}

	/**
	 * Notice for extension WP Hotelier Uncode.
	 */
	public function show_notice_for_uncode_integration() {
		echo '<div class="error"><p>' . sprintf( wp_kses_post( __( 'Looks like you are using Uncode without the official WP Hotelier integration. You can download the plugin for free <a href="%s" target="_blank">at this address</a>. Once you have downloaded the zip file, upload it to <a href="%s">Plugins > Add New</a> like any other plugin.', 'wp-hotelier' ) ), 'https://github.com/wp-hotelier/wp-hotelier-uncode/releases', admin_url( 'plugin-install.php' ) ) . '</p></div>';
	}
}

endif;

return new HTL_Admin_Notices();

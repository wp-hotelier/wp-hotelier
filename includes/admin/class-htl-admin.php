<?php
/**
 * Hotelier Admin
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.2.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin' ) ) :

/**
 * HTL_Admin Class
 */
class HTL_Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_filter( 'admin_footer_text', array( $this, 'rate_us_text' ), 1 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( 'htl-admin-ui-functions.php' );
		include_once( 'settings/class-htl-admin-settings.php' );
		include_once( 'class-htl-admin-functions.php' );
		include_once( 'meta-boxes/class-htl-admin-meta-boxes-helper.php' );
		include_once( 'meta-boxes/class-htl-admin-meta-boxes-views.php' );
		include_once( 'meta-boxes/class-htl-admin-meta-boxes-validation.php' );
		include_once( 'class-htl-admin-post-types.php' );
		include_once( 'class-htl-admin-menus.php' );
		include_once( 'class-htl-admin-scripts.php' );
		include_once( 'class-htl-admin-notices.php' );
		include_once( 'new-reservation/class-htl-admin-new-reservation.php' );
		include_once( 'calendar/class-htl-admin-calendar.php' );
		include_once( 'settings/class-htl-admin-logs.php' );
	}

	/**
	 * Add rating text to the admin dashboard.
	 */
	public function rate_us_text( $footer_text ) {
		if ( ! current_user_can( 'manage_hotelier' ) ) {
			return $footer_text;
		}

		$screen = get_current_screen();

		if ( class_exists( 'HTL_Admin_Functions' ) && in_array( $screen->id, HTL_Admin_Functions::get_screen_ids() ) ) {
			$footer_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">WP Hotelier</a>! If you like it, please leave us a %2$s rating. Cheers :)', 'wp-hotelier' ),
				'https://wphotelier.com',
				'<a href="https://wordpress.org/support/plugin/wp-hotelier/reviews?rate=5#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}
}

endif;

return new HTL_Admin();

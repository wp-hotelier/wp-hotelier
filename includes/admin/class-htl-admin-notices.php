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
}

endif;

return new HTL_Admin_Notices();

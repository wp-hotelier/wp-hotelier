<?php
/**
 * Hotelier API.
 *
 * Handles HTL-API endpoint requests (used by gateways)
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_API' ) ) :

/**
 * HTL_API Class
 */
class HTL_API {

	/**
	 * Get things going
	 *
	 * @return HTL_API
	 */
	public function __construct() {
		// add query vars
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// handle htl-api endpoint requests
		add_action( 'parse_request', array( $this, 'handle_api_requests' ), 0 );

		// Ensure payment gateways are initialized in time for API requests
		add_action( 'hotelier_api_request', array( 'HTL_Payment_Gateways', 'instance' ), 0 );
	}

	/**
	 * Add new query vars.
	 *
	 * @param $vars
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'htl-api';
		return $vars;
	}

	/**
	 * API request
	 */
	public function handle_api_requests() {
		global $wp;

		if ( ! empty( $_GET[ 'htl-api' ] ) ) {
			$wp->query_vars[ 'htl-api' ] = $_GET[ 'htl-api' ];
		}

		// htl-api endpoint requests
		if ( ! empty( $wp->query_vars[ 'htl-api' ] ) ) {

			// Buffer, we won't want any output here
			ob_start();

			// No cache headers
			nocache_headers();

			// Clean the API request
			$api_request = strtolower( sanitize_text_field( $wp->query_vars[ 'htl-api' ] ) );

			// Trigger generic action before request hook
			do_action( 'hotelier_api_request', $api_request );

			// Is there actually something hooked into this API request? If not trigger 400 - Bad request
			status_header( has_action( 'hotelier_api_' . $api_request ) ? 200 : 400 );

			// Trigger an action which plugins can hook into to fulfill the request
			do_action( 'hotelier_api_' . $api_request );

			// Done, clear buffer and exit
			ob_end_clean();
			die('-1');
		}
	}
}

endif;

return new HTL_API();

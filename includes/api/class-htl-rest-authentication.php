<?php
/**
 * REST API Authentication.
 *
 * @author   Starter
 * @category API
 * @package  Hotelier/API
 * @version  2.18.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTL_REST_Authentication Class.
 *
 * Handles authentication and permission checking for REST API endpoints.
 */
class HTL_REST_Authentication {

	/**
	 * Check if the current user can manage hotelier.
	 *
	 * @return bool|WP_Error
	 */
	public static function check_manage_hotelier_permission() {
		if ( ! current_user_can( 'manage_hotelier' ) ) {
			return new WP_Error(
				'hotelier_rest_cannot_view',
				__( 'Sorry, you are not allowed to access this resource.', 'wp-hotelier' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Check if the current user can edit rooms.
	 *
	 * @return bool|WP_Error
	 */
	public static function check_edit_rooms_permission() {
		if ( ! current_user_can( 'edit_rooms' ) ) {
			return new WP_Error(
				'hotelier_rest_cannot_edit',
				__( 'Sorry, you are not allowed to edit this resource.', 'wp-hotelier' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Check if the request is for public read access.
	 *
	 * Public endpoints are always accessible.
	 *
	 * @return bool
	 */
	public static function check_public_permission() {
		return true;
	}

	/**
	 * Get the authorization required code based on login status.
	 *
	 * @return int HTTP status code.
	 */
	public static function get_authorization_code() {
		return is_user_logged_in() ? 403 : 401;
	}
}

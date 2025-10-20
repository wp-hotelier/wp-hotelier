<?php
/**
 * Hotelier Logs.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Logs' ) ) :

/**
 * HTL_Admin_Logs Class
 */
class HTL_Admin_Logs {
	/** @var HTL_Log Logger instance */
	public static $log = false;

	/**
	 * Scan the log files
	 * @return array
	 */
	public static function scan_log_files() {
		$files  = @scandir( HTL_LOG_DIR );
		$result = array();

		if ( $files ) {

			foreach ( $files as $key => $value ) {

				if ( ! in_array( $value, array( '.', '..' ) ) ) {
					if ( ! is_dir( $value ) && strstr( $value, '.log' ) ) {
						$result[ sanitize_title( $value ) ] = $value;
					}
				}
			}

		}

		return $result;
	}

	/**
	 * Show the logs page
	 */
	public static function output() {

		$logs = self::scan_log_files();

		if ( ! empty( $_REQUEST[ 'log_file' ] ) && isset( $logs[ sanitize_title( $_REQUEST[ 'log_file' ] ) ] ) ) {
			$viewed_log = $logs[ sanitize_title( $_REQUEST[ 'log_file' ] ) ];
		} elseif ( ! empty( $logs ) ) {
			$viewed_log = current( $logs );
		}

		if ( ! empty( $_REQUEST[ 'delete_log_file' ] ) && isset( $logs[ sanitize_title( $_REQUEST[ 'delete_log_file' ] ) ] ) ) {
			$log_to_delete = $logs[ sanitize_title( $_REQUEST[ 'delete_log_file' ] ) ];
			$handle   = preg_replace( '/-[^-]+$/', '', $log_to_delete );

			if ( $handle && check_admin_referer( 'delete_log' . $log_to_delete ) ) {
				if ( empty( self::$log ) ) {
					self::$log = new HTL_Log();
				}

				self::$log->clear( $handle );
			}
		}

		include_once( 'views/html-admin-logs.php' );
	}
}

endif;

<?php
/**
 * Cache Helper Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Cache' ) ) :

/**
 * HTL_Cache Class
 */
class HTL_Cache {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'before_hotelier_init', array( __CLASS__, 'prevent_caching' ) );
	}

	/**
	 * Get the page name/id for a HTL page.
	 * @param  string $htl_page
	 * @return array
	 */
	private static function get_page_uris( $htl_page ) {
		$htl_page_uris = array();

		if ( ( $page_id = htl_get_page_id( $htl_page ) ) && $page_id > 0 && ( $page = get_post( $page_id ) ) ) {
			$htl_page_uris[] = 'p=' . $page_id;
			$htl_page_uris[] = '/' . $page->post_name . '/';
		}

		return $htl_page_uris;
	}

	/**
	 * Prevent caching on dynamic pages.
	 * @access public
	 */
	public static function prevent_caching() {
		if ( false === ( $htl_page_uris = get_transient( 'hotelier_cache_excluded_uris' ) ) ) {
			$htl_page_uris   = array_filter( array_merge( self::get_page_uris( 'listing' ), self::get_page_uris( 'booking' ) ) );
	    	set_transient( 'hotelier_cache_excluded_uris', $htl_page_uris );
		}

		if ( is_array( $htl_page_uris ) ) {
			foreach( $htl_page_uris as $uri ) {
				if ( stristr( $_SERVER[ 'REQUEST_URI' ], $uri ) ) {
					self::nocache();
					break;
				}
			}
		}
	}

	/**
	 * Set nocache constants and headers.
	 * @access private
	 */
	private static function nocache() {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( "DONOTCACHEPAGE", "true" );
		}

		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( "DONOTCACHEOBJECT", "true" );
		}

		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( "DONOTCACHEDB", "true" );
		}

		nocache_headers();
	}
}

endif;

HTL_Cache::init();

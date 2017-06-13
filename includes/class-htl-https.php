<?php
/**
 * Handle rewriting asset URLs for SSL enforced booking
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_HTTPS' ) ) :

/**
 * HTL_HTTPS Class
 */
class HTL_HTTPS {

	/**
	 * This will ensure any links output to a page (when viewing via HTTPS) are also served over HTTPS.
	 */
	public static function init() {
		if ( htl_get_option( 'enforce_ssl_booking' ) && ! is_admin() ) {

			// HTTPS urls with SSL on
			$filters = array(
				'post_thumbnail_html',
				'wp_get_attachment_image_attributes',
				'wp_get_attachment_url',
				'option_stylesheet_url',
				'option_template_url',
				'script_loader_src',
				'style_loader_src',
				'template_directory_uri',
				'stylesheet_directory_uri',
				'site_url'
			);

			foreach ( $filters as $filter ) {
				add_filter( $filter, array( __CLASS__, 'force_https_url' ), 999 );
			}

			add_filter( 'page_link', array( __CLASS__, 'force_https_page_link' ), 10, 2 );
			add_action( 'template_redirect', array( __CLASS__, 'force_https_template_redirect' ) );

			if ( htl_get_option( 'unforce_ssl_booking' ) ) {
				add_action( 'template_redirect', array( __CLASS__, 'unforce_https_template_redirect' ) );
			}
		}
	}

	/**
	 * force_https_url function.
	 *
	 * @param mixed $content
	 * @return string
	 */
	public static function force_https_url( $content ) {
		if ( is_ssl() ) {
			if ( is_array( $content ) ) {
				$content = array_map( 'HTL_HTTPS::force_https_url', $content );
			} else {
				$content = str_replace( 'http:', 'https:', $content );
			}
		}

		return $content;
	}

	/**
	 * Force a post link to be SSL if needed.
	 *
	 * @return string
	 */
	public static function force_https_page_link( $link, $page_id ) {
		if ( $page_id == get_option( 'hotelier_booking_page_id' ) ) {
			$link = str_replace( 'http:', 'https:', $link );
		} elseif ( htl_get_option( 'unforce_ssl_booking' ) ) {
			$link = str_replace( 'https:', 'http:', $link );
		}

		return $link;
	}

	/**
	 * Template redirect - if we end up on a page ensure it has the correct http/https url.
	 */
	public static function force_https_template_redirect() {
		if ( ! is_ssl() && ( is_booking() || apply_filters( 'hotelier_enforce_ssl_booking', false ) ) ) {

			if ( 0 === strpos( $_SERVER[ 'REQUEST_URI' ], 'http' ) ) {
				wp_safe_redirect( preg_replace( '|^http://|', 'https://', $_SERVER[ 'REQUEST_URI' ] ) );
				exit;
			} else {
				wp_safe_redirect( 'https://' . ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED_HOST' ] ) ? $_SERVER[ 'HTTP_X_FORWARDED_HOST' ] : $_SERVER[ 'HTTP_HOST' ] ) . $_SERVER[ 'REQUEST_URI' ] );
				exit;
			}
		}
	}

	/**
	 * Template redirect - if we end up on a page ensure it has the correct http/https url.
	 */
	public static function unforce_https_template_redirect() {
		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
			return;
		}

		if ( is_ssl() && $_SERVER[ 'REQUEST_URI' ] && ! is_booking() && ! is_ajax() && apply_filters( 'hotelier_unforce_ssl_booking', true ) ) {

			if ( 0 === strpos( $_SERVER[ 'REQUEST_URI' ], 'http' ) ) {
				wp_safe_redirect( preg_replace( '|^https://|', 'http://', $_SERVER[ 'REQUEST_URI' ] ) );
				exit;
			} else {
				wp_safe_redirect( 'http://' . ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED_HOST' ] ) ? $_SERVER[ 'HTTP_X_FORWARDED_HOST' ] : $_SERVER['HTTP_HOST'] ) . $_SERVER[ 'REQUEST_URI' ] );
				exit;
			}
		}
	}

}

endif;

HTL_HTTPS::init();

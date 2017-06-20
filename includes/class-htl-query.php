<?php
/**
 * Query Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Query' ) ) :

/**
 * HTL_Query Class
 */
class HTL_Query {

	/** @public array Query vars to add to wp */
	public $query_vars = array();

	/**
	 * Constructor for the query class. Hooks in methods.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );

		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			add_action( 'wp', array( $this, 'remove_rooms_query' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
			add_action( 'parse_request', array( $this, 'parse_request'), 0 );
		}

		$this->init_query_vars();
	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP
		$this->query_vars = array(
			'pay-reservation'      => htl_get_option( 'pay_endpoint', 'pay-reservation' ),
			'reservation-received' => htl_get_option( 'reservation_received', 'reservation-received' ),
		);
	}

	/**
	 * Get page title for an endpoint
	 * @param  string
	 * @return string
	 */
	public function get_endpoint_title( $endpoint ) {
		global $wp;

		switch ( $endpoint ) {
			case 'pay-reservation' :
				$title = esc_html__( 'Pay reservation', 'wp-hotelier' );
			break;
			case 'reservation-received' :
				$title = esc_html__( 'Reservation received', 'wp-hotelier' );
			break;
			default :
				$title = '';
			break;
		}
		return $title;
	}

	/**
	 * Add endpoints for query vars
	 */
	public function add_endpoints() {
		foreach ( $this->query_vars as $key => $var ) {
			add_rewrite_endpoint( $var, EP_ROOT | EP_PAGES );
		}
	}

	/**
	 * add_query_vars function.
	 *
	 * @access public
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->query_vars as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Get query vars
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Get query current active query var
	 *
	 * @return string
	 */
	public function get_current_endpoint() {
		global $wp;
		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}
		return '';
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			}

			elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}

	/**
	 * Hook into pre_get_posts to change the main room query.
	 *
	 * @param mixed $q query object
	 */
	public function pre_get_posts( $q ) {
		// We only want to affect the main query
		if ( ! $q->is_main_query() ) {
			return;
		}

		if ( $q->is_post_type_archive( 'room' ) || $q->is_tax( get_object_taxonomies( 'room' ) ) ) {

			$this->room_query( $q );

			// Remove the pre_get_posts hook
			$this->remove_rooms_query();
		}
	}

	/**
	 * Remove the query.
	 */
	public function remove_rooms_query() {
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Change number of posts per page in room archive
	 *
	 * @param mixed $q
	 */
	public function room_query( $q ) {
		$q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'loop_room_per_page', get_option( 'posts_per_page' ) ) );
	}
}

endif;

return new HTL_Query();

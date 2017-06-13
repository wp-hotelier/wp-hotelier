<?php
/**
 * Session Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Session' ) ) :

/**
 * HTL_Session Class
 */
class HTL_Session {

	/** @var int $_guest_id */
	protected $_guest_id;

	/** @var array $_data  */
	protected $_data = array();

	/** @var bool $_dirty When something changes */
	protected $_dirty = false;

	/** @var string cookie name */
	private $_cookie;

	/** @var string session due to expire timestamp */
	private $_session_expiring;

	/** @var string session expiration timestamp */
	private $_session_expiration;

	/** $var bool Bool based on whether a cookie exists **/
	private $_has_cookie = false;

	/** @var string Custom session table name */
	private $_table;

	/**
	 * Constructor for the session class.
	 */
	public function __construct() {
		global $wpdb;

		$this->_cookie = 'wp_hotelier_session_' . COOKIEHASH;
		$this->_table  = $wpdb->prefix . 'hotelier_sessions';

		if ( $cookie = $this->get_session_cookie() ) {
			$this->_guest_id           = $cookie[0];
			$this->_session_expiration = $cookie[1];
			$this->_session_expiring   = $cookie[2];
			$this->_has_cookie         = true;

			// Update session if its close to expiring
			if ( time() > $this->_session_expiring ) {
				$this->set_session_expiration();
				$this->update_session_timestamp( $this->_guest_id, $this->_session_expiration );
			}

		} else {
			$this->set_session_expiration();
			$this->_guest_id = $this->generate_guest_id();
		}

		$this->_data = $this->get_session_data();

		// Actions
		add_action( 'hotelier_set_cookies', array( $this, 'set_guest_session_cookie' ), 10 );
		add_action( 'hotelier_cleanup_sessions', array( $this, 'cleanup_sessions' ), 10 );
		add_action( 'shutdown', array( $this, 'save_data' ), 20 );
		add_action( 'wp_logout', array( $this, 'destroy_session' ) );

		if ( ! is_user_logged_in() ) {
			add_action( 'hotelier_received', array( $this, 'destroy_session' ) );
			add_filter( 'nonce_user_logged_out', array( $this, 'nonce_user_logged_out' ) );
		}
	}

	/**
	 * __get function.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * __set function.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * __isset function.
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->_data[ sanitize_title( $key ) ] );
	}

	/**
	 * __unset function.
	 *
	 * @param mixed $key
	 */
	public function __unset( $key ) {
		if ( isset( $this->_data[ $key ] ) ) {
			unset( $this->_data[ $key ] );
			$this->_dirty = true;
		}
	}

	/**
	 * Get a session variable.
	 *
	 * @param string $key
	 * @param  mixed $default used if the session variable isn't set
	 * @return mixed value of session variable
	 */
	public function get( $key, $default = null ) {
		$key = sanitize_key( $key );
		return isset( $this->_data[ $key ] ) ? maybe_unserialize( $this->_data[ $key ] ) : $default;
	}

	/**
	 * Set a session variable.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$this->_data[ sanitize_key( $key ) ] = maybe_serialize( $value );
			$this->_dirty = true;
		}
	}

	/**
	 * get_guest_id function.
	 *
	 * @access public
	 * @return int
	 */
	public function get_guest_id() {
		return $this->_guest_id;
	}

	/**
	 * Sets the session cookie on-demand.
	 *
	 * Warning: Cookies will only be set if this is called before the headers are sent.
	 */
	public function set_guest_session_cookie( $set ) {
		if ( $set ) {
			// Set/renew our cookie
			$to_hash           = $this->_guest_id . '|' . $this->_session_expiration;
			$cookie_hash       = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
			$cookie_value      = $this->_guest_id . '||' . $this->_session_expiration . '||' . $this->_session_expiring . '||' . $cookie_hash;
			$this->_has_cookie = true;

			// Set the cookie
			htl_setcookie( $this->_cookie, $cookie_value, $this->_session_expiration, apply_filters( 'hotelier_session_use_secure_cookie', false ) );
		}
	}

	/**
	 * Return true if the current user has an active session, i.e. a cookie to retrieve values.
	 *
	 * @return bool
	 */
	public function has_session() {
		return isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie || is_user_logged_in();
	}

	/**
	 * Set session expiration.
	 */
	public function set_session_expiration() {
		$this->_session_expiring   = time() + intval( apply_filters( 'hotelier_session_expiring', 60 * 60 * 47 ) ); // 47 Hours.
		$this->_session_expiration = time() + intval( apply_filters( 'hotelier_session_expiration', 60 * 60 * 48 ) ); // 48 Hours.
	}

	/**
	 * Generate a unique guest ID, or return user ID if logged in.
	 *
	 * Uses Portable PHP password hashing framework to generate a unique cryptographically strong ID.
	 *
	 * @return int|string
	 */
	public function generate_guest_id() {
		if ( is_user_logged_in() ) {
			return get_current_user_id();
		} else {
			require_once( ABSPATH . 'wp-includes/class-phpass.php');
			$hasher = new PasswordHash( 8, false );
			return md5( $hasher->get_random_bytes( 32 ) );
		}
	}

	/**
	 * Get session cookie.
	 *
	 * @return bool|array
	 */
	public function get_session_cookie() {
		if ( empty( $_COOKIE[ $this->_cookie ] ) || ! is_string( $_COOKIE[ $this->_cookie ] ) ) {
			return false;
		}

		list( $guest_id, $session_expiration, $session_expiring, $cookie_hash ) = explode( '||', $_COOKIE[ $this->_cookie ] );

		// Validate hash
		$to_hash = $guest_id . '|' . $session_expiration;
		$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

		if ( empty( $cookie_hash ) || ! hash_equals( $hash, $cookie_hash ) ) {
			return false;
		}

		return array( $guest_id, $session_expiration, $session_expiring, $cookie_hash );
	}

	/**
	 * Get session data.
	 *
	 * @return array
	 */
	public function get_session_data() {
		return $this->has_session() ? (array) $this->get_session( $this->_guest_id, array() ) : array();
	}

	/**
	 * Gets a cache prefix. This is used in session names so the entire cache can be invalidated with 1 function call.
	 *
	 * @return string
	 */
	private function get_cache_prefix() {
		$prefix_num = wp_cache_get( 'hotelier_session_prefix', HTL_SESSION_CACHE_GROUP );

		if ( $prefix_num === false ) {
			$prefix_num = 1;
			wp_cache_set( 'hotelier_session_prefix', $prefix_num, HTL_SESSION_CACHE_GROUP );
		}

		return 'hotelier_session_' . $prefix_num . '_';
	}

	/**
	 * Save data.
	 */
	public function save_data() {
		// Dirty if something changed - prevents saving nothing new
		if ( $this->_dirty && $this->has_session() ) {
			global $wpdb;

			$wpdb->replace(
				$this->_table,
				array(
					'session_key'    => $this->_guest_id,
					'session_value'  => maybe_serialize( $this->_data ),
					'session_expiry' => $this->_session_expiration
				),
				array(
					'%s',
					'%s',
					'%d'
				)
			);

			// Set cache
			wp_cache_set( $this->get_cache_prefix() . $this->_guest_id, $this->_data, HTL_SESSION_CACHE_GROUP, $this->_session_expiration - time() );

			// Mark session clean after saving
			$this->_dirty = false;
		}
	}

	/**
	 * Destroy all session data.
	 */
	public function destroy_session() {
		// Clear cookie
		htl_setcookie( $this->_cookie, '', time() - YEAR_IN_SECONDS, apply_filters( 'hotelier_session_use_secure_cookie', false ) );

		$this->delete_session( $this->_guest_id );

		// Clear cart
		htl_empty_cart();

		// Clear data
		$this->_data        = array();
		$this->_dirty       = false;
		$this->_guest_id = $this->generate_guest_id();
	}

	/**
	 * When a user is logged out, ensure they have a unique nonce.
	 *
	 * @return string
	 */
	public function nonce_user_logged_out( $uid ) {
		return $this->has_session() && $this->_guest_id ? $this->_guest_id : $uid;
	}

	/**
	 * Cleanup sessions.
	 */
	public function cleanup_sessions() {
		global $wpdb;

		if ( ! defined( 'WP_SETUP_CONFIG' ) && ! defined( 'WP_INSTALLING' ) ) {

			// Delete expired sessions
			$wpdb->query( $wpdb->prepare( "DELETE FROM $this->_table WHERE session_expiry < %d", time() ) );

			// Invalidate cache
			wp_cache_incr( 'hotelier_session_prefix', 1, HTL_SESSION_CACHE_GROUP );
		}
	}

	/**
	 * Returns the session.
	 *
	 * @param string $guest_id
	 * @param mixed $default
	 * @return string|array
	 */
	public function get_session( $guest_id, $default = false ) {
		global $wpdb;

		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return false;
		}

		// Try get it from the cache, it will return false if not present or if object cache not in use
		$value = wp_cache_get( $this->get_cache_prefix() . $guest_id, HTL_SESSION_CACHE_GROUP );

		if ( false === $value ) {
			$value = $wpdb->get_var( $wpdb->prepare( "SELECT session_value FROM $this->_table WHERE session_key = %s", $guest_id ) );

			if ( is_null( $value ) ) {
				$value = $default;
			}

			wp_cache_add( $this->get_cache_prefix() . $guest_id, $value, HTL_SESSION_CACHE_GROUP, $this->_session_expiration - time() );
		}

		return maybe_unserialize( $value );
	}

	/**
	 * Delete the session from the cache and database.
	 *
	 * @param int $guest_id
	 */
	public function delete_session( $guest_id ) {
		global $wpdb;

		wp_cache_delete( $this->get_cache_prefix() . $guest_id, HTL_SESSION_CACHE_GROUP );

		$wpdb->delete(
			$this->_table,
			array(
				'session_key' => $guest_id
			)
		);
	}

	/**
	 * Update the session expiry timestamp.
	 *
	 * @param string $guest_id
	 * @param int $timestamp
	 */
	public function update_session_timestamp( $guest_id, $timestamp ) {
		global $wpdb;

		$wpdb->update(
			$this->_table,
			array(
				'session_expiry' => $timestamp
			),
			array(
				'session_key' => $guest_id
			),
			array(
				'%d'
			)
		);
	}
}

endif;

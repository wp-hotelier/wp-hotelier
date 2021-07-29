<?php
/**
 * Hotelier Core Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include core functions (available in both admin and frontend)
include( 'htl-conditional-functions.php' );
include( 'htl-price-functions.php' );
include( 'htl-room-functions.php' );
include( 'htl-reservation-functions.php' );
include( 'htl-coupon-functions.php' );
include( 'htl-extra-functions.php' );

/**
 * Short Description (excerpt)
 */
add_filter( 'hotelier_short_description', 'wptexturize' );
add_filter( 'hotelier_short_description', 'convert_smilies' );
add_filter( 'hotelier_short_description', 'convert_chars' );
add_filter( 'hotelier_short_description', 'wpautop' );
add_filter( 'hotelier_short_description', 'shortcode_unautop' );
add_filter( 'hotelier_short_description', 'prepend_attachment' );
add_filter( 'hotelier_short_description', 'do_shortcode', 11 ); // AFTER wpautop()

/**
 * Get an option from the "hotelier_settings" array.
 * @return mixed
 */
function htl_get_option( $key = '', $default = false ) {
	$hotelier_settings = get_option( 'hotelier_settings' );
	$value             = isset( $hotelier_settings[ $key ] ) ? $hotelier_settings[ $key ] : $default;
	$value             = apply_filters( 'hotelier_get_option', $value, $key, $default );

	return apply_filters( 'hotelier_get_option_' . $key, $value, $key, $default );
}

/**
 * Update an option
 *
 * Updates an hotelier setting value in the db.
 *
 * @param string $key The Key to update
 * @param string|bool|int $value The value to set the key to
 * @return boolean True if updated, false if not.
 */
function htl_update_option( $key = '', $value = false ) {

	// If no key, exit
	if ( empty( $key ) || empty( $value ) ){
		return false;
	}

	// First let's grab the current settings
	$options = get_option( 'hotelier_settings' );

	// Allow plugins alter that value coming in
	$value = apply_filters( 'hotelier_update_option', $value, $key );

	// Next let's try to update the value
	$options[ $key ] = $value;
	$did_update = update_option( 'hotelier_settings', $options );

	return $did_update;
}

/**
 * Set a cookie.
 *
 * @param  string  $name   Name of the cookie being set.
 * @param  string  $value  Value of the cookie.
 * @param  integer $expire Expiry of the cookie.
 * @param  string  $secure Whether the cookie should be served only over https.
 */
function htl_setcookie( $name, $value, $expire = 0, $secure = false ) {
	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		headers_sent( $file, $line );
		trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
	}
}

/**
 * Get an image size.
 *
 * @param mixed $image_size
 * @return array
 */
function htl_get_image_size( $image_size ) {
	if ( in_array( $image_size, array( 'room_thumbnail', 'room_catalog', 'room_single' ) ) ) {
		$size             = htl_get_option( $image_size . '_image_size', array() );
		$size[ 'width' ]  = isset( $size[ 'width' ] ) ? $size[ 'width' ] : '300';
		$size[ 'height' ] = isset( $size[ 'height' ] ) ? $size[ 'height' ] : '300';
		$size[ 'crop' ]   = isset( $size[ 'crop' ] ) ? $size[ 'crop' ] : 1;

	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'hotelier_get_image_size_' . $image_size, $size );
}

/**
 * Enables template debug mode
 */
function htl_template_debug_mode() {
	if ( ! defined( 'HTL_TEMPLATE_DEBUG_MODE' ) ) {
		$debug_mode = htl_get_option( 'template_debug_mode' );
		if ( ! empty( $debug_mode ) && current_user_can( 'manage_options' ) ) {
			define( 'HTL_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'HTL_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'htl_template_debug_mode', 20 );

/**
 * Get template part.
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
function htl_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/hotelier/slug-name.php
	if ( $name && ! HTL_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}-{$name}.php", HTL()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( HTL()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = HTL()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/hotelier/slug.php
	if ( ! $template && ! HTL_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}.php", HTL()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugin filter template file from their plugin
	if ( ( ! $template && HTL_TEMPLATE_DEBUG_MODE ) || $template ) {
		$template = apply_filters( 'hotelier_get_template_part', $template, $slug, $name );
	}

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function htl_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = htl_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0.0' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'hotelier_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'hotelier_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'hotelier_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function htl_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = HTL()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = HTL()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template || HTL_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'hotelier_locate_template', $template, $template_name, $template_path );
}

/**
 * Get a log file path
 *
 * @param string $handle name
 * @return string the log file path
 */
function htl_get_log_file_path( $handle ) {
	return trailingslashit( HTL_LOG_DIR ) . $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.log';
}

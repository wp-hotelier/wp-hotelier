<?php
/**
 * Twenty TwentyOne support.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @package  Hotelier/Classes
 * @version  2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Twenty_TwentyOne' ) ) :

/**
 * HTL_Twenty_TwentyOne Class
 */
class HTL_Twenty_TwentyOne {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Remove default wrappers
		remove_action( 'hotelier_before_main_content', 'hotelier_output_content_wrapper', 10 );
		remove_action( 'hotelier_after_main_content', 'hotelier_output_content_wrapper_end', 10 );

		// Remove sidebar
		remove_action( 'hotelier_sidebar', 'hotelier_get_sidebar', 10 );

		// Filter single room header class
		add_filter( 'hotelier_single_room_header_classes', array( $this, 'single_room_header_classes' ) );

		// Filter single room wrapper tag
		add_filter( 'hotelier_single_room_wrapper_tag', array( $this, 'single_room_wrapper_tag' ) );
	}

	/**
	 * Filter single room header class.
	 */
	public function single_room_header_classes( $classes ) {
		$classes[] = 'alignwide';

		return $classes;
	}

	/**
	 * Filter single room wrapper tag.
	 */
	public function single_room_wrapper_tag( $tag ) {
		$tag = 'article';

		return $tag;
	}
}

endif;

new HTL_Twenty_TwentyOne();

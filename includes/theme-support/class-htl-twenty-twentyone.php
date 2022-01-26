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

		// Add wrapper in archive pages
		add_action( 'hotelier_before_main_content', array( $this, 'open_archive_wrapper' ), 10 );
		add_action( 'hotelier_after_main_content', array( $this, 'close_archive_wrapper' ), 10 );

		// Remove sidebar
		remove_action( 'hotelier_sidebar', 'hotelier_get_sidebar', 10 );

		// Filter single room header class
		add_filter( 'hotelier_single_room_header_classes', array( $this, 'single_room_header_classes' ) );

		// Filter archive header class
		add_filter( 'hotelier_archive_header_classes', array( $this, 'archive_header_classes' ) );

		// Filter single room wrapper tag
		add_filter( 'hotelier_single_room_wrapper_tag', array( $this, 'single_room_wrapper_tag' ) );

		// Filter single room thumbnail class
		add_filter( 'hotelier_single_room_thumbnail_classes', array( $this, 'single_room_thumbnail_classes' ) );

		// Remove default styles
		add_filter( 'hotelier_enqueue_styles', '__return_false' );

		// Enqueue custom style
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Place room gallery inside the header tag
		add_action( 'hotelier_after_room_title', 'hotelier_template_single_room_image', 10 );
		add_action( 'hotelier_after_room_title', 'hotelier_template_single_room_gallery', 20 );

		// Remove original room gallery
		remove_action( 'hotelier_single_room_images', 'hotelier_template_single_room_image', 10 );
		remove_action( 'hotelier_single_room_images', 'hotelier_template_single_room_gallery', 20 );

		// Remove post thumbnail class when disabled via settings
		add_filter('post_class', array( $this, 'post_classes' ) );
	}

	/**
	 * Filter single room header class.
	 */
	public function single_room_header_classes( $classes ) {
		$classes[] = 'alignwide';

		return $classes;
	}

	/**
	 * Filter archive header class.
	 */
	public function archive_header_classes( $classes ) {
		$classes[] = 'entry-header';
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

	/**
	 * Enqueue styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'hotelier-twenty-twentyone', HTL_PLUGIN_URL . 'assets/css/frontend/twenty-twentyone.css', array(), HTL_VERSION );
	}

	/**
	 * Remove post thumbnail class when disabled via settings.
	 */
	public function post_classes( $classes ) {
		if ( htl_get_option( 'room_hide_gallery', false ) ) {
			$new_classes = array();

			foreach ( $classes as $class ) {
				if ( $class === 'has-post-thumbnail' ) {
					continue;
				}

				$new_classes[] = $class;

				return $new_classes;
			}
		}

		return $classes;
	}

	/**
	 * Filter single room thumbnail class.
	 */
	public function single_room_thumbnail_classes( $classes ) {
		$classes[] = 'post-thumbnail';

		return $classes;
	}

	/**
	 * Open wrapper in archive pages.
	 */
	public function open_archive_wrapper() {
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
	}

	/**
	 * Close wrapper in archive pages.
	 */
	public function close_archive_wrapper() {
		?>
		</article>

		<?php
	}
}

endif;

new HTL_Twenty_TwentyOne();

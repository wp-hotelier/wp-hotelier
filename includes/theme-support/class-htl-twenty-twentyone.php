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

		// Add wrapper to content in archive pages
		add_action( 'hotelier_after_archive_title', array( $this, 'open_archive_content_wrapper' ), 10 );
		add_action( 'hotelier_after_main_content', array( $this, 'close_archive_content_wrapper' ), 5 );

		// Print archive description inside the header in archives pages (and remove the original one)
		remove_action( 'hotelier_archive_description', 'hotelier_taxonomy_archive_description', 10 );
		add_action( 'hotelier_after_page_title', array( $this, 'archive_description' ), 10 );

		// Remove default pagination and use a custom one
		remove_action( 'hotelier_pagination', 'hotelier_pagination', 10 );
		add_action( 'hotelier_after_main_content', array( $this, 'pagination' ), 15 );

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

		// Add wrapper to room deposit (single room page)
		add_action( 'hotelier_before_single_room_deposit', array( $this, 'before_room_deposit' ) );
		add_action( 'hotelier_after_single_room_deposit', array( $this, 'after_room_deposit' ) );

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
		if ( is_room() ) {
			return;
		}
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
	}

	/**
	 * Close wrapper in archive pages.
	 */
	public function close_archive_wrapper() {
		if ( is_room() ) {
			return;
		}
		?>
		</article>

		<?php
	}

	/**
	 * Add a custom pagination (in the style of Twenty TwentyOne).
	 */
	public function pagination() {
		global $wp_query;

		// Don't print empty markup if there's only one page.
		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		the_posts_pagination(
			array(
				'before_page_number' => esc_html__( 'Page', 'wp-hotelier' ) . ' ',
				'mid_size'           => 0,
				'prev_text'          => sprintf(
					'%s <span class="nav-prev-text">%s</span>',
					is_rtl() ? twenty_twenty_one_get_icon_svg( 'ui', 'arrow_right' ) : twenty_twenty_one_get_icon_svg( 'ui', 'arrow_left' ),
					wp_kses(
						__( 'Next <span class="nav-short">rooms</span>', 'wp-hotelier' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					)
				),
				'next_text'          => sprintf(
					'<span class="nav-next-text">%s</span> %s',
					wp_kses(
						__( 'Previous <span class="nav-short">rooms</span>', 'wp-hotelier' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					is_rtl() ? twenty_twenty_one_get_icon_svg( 'ui', 'arrow_left' ) : twenty_twenty_one_get_icon_svg( 'ui', 'arrow_right' )
				),
			)
		);
	}

	/**
	 * Open wrapper for content in archive pages.
	 */
	public function open_archive_content_wrapper() {
		?>
		<div class="entry-content">

		<?php
	}

	/**
	 * Close wrapper for content in archive pages.
	 */
	public function close_archive_content_wrapper() {
		?>
		</div>

		<?php
	}

	/**
	 * Print description in archive pages.
	 */
	public function archive_description() {
		if ( is_tax( 'room_cat' ) ) {
			$description = do_shortcode( shortcode_unautop( wpautop( term_description() ) ) );

			if ( $description ) {
				echo '<div class="archive-description taxonomy-description page__description">' . $description . '</div>';
			}
		}
	}

	/**
	 * Open wrapper before room deposit.
	 */
	public function before_room_deposit() {
		if ( ! is_room() ) {
			return;
		}
		?>
		<div class="room-deposit-wrapper">

		<?php
	}

	/**
	 * Close wrapper before room deposit.
	 */
	public function after_room_deposit() {
		if ( ! is_room() ) {
			return;
		}
		?>
		</div>

		<?php
	}
}

endif;

new HTL_Twenty_TwentyOne();

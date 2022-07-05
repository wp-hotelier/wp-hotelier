<?php
/**
 * Hello Elementor theme support.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @package  Hotelier/Classes
 * @version  2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Theme_Hello_Elementor' ) ) :

/**
 * HTL_Theme_Hello_Elementor Class
 */
class HTL_Theme_Hello_Elementor {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Remove default wrappers
		remove_action( 'hotelier_before_main_content', 'hotelier_output_content_wrapper', 10 );
		remove_action( 'hotelier_after_main_content', 'hotelier_output_content_wrapper_end', 10 );

		// Add new wrappers
		add_action( 'hotelier_before_main_content', array( $this, 'output_content_wrapper' ), 10 );
		add_action( 'hotelier_after_main_content', array( $this, 'output_content_wrapper_end' ), 10 );

		// Remove default wrapper in single room
		add_filter( 'hotelier_print_single_room_wrapper', '__return_false' );

		// Add class to room's page header
		add_filter( 'hotelier_single_room_header_classes', array( $this, 'add_page_header_class' ) );

		// Add page content wrappers
		add_action( 'hotelier_single_room_before_content', array( $this, 'output_page_content_wrapper' ), 10 );
		add_action( 'hotelier_single_room_after_content', array( $this, 'output_page_content_wrapper_end' ), 10 );

		// Remove default styles
		add_filter( 'hotelier_enqueue_styles', '__return_false' );

		// Enqueue custom style
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles
	 */
	public function enqueue_styles() {
		if ( apply_filters( 'hotelier_enqueue_hello_elementor_styles', true ) ) {
			wp_enqueue_style( 'hotelier-hello-elementor', HTL_PLUGIN_URL . 'assets/css/frontend/hello-elementor.css', array(), HTL_VERSION );
		}
	}

	/**
	 * Open content wrapper.
	 */
	public function output_content_wrapper() {
		?>
		<main id="content" <?php post_class( 'site-main' ); ?> role="main">
		<?php
	}

	/**
	 * End content wrapper.
	 */
	public function output_content_wrapper_end() {
		?>
		</main>
		<?php
	}

	/**
	 * Open page content wrapper.
	 */
	public function output_page_content_wrapper() {
		?>
		<div class="page-content">
		<?php
	}

	/**
	 * End page content wrapper.
	 */
	public function output_page_content_wrapper_end() {
		?>
		</div>
		<?php
	}

	/**
	 * Add class to room's page header.
	 */
	public function add_page_header_class( $classes ) {
		$classes[] = 'page-header';

		return $classes;
	}

}

endif;

new HTL_Theme_Hello_Elementor();

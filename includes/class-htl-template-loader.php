<?php
/**
 * Template Loader.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Template_Loader' ) ) :

	class HTL_Template_Loader {

		/**
		 * Hook in methods.
		 */
		public static function init() {
			add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
		}

		/**
		 * Load a template.
		 *
		 * @param mixed $template
		 * @return string
		 */
		public static function template_loader( $template ) {
			$find = array();
			$file = '';

			if ( is_single() && get_post_type() == 'room' ) {

				$file 	= 'single-room/single-room.php';
				$find[] = $file;
				$find[] = HTL()->template_path() . $file;

			} elseif ( is_room_category() ) {

				$term   = get_queried_object();

				if ( is_tax( 'room_cat' ) ) {
					$file = 'archive/taxonomy-' . $term->taxonomy . '.php';
				} else {
					$file = 'archive/archive-room.php';
				}

				$find[] = $file;
				$find[] = HTL()->template_path() . $file;

			} elseif ( is_post_type_archive( 'room' ) ) {

				$file 	= 'archive/archive-room.php';
				$find[] = $file;
				$find[] = HTL()->template_path() . $file;

			}

			if ( $file ) {
				$template       = locate_template( array_unique( $find ) );
				if ( ! $template || HTL_TEMPLATE_DEBUG_MODE ) {
					$template = HTL()->plugin_path() . '/templates/' . $file;
				}
			}

			return $template;
		}

	}

endif;

HTL_Template_Loader::init();

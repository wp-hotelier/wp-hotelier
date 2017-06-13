<?php
/**
 * The Template for displaying all single rooms
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/single-room.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'hotelier' ); ?>

	<?php
		/**
		 * hotelier_before_main_content hook.
		 *
		 * @hooked hotelier_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'hotelier_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php htl_get_template_part( 'single-room/content', 'single-room' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * hotelier_after_main_content hook.
		 *
		 * @hooked hotelier_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'hotelier_after_main_content' );
	?>

	<?php
		/**
		 * hotelier_sidebar hook.
		 *
		 * @hooked hotelier_get_sidebar - 10
		 */
		do_action( 'hotelier_sidebar' );
	?>

<?php get_footer( 'hotelier' ); ?>

<?php
/**
 * The Template for displaying room archives
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/archive-room.php.
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

	<?php if ( apply_filters( 'hotelier_show_page_title', true ) ) : ?>

		<?php hotelier_page_title(); ?>

	<?php endif; ?>

	<?php do_action( 'hotelier_after_archive_title' ); ?>

	<?php
		/**
		 * hotelier_archive_description hook.
		 *
		 * @hooked hotelier_taxonomy_archive_description - 10
		 */
		do_action( 'hotelier_archive_description' );
	?>

	<?php if ( have_posts() ) : ?>

		<?php
			/**
			 * hotelier_before_archive_room_loop hook.
			 *
			 * @hooked hotelier_output_loop_wrapper - 10
			 */
			do_action( 'hotelier_before_archive_room_loop' );
		?>

		<?php hotelier_room_loop_start(); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php htl_get_template_part( 'archive/content', 'room' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php hotelier_room_loop_end(); ?>

		<?php
			/**
			 * hotelier_after_archive_room_loop hook.
			 *
			 * @hooked hotelier_output_loop_wrapper_end - 10
			 */
			do_action( 'hotelier_after_archive_room_loop' );
		?>

		<?php
			/**
			 * hotelier_pagination hook.
			 *
			 * @hooked hotelier_pagination - 10
			 */
			do_action( 'hotelier_pagination' );
		?>

	<?php else : ?>

		<?php htl_get_template( 'loop/no-rooms-found.php' ); ?>

	<?php endif; ?>

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

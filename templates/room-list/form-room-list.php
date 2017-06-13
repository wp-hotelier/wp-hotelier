<?php
/**
 * The Template for displaying rooms available on the given dates
 *
 * Override this template by copying it to yourtheme/hotelier/room-list/form-room-list.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

htl_print_notices();

if ( $rooms && $rooms->have_posts() ) : ?>

	<?php
		/**
		 * hotelier_room_list_datepicker hook
		 *
		 * @hooked hotelier_template_datepicker - 10
		 */
		do_action( 'hotelier_room_list_datepicker' );
	?>

	<?php
		/**
		 * hotelier_room_list_selected_nights hook
		 *
		 * @hooked hotelier_template_selected_nights - 10
		 */
		do_action( 'hotelier_room_list_selected_nights' );
	?>

	<form name="room_list" method="post" class="form--listing listing" enctype="multipart/form-data">

		<?php do_action( 'hotelier_before_room_list_loop' ); ?>

		<?php hotelier_room_list_start(); ?>

			<?php while ( $rooms->have_posts() ) : $rooms->the_post();

				global $room;

				// Ensure visibility
				if ( ! $room || ! $room->is_visible() ) {
					return;
				}

				?>

				<?php
					/**
					 * hotelier_room_list_item_content hook
					 *
					 * @hooked hotelier_template_loop_room_content - 10
					 */
					do_action( 'hotelier_room_list_item_content' );
				?>

			<?php endwhile; // end of the loop. ?>

		<?php hotelier_room_list_end(); ?>

		<?php wp_reset_postdata(); ?>

		<?php
			/**
			 * hotelier_reserve_button hook
			 *
			 * @hooked hotelier_template_loop_room_reserve_button - 10
			 */
			do_action( 'hotelier_reserve_button' );
		?>

		<?php do_action( 'hotelier_after_room_list_loop' ); ?>

	</form>

<?php else: ?>

	<?php htl_get_template( 'room-list/no-rooms-available.php' ); ?>

<?php endif;  ?>

<?php do_action( 'hotelier_after_room_list_form' ); ?>

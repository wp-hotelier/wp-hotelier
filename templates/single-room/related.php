<?php
/**
 * Related rooms.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/related.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_rooms && $related_rooms->have_posts() ) : ?>

	<div class="related-rooms">

		<h3 class="related-rooms-title"><?php _e( 'Related rooms', 'wp-hotelier' ); ?></h3>

		<?php
		$custom_class = apply_filters( 'hotelier_related_rooms_loop_wrapper_class', '', $columns );
		?>

		<div class="hotelier room-loop room-loop--related-rooms room-loop--columns-<?php echo absint( $columns ); ?> <?php echo esc_attr( $custom_class ); ?>">

			<?php do_action( 'hotelier_before_related_room_loop', $columns ); ?>

			<?php hotelier_room_loop_start(); ?>

				<?php while ( $related_rooms->have_posts() ) : $related_rooms->the_post(); ?>

					<?php htl_get_template_part( 'archive/content', 'room' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php hotelier_room_loop_end(); ?>

			<?php do_action( 'hotelier_after_related_room_loop', $columns ); ?>

		</div>

	</div>

<?php endif;

wp_reset_postdata();

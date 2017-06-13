<?php
/**
 * Related rooms.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/related.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_rooms && $related_rooms->have_posts() ) : ?>

	<div class="related-rooms">

		<h3 class="related-rooms-title"><?php _e( 'Related rooms', 'hotelier' ); ?></h3>

		<div class="hotelier room-loop room-loop--related-rooms room-loop--columns-<?php echo absint( $columns ); ?>">

			<?php hotelier_room_loop_start(); ?>

				<?php while ( $related_rooms->have_posts() ) : $related_rooms->the_post(); ?>

					<?php htl_get_template_part( 'archive/content', 'room' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php hotelier_room_loop_end(); ?>

		</div>

	</div>

<?php endif;

wp_reset_postdata();

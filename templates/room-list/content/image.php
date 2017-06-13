<?php
/**
 * Room thumbnail
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/image.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $room;

?>

<div class="room__image room__image--listing">

	<?php
		if ( has_post_thumbnail() ) {
			$thumb_id      = get_post_thumbnail_id();
			$image_title   = esc_attr( get_the_title( $thumb_id ) );
			$image_caption = get_post( $thumb_id ) ? get_post( $thumb_id )->post_excerpt : '';
			$image_large   = wp_get_attachment_image_src( $thumb_id, 'full' );
			$image_link    = esc_url( $image_large[ 0 ] );
			$image_width   = absint( $image_large[ 1 ] );
			$image_height  = absint( $image_large[ 2 ] );
			$image         = get_the_post_thumbnail( $post->ID, 'room_thumbnail', array(
				'title'	=> $image_title,
				'alt'	=> $image_title
				) );

			echo apply_filters( 'hotelier_room_list_image_html', sprintf( '<a href="%s" data-size="%sx%s" data-index="0" class="room__gallery-thumbnail room__gallery-thumbnail--visible room__gallery-thumbnail--listing" title="%s">%s</a>', $image_link, $image_width, $image_height, $image_caption, $image ), $post->ID );

		} else {

			echo '<a href="' . esc_url ( get_the_permalink() ) . '">' . htl_placeholder_img( 'room_thumbnail' ) . '</a>';

		}
	?>
</div>

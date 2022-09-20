<?php
/**
 * Room thumbnail
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/image.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $room;

$thumb_id        = has_post_thumbnail() ? get_post_thumbnail_id() : false;
$room_thumbnails = array();

if ( $thumb_id > 0 ) {
	$thumbnail         = wp_get_attachment_image_src( $thumb_id, 'full' );
	$thumbnail_src     = $thumbnail[0];
	$room_thumbnails[] = $thumb_id;
} else {
	$thumbnail_src = htl_placeholder_img_src();
}

$room_gallery_ids = $room->get_gallery_attachment_ids();

if ( $room_gallery_ids ) {
	$room_thumbnails = array_merge( $room_thumbnails, $room_gallery_ids );
}
?>

<div class="room-card__gallery">
	<?php
		if ( has_post_thumbnail() ) {

			the_post_thumbnail( 'full', array( 'class' => 'room__gallery-image room__gallery-image--listing' ) );

		} else {

			echo '<a href="' . esc_url ( get_the_permalink() ) . '" class="room__gallery-image room__gallery-image--listing">' . htl_placeholder_img( 'full' ) . '</a>';

		}
	?>
	<a href="#thumbnails-<?php echo esc_attr( $post->ID ); ?>" class="room__gallery-link" data-index="0"><?php esc_html_e( 'View gallery', 'wp-hotelier' ); ?></a>

	<?php if ( is_array( $room_thumbnails ) && count( $room_thumbnails ) > 0 ) :
		$loop = 0;
		?>

		<ul style="display:none">

			<?php foreach ( $room_thumbnails as $attachment_id ) {
				$classes = array( 'room__gallery-thumbnail', 'room__gallery-thumbnail--listing' );

				$image_large = wp_get_attachment_image_src( $attachment_id, 'full' );

				if ( ! $image_large ) {
					continue;
				}

				$image_link    = esc_url( $image_large[ 0 ] );
				$image_width   = absint( $image_large[ 1 ] );
				$image_height  = absint( $image_large[ 2 ] );
				$image_title   = esc_attr( get_the_title( $attachment_id ) );
				$image_caption = esc_attr( get_post_field( 'post_excerpt', $attachment_id ) );
				$image_class   = esc_attr( implode( ' ', $classes ) );
				$image_index   = has_post_thumbnail() ? absint( $loop + 1 ) : absint( $loop );

				echo apply_filters( 'hotelier_room_list_card_gallery_thumbnail_html', sprintf( '<li><a href="%s" data-size="%sx%s" data-index="%s" class="%s" title="%s">%s</a></li>', $image_link, $image_width, $image_height, $image_index, $image_class, $image_caption, $image_title ), $attachment_id, $post->ID, $image_class );

				$loop++;
			} ?>

		</ul>
	<?php endif; ?>
</div><!-- .room-card__gallery -->

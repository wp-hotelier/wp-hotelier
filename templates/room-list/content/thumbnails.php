<?php
/**
 * Room thumbnail
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/thumbnails.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $room;

$attachment_ids = $room->get_gallery_attachment_ids();

if ( $attachment_ids ) {
	$loop = 0;
	?>

	<p><a href="#thumbnails-<?php echo esc_attr( $post->ID ); ?>" class="room__gallery-link room__gallery-link--listing" data-index="0"><?php esc_html_e( 'View gallery', 'wp-hotelier' ); ?></a></p>

	<ul style="display:none">

		<?php foreach ( $attachment_ids as $attachment_id ) {
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

			echo apply_filters( 'hotelier_room_list_image_thumbnail_html', sprintf( '<li><a href="%s" data-size="%sx%s" data-index="%s" class="%s" title="%s">%s</a></li>', $image_link, $image_width, $image_height, $image_index, $image_class, $image_caption, $image_title ), $attachment_id, $post->ID, $image_class );

			$loop++;
		} ?>

	</ul>

<?php } ?>

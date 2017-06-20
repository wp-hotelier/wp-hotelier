<?php
/**
 * Room gallery.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/gallery.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

// Do not show the gallery button if the room has not images
// but close the .room__thumbnail div before return!!!
if ( ! htl_get_option( 'room_lightbox', true ) || ! $room->get_gallery_attachment_ids() ) {
	echo '</div><!-- .room__thumbnail -->';
	return;
}

$gallery_images = array();

if ( has_post_thumbnail() ) {
	$image         = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
	$image_link    = esc_url( $image[ 0 ] );
	$image_width   = absint( $image[ 1 ] );
	$image_height  = absint( $image[ 2 ] );
	$image_title   = get_post( get_post_thumbnail_id() ) ? get_post( get_post_thumbnail_id() )->post_title : '';
	$image_caption = get_post( get_post_thumbnail_id() ) ? get_post( get_post_thumbnail_id() )->post_excerpt : '';

	$gallery_images[] = array(
		'name'   => esc_html( $image_title ),
		'url'    => $image_link,
		'title'  => esc_attr( $image_caption ),
		'width'  => $image_width,
		'height' => $image_height,
	);
}

$attachment_ids = $room->get_gallery_attachment_ids();

foreach ( $attachment_ids as $attachment_id ) {
	$attachment  = wp_get_attachment_image_src( $attachment_id, 'full' );

	if ( ! $attachment ) {
		continue;
	}

	$attachment_link    = $attachment[ 0 ];
	$attachment_width   = $attachment[ 1 ];
	$attachment_height  = $attachment[ 2 ];
	$attachment_title   = get_post_field( 'post_title', $attachment_id );
	$attachment_caption = get_post_field( 'post_excerpt', $attachment_id );

	$gallery_images[] = array(
		'name'   => $attachment_title,
		'url'    => $attachment_link,
		'title'  => $attachment_caption,
		'width'  => $attachment_width,
		'height' => $attachment_height,
	);
}

?>

<?php if ( $gallery_images ) :
	$i = 0; ?>

	<div class="room__gallery room__gallery--single">

		<a href="#" class="room__gallery-link room__gallery-link--single" data-index="0"><?php esc_html_e( 'View room gallery', 'wp-hotelier' ); ?></a>

		<ul style="display:none">

			<?php foreach ( $gallery_images as $gallery_image ) : ?>

				<li><a href="<?php echo esc_url( $gallery_image[ 'url' ] ); ?>" data-size="<?php echo absint( $gallery_image[ 'width' ] ); ?>x<?php echo absint( $gallery_image[ 'height' ] ); ?>" data-index="<?php echo absint( $i ); ?>" class="room__gallery-thumbnail room__gallery-thumbnail--single" title="<?php echo esc_attr( $gallery_image[ 'title' ] ); ?>"><?php echo esc_html( $gallery_image[ 'name' ] ); ?></a></li>

			<?php
			$i ++;
			endforeach; ?>

		</ul>

	</div>
<?php endif; ?>

</div><!-- .room__thumbnail -->

<?php
/**
 * Room image.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/image.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The .room__thumbnail div is closed in single-room/gallery.php!

$single_room_thumbnail_classes = apply_filters(
	'hotelier_single_room_thumbnail_classes', array(
		'room__thumbnail',
		'room__thumbnail--single',
	)
);

?>

<div class="<?php echo esc_attr( implode( ' ', $single_room_thumbnail_classes ) ) ?>">

	<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'room_single' );

		} else {

			echo htl_placeholder_img( 'room_single' );
		}
	?>

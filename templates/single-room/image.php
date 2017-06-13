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

?>

<div class="room__thumbnail room__thumbnail--single">

	<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'room_single' );

		} else {

			echo htl_placeholder_img( 'room_single' );
		}
	?>

<?php
/**
 * The room thumbnail
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/content/room-image.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<a href="<?php the_permalink() ?>" class="room__thumbnail room__thumbnail--loop">
	<?php if ( has_post_thumbnail() ) :
		the_post_thumbnail( 'room_catalog' );
	else :
		echo htl_placeholder_img( 'room_catalog' );
	endif; ?>
</a>

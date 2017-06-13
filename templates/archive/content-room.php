<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/content-room.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room, $hotelier_loop;

// Store loop count we're currently on
if ( empty( $hotelier_loop[ 'loop' ] ) ) {
	$hotelier_loop[ 'loop' ] = 0;
}

// Store column count for displaying the grid
if ( empty( $hotelier_loop[ 'columns' ] ) ) {
	$hotelier_loop[ 'columns' ] = apply_filters( 'loop_room_columns', 3 );
}

// Ensure visibility
if ( ! $room ) {
	return;
}

// Increase loop count
$hotelier_loop[ 'loop' ]++;

// Extra post classes
$classes = array();
$classes[] = 'room-loop__item';

// first row item
if ( 0 == ( $hotelier_loop[ 'loop' ] - 1 ) % $hotelier_loop[ 'columns' ] || 1 == $hotelier_loop[ 'columns' ] ) {
	$classes[] = 'room-loop__item--first';
}

// last row item
if ( 0 == $hotelier_loop[ 'loop' ] % $hotelier_loop[ 'columns' ] ) {
	$classes[] = 'room-loop__item--last';
}

// even/odd items
if ( 0 == $hotelier_loop[ 'loop' ] % 2 ) {
	$classes[] = 'room-loop__item--even';
} else {
	$classes[] = 'room-loop__item--odd';
}

// number of columns (last rule, to override the previous ones)
$classes[] = 'room-loop__item--columns-' . $hotelier_loop[ 'columns' ];
?>

<li <?php post_class( $classes ); ?>>

	<?php
	/**
	 * hotelier_archive_item_room hook.
	 *
	 * @hooked hotelier_template_archive_room_image - 5
	 * @hooked hotelier_template_archive_room_title - 10
	 * @hooked hotelier_template_archive_room_description - 20
	 * @hooked hotelier_template_archive_room_price - 30
	 * @hooked hotelier_template_archive_room_more - 40
	 */
	do_action( 'hotelier_archive_item_room' );
	?>

</li>

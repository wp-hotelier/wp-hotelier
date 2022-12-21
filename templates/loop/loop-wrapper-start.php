<?php
/**
 * Archive Loop Wrapper Start
 *
 * This template can be overridden by copying it to yourtheme/hotelier/loop/loop-wrapper-start.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Store column count for displaying the grid
if ( empty( $hotelier_loop[ 'columns' ] ) ) {
	$hotelier_loop[ 'columns' ] = apply_filters( 'loop_room_columns', 3 );
}

$archive_rooms_wrapper_class = apply_filters( 'hotelier_archive_rooms_wrapper_class', '' );
?>

<div class="hotelier room-loop room-loop--archive-rooms room-loop--columns-<?php echo absint( $hotelier_loop[ 'columns' ] ); ?> <?php echo esc_attr( $archive_rooms_wrapper_class ); ?>>">

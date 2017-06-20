<?php
/**
 * Rooms left message
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/rooms-left.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$available_rooms         = absint( $room->get_available_rooms( $checkin, $checkout ) );
$low_room_threshold      = htl_get_option( 'low_room_threshold', 2 );
$show_left_rooms_message = ( $available_rooms <= $low_room_threshold && $is_available ) ? true : false;

?>

<?php if ( apply_filters( 'hotelier_show_left_rooms_message', $show_left_rooms_message ) ) : ?>
	<mark class="room__only-x-left"><?php echo sprintf( _n( '%s room left!', '%s rooms left!', $available_rooms, 'wp-hotelier' ), $available_rooms ); ?></mark>
<?php endif; ?>

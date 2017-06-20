<?php
/**
 * Show info when a room is not available
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/not-available-info.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( $is_available ) {
	return;
}
?>

<div class="room__not-available-info">
	<p><?php echo ( apply_filters( 'hotelier_room_list_not_available_info_text', esc_html__( 'The room is not available for this date', 'wp-hotelier' ) ) ); ?></p>
</div>

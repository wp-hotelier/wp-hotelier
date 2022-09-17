<?php
/**
 * Show info when a room is not available
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/not-available-info.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( $is_available ) {
	return;
}
?>

<div class="room-card__not-available-info room-card__info">
	<?php echo ( apply_filters( 'hotelier_room_list_not_available_info_text', esc_html__( 'The room is not available for this date', 'wp-hotelier' ) ) ); ?>
</div>

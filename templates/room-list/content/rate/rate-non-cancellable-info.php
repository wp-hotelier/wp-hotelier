<?php
/**
 * Show info when a room is non-cancellable
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/rate/rate-non-cancellable-info.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $variation->is_cancellable() ) {
	return;
}
?>

<div class="rate__non-cancellable-info rate__non-cancellable-info--listing">
	<p><?php echo ( apply_filters( 'hotelier_room_list_non_cancellable_info_text', esc_html__( 'Non-refundable', 'wp-hotelier' ) ) ); ?></p>
</div>

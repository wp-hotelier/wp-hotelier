<?php
/**
 * Show info when a room is non-cancellable
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/content/rate/rate-non-cancellable-info.php.
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

<span class="rate__non-cancellable-info rate__non-cancellable-info--single"><?php echo esc_html( apply_filters( 'hotelier_single_room_non_cancellable_info_text', __( 'Non-refundable', 'wp-hotelier' ) ) ); ?></span>

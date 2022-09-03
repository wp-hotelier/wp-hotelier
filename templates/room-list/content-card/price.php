<?php
/**
 * Room price
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/price.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( $price_html = $room->get_price_html( $checkin, $checkout ) ) : ?>

	<div class="room-card__price-wrapper">
		<span class="room-card__price"><?php echo $price_html; ?></span>
		<span class="room-card__price-description"><?php esc_html_e( 'Prices are per room', 'wp-hotelier' ); ?></span>
	</div>

<?php endif; ?>

<?php
/**
 * Room price
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/price.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( $price_html = $room->get_price_html( $checkin, $checkout ) ) : ?>

	<div class="room__price-wrapper room__price-wrapper--listing">
		<span class="room__price room__price--listing"><?php echo $price_html; ?></span>
		<span class="room__price-description"><?php esc_html_e( 'Prices are per room', 'wp-hotelier' ); ?></span>
	</div>

<?php endif; ?>

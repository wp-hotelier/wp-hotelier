<?php
/**
 * Room rate price
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/rate/rate-price.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( $price_html = $variation->get_price_html( $checkin, $checkout ) ) && apply_filters( 'hotelier_show_rate_price', true, $checkin, $checkout, $variation ) ) : ?>

	<div class="room-card__price-wrapper">
		<span class="room-card__price"><?php echo $price_html; ?></span>
		<span class="room-card__price-description"><?php esc_html_e( 'Prices are per room', 'wp-hotelier' ); ?></span>
	</div>

<?php endif; ?>

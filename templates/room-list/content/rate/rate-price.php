<?php
/**
 * Room rate price
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/rate/rate-price.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( $price_html = $variation->get_price_html( $checkin, $checkout ) ) && apply_filters( 'hotelier_show_rate_price', true, $checkin, $checkout, $variation ) ) : ?>

	<div class="rate__price rate__price--listing">
		<span class="rate__price rate__price--listing"><?php echo $price_html; ?></span>
		<span class="rate__price-description"><?php esc_html_e( 'Prices are per room', 'wp-hotelier' ); ?></span>
	</div>

<?php endif; ?>

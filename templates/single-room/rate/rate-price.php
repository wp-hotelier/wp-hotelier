<?php
/**
 * Room rate price
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/content/rate/rate-price.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="rate__price-wrapper rate__price-wrapper--single">
	<span class="rate__price rate__price--single"><?php echo $variation->get_min_price_html(); ?></span>
</div>

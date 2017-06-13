<?php
/**
 * The room price
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/content/room-price.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<span class="room__price room__price--loop"><?php echo $room->get_min_price_html(); ?></span>

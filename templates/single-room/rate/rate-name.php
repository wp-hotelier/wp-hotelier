<?php
/**
 * Room rate name
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/content/rate/rate-name.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h4 class="rate__name rate__name--single"><?php echo esc_html( $variation->get_formatted_room_rate() ); ?></h4>

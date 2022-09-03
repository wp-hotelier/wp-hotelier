<?php
/**
 * Room rate name
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/rate/rate-name.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h4 class="room-card__rate-name"><?php echo esc_html( $variation->get_formatted_room_rate() ); ?></h4>

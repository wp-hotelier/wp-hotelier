<?php
/**
 * Room meta
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/reserve-button.php.
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

<div id="reserve-rooms-button">

	<?php wp_nonce_field( 'hotelier_reserve_rooms' ); ?>

	<?php do_action( 'hotelier_room_list_before_submit' ); ?>

	<?php echo apply_filters( 'hotelier_reserve_button_html', '<input type="submit" class="button button--reserve" name="hotelier_reserve_rooms_button" id="reserve-button" value="' . esc_html__( 'Reserve', 'wp-hotelier' ) . '" />' ); ?>

	<?php do_action( 'hotelier_room_list_after_submit' ); ?>

</div>

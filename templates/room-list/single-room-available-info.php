<?php
/**
 * Displayed when the queried room is not available.
 *
 * Override this template by copying it to yourtheme/hotelier/room-list/single-room-available-info.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="hotelier-notice hotelier-notice--info hotelier-notice--single-room-available-info"><?php echo $rooms ? esc_html__( 'Hooray this room is available! For your convenience, below you can find other rooms that are available on the same dates.', 'wp-hotelier' ) : esc_html__( 'Hooray this room is available!', 'wp-hotelier' ); ?></p>

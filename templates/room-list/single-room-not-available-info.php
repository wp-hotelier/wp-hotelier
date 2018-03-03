<?php
/**
 * Displayed when the queried room is not available.
 *
 * Override this template by copying it to yourtheme/hotelier/room-list/single-room-not-available.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="hotelier-notice hotelier-notice--info hotelier-notice--single-room-not-available"><?php echo $rooms ? esc_html__( 'We are sorry, this room is not available on your requested dates. Please try again with some different dates or have a look at the available rooms below.', 'wp-hotelier' ) : esc_html__( 'We are sorry, this room is not available on your requested dates. Please try again with some different dates.', 'wp-hotelier' ); ?></p>

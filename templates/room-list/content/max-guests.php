<?php
/**
 * Room max guests
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/max-guests.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$max_guests   = $room->get_max_guests();
$max_children = $room->get_max_children();

?>

<div class="room__max-guests">
	<strong class="room__max-guests-label"><?php esc_html_e( 'Guests:', 'wp-hotelier' ); ?></strong> <span class="max max<?php echo absint( $max_guests ); ?>"><?php echo absint( $max_guests ); ?></span>

	<?php if ( $max_children > 0 ) : ?>
		<div class="room__max-guests-recommendation"><?php printf( esc_html__( 'Recommended for %s adult(s) and %s child(ren)', 'wp-hotelier' ), absint( $max_guests ), absint( $max_children ) ); ?></div>
	<?php endif; ?>
</div>

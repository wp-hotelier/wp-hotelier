<?php
/**
 * Room max guests
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/max-guests.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$max_guests   = $room->get_max_guests();
$max_children = $room->get_max_children();

?>

<div class="room-card__max-guests room-card__info">
	<?php if ( $max_children > 0 ) : ?>
		<div class="room-card__max-guests-recommendation"><?php printf( esc_html__( 'Recommended for %s adult(s) and %s child(ren)', 'wp-hotelier' ), absint( $max_guests ), absint( $max_children ) ); ?></div>
	<?php endif; ?>
</div>

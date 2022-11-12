<?php
/**
 * Room meta
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/meta.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<div class="room-card__meta">

	<?php if ( $bed_size = $room->get_bed_size() ) : ?>
		<span class="room-card__beds"><?php echo esc_html( $bed_size ); ?></span>
	<?php endif; ?>

	<?php if ( $bathrooms = $room->get_bathrooms() ) : ?>
		<span class="room-card__bathrooms"><?php echo esc_html( sprintf( _n( '%s bathroom', '%s bathrooms', $bathrooms, 'wp-hotelier' ), absint( $bathrooms ) ) ); ?></span>
	<?php endif; ?>

	<?php if ( $room->get_room_size() ) : ?>
		<span class="room-card__size"><?php echo esc_html( $room->get_formatted_room_size() ); ?></span>
	<?php endif; ?>

</div>

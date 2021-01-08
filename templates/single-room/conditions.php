<?php
/**
 * Room conditions.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/conditions.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->has_conditions() ) {
	return;
}

?>

<div class="room__conditions room__conditions--single">

	<?php if ( apply_filters( 'hotelier_single_room_conditions_show_title', true ) ) : ?>
		<h3 class="room__conditions-title room__conditions-title--single"><?php esc_html_e( 'Room conditions', 'wp-hotelier' ); ?></h3>
	<?php endif; ?>

	<ul class="room__conditions-list room__conditions-list--single">

	<?php foreach ( $room->get_room_conditions() as $condition ) : ?>

		<li class="room__conditions-item room__conditions-item--single"><?php echo esc_html( $condition ); ?></li>

	<?php endforeach; ?>

	</ul>

</div>

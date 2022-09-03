<?php
/**
 * Room conditions
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/conditions.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->has_conditions() ) {
	return;
}

?>

<div class="room-card__conditions">

	<ul class="room-card__conditions-list">

		<?php foreach ( $room->get_room_conditions() as $condition ) : ?>

			<li class="room-card__conditions-item"><?php echo esc_html( $condition ); ?></li>

		<?php endforeach; ?>

	</ul>

</div>

<?php
/**
 * Room rate conditions
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/rate/rate-conditions.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $variation->has_conditions() ) {
	return;
}

?>

<div class="room-card__conditions">

	<ul class="room-card__conditions-list">

		<?php foreach ( $variation->get_room_conditions() as $condition ) : ?>

			<li class="room-card__conditions-item"><?php echo esc_html( $condition ); ?></li>

		<?php endforeach; ?>

	</ul>

</div>

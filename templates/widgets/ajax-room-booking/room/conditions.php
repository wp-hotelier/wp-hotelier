<?php
/**
 * Room conditions
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/room/conditions.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $show_room_conditions && $room->has_conditions() ) : ?>
	<div class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--conditions room__conditions room__conditions--widget-ajax-room-booking">
		<strong class="room__conditions-title room__conditions-title--widget-ajax-room-booking"><?php esc_html_e( 'Room conditions:', 'wp-hotelier' ) ?></strong>

		<ul class="room__conditions-list room__conditions-list--widget-ajax-room-booking">
		<?php foreach ( $room->get_room_conditions() as $condition ) : ?>
			<li class="room__conditions-item room__conditions-item--widget-ajax-room-booking"><?php echo esc_html( $condition ); ?></li>
		<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

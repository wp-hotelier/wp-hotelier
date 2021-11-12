<?php
/**
 * Rate conditions
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/rate/conditions.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $show_room_conditions && $variation->has_conditions() ) : ?>
	<div class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--conditions rate__conditions rate__conditions--widget-ajax-room-booking widget-ajax-room-booking__data--rate" data-rate-id="<?php echo esc_attr( $variation->get_room_index() ); ?>">
		<strong class="rate__conditions-title rate__conditions-title--widget-ajax-room-booking"><?php esc_html_e( 'Rate conditions:', 'wp-hotelier' ) ?></strong>

		<ul class="rate__conditions-list rate__conditions-list--widget-ajax-room-booking">
		<?php foreach ( $variation->get_room_conditions() as $condition ) : ?>
			<li class="rate__conditions-item rate__conditions-item--widget-ajax-room-booking"><?php echo esc_html( $condition ); ?></li>
		<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php
/**
 * Rate non cancellable info
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/rate/non-cancellable-info.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( ! $variation->is_cancellable() ) : ?>
	<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--info rate__non-cancellable-info widget-ajax-room-booking__data--rate" data-rate-id="<?php echo esc_attr( $variation->get_room_index() ); ?>">
		<?php echo ( apply_filters( 'hotelier_room_list_non_cancellable_info_text', esc_html__( 'Non-refundable', 'wp-hotelier' ) ) ); ?>
	</p>
<?php endif; ?>

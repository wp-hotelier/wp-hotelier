<?php
/**
 * Room non cancellable info
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/room/non-cancellable-info.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( ! $room->is_cancellable() ) : ?>
	<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--info room__non-cancellable-info">
		<?php echo ( apply_filters( 'hotelier_room_list_non_cancellable_info_text', esc_html__( 'Non-refundable', 'wp-hotelier' ) ) ); ?>
	</p>
<?php endif; ?>

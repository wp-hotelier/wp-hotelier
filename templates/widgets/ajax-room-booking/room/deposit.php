<?php
/**
 * Room deposit
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/room/deposit.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $show_room_deposit && $room->needs_deposit() ) : ?>
	<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--deposit">
		<span class="room__deposit room__deposit--single">
			<?php if ( apply_filters( 'hotelier_single_room_long_formatted_deposit', false ) ) : ?>
				<span class="room__deposit-amount room__deposit-amount--single"><?php echo wp_kses_post( $room->get_long_formatted_deposit() ); ?></span>
			<?php else:  ?>
				<span class="room__deposit-label room__deposit-label--single"><?php esc_html_e( 'Deposit required', 'wp-hotelier' ); ?></span>
				<span class="room__deposit-amount room__deposit-amount--single"><?php echo wp_kses( $room->get_formatted_deposit(), array( 'span' => array( 'class' => array() ) ) ); ?></span>
			<?php endif; ?>
		</span>
	</p>
<?php endif; ?>

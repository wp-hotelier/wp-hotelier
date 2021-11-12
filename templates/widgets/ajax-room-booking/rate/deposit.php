<?php
/**
 * Rate deposit
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/rate/deposit.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $show_room_deposit && $variation->needs_deposit() ) : ?>
	<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--deposit widget-ajax-room-booking__data--rate" data-rate-id="<?php echo esc_attr( $variation->get_room_index() ); ?>">
		<span class="rate__deposit rate__deposit--widget-ajax-room-booking">
			<span class="rate__deposit-label rate__deposit-label--widget-ajax-room-booking"><?php esc_html_e( 'Deposit required', 'wp-hotelier' ); ?></span>
			<span class="rate__deposit-amount rate__deposit-amount--widget-ajax-room-booking"><?php echo wp_kses( $variation->get_formatted_deposit(), array( 'span' => array( 'class' => array() ) ) ); ?></span>
		</span>
	</p>
<?php endif; ?>

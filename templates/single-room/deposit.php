<?php
/**
 * Room deposit.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/deposit.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->is_variable_room() && $room->needs_deposit() ) : ?>

<?php do_action( 'hotelier_before_single_room_deposit' ); ?>

<?php if ( apply_filters( 'hotelier_single_room_long_formatted_deposit', false ) ) : ?>
	<div class="room__deposit room__deposit--single">
		<?php echo wp_kses_post( $room->get_long_formatted_deposit() ); ?>
	</div>
<?php else:  ?>
	<div class="room__deposit room__deposit--single">
		<span class="room__deposit-label room__deposit-label--single"><?php esc_html_e( 'Deposit required', 'wp-hotelier' ); ?></span>
		<span class="room__deposit-amount room__deposit-amount--single"><?php echo wp_kses( $room->get_formatted_deposit(), array( 'span' => array( 'class' => array() ) ) ); ?></span>
	</div>
<?php endif; ?>

<?php do_action( 'hotelier_after_single_room_deposit' ); ?>

<?php endif; ?>

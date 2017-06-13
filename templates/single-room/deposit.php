<?php
/**
 * Room deposit.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/deposit.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->is_variable_room() && $room->needs_deposit() ) : ?>

<div class="room__deposit room__deposit--single">
	<span class="room__deposit-label room__deposit-label--single"><?php esc_html_e( 'Deposit required', 'hotelier' ); ?></span>
	<span class="room__deposit-amount room__deposit-amount--single"><?php echo esc_html( $room->get_formatted_deposit() ); ?></span>
</div>

<?php endif; ?>

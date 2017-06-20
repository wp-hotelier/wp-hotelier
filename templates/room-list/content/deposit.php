<?php
/**
 * Room desposit
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/deposit.php.
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

<div class="room__deposit room__deposit--listing">
	<span class="room__deposit-label room__deposit-label--listing"><?php esc_html_e( 'Deposit required', 'wp-hotelier' ); ?></span>
	<span class="room__deposit-amount room__deposit-amount--listing"><?php echo esc_html( $room->get_formatted_deposit() ); ?></span>
</div>

<?php endif; ?>

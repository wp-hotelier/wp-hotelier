<?php
/**
 * Room rate desposit
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/rate/rate-deposit.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $variation->needs_deposit() ) : ?>

<div class="room-card__deposit">
	<?php echo wp_kses_post( $variation->get_long_formatted_deposit() ); ?>
</div>

<?php endif; ?>

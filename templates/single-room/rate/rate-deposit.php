<?php
/**
 * Room rate desposit
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/content/rate/rate-deposit.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $variation->needs_deposit() ) : ?>

<div class="rate__deposit rate__deposit--single">
	<?php if ( apply_filters( 'hotelier_single_room_long_formatted_deposit', false ) ) : ?>
		<?php echo wp_kses_post( $variation->get_long_formatted_deposit() ); ?>
	<?php else:  ?>
		<span class="rate__deposit-label rate__deposit-label--single"><?php esc_html_e( 'Deposit required', 'wp-hotelier' ); ?></span>
		<span class="rate__deposit-amount rate__deposit-amount--single"><?php echo wp_kses( $variation->get_formatted_deposit(), array( 'span' => array( 'class' => array() ) ) ); ?></span>
	<?php endif; ?>

</div>

<?php endif; ?>

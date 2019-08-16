<?php
/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/payment-method.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<li class="payment-method payment-method--<?php echo esc_attr( $gateway->id ); ?> <?php echo $single ? 'payment-method--single' : '' ?>">
	<input id="payment-method-<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="payment-method__input input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->selected, true ); ?> />

	<label class="payment-method__label" for="payment-method-<?php echo esc_attr( $gateway->id ); ?>">
		<?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?>
	</label>

	<?php if ( $gateway->get_description() ) : ?>
		<div class="payment-method__description">
			<?php echo wp_kses_post( $gateway->get_description() ); ?>

			<?php if ( $gateway->has_fields() ) : ?>
				<?php $gateway->payment_fields(); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</li>

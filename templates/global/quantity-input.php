<?php
/**
 * Room quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/hotelier/global/quantity-input.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="room-quantity">
	<label class="room-quantity__label" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Nr. rooms', 'wp-hotelier' ); ?></label>
	<input type="number" id="<?php echo esc_attr( $id ); ?>" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Room quantity input tooltip', 'wp-hotelier' ) ?>" class="input-text room-quantity__input" size="4" />
</div>

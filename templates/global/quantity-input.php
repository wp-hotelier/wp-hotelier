<?php
/**
 * Room quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/hotelier/global/quantity-input.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="<?php echo esc_attr( $class ); ?>">
	<label class="<?php echo esc_attr( $class ); ?>__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $input_label ); ?></label>
	<input type="number" id="<?php echo esc_attr( $id ); ?>" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Room quantity input tooltip', 'wp-hotelier' ) ?>" class="input-text <?php echo esc_attr( $class ); ?>__input" size="4" />

	<?php if ( is_array( $adults_args ) && count( $adults_args ) > 0 ) : ?>
		<div class="guests-quantity guests-quantity--adults">
			<label class="guests-quantity__label" for="<?php echo esc_attr( $adults_args['input_name'] );  ?>"><?php echo esc_html( $adults_args['label'] ); ?></label>
			<select name="<?php echo esc_attr( $adults_args['input_name'] ); ?>" id="<?php echo esc_attr( $adults_args['input_name'] );  ?>" class="select">
				<?php foreach ( $adults_args['options'] as $adults_key => $adults_value ) : ?>
					<option value="<?php echo esc_attr( $adults_key ); ?>" <?php echo selected( $adults_args['default'], $adults_key, false ); ?>><?php echo esc_html( $adults_value );  ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<?php if ( is_array( $children_args ) && count( $children_args ) > 0 ) : ?>
		<div class="guests-quantity guests-quantity--children">
			<label class="guests-quantity__label" for="<?php echo esc_attr( $children_args['input_name'] );  ?>"><?php echo esc_html( $children_args['label'] ); ?></label>
			<select name="<?php echo esc_attr( $children_args['input_name'] ); ?>" id="<?php echo esc_attr( $children_args['input_name'] );  ?>" class="select">
				<?php foreach ( $children_args['options'] as $children_key => $children_value ) : ?>
					<option value="<?php echo esc_attr( $children_key ); ?>" <?php echo selected( $children_args['default'], $children_key, false ); ?>><?php echo esc_html( $children_value );  ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<?php do_action( 'hotelier_after_quantity_input' ); ?>
</div>

<?php
/**
 * Meta Box Field "Textarea"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $thepostid, $post;

$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;
$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
$field[ 'name' ]          = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';
$field[ 'placeholder' ]   = isset( $field[ 'placeholder' ] ) ? $field[ 'placeholder' ] : '';

// Set field value
if ( isset( $field[ 'value' ] ) && $field[ 'value' ] ) {
	$field_value = $field[ 'value' ];
} else if ( isset( $field[ 'std' ] ) ) {
	// This means that if a std value is passed
	// the input will never be empty
	$field_value = $field[ 'std' ];
} else {
	$field_value = '';
}

?>

<div class="htl-ui-setting htl-ui-setting--metabox htl-ui-setting--textarea <?php echo esc_attr( $field[ 'wrapper_class' ] ); ?> htl-ui-layout htl-ui-layout--two-columns">

	<div class="htl-ui-layout__column htl-ui-layout__column--left">
		<h3 class="htl-ui-heading htl-ui-setting__title"><?php echo esc_html( $field[ 'label' ] ); ?></h3>

		<?php if ( isset( $field[ 'after_label' ] ) ) : ?>
			<div class="htl-ui-setting__title-description"><?php echo wp_kses_post( $field[ 'after_label' ] ); ?></div>
		<?php endif; ?>
	</div>

	<div class="htl-ui-layout__column htl-ui-layout__column--right">
		<textarea class="<?php echo esc_attr( $field[ 'class' ] ); ?> htl-ui-input htl-ui-input--textarea" name="<?php echo esc_attr( $field[ 'name' ] ); ?>" placeholder="<?php echo esc_attr( $field[ 'placeholder' ] ); ?>"><?php echo esc_attr( $field_value ); ?></textarea>

		<?php if ( isset( $field[ 'after_input' ] ) ) : ?>
			<span class="htl-ui-setting__after-input"><?php echo wp_kses_post( $field[ 'after_input' ] ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $field[ 'description' ] ) ) : ?>
			<div class="htl-ui-setting__description htl-ui-setting__description--textarea"><?php echo wp_kses_post( $field[ 'description' ] ); ?></div>
		<?php endif; ?>
	</div>
</div>

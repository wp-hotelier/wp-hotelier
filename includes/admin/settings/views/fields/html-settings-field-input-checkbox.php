<?php
/**
 * Field "Input Checkbox"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$value = $this->options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$value        = $value ? true : false;
$is_toggle    = isset( $args[ 'toggle' ] ) ? true : false;
$show_if      = isset( $args[ 'show-if' ] ) ? true : false;
$show_element = isset( $args[ 'show-element' ] ) ? $args[ 'show-element' ] : '';
$toggle_class = $show_if && $show_element ? 'show-if-setting-toggle' : '';

// Multiple elements must be comma separated
if ( is_array( $show_element ) ) {
	$show_element = implode( ',', $show_element );
}

?>

<div class="htl-ui-setting htl-ui-setting--checkbox htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<?php if ( $is_toggle ) : ?>
		<div class="htl-ui-toggle <?php echo esc_attr( $toggle_class ); ?>" <?php echo $show_if ? 'data-show-if="true"' : ''; ?> <?php echo $show_element ? 'data-show-element="' . esc_attr( $show_element ) . '"' : ''; ?>>
	<?php endif; ?>

		<input type="checkbox" class="htl-ui-input htl-ui-input--checkbox <?php echo $is_toggle ? 'htl-ui-toggle__input' : ''; ?>" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="1" <?php echo checked( $value, 1, false ); ?> />

		<label class="htl-ui-label htl-ui-label--checkbox <?php echo $is_toggle ? 'htl-ui-toggle__label' : ''; ?>" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]">
			<?php if ( $is_toggle ) : ?>
				<span class="htl-ui-toggle__handle"></span>
			<?php else : ?>
				<?php echo wp_kses_post( $args[ 'desc' ] ); ?>
			<?php endif; ?>
		</label>

	<?php if ( $is_toggle ) : ?>
		</div>
	<?php endif; ?>

	<?php if ( $args[ 'subdesc' ] ) : ?>
		<p class="htl-ui-setting__description htl-ui-setting__description--checkbox htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'subdesc' ] ); ?></p>
	<?php endif; ?>
</div>

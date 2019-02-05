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
?>

<div class="htl-ui-setting htl-ui-setting--checkbox htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<input type="checkbox" class="htl-ui-input htl-ui-input--checkbox" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="1" <?php echo checked( $value, 1, false ); ?> />

	<label class="htl-ui-label htl-ui-label--checkbox" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>

	<?php if ( $args[ 'subdesc' ] ) : ?>
		<p class="htl-ui-setting__description htl-ui-setting__description--checkbox htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'subdesc' ] ); ?></p>
	<?php endif; ?>
</div>

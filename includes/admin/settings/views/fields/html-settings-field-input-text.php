<?php
/**
 * Field "Input Text"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$value = $this->options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$placeholder = isset( $args[ 'placeholder' ] ) ? $args[ 'placeholder' ] : '';
$size        = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? 'htl-ui-input--' . $args[ 'size' ] : '';
?>

<div class="htl-ui-setting htl-ui-setting--text htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<input type="text" class="<?php echo esc_attr( $size ); ?> htl-ui-input htl-ui-input--text" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" />

	<?php if ( $args[ 'desc' ] ) : ?>
		<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" class="htl-ui-label htl-ui-label--text htl-ui-setting__description htl-ui-setting__description--text htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
	<?php endif; ?>
</div>

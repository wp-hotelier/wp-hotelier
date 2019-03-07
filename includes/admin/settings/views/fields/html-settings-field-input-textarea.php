<?php
/**
 * Field "Input Textarea"
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

<div class="htl-ui-setting htl-ui-setting--textarea htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<textarea class="<?php echo esc_attr( $size ); ?> htl-ui-input htl-ui-input--textarea" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" placeholder="<?php echo esc_attr( $placeholder ); ?>"><?php echo esc_attr( $value ); ?></textarea>

	<?php if ( $args[ 'desc' ] ) : ?>
		<label class="htl-ui-label htl-ui-label--textarea htl-ui-setting__description htl-ui-setting__description--textarea htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
	<?php endif; ?>
</div>

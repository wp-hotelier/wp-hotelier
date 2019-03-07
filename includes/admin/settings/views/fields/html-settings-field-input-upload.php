<?php
/**
 * Field "Input Upload"
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
$size        = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
?>

<div class="htl-ui-setting htl-ui-setting--upload htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<input type="text" class="<?php echo esc_attr( $size ); ?>-text htl-ui-input htl-ui-input--text htl-ui-input--upload" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" />

	<a href="#" class="htl-ui-button htl-ui-button--upload"><?php esc_html_e( 'Upload', 'wp-hotelier' ); ?></a>

	<?php if ( $args[ 'desc' ] ) : ?>
		<label class="htl-ui-label htl-ui-label--text htl-ui-setting__description htl-ui-setting__description--upload htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
	<?php endif; ?>
</div>

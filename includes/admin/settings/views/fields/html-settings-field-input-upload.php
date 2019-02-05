<?php
/**
 * Field "Input Upload"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $options[ $args[ 'id' ] ] ) ) {
	$value = $options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$placeholder = isset( $args[ 'placeholder' ] ) ? $args[ 'placeholder' ] : '';
$size        = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
?>

<div class="htl-ui-setting htl-ui-setting--upload htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<input type="text" class="<?php echo esc_attr( $size ); ?>-text htl-ui-input htl-ui-input--text htl-ui-input--upload" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" />

	<a href="#" class="htl-ui-button htl-ui-button--upload"><?php esc_html_e( 'Upload', 'wp-hotelier' ); ?></a>

	<label class="htl-ui-label htl-ui-label--text htl-ui-setting__description htl-ui-setting__description--upload htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
</div>

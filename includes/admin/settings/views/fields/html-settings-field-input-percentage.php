<?php
/**
 * Field "Input Percentage"
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
?>

<input type="text" class="medium-text" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" />

<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>

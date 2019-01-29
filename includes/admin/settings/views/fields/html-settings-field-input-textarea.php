<?php
/**
 * Field "Input Textarea"
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

<textarea class="<?php echo esc_attr( $size ); ?>-text" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" placeholder="<?php echo esc_attr( $placeholder ); ?>"><?php echo esc_attr( $value ); ?></textarea>

<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>

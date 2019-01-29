<?php
/**
 * Field "Input Number"
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
$min         = isset( $args[ 'min' ] ) ? $args[ 'min' ] : '';
$max         = isset( $args[ 'max' ] ) ? $args[ 'max' ] : '';
?>

<input type="number" class="<?php echo esc_attr( $size ); ?>-text" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" />

<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>

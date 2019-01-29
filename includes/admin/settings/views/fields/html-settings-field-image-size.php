<?php
/**
 * Field "Image Size"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $options[ $args[ 'id' ] ] ) ) {
	$value = $options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$width   = $value[ 'width' ];
$height  = $value[ 'height' ];
$checked = isset( $options[ $args[ 'id' ] ][ 'crop' ] ) ? checked( 1, $options[ $args[ 'id' ] ][ 'crop' ], false ) : checked( 1, isset( $value[ 'crop' ] ), false );
?>

<input name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][width]" id="<?php echo esc_attr( $args[ 'id' ] ); ?>-width" type="text" size="3" value="<?php echo absint( $width ); ?>" /> &times; <input name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][height]" id="<?php echo esc_attr( $args[ 'id' ] ); ?>-height" type="text" size="3" value="<?php echo absint( $height ); ?>" />px

<label><input name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][crop]" id="<?php echo esc_attr( $args[ 'id' ] ); ?>-crop" type="checkbox" value="1" <?php echo $checked; ?> /><?php esc_html_e( 'Hard crop?', 'wp-hotelier' ); ?></label>

<?php
/**
 * Field "Image Size"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$value = $this->options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$width   = $value[ 'width' ];
$height  = $value[ 'height' ];
$checked = isset( $this->options[ $args[ 'id' ] ][ 'crop' ] ) ? checked( 1, $this->options[ $args[ 'id' ] ][ 'crop' ], false ) : checked( 1, isset( $value[ 'crop' ] ), false );
?>

<div class="htl-ui-setting htl-ui-setting--image-size htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<input class="htl-ui-input htl-ui-input--text htl-ui-input--image-size" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][width]" id="<?php echo esc_attr( $args[ 'id' ] ); ?>-width" type="text" size="3" value="<?php echo absint( $width ); ?>" /> &times; <input class="htl-ui-input htl-ui-input--text htl-ui-input--image-size" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][height]" id="<?php echo esc_attr( $args[ 'id' ] ); ?>-height" type="text" size="3" value="<?php echo absint( $height ); ?>" />px

	<label class="htl-ui-label htl-ui-label--image-size">
		<input class="htl-ui-input htl-ui-input--checkbox htl-ui-input--image-size" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][crop]" id="<?php echo esc_attr( $args[ 'id' ] ); ?>-crop" type="checkbox" value="1" <?php echo $checked; ?> /><?php esc_html_e( 'Hard crop?', 'wp-hotelier' ); ?>
	</label>
</div>

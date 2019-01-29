<?php
/**
 * Field "Input Multi Checkbox"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php foreach ( $args[ 'options' ] as $key => $option ) : ?>
	<?php $enabled = ( isset( $options[ $args[ 'id' ] ][ $key ] ) ) ? '1' : null; ?>

	<input name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" type="checkbox" value="1" <?php echo checked( '1', $enabled, false ); ?> />

	<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]"><?php echo wp_kses_post( $option ); ?></label>

<?php endforeach; ?>

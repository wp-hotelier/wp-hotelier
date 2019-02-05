<?php
/**
 * Field "Input Multi Checkbox"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="htl-ui-setting htl-ui-setting--multicheckbox htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<?php foreach ( $args[ 'options' ] as $key => $option ) : ?>
		<?php $enabled = ( isset( $options[ $args[ 'id' ] ][ $key ] ) ) ? '1' : null; ?>

		<input class="htl-ui-input htl-ui-input--checkbox" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" type="checkbox" value="1" <?php echo checked( '1', $enabled, false ); ?> />

		<label class="htl-ui-label htl-ui-label--multicheckbox" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]"><?php echo wp_kses_post( $option ); ?></label>

	<?php endforeach; ?>
</div>

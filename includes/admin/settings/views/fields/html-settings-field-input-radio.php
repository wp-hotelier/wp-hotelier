<?php
/**
 * Field "Input Radio"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php foreach ( $args[ 'options' ] as $key => $option ) : ?>
	<?php
	$checked = false;

	if ( isset( $this->options[ $args[ 'id' ] ] ) && $this->options[ $args[ 'id' ] ] == $key ) {
		$checked = true;
	} elseif ( isset( $args[ 'std' ] ) && $args[ 'std' ] == $key && ! isset( $this->options[ $args[ 'id' ] ] ) ) {
		$checked = true;
	}
	?>

	<input name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" type="radio" value="<?php echo esc_attr( $key ); ?>" <?php echo checked( true, $checked, false ); ?> />


	<label class="input-radio" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]"><?php echo wp_kses_post( $option ); ?></label>

<?php endforeach; ?>

<div class="description radio-description"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></div>

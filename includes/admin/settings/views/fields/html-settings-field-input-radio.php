<?php
/**
 * Field "Input Radio"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="htl-ui-setting htl-ui-setting--radio htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<?php foreach ( $args[ 'options' ] as $key => $option ) : ?>
		<?php
		$checked = false;

		if ( isset( $this->options[ $args[ 'id' ] ] ) && $this->options[ $args[ 'id' ] ] == $key ) {
			$checked = true;
		} elseif ( isset( $args[ 'std' ] ) && $args[ 'std' ] == $key && ! isset( $this->options[ $args[ 'id' ] ] ) ) {
			$checked = true;
		}
		?>

		<input class="htl-ui-input htl-ui-input--radio" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" type="radio" value="<?php echo esc_attr( $key ); ?>" <?php echo checked( true, $checked, false ); ?> />


		<label class="htl-ui-label htl-ui-label--radio" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]"><?php echo wp_kses_post( $option ); ?></label>

	<?php endforeach; ?>

	<?php if ( $args[ 'desc' ] ) : ?>
		<div class="htl-ui-setting__description htl-ui-setting__description--radio"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></div>
	<?php endif; ?>
</div>

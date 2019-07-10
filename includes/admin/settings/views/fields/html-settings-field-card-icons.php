<?php
/**
 * Field "Card Icons"
 *
 * Extensions can use the class of the 'span' to add the gateway icon with CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="htl-ui-setting htl-ui-setting--card-icons htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<?php foreach ( $args[ 'options' ] as $key => $option ) : ?>
		<?php $enabled = isset( $this->options[ $args[ 'id' ] ][ $key ] ) ? 1 : NULL; ?>
		<div class="htl-ui-card-icon-wrapper">
			<input name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" class="htl-ui-input htl-ui-input--checkbox htl-ui-input--card-icon" type="checkbox" value="1" <?php echo checked( 1, $enabled, false ); ?> />

			<label class="htl-ui-label htl-ui-label--checkbox htl-ui-label--card-icon" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]">
				<span class="htl-ui-card-icon htl-ui-card-icon--<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $option ); ?></span>
			</label>
		</div>
	<?php endforeach; ?>

	<div class="htl-ui-setting__description htl-ui-setting__description--card-icons"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></div>
</div>

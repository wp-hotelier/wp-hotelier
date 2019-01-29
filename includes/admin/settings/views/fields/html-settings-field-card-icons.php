<?php
/**
 * Field "Card Icons"
 *
 * Extensions can use the ID of the 'span' to add the gateway icon with CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php foreach ( $args[ 'options' ] as $key => $option ) : ?>
	<?php $enabled = isset( $options[ $args[ 'id' ] ][ $key ] ) ? 1 : NULL; ?>

	<input name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]" class="card-icons" type="checkbox" value="1" <?php echo checked( 1, $enabled, false ); ?> />

	<label class="input-checkbox card-icons" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][<?php echo esc_attr( $key ); ?>]"><span class="hotelier-accepted-cards" id="hotelier-accepted-cards-<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $option ); ?></span></label>
<?php endforeach; ?>

<div class="description cards-description"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></div>

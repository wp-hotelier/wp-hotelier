<?php
/**
 * Field "Input Checkbox"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $options[ $args[ 'id' ] ] ) ) {
	$value = $options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}
?>

<input type="checkbox" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="1" <?php echo checked( $value, 1, false ); ?> />

<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>

<?php if ( $args[ 'subdesc' ] ) : ?>
	<p class="description subdesc"><?php echo wp_kses_post( $args[ 'subdesc' ] ); ?></p>
<?php endif; ?>

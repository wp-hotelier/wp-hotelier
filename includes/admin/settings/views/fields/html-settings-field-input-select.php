<?php
/**
 * Field "Input Select"
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

<?php ob_start(); ?>

<select id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]">
	<?php foreach ( $args[ 'options' ] as $option => $name ) : ?>
		<?php $selected = selected( $option, $value, false ); ?>

		<option value="<?php echo esc_attr( $option ); ?>" <?php echo $selected ?>><?php echo esc_html( $name ); ?></option>
	<?php endforeach; ?>

</select>

<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>

<?php
$html = ob_get_clean();
echo apply_filters( 'hotelier_settings_print_select', $html, $args, $value );

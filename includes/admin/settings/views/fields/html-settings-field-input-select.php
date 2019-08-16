<?php
/**
 * Field "Input Select"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$value = $this->options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$size = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? 'htl-ui-input--' . $args[ 'size' ] : '';

?>

<?php ob_start(); ?>

<div class="htl-ui-setting htl-ui-setting--select htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<select class="<?php echo esc_attr( $size ); ?> htl-ui-input htl-ui-input--select" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]">
		<?php foreach ( $args[ 'options' ] as $option => $name ) : ?>
			<?php $selected = selected( $option, $value, false ); ?>

			<option value="<?php echo esc_attr( $option ); ?>" <?php echo $selected ?>><?php echo esc_html( $name ); ?></option>
		<?php endforeach; ?>

	</select>

	<?php if ( $args[ 'desc' ] ) : ?>
		<label class="htl-ui-label htl-ui-label--text htl-ui-setting__description htl-ui-setting__description--select htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
	<?php endif; ?>
</div>

<?php
$html = ob_get_clean();
echo apply_filters( 'hotelier_settings_print_select', $html, $args, $value );

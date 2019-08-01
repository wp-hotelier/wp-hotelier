<?php
/**
 * Field "Switch"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$field_value = $this->options[ $args[ 'id' ] ];

	if ( is_array( $args[ 'options' ] ) && ! empty( $args[ 'options' ] ) && ! array_key_exists( $field_value, $args[ 'options' ] )  ) {
		// If for some reason the saved value is not in
		// our array set the first option as selected
		reset( $args[ 'options' ] );
		$field_value = key( $args[ 'options' ] );
	}
} else if ( isset( $args[ 'std' ] ) ) {
	$field_value = $args[ 'std' ];
} else {
	if ( is_array( $args[ 'options' ] ) && ! empty( $args[ 'options' ] ) ) {
		// Set first option as selected
		reset( $args[ 'options' ] );
		$field_value = key( $args[ 'options' ] );
	} else {
		$field_value = '';
	}
}

$show_if      = isset( $args[ 'show-if' ] ) ? $args[ 'show-if' ] : false;
$show_element = isset( $args[ 'show-element' ] ) ? $args[ 'show-element' ] : '';
$switch_class = $show_if && $show_element ? 'show-if-setting-switch' : '';

// Multiple elements must be comma separated
if ( is_array( $show_element ) ) {
	$show_element = implode( ',', $show_element );
}

?>

<div class="htl-ui-setting htl-ui-setting--switch htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">

	<div class="<?php echo esc_attr( $switch_class ); ?> htl-ui-switch" <?php echo $show_if ? 'data-show-if="' . esc_attr( $show_if ) . '"' : ''; ?> <?php echo $show_element ? 'data-show-element="' . esc_attr( $show_element ) . '"' : ''; ?>>

		<?php foreach ( $args[ 'options' ] as $key => $value ) : ?>
			<?php
			$input_id = $args[ 'id' ] . rand();
			$checked  = checked( $field_value, $key, false );
			?>

			<input id="<?php echo esc_attr( $input_id ); ?>" type="radio" class="htl-ui-input htl-ui-input--switch htl-ui-switch__input htl-ui-switch__input--<?php echo esc_html( $key ); ?>" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_html( $key ); ?>" <?php echo $checked; ?>>
			<label class="htl-ui-switch__label" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $value ); ?></label>
		<?php endforeach; ?>

	</div>

	<?php if ( $args[ 'desc' ] ) : ?>
		<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" class="htl-ui-label htl-ui-label--switch htl-ui-setting__description htl-ui-setting__description--switch htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
	<?php endif; ?>
</div>

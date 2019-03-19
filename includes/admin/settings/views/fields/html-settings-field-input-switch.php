<?php
/**
 * Field "Switch"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$field_value = $this->options[ $args[ 'id' ] ];

	// Fallback for old checkboxes
	if ( isset( $args[ 'checkbox-fallback' ] ) ) {
		if ( $field_value === '1' ) {
			$field_value = 'yes';
		} else if ( $field_value === '0' || $field_value === 0 ) {
			$field_value = 'no';
		}
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

$show_if              = isset( $args[ 'show-if' ] ) ? $args[ 'show-if' ] : false;
$show_element         = isset( $args[ 'show-element' ] ) ? $args[ 'show-element' ] : '';
$conditional          = isset( $args[ 'conditional' ] ) ? $args[ 'conditional' ] : false;
$conditional_selector = isset( $args[ 'conditional-selector' ] ) ? $args[ 'conditional-selector' ] : '';
$switch_class         = '';

if ( ! $conditional && $show_if && $show_element ) {
	$switch_class .= ' show-if-switch';
}

if ( ! $show_if && $conditional && $conditional_selector ) {
	$switch_class .= ' conditional-switch';
}

?>

<div class="htl-ui-setting htl-ui-setting--switch htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">

	<div class="<?php echo esc_attr( $switch_class ); ?> htl-ui-switch" <?php echo $show_if && ! $conditional ? 'data-show-if="' . esc_attr( $show_if ) . '"' : ''; ?> <?php echo $show_element && ! $conditional ? 'data-show-element="' . esc_attr( $show_element ) . '"' : ''; ?> <?php echo $conditional_selector && ! $show_if ? 'data-conditional-selector="' . esc_attr( $conditional_selector ) . '"' : ''; ?>>

		<?php foreach ( $args[ 'options' ] as $key => $value ) : ?>
			<?php
			$input_id = $args[ 'id' ] . rand();
			$checked  = checked( $field_value, $key, false );
			?>

			<input id="<?php echo esc_attr( $input_id ); ?>" type="radio" class="htl-ui-input htl-ui-input--switch htl-ui-switch__input htl-ui-switch__input--<?php echo esc_html( $key ); ?>" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_html( $key ); ?>" <?php echo $checked; ?>>
			<label class="htl-ui-label htl-ui-label--switch htl-ui-switch__label" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $value ); ?></label>
		<?php endforeach; ?>

	</div>

	<?php if ( $args[ 'desc' ] ) : ?>
		<label for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" class="htl-ui-label htl-ui-label--switch htl-ui-setting__description htl-ui-setting__description--switch htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
	<?php endif; ?>
</div>

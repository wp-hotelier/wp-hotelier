<?php
/**
 * Meta Box Field "Switch"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $thepostid, $post;

$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;
$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
$field[ 'name' ]          = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';
$field[ 'show-if' ]       = isset( $field[ 'show-if' ] ) ? $field[ 'show-if' ] : false;
$field[ 'show-element' ]  = isset( $field[ 'show-element' ] ) ? $field[ 'show-element' ] : '';

if ( $field[ 'show-if' ] && $field[ 'show-element' ] ) {
	$field[ 'class' ] .= ' conditional-switch';
}

// Set field value
if ( isset( $field[ 'value' ] ) && $field[ 'value' ] ) {
	$field_value = $field[ 'value' ];
} else if ( isset( $field[ 'std' ] ) ) {
	$field_value = $field[ 'std' ];
} else {
	if ( is_array( $field[ 'options' ] ) && ! empty( $field[ 'options' ] ) ) {
		// Set first option as selected
		reset( $field[ 'options' ] );
		$field_value = key( $field[ 'options' ] );
	} else {
		$field_value = '';
	}
}

?>

<div class="htl-ui-setting htl-ui-setting--metabox htl-ui-setting--select <?php echo esc_attr( $field[ 'wrapper_class' ] ); ?> htl-ui-layout htl-ui-layout--two-columns">

	<div class="htl-ui-layout__column htl-ui-layout__column--left">
		<h3 class="htl-ui-heading htl-ui-setting__title"><?php echo esc_html( $field[ 'label' ] ); ?></h3>

		<?php if ( isset( $field[ 'after_label' ] ) ) : ?>
			<div class="htl-ui-setting__title-description"><?php echo wp_kses_post( $field[ 'after_label' ] ); ?></div>
		<?php endif; ?>
	</div>

	<div class="htl-ui-layout__column htl-ui-layout__column--right">
		<div class="<?php echo esc_attr( $field[ 'class' ] ); ?> htl-ui-input htl-ui-input--switch htl-ui-switch" <?php echo $field[ 'show-if' ] ? 'data-show-if="' . esc_attr( $field[ 'show-if' ] ) . '"' : ''; ?> <?php echo $field[ 'show-element' ] ? 'data-show-element="' . esc_attr( $field[ 'show-element' ] ) . '"' : ''; ?>>
			<?php foreach ( $field[ 'options' ] as $key => $value ) : ?>
				<?php
				$input_id = $field[ 'name' ] . rand();
				$checked  = checked( $field_value, $key, false );
				?>

				<input id="<?php echo esc_attr( $input_id ); ?>" type="radio" class="htl-ui-input htl-ui-input--switch htl-ui-switch__input" name="<?php echo esc_attr( $field[ 'name' ] ); ?>" value="<?php echo esc_html( $key ); ?>" <?php echo $checked; ?>>
				<label class="htl-ui-label htl-ui-label--switch htl-ui-switch__label" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $value ); ?></label>
			<?php endforeach; ?>
		</div>
	</div>
</div>

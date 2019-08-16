<?php
/**
 * Meta Box Field "Checkbox"
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

// Set field value
if ( isset( $field[ 'value' ] ) && $field[ 'value' ] ) {
	$checked = checked( 1, $field[ 'value' ], false );
} else {
	$checked = '';
}

if ( $field[ 'show-if' ] && $field[ 'show-element' ] ) {
	$field[ 'class' ] .= ' show-if-toggle';
}

$is_toggle = isset( $field[ 'toggle' ] ) ? true : false;

?>

<div class="htl-ui-setting htl-ui-setting--metabox htl-ui-setting--checkbox <?php echo esc_attr( $field[ 'wrapper_class' ] ); ?> htl-ui-layout htl-ui-layout--two-columns">

	<div class="htl-ui-layout__column htl-ui-layout__column--left">
		<h3 class="htl-ui-heading htl-ui-setting__title"><?php echo esc_html( $field[ 'label' ] ); ?></h3>

		<?php if ( isset( $field[ 'after_label' ] ) ) : ?>
			<div class="htl-ui-setting__title-description"><?php echo wp_kses_post( $field[ 'after_label' ] ); ?></div>
		<?php endif; ?>
	</div>

	<div class="htl-ui-layout__column htl-ui-layout__column--right">
		<?php if ( $is_toggle ) : ?>
			<div class="htl-ui-toggle <?php echo esc_attr( $field[ 'class' ] ); ?>" <?php echo $field[ 'show-if' ] ? 'data-show-if="true"' : ''; ?> <?php echo $field[ 'show-element' ] ? 'data-show-element="' . esc_attr( $field[ 'show-element' ] ) . '"' : ''; ?>>
		<?php endif; ?>

			<input type="checkbox" class="htl-ui-input htl-ui-input--checkbox <?php echo $is_toggle ? 'htl-ui-toggle__input' : ''; ?>" id="<?php echo esc_attr( $field[ 'name' ] ); ?>" name="<?php echo esc_attr( $field[ 'name' ] ); ?>" value="1" <?php echo $checked; ?> />
			<label class="<?php echo $is_toggle ? 'htl-ui-toggle__label' : ''; ?>" for="<?php echo esc_attr( $field[ 'name' ] ); ?>">
				<?php if ( $is_toggle ) : ?>
					<span class="htl-ui-toggle__handle"></span>
				<?php endif; ?>
			</label>
		<?php if ( $is_toggle ) : ?>
			</div>
		<?php endif; ?>

		<?php if ( isset( $field[ 'after_input' ] ) ) : ?>
			<span class="htl-ui-setting__after-input"><?php echo wp_kses_post( $field[ 'after_input' ] ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $field[ 'description' ] ) ) : ?>
			<div class="htl-ui-setting__description htl-ui-setting__description--checkbox"><?php echo wp_kses_post( $field[ 'description' ] ); ?></div>
		<?php endif; ?>
	</div>
</div>

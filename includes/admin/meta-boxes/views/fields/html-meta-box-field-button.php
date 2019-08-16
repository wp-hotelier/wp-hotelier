<?php
/**
 * Meta Box Field "button"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $thepostid, $post;

$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;
$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';
$field[ 'type' ]          = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'link';
$field[ 'button_id' ]     = isset( $field[ 'button_id' ] ) ? $field[ 'button_id' ] : '';
$field[ 'button_url' ]    = isset( $field[ 'button_url' ] ) ? $field[ 'button_url' ] : '';
$field[ 'button_text' ]   = isset( $field[ 'button_text' ] ) ? $field[ 'button_text' ] : '';

?>

<div class="htl-ui-setting htl-ui-setting--metabox htl-ui-setting--text <?php echo esc_attr( $field[ 'wrapper_class' ] ); ?> htl-ui-layout htl-ui-layout--two-columns">

	<div class="htl-ui-layout__column htl-ui-layout__column--left">
		<h3 class="htl-ui-heading htl-ui-setting__title"><?php echo esc_html( $field[ 'label' ] ); ?></h3>

		<?php if ( isset( $field[ 'after_label' ] ) ) : ?>
			<div class="htl-ui-setting__title-description"><?php echo wp_kses_post( $field[ 'after_label' ] ); ?></div>
		<?php endif; ?>
	</div>

	<div class="htl-ui-layout__column htl-ui-layout__column--right">
		<?php if ( $field[ 'type' ] === 'link' ) : ?>
			<a id="<?php echo esc_attr( $field[ 'button_id' ] ); ?>" class="htl-ui-button htl-ui-button--meta-box-button <?php echo esc_attr( $field[ 'class' ] ); ?>" href="<?php echo esc_url( $field[ 'button_url' ] ) ?>"><?php echo esc_html( $field[ 'button_text' ] ) ?></a>
		<?php else : ?>
			<button type="button" id="<?php echo esc_attr( $field[ 'button_id' ] ); ?>" class="htl-ui-button htl-ui-button--meta-box-button <?php echo esc_attr( $field[ 'class' ] ); ?>"><?php echo esc_html( $field[ 'button_text' ] ) ?></button>
		<?php endif; ?>

		<?php if ( ! empty( $field[ 'description' ] ) ) : ?>
			<div class="htl-ui-setting__description htl-ui-setting__description--text"><?php echo wp_kses_post( $field[ 'description' ] ); ?></div>
		<?php endif; ?>
	</div>
</div>

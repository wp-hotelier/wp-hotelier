<?php
/**
 * Meta Box Field "Price per day"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $thepostid, $post, $wp_locale;

$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;
$field[ 'wrapper_class' ] = isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
$field[ 'class' ]         = isset( $field[ 'class' ] ) ? $field[ 'class' ] : '';
$field[ 'name' ]          = isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
$field[ 'label' ]         = isset( $field[ 'label' ] ) ? $field[ 'label' ] : '';

// Set field value
if ( isset( $field[ 'value' ] ) && $field[ 'value' ] ) {
	$field_value = $field[ 'value' ];
} else {
	$field_value = '';
}

$locale_start_of_week = get_option( 'start_of_week' );

?>

<div class="htl-ui-setting htl-ui-setting--metabox htl-ui-setting--price-per-day <?php echo esc_attr( $field[ 'wrapper_class' ] ); ?> htl-ui-layout htl-ui-layout--two-columns">

	<div class="htl-ui-layout__column htl-ui-layout__column--left">
		<h3 class="htl-ui-heading htl-ui-setting__title"><?php echo esc_html( $field[ 'label' ] ); ?></h3>

		<?php if ( isset( $field[ 'after_label' ] ) ) : ?>
			<div class="htl-ui-setting__title-description"><?php echo wp_kses_post( $field[ 'after_label' ] ); ?></div>
		<?php endif; ?>
	</div>

	<div class="htl-ui-layout__column htl-ui-layout__column--right">
		<div class="htl-ui-price-per-day-wrapper">
			<?php for ( $i = 0; $i < 7; $i ++ ) : ?>
				<?php
				$day_index   = ( $locale_start_of_week + $i ) % 7;
				$day         = $wp_locale->get_weekday( $day_index );
				$day_initial = $wp_locale->get_weekday_initial( $day );
				$value       = is_array( $field_value ) && isset( $field_value[ $day_index ] ) ? HTL_Formatting_Helper::localized_amount( $field_value[ $day_index ] ) : '';
				?>

				<div class="htl-ui-price-day">
					<label class="htl-ui-label htl-ui-price-day__label">
						<span class="htl-ui-price-day__name"><?php echo esc_html( $day_initial ); ?></span>
						<input type="text" class="<?php echo esc_attr( $field[ 'class' ] ); ?> htl-ui-input--small htl-ui-input htl-ui-input--text htl-ui-input--price htl-ui-price-day__input" name="<?php echo esc_attr( $field[ 'name' ] ); ?>[<?php echo absint( $day_index ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( HTL_Meta_Box_Room_Settings::get_price_placeholder() ); ?>" />
					</label>
				</div>
			<?php endfor; ?>
		</div>

		<?php if ( ! empty( $field[ 'description' ] ) ) : ?>
			<div class="htl-ui-setting__description htl-ui-setting__description--price-per-day"><?php echo wp_kses_post( $field[ 'description' ] ); ?></div>
		<?php endif; ?>
	</div>
</div>

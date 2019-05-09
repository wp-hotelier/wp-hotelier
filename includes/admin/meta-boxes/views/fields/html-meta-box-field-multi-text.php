<?php
/**
 * Meta Box Field "Multi Text"
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
$field[ 'placeholder' ]   = isset( $field[ 'placeholder' ] ) ? $field[ 'placeholder' ] : '';
$field[ 'button_label' ]  = isset( $field[ 'button_label' ] ) ? $field[ 'button_label' ] : __( 'Add new', 'wp-hotelier' );
$field[ 'type' ]          = isset( $field[ 'type' ] ) ? $field[ 'type' ] : '';

// Set field value
if ( isset( $field[ 'value' ] ) && $field[ 'value' ] ) {
	$field_value = $field[ 'value' ];
} else {
	$field_value = false;
}
?>

<div class="htl-ui-setting htl-ui-setting--metabox htl-ui-setting--multi-text <?php echo esc_attr( $field[ 'wrapper_class' ] ); ?> htl-ui-layout htl-ui-layout--two-columns">

	<div class="htl-ui-layout__column htl-ui-layout__column--left">
		<h3 class="htl-ui-heading htl-ui-setting__title"><?php echo esc_html( $field[ 'label' ] ); ?></h3>

		<?php if ( isset( $field[ 'after_label' ] ) ) : ?>
			<div class="htl-ui-setting__title-description"><?php echo wp_kses_post( $field[ 'after_label' ] ); ?></div>
		<?php endif; ?>
	</div>

	<div class="htl-ui-layout__column htl-ui-layout__column--right">
		<table class="htl-ui-table htl-ui-table--multi-text htl-ui-table--sortable" data-type="<?php echo esc_attr( $field[ 'type' ] ); ?>">

			<tbody class="htl-ui-table__body htl-ui-table__body--multi-text htl-ui-table__body--sortable">

				<?php
				$has_row     = $field_value && is_array( $field_value ) ? true : false;
				$rows_lenght = $field_value ? count( $field_value ) : 1;

				for ( $i = 1; $i <= $rows_lenght; $i++ ) :
					$row_value = $has_row ? $field_value[ absint( $i ) ][ 'name' ] : '';
					?>

					<tr class="htl-ui-table__row htl-ui-table__row--body htl-ui-table__row--multi-text htl-ui-table__row--sortable" data-key="<?php echo absint( $i ); ?>">
						<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--row-value htl-ui-table__cell--multi-text-value">
							<input type="text" class="htl-ui-input htl-ui-input--text" name="<?php echo esc_attr( $field[ 'name' ] ); ?>[<?php echo absint( $i ); ?>][name]" value="<?php echo esc_attr( $row_value ); ?>" placeholder="<?php echo esc_attr( $field[ 'placeholder' ] ); ?>" />

							<input type="hidden" class="htl-ui-input htl-ui-input--hidden htl-ui-input--row_index" name="<?php echo esc_attr( $field[ 'name' ] ); ?>[<?php echo absint( $i ); ?>][index]" value="<?php echo absint( $i ); ?>">
						</td>

						<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--remove-row">
							<button type="button" class="htl-ui-button htl-ui-button--remove-row"><?php esc_html_e( 'Remove', 'wp-hotelier' ); ?></button>
						</td>

						<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--sort-row">
							<?php esc_html_e( 'Sort', 'wp-hotelier' ); ?>
						</td>
					</tr>

				<?php endfor; ?>

			</tbody>

			<tfoot class="htl-ui-table__footer htl-ui-table__footer--multi-text htl-ui-table__footer--sortable">
				<tr class="htl-ui-table__row htl-ui-table__row--footer htl-ui-table__row--multi-text">
					<td colspan="3" class="htl-ui-table__cell htl-ui-table__cell--footer htl-ui-table__cell--add-row">
						<button type="button" class="htl-ui-button htl-ui-button--add-row"><?php echo esc_html( $field[ 'button_label' ] ); ?></button>
					</td>
				</tr>
			</tfoot>

		</table>

		<?php if ( ! empty( $field[ 'description' ] ) ) : ?>
			<div class="htl-ui-setting__description htl-ui-setting__description--multi-text"><?php echo wp_kses_post( $field[ 'description' ] ); ?></div>
		<?php endif; ?>
	</div>
</div>

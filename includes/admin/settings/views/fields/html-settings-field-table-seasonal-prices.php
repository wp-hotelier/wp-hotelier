<?php
/**
 * Field "Table Seasonal Prices"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rules = htl_get_option( 'seasonal_prices_schema', array() );
?>



<table id="hotelier-seasonal-schema-table" class="widefat">
	<tbody>

	<?php if ( ! empty( $rules ) ) : ?>

		<?php foreach( $rules as $key => $rule ) : ?>
			<?php $every_year = isset( $rule[ 'every_year' ] ) ? 1 : 0; ?>

			<tr class="rule-row" data-key="<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>">
				<td class="season-dates">
					<label>
						<?php esc_html_e( 'From', 'wp-hotelier' ); ?>
						<input class="date-from" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][from]" value="<?php echo esc_attr( $rule['from'] ) ?>">
					</label>
					<label>
						<?php esc_html_e( 'To', 'wp-hotelier' ); ?>
						<input class="date-to" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][to]" value="<?php echo esc_attr( $rule['to'] ) ?>">
					</label>
					<label class="date-every-year-label">
						<?php esc_html_e( 'Every year?', 'wp-hotelier' ); ?>
						<input class="date-every-year" value="1" type="checkbox" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][every_year]" <?php echo checked( $every_year, 1, false ); ?>>
					</label>
					<input class="rule-index" type="hidden" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][index]" value="<?php echo esc_attr( HTL_Formatting_Helper::sanitize_key( $key ) ); ?>">
				</td>
				<td>
					<button type="button" class="remove-rule button"><?php esc_html_e( 'Remove', 'wp-hotelier' ); ?></button>
				</td>
				<td class="sort-rules"><i class="htl-icon htl-bars"></i></td>
			</tr>
		<?php endforeach; ?>

	<?php else : ?>

		<tr class="rule-row" data-key="1">
			<td class="season-dates">
				<label>
					<?php esc_html_e( 'From', 'wp-hotelier' ); ?>
					<input class="date-from" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][1][from]">
				</label>
				<label>
					<?php esc_html_e( 'To', 'wp-hotelier' ); ?>
					<input class="date-to" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][1][to]">
				</label>
				<label class="date-every-year-label">
					<?php esc_html_e( 'Every year?', 'wp-hotelier' ); ?>
					<input class="date-every-year" value="1" type="checkbox" name="hotelier_settings[seasonal_prices_schema][1][every_year]">
				</label>
				<input class="rule-index" type="hidden" name="hotelier_settings[seasonal_prices_schema][1][index]" value="1">
			</td>
			<td>
				<button type="button" class="remove-rule button"><?php esc_html_e( 'Remove', 'wp-hotelier' ); ?></button>
			</td>
			<td class="sort-rules"><i class="htl-icon htl-bars"></i></td>
		</tr>

	<?php endif; ?>

	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<button type="button" class="add-rule button button-primary"><?php esc_html_e( 'Add new rule', 'wp-hotelier' ); ?></button>
			</td>
		</tr>
	</tfoot>
</table>

<span class="description seasonal-prices-table-description"><?php echo esc_html( $args[ 'desc' ] ); ?></span>

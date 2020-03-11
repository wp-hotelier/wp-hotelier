<?php
/**
 * Field "Table Seasonal Prices"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rules = htl_get_option( 'seasonal_prices_schema', array() );
?>

<div class="htl-ui-setting htl-ui-setting--seasonal-prices-table htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<table id="hotelier-seasonal-schema-table" class="htl-ui-table htl-ui-table--sortable htl-ui-table--seasonal-prices" data-type="seasonal-prices">
		<tbody class="htl-ui-table__body htl-ui-table__body--sortable">

			<?php if ( ! empty( $rules ) ) : ?>

				<?php foreach( $rules as $key => $rule ) : ?>
					<?php $every_year = isset( $rule[ 'every_year' ] ) ? 1 : 0; ?>

					<tr class="htl-ui-table__row htl-ui-table__row--body htl-ui-table__row--sortable" data-key="<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>">
						<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--seasonal-prices-dates">
							<label class="htl-ui-label">
								<?php esc_html_e( 'From', 'wp-hotelier' ); ?>
								<span class="htl-ui-datepicker-wrapper">
									<input class="htl-ui-input htl-ui-input--datepicker htl-ui-input--start-date" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][from]" value="<?php echo esc_attr( $rule['from'] ) ?>" autocomplete="off">
								</span>
							</label>
							<label class="htl-ui-label">
								<?php esc_html_e( 'To', 'wp-hotelier' ); ?>
								<span class="htl-ui-datepicker-wrapper">
									<input class="htl-ui-input htl-ui-input--datepicker htl-ui-input--end-date" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][to]" value="<?php echo esc_attr( $rule['to'] ) ?>" autocomplete="off">
								</span>
							</label>

							<span class="htl-ui-text-icon htl-ui-text-icon--right htl-ui-text-icon--show-advanced" data-hide-text="<?php esc_html_e( 'Hide advanced settings', 'wp-hotelier' ); ?>" data-show-text="<?php esc_html_e( 'Show advanced settings', 'wp-hotelier' ); ?>"><?php esc_html_e( 'Show advanced settings', 'wp-hotelier' ); ?></span>

							<div class="htl-ui-advanced-settings-wrapper">
								<label class="htl-ui-label">
									<?php esc_html_e( 'Every year?', 'wp-hotelier' ); ?>
									<input class="htl-ui-input htl-ui-input--checkbox" value="1" type="checkbox" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][every_year]" <?php echo checked( $every_year, 1, false ); ?>>
								</label>

								<label class="htl-ui-label htl-ui-label--season-name">
									<?php esc_html_e( 'Season name', 'wp-hotelier' ); ?>
									<input class="htl-ui-input htl-ui-input--text" value="<?php echo esc_attr( isset( $rule['season_name'] ) ? $rule['season_name'] : '' ); ?>" type="text" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][season_name]">
								</label>
							</div>

							<input class="htl-ui-input htl-ui-input--hidden htl-ui-input--row_index" type="hidden" name="hotelier_settings[seasonal_prices_schema][<?php echo HTL_Formatting_Helper::sanitize_key( $key ); ?>][index]" value="<?php echo esc_attr( HTL_Formatting_Helper::sanitize_key( $key ) ); ?>">
						</td>
						<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--remove-row">
							<button type="button" class="htl-ui-button htl-ui-button--remove-row"><?php esc_html_e( 'Remove', 'wp-hotelier' ); ?></button>
						</td>
						<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--sort-row">
							<?php esc_html_e( 'Sort', 'wp-hotelier' ); ?>
						</td>
					</tr>
				<?php endforeach; ?>

			<?php else : ?>

				<tr class="htl-ui-table__row htl-ui-table__row--body htl-ui-table__row--sortable" data-key="1">
					<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--seasonal-prices-dates">
						<label class="htl-ui-label">
							<?php esc_html_e( 'From', 'wp-hotelier' ); ?>
							<span class="htl-ui-datepicker-wrapper">
								<input class="htl-ui-input htl-ui-input--datepicker htl-ui-input--start-date" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][1][from]" autocomplete="off">
							</span>
						</label>
						<label class="htl-ui-label">
							<?php esc_html_e( 'To', 'wp-hotelier' ); ?>
							<span class="htl-ui-datepicker-wrapper">
								<input class="htl-ui-input htl-ui-input--datepicker htl-ui-input--end-date" type="text" placeholder="YYYY-MM-DD" name="hotelier_settings[seasonal_prices_schema][1][to]" autocomplete="off">
							</span>
						</label>

						<span class="htl-ui-text-icon htl-ui-text-icon--right htl-ui-text-icon--show-advanced" data-hide-text="<?php esc_html_e( 'Hide advanced settings', 'wp-hotelier' ); ?>" data-show-text="<?php esc_html_e( 'Show advanced settings', 'wp-hotelier' ); ?>"><?php esc_html_e( 'Show advanced settings', 'wp-hotelier' ); ?></span>

						<div class="htl-ui-advanced-settings-wrapper">
							<label class="htl-ui-label">
								<?php esc_html_e( 'Every year?', 'wp-hotelier' ); ?>
								<input class="htl-ui-input htl-ui-input--checkbox" value="1" type="checkbox" name="hotelier_settings[seasonal_prices_schema][1][every_year]">
							</label>
						</div>

						<input class="htl-ui-input htl-ui-input--hidden htl-ui-input--row_index" type="hidden" name="hotelier_settings[seasonal_prices_schema][1][index]">
					</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--remove-row">
						<button type="button" class="htl-ui-button htl-ui-button--remove-row"><?php esc_html_e( 'Remove', 'wp-hotelier' ); ?></button>
					</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--sort-row">
						<?php esc_html_e( 'Sort', 'wp-hotelier' ); ?>
					</td>
				</tr>

			<?php endif; ?>

		</tbody>

		<tfoot class="htl-ui-table__footer htl-ui-table__footer--sortable">
			<tr class="htl-ui-table__row htl-ui-table__row--footer">
				<td colspan="3" class="htl-ui-table__cell htl-ui-table__cell--footer htl-ui-table__cell--add-row">
					<button type="button" class="htl-ui-button htl-ui-button--add-row"><?php esc_html_e( 'Add new rule', 'wp-hotelier' ); ?></button>
				</td>
			</tr>
		</tfoot>
	</table>

	<div class="htl-ui-setting__description htl-ui-setting__description--seasonal-prices-table"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></div>
</div>

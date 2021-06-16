<?php
/**
 * Admin View: New reservation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$today    = new Datetime();
$today    = $today->format( 'Y-m-d' );
$tomorrow = new Datetime();
$tomorrow = $tomorrow->modify( '+1 day' );
$tomorrow = $tomorrow->format( 'Y-m-d' );
?>

<div class="wrap htl-ui-scope hotelier-settings hotelier-settings--new-reservation">
	<?php
	$settings   = new HTL_Admin_Settings();
	$tabs       = $settings->get_settings_tabs();
	$active_tab = 'new-reservation';

	include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-navigation.php'; ?>

	<div class="hotelier-settings-wrapper">
		<?php include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-header.php'; ?>

		<div class="hotelier-settings-panel">

		<?php
		// Check if we have at least one room
		$args = array(
			'post_type'           => 'room',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => -1,
			'orderby'             => 'title',
			'order'               => 'ASC',
			'meta_query'          => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'compare' => '>',
				),
			),
		);

		do_action( 'hotelier_admin_add_new_reservation_before_rooms_query' );

		$rooms = new WP_Query( apply_filters( 'hotelier_admin_add_new_reservation_rooms_query_args', $args ) );

		if ( $rooms->have_posts() ) : ?>

			<h3 class="htl-ui-heading htl-ui-heading--section-header"><?php esc_html_e( 'Add new reservation', 'wp-hotelier' ); ?></h3>

			<form method="post" class="add-new-reservation-form">
				<table class="hotelier-settings-table-form">
					<tbody>
						<tr>
							<th scope="row"><?php esc_html_e( 'Select room:', 'wp-hotelier' ); ?></th>
							<td>
								<div class="htl-ui-setting">
									<table class="htl-ui-table htl-ui-table--sortable htl-ui-table--add-new-room-to-reservation">
										<thead class="htl-ui-table__head">
											<tr class="htl-ui-table__row htl-ui-table__row--head">
												<th class="htl-ui-table__cell htl-ui-table__cell--head">
													<?php esc_html_e( 'Room name', 'wp-hotelier' ); ?>
												</th>
												<th colspan="2" class="htl-ui-table__cell htl-ui-table__cell--head">
													<?php esc_html_e( 'Quantity', 'wp-hotelier' ); ?>
												</th>
											</tr>
										</thead>
										<tbody class="htl-ui-table__body htl-ui-table__body--sortable">
											<tr class="htl-ui-table__row htl-ui-table__row--body htl-ui-table__row--sortable" data-key="1">
												<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--room-select">
													<?php echo htl_get_list_of_rooms_html( 'room[1]' ); ?>
													<div class="room-guests-wrapper">
														<label class="htl-ui-label"><?php esc_html_e( 'Adults', 'wp-hotelier' ); ?>
															<input class="htl-ui-input htl-ui-input--small htl-ui-input--number" type="number" name="room_adults[1]" value="2" min="0">
														</label>
														<label class="htl-ui-label"><?php esc_html_e( 'Children', 'wp-hotelier' ); ?>
															<input class="htl-ui-input htl-ui-input--small htl-ui-input--number" type="number" name="room_children[1]" value="0" min="0">
														</label>
													</div>
													<?php do_action( 'hotelier_admin_add_new_reservation_after_rooms_select' ); ?>
												</td>
												<td class="htl-ui-table__cell htl-ui-table__cell--body">
													<input class="htl-ui-input htl-ui-input--small htl-ui-input--number" type="number" name="room_qty[1]" value="1" min="0">
												</td>
												<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--remove-room">
													<button type="button" class="htl-ui-button htl-ui-button--remove-row"><?php esc_html_e( 'Remove room', 'wp-hotelier' ); ?></button>
												</td>
											</tr>
										</tbody>

										<tfoot class="htl-ui-table__footer htl-ui-table__footer--sortable">
											<tr class="htl-ui-table__row htl-ui-table__row--footer">
												<td colspan="3" class="htl-ui-table__cell htl-ui-table__cell--footer">
													<button type="button" class="htl-ui-button htl-ui-button--add-row"><?php esc_html_e( 'Add another room', 'wp-hotelier' ); ?></button>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Check-in:', 'wp-hotelier' ); ?></th>
							<td>
								<div class="htl-ui-setting">
									<span class="htl-ui-datepicker-wrapper">
										<input class="htl-ui-input htl-ui-input--datepicker htl-ui-input--start-date" type="text" placeholder="YYYY-MM-DD" name="from" value="<?php echo esc_attr( $today ); ?>" autocomplete="off">
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Check-out:', 'wp-hotelier' ); ?></th>
							<td>
								<div class="htl-ui-setting">
									<span class="htl-ui-datepicker-wrapper">
										<input class="htl-ui-input htl-ui-input--datepicker htl-ui-input--end-date" type="text" placeholder="YYYY-MM-DD" name="to" value="<?php echo esc_attr( $tomorrow ); ?>" autocomplete="off">
									</span>
								</div>
							</td>
						</tr>
						<?php if ( htl_coupons_enabled() ) :
							$all_coupons = htl_get_all_coupons();

							$coupon_values = array(
								0 => '--- ' . esc_html__( "Don't apply a coupon", "wp-hotelier" )
							);

							if ( is_array( $all_coupons ) ) {
								foreach ( $all_coupons as $coupon_id => $coupon_value ) {
									if ( isset( $coupon_value['code'] ) ) {
										$coupon_code = $coupon_value['code'];

										if ( isset( $coupon_value['title'] ) ) {
											$coupon_code .= ' (' . $coupon_value['title'] . ')';
										}

										$coupon_values[$coupon_id] = $coupon_code;
									}
								}
							}
							?>
							<tr>
								<th scope="row"><?php esc_html_e( 'Apply coupon:', 'wp-hotelier' ); ?></th>
								<td>
									<div class="htl-ui-setting">
										<select class="htl-ui-input htl-ui-input--select" name="coupon_id">
											<?php foreach ( $coupon_values as $coupon_id => $coupon_value ) : ?>
												<option value="<?php echo esc_attr( $coupon_id ); ?>"><?php echo esc_html( $coupon_value ) ?></option>
											<?php endforeach; ?>
										</select>

										<div class="htl-ui-setting__description"><?php esc_html_e( 'Select the coupon you want to apply', 'wp-hotelier' ); ?></div>
									</div>
								</td>
							</tr>
						<?php endif; ?>
						<tr>
							<th scope="row"><?php esc_html_e( 'Force booking:', 'wp-hotelier' ); ?></th>
							<td>
								<div class="htl-ui-setting">
									<div class="htl-ui-toggle">
										<input type="checkbox" class="htl-ui-input htl-ui-input--checkbox htl-ui-toggle__input" name="force_booking" id="force_booking" value="1" />

										<label class="htl-ui-label htl-ui-label--checkbox htl-ui-toggle__label" for="force_booking">
											<span class="htl-ui-toggle__handle"></span>
										</label>

										<p class="htl-ui-setting__description htl-ui-setting__description--checkbox "><?php esc_html_e( 'Enable this option to ignore booking rules (disabled dates, minimum nights, coupon rules, etc)', 'wp-hotelier' ); ?></p>
									</div>
								</div>
							</td>
						</tr>
						<?php foreach ( HTL_Meta_Box_Reservation_Data::get_guest_details_fields() as $key => $field ) :
							$type     = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';
							$required = isset( $field[ 'required' ] ) ? '<span class="htl-ui-required-symbol"> * </span>' : '';
							?>
							<tr>
								<th scope="row"><?php echo esc_html( $field[ 'label' ] ) . $required; ?></th>
								<td>
									<div class="htl-ui-setting">
										<?php if ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'select' ) : ?>
											<select class="htl-ui-input htl-ui-input--select" name="<?php echo esc_attr( $key ); ?>">
												<?php foreach ( $field[ 'options' ] as $field_options_key => $field_options_value ) : ?>
													<option value="<?php echo esc_attr( $field_options_key ); ?>"><?php echo esc_html( $field_options_value ); ?></option>
												<?php endforeach; ?>
											</select>
										<?php else : ?>
											<input class="htl-ui-input htl-ui-input--text" type="text" name="<?php echo esc_attr( $key ); ?>">
										<?php endif; ?>

										<?php if ( isset( $field[ 'description' ] ) ) : ?>
											<div class="htl-ui-setting__description"><?php echo esc_html( $field[ 'description' ] ); ?></div>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>

				<input type="submit" name="hotelier_admin_add_new_reservation" class="button htl-ui-button htl-ui-button--add-new-reservation" value="<?php esc_attr_e( 'Save reservation', 'wp-hotelier' ); ?>">

				<?php wp_nonce_field( 'hotelier_admin_process_new_reservation' ); ?>
			</form>

		<?php else : ?>

			<?php
				$notice_wrapper_class = array(
				'htl-ui-setting',
				'htl-ui-setting--section-description'
			);

			$notice_class = array(
				'htl-ui-setting--section-description__text',
				'wide'
			);

			$notice_text = __( 'In order to create a reservation, you need to have at least one room. click on the button below to create your first room.', 'wp-hotelier' );

			htl_ui_print_notice( $notice_text, 'error', $notice_wrapper_class, $notice_class );
			?>

			<a class="htl-ui-button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=room' ) ); ?>"><?php esc_html_e( 'Create new room', 'wp-hotelier' ); ?></a>

		<?php endif; ?>

		<?php do_action( 'hotelier_admin_add_new_reservation_after_rooms_query' ); ?>
		</div>

		<?php include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-pro-section.php'; ?>

	</div>
</div>

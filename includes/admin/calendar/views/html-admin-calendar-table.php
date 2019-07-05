<?php
/**
 * Calendar table
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$today            = new DateTime();
$week_limits      = htl_get_week_limits( $marker->format( 'Y-m-d' ), $weeks );
$start_range      = $week_limits[ 0 ];
$end_range        = $week_limits[ 1 ];
$begin            = new DateTime( $start_range );
$end              = new DateTime( $end_range );
$end              = $end->modify( '+1 day' );
$interval         = new DateInterval( 'P1D' );
$daterange        = new DatePeriod( $begin, $interval, $end );
$all_reservations = htl_get_all_reservations( $start_range, $end_range );
?>

<table class="htl-ui-table htl-ui-table--booking-calendar booking-calendar-table">
	<thead class="booking-calendar-table__header">
		<tr class="booking-calendar-table__row booking-calendar-table__row--header">
			<?php foreach ( $daterange as $date ) : ?>
				<th colspan="2" class="booking-calendar-table__cell booking-calendar-table__cell--header <?php echo $date->format( 'Y-m-d' ) == $today->format( 'Y-m-d' ) ? 'booking-calendar-table__cell--today' : ''; ?>">
					<span class="booking-calendar-table__date booking-calendar-table__date--day"><?php echo date_i18n( 'l', $date->getTimestamp() ) ?></span>
					<span class="booking-calendar-table__date booking-calendar-table__date--number"><?php echo date_i18n( 'd', $date->getTimestamp() ) ?></span>
					<span class="booking-calendar-table__date booking-calendar-table__date--month"><?php echo date_i18n( 'M', $date->getTimestamp() ) ?></span>
				</th>
			<?php endforeach; ?>
		</tr>
	</thead>

	<tbody class="booking-calendar-table__body">
		<?php foreach ( $all_reservations as $room_id => $reservations ) : ?>
			<tr class="booking-calendar-table__row booking-calendar-table__row--body booking-calendar-table__row--room-name">
				<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--room-link" colspan="<?php echo absint( $weeks * 14 ); ?>"><a class="booking-calendar-table__room-link" href="<?php echo esc_url( get_edit_post_link( $room_id ) ); ?>"><?php echo get_the_title( $room_id ); ?></a></td>
			</tr>
			<tr class="booking-calendar-table__row booking-calendar-table__row--body booking-calendar-table__row--empty">
				<td class="booking-calendar-table__cell booking-calendar-table__cell--body" colspan="<?php echo absint( $weeks * 14 ); ?>">
					<table class="booking-calendar-table__table booking-calendar-table__table--week">
						<tbody class="booking-calendar-table__body booking-calendar-table__body--week">
							<tr class="booking-calendar-table__row booking-calendar-table__row--week booking-calendar-table__row--empty" data-room="<?php echo absint( $room_id ); ?>">
								<?php foreach ( $daterange as $date ) : ?>
									<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--empty booking-calendar-table__cell--day booking-calendar-table__cell--first"><span class="booking-calendar-table__date-helper"><?php echo date_i18n( 'd', $date->getTimestamp() ) ?></span></td>
									<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--empty booking-calendar-table__cell--day">&nbsp;</td>
								<?php endforeach; ?>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr class="booking-calendar-table__row booking-calendar-table__row--body">
				<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--week" colspan="<?php echo absint( $weeks * 14 ); ?>">
					<table class="booking-calendar-table__table booking-calendar-table__table--week">
						<tbody class="booking-calendar-table__body booking-calendar-table__body--week">
							<?php if ( $reservations ) :
								$rows      = array();
								$empty_row = array();

								for ( $i = 0; $i < ( 8 * $weeks ); $i++ ) {
									$empty_row[] = false;
								}

								$rows[] = $empty_row;

								foreach ( $reservations as $reservation ) { ?>
									<?php
									$checkin      = new DateTime( $reservation[ 'checkin' ]);
									$checkout     = new DateTime( $reservation[ 'checkout' ]);
									$initial_date = clone( $begin );
									$initial_date = $initial_date->modify( '-1 day' );
									$duration     = $checkin->diff( $checkout );
									$duration     = $duration->days;
									$offset       = $initial_date->diff( $checkin );
									$offset       = $offset->days;

									if ( $checkin < $initial_date ) {
										$duration -= $offset;
										$offset = 0;
									}

									foreach ( $rows as $row_index => $row ) {
										$has_space = true;

										$count = min( $duration, 8 * $weeks - $offset );

										for ( $i = 0; $i < $count ; $i++ ) {
											if ( $rows[ $row_index ][ $offset + $i ] != false ) {
												$has_space = false;
												break;
											}

											if ( $i == $count - 1 ) {
												break 2;
											}
										}
									}

									if ( $has_space ) {
										for ( $i = $offset;  $i < min( $offset + $duration, 8 * $weeks ); $i++ ) {
											if ( $i == $offset ) {
												$rows[ $row_index ][ $i ][ 'size' ] = 2 * min( $duration, ( 8 * $weeks - $offset ) );
												$rows[ $row_index ][ $i ][ 'id' ] = $reservation[ 'reservation_id' ];
												$rows[ $row_index ][ $i ][ 'status' ] = $reservation[ 'status' ];
											} else {
												$rows[ $row_index ][ $i ] = true;
											}
										}
									} else {
										$rows[] = $empty_row;

										for ( $i = $offset;  $i < min( $offset + $duration, 8 * $weeks ); $i++ ) {
											if ( $i == $offset ) {
												$rows[ count( $rows ) - 1 ][ $i ][ 'size' ] = 2 * min( $duration, ( 8 * $weeks - $offset ) );
												$rows[ count( $rows ) - 1 ][ $i ][ 'id' ] = $reservation[ 'reservation_id' ];
												$rows[ count( $rows ) - 1 ][ $i ][ 'status' ] = $reservation[ 'status' ];
											} else {
												$rows[ count( $rows ) - 1 ][ $i ] = true;
											}
										}
									}
								} ?>

								<?php foreach ($rows as $row) : ?>
									<tr class="booking-calendar-table__row booking-calendar-table__row--week booking-calendar-table__row--bookings" data-room="<?php echo absint( $room_id ); ?>">

										<?php foreach ( $row as $index => $cell ) : ?>
											<?php if ( $index <= $weeks * 7 ) : ?>
												<?php if ( is_array( $cell ) ) : ?>
													<?php
													$colspan = $index == 0 || $index == $weeks * 7 ? $cell[ 'size' ] - 1 : $cell[ 'size' ];

													$class = '';

													if ( $index == 0 ) {
														$class .= ' booking-calendar-table__day-booked--past-week';

														if ( $cell[ 'size' ] < 3 ) {
															$class .= ' booking-calendar-table__day-booked--hidden';
														}
													}

													if ( $index + $cell[ 'size' ] / 2 > $weeks * 7  ) {
														$colspan = 2 * ( $weeks * 7 - $index ) + 1;
														$class .= ' booking-calendar-table__day-booked--next-week';

														if ( $cell[ 'size' ] < 3 ) {
															$class .= ' booking-calendar-table__day-booked--hidden';
														}
													}

													if ( $index == 0 && $cell[ 'size' ] > 2 * $weeks * 7 ) {
														$colspan = 2 * $weeks * 7;
													}

													if ( in_array( $cell[ 'status' ], $default_disabled_statuses ) ) {
														$class .= ' not-active';
													}
													?>

													<td colspan="<?php echo absint( $colspan ); ?>" data-status="<?php echo esc_attr( $cell[ 'status' ] ); ?>" data-room="<?php echo esc_attr( $room_id ); ?>" class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--day booking-calendar-table__day-booked <?php echo esc_attr( $class ); ?>">

														<a href="<?php echo esc_url( get_edit_post_link( $cell[ 'id' ] ) ); ?>" class="booking-calendar-table__reservation-link"><span class="booking-calendar-table__reservation-label">#<?php echo esc_html( $cell[ 'id' ] ); ?></span></a>

														<?php include HTL_PLUGIN_DIR . 'includes/admin/calendar/views/html-admin-calendar-booking-card.php'; ?>
													</td>
												<?php elseif ( $cell == false ) : ?>
													<?php
													$class = '';

													if ( $index == 0 ) {
														$class = ' booking-calendar-table__cell--first';
													}
													?>

													<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--day <?php echo esc_attr( $class ); ?>"></td>

													<?php if ( $index != 0 && $index != $weeks * 7 ) : ?>
														<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--day booking-calendar-table__cell--first"></td>
													<?php endif; ?>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							<?php else : ?>
								<tr class="booking-calendar-table__row booking-calendar-table__row--week booking-calendar-table__row--bg" data-room="<?php echo absint( $room_id ); ?>">
									<?php foreach ( $daterange as $date ) : ?>
										<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--bg booking-calendar-table__cell--day booking-calendar-table__cell--first">&nbsp;</td>
										<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--bg booking-calendar-table__cell--day">&nbsp;</td>
									<?php endforeach; ?>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr class="booking-calendar-table__row booking-calendar-table__row--body booking-calendar-table__row--empty">
				<td class="booking-calendar-table__cell booking-calendar-table__cell--body" colspan="<?php echo absint( $weeks * 14 ); ?>">
					<table class="booking-calendar-table__table booking-calendar-table__table--week">
						<tbody class="booking-calendar-table__body booking-calendar-table__body--week">
							<tr class="booking-calendar-table__row booking-calendar-table__row--week booking-calendar-table__row--empty" data-room="<?php echo absint( $room_id ); ?>">
								<?php foreach ( $daterange as $date ) : ?>
									<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--empty booking-calendar-table__cell--day booking-calendar-table__cell--first">&nbsp;</td>
									<td class="booking-calendar-table__cell booking-calendar-table__cell--body booking-calendar-table__cell--empty booking-calendar-table__cell--day">&nbsp;</td>
								<?php endforeach; ?>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

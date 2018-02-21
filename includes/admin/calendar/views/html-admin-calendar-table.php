<?php
/**
 * Admin View: Calendar table
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

<table class="bc__table bc__table--main">
	<thead class="bc__header">
		<tr class="bc__row bc__row--header">
			<th class="bc__cell bc__cell--header bc__cell--room-name">
				&nbsp;
			</th>
			<?php foreach ( $daterange as $date ) : ?>
				<th colspan="2" class="bc__cell bc__cell--header <?php echo $date->format( 'Y-m-d' ) == $today->format( 'Y-m-d' ) ? 'bc__cell--today' : ''; ?>">
					<span class="bc__date bc__date--month"><?php echo date_i18n( 'M', $date->getTimestamp() ) ?></span>
					<span class="bc__date bc__date--number"><?php echo date_i18n( 'd', $date->getTimestamp() ) ?></span>
					<span class="bc__date bc__date--day"><?php echo date_i18n( 'D', $date->getTimestamp() ) ?></span>
				</th>
			<?php endforeach; ?>
		</tr>
	</thead>

	<tbody class="bc__content">
		<?php foreach ( $all_reservations as $room_id => $reservations ) : ?>
			<tr class="bc__row bc__row--content">
				<td class="bc__cell bc__cell--data bc__cell--room-name">
					<a class="bc__room-link" href="<?php echo esc_url( get_edit_post_link( $room_id ) ); ?>"><?php echo get_the_title( $room_id ); ?></a>
				</td>
				<td class="bc__cell bc__cell--data bc__cell--week" colspan="<?php echo absint( $weeks * 14 ); ?>">
					<table class="bc__table bc__table--week">
						<tbody class="bc__content bc__content--week">
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

								<?php
								foreach ($rows as $row) : ?>
									<tr class="bc__row bc__row--week bc__row--bookings" data-room="<?php echo absint( $room_id ); ?>">
										<?php

										foreach ( $row as $index => $cell ) {
											if ( $index <= $weeks * 7 ) {
												if ( is_array( $cell ) ) {
													$colspan = $index == 0 || $index == $weeks * 7 ? $cell[ 'size' ] - 1 : $cell[ 'size' ];

													$class = '';

													if ( $index == 0 ) {
														$class .= ' bc__day-booked--past-week';

														if ( $cell[ 'size' ] < 3 ) {
															$class .= ' bc__day-booked--hidden';
														}
													}

													if ( $index + $cell[ 'size' ] / 2 > $weeks * 7  ) {
														$colspan = 2 * ( $weeks * 7 - $index ) + 1;
														$class .= ' bc__day-booked--next-week';

														if ( $cell[ 'size' ] < 3 ) {
															$class .= ' bc__day-booked--hidden';
														}
													}

													if ( $index == 0 && $cell[ 'size' ] > 2 * $weeks * 7 ) {
														$colspan = 2 * $weeks * 7;
													}

													echo '<td colspan="' . absint( $colspan ) . '" data-status="' . esc_attr( $cell[ 'status' ] ) . '" data-room="' . esc_attr( $room_id ) . '" class="bc__cell bc__cell--data bc__cell--day bc__day-booked' . esc_attr( $class ) . '">
															<a href="' . esc_url( get_edit_post_link( $cell[ 'id' ] ) ) . '" class="bc__reservation-link hastip" title="' . esc_attr( get_the_title( $cell[ 'id' ] ) ) . '"><span class="bc__reservation-label">' . get_the_title( $cell[ 'id' ] ) . '</span></a>
														</td>';
												} else if ( $cell == false ) {
													$class = '';

													if ( $index == 0 ) {
														$class = ' bc__cell--first';
													}

													echo '<td class="bc__cell bc__cell--data bc__cell--day' . esc_attr( $class ) . '"></td>';
													if ( $index != 0 && $index != $weeks * 7 ) {
														echo '<td class="bc__cell bc__cell--data bc__cell--day bc__cell--first"></td>';
													}
												}
											}
										} ?>
									</tr>
								<?php endforeach; ?>
							<?php else : ?>
								<tr class="bc__row bc__row--week bc__row--bg" data-room="<?php echo absint( $room_id ); ?>">
									<?php foreach ( $daterange as $date ) : ?>
										<td class="bc__cell bc__cell--data bc__cell--bg bc__cell--day bc__cell--first">&nbsp;</td>
										<td class="bc__cell bc__cell--data bc__cell--bg bc__cell--day">&nbsp;</td>
									<?php endforeach; ?>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

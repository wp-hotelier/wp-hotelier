<?php
/**
 * Hotelier Extras Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning extras.
 *
 * @param  mixed $the_extra Post object or post ID of the extra.
 * @return HTL_Extra
 */
function htl_get_extra( $the_extra = false ) {
	return new HTL_Extra( $the_extra );
}

/**
 * Get all available extras IDs.
 */
function htl_get_all_extras_ids() {
	$extras_ids = get_transient( 'hotelier_extras_ids' );

	// Valid cache found
	if ( false !== $extras_ids ) {
		return $extras_ids;
	}

	$extras = get_posts( array(
		'post_type'           => 'extra',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'fields'              => 'ids',
	) );

	set_transient( 'hotelier_extras_ids', $extras, DAY_IN_SECONDS * 30 );

	return $extras;
}

/**
 * Get room extras.
 */
function htl_get_room_extras( $line_price, $extras, $values, $room, $checkin, $checkout ) {
	$extras     = array();
	$extras_ids = htl_get_room_extras_ids( $room );

	foreach ( $extras_ids as $extra_id ) {
		// Get extra
		$extra = htl_get_extra( $extra_id );

		if ( $extra->is_optional() ) {
			continue;
		}

		$extras[$extra_id] = htl_calculate_single_extra( $extra, 1, $line_price, $values, $room, $checkin, $checkout );
	}

	return $extras;
}

/**
 * Get room extras (IDs).
 */
function htl_get_room_extras_ids( $room ) {
	$room_extras_ids = array();
	$all_extras      = htl_get_all_extras_ids();

	if ( is_array( $all_extras ) && count( $all_extras ) > 0 ) {
		foreach ( $all_extras as $extra_id ) {
			$can_apply = htl_can_apply_extra( $extra_id, $room->id );

			if ( isset( $can_apply['can_apply'] ) && $can_apply['can_apply'] ) {
				$room_extras_ids[] = $extra_id;
			}
		}
	}

	return $room_extras_ids;
}

/**
 * Check if we can apply this extra.
 *
 * @param  int $extra_id Extra ID.
 * @return array
 */
function htl_can_apply_extra( $extra_id, $room_id, $force = false ) {
	$can_apply = true;
	$reason    = false;

	$extra = htl_get_extra( $extra_id );

	// Check if extra exists
	if ( ! $extra->exists() ) {
		$reason    = esc_html__( 'This extra does not exists.', 'wp-hotelier' );
		$can_apply = false;
	}

	if ( $force ) {
		return array( 'can_apply' => true, 'reason' => '' );
	}

	// Check if extra is enabled
	if ( ! $extra->is_enabled() ) {
		$reason    = esc_html__( 'This extra is not enabled.', 'wp-hotelier' );
		$can_apply = false;
	}

	$data = apply_filters(
		'hotelier_can_apply_extra',
		array(
			'can_apply' => $can_apply,
			'reason'    => $reason
		),
		$extra_id,
		$room_id,
	);

	return $data;
}

/**
 * Calculate single extra.
 */
function htl_calculate_single_extra( $extra, $qty, $line_price, $values, $room, $checkin, $checkout ) {
	$extra_to_add = 0;

	if ( $extra->get_type() === 'per_room' ) {
		// Extra per room
		if ( $extra->get_amount_type() === 'fixed' ) {
			// Fixed cost
			$extra_to_add = $extra->get_amount();

			// Calculate cost per night if enabled
			if ( $extra->calculate_per_night() ) {
				$checkin   = new DateTime( $checkin );
				$checkout  = $checkout ? new DateTime( $checkout ) : $checkin;
				$interval  = new DateInterval( 'P1D' );
				$daterange = new DatePeriod( $checkin, $interval ,$checkout );

				$extra_per_day_to_add = 0;

				foreach( $daterange as $date ) {
					$extra_per_day_to_add += $extra_to_add;
				}

				// Check max cost if any
				$max_cost = $extra->get_max_cost();

				if ( $max_cost > 0 && $extra_per_day_to_add > $max_cost ) {
					$extra_per_day_to_add = $max_cost;
				}

				$extra_to_add = $extra_per_day_to_add;
			}
		} else {
			// Percentage cost
			$percentage_to_add = $extra->get_amount();
			$extra_to_add      = ( $line_price * $percentage_to_add ) / 100;
		}
	} else {
		// Extra per person
		if ( isset( $values['guests'] ) && isset( $values['guests'][0] ) ) {
			// We can use just the first key
			$guests = $values['guests'][0];

			if ( is_array( $guests ) ) {
				$adults             = isset( $guests['adults'] ) ? absint( $guests['adults'] ) : 0;
				$children           = isset( $guests['children'] ) ? absint( $guests['children'] ) : 0;
				$allowed_guest_type = $extra->get_allowed_guest_type();
				$calculate_adults   = false;
				$calculate_children = false;

				if ( $allowed_guest_type === 'default' ) {
					$calculate_adults   = true;
					$calculate_children = true;
				} else if ( $allowed_guest_type === 'adults_only' ) {
					$calculate_adults = true;
				} else if ( $allowed_guest_type === 'children_only' ) {
					$calculate_children = true;
				}

				if ( $extra->get_amount_type() === 'fixed' ) {
					// Fixed cost
					$extra_amount_to_add = $extra->get_amount();

					// Calculate cost per night if enabled
					if ( $extra->calculate_per_night() ) {
						$checkin   = new DateTime( $checkin );
						$checkout  = $checkout ? new DateTime( $checkout ) : $checkin;
						$interval  = new DateInterval( 'P1D' );
						$daterange = new DatePeriod( $checkin, $interval ,$checkout );

						$extra_per_day_to_add = 0;

						foreach( $daterange as $date ) {
							if ( $calculate_adults ) {
								$extra_per_day_to_add += $extra_amount_to_add * $adults;
							}

							if ( $calculate_children ) {
								$extra_per_day_to_add += $extra_amount_to_add * $children;
							}
						}

						// Check max cost if any
						$max_cost = $extra->get_max_cost();

						if ( $max_cost > 0 && $extra_per_day_to_add > $max_cost ) {
							$extra_per_day_to_add = $max_cost;
						}

						$extra_to_add = $extra_per_day_to_add;
					} else {
						if ( $calculate_adults ) {
							$extra_to_add += $extra_amount_to_add * $adults;
						}

						if ( $calculate_children ) {
							$extra_to_add += $extra_amount_to_add * $children;
						}
					}
				} else {
					// Percentage cost
					$percentage_to_add   = $extra->get_amount();
					$extra_amount_to_add = ( $line_price * $percentage_to_add ) / 100;

					if ( $calculate_adults ) {
						$extra_to_add += $extra_amount_to_add * $adults;
					}

					if ( $calculate_children ) {
						$extra_to_add += $extra_amount_to_add * $children;
					}
				}
			}
		}
	}

	$extra_to_add = ceil( $extra_to_add );
	$extra_to_add = absint( apply_filters( 'hotelier_calulate_extra', $extra_to_add, $extra, $line_price, $values, $room ) );

	return array(
		'qty'   => $qty,
		'price' => $extra_to_add,
	);
}
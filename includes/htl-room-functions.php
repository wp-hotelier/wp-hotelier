<?php
/**
 * Hotelier Room Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning rooms.
 *
 * @param  mixed $the_room Post object or post ID of the room.
 * @return HTL_Room
 */
function htl_get_room( $the_room = false ) {
	return new HTL_Room( $the_room );
}

/**
 * Get the placeholder image URL for rooms etc
 *
 * @access public
 * @return string
 */
function htl_placeholder_img_src() {
	return apply_filters( 'hotelier_placeholder_img_src', HTL()->plugin_url() . '/assets/images/placeholder.png' );
}

/**
 * Get the placeholder image
 *
 * @access public
 * @return string
 */
function htl_placeholder_img( $size = 'room_thumbnail' ) {
	$dimensions = htl_get_image_size( $size );

	return apply_filters( 'hotelier_placeholder_img', '<img src="' . htl_placeholder_img_src() . '" alt="' . esc_attr__( 'Placeholder', 'wp-hotelier' ) . '" width="' . esc_attr( $dimensions[ 'width' ] ) . '" class="hotelier-placeholder wp-post-image" height="' . esc_attr( $dimensions[ 'height' ] ) . '" />', $size, $dimensions );
}

/**
 * Function that returns an array containing the IDs of the rooms that are in stock.
 *
 * @access public
 * @return array.
 */
function htl_get_room_ids() {

	// Load from cache
	$room_ids = get_transient( 'hotelier_room_ids' );

	// Valid cache found
	if ( false !== $room_ids ) {
		return $room_ids;
	}

	$rooms = get_posts( array(
		'post_type'           => 'room',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'fields'              => 'ids',
		'meta_query'          => array(
			array(
				'key'     => '_stock_rooms',
				'value'   => 0,
				'type'    => 'numeric',
				'compare' => '>',
			),
		),
	) );

	set_transient( 'hotelier_room_ids', $rooms, DAY_IN_SECONDS * 30 );

	return $rooms;
}

/**
 * Function that returns an array containing the IDs of the rooms that are reserved on the given dates.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @param array $args
 * @return array on success or false on failure.
 */
function htl_get_room_ids_unavailable( $checkin, $checkout, $args = array() ) {

	// Check if we want to show also the unavailable rooms
	if ( htl_get_option( 'room_unavailable_visibility', false ) ) {
		return array();
	}

	try {
		global $wpdb;

		if ( ! HTL_Formatting_Helper::is_valid_checkin_checkout( $checkin, $checkout ) ) {
			throw new Exception( esc_html__( 'Please check your dates.', 'wp-hotelier' ) );
		}

		// query already reserved rooms and check if there is enough stock
		$sql            = $wpdb->prepare( "SELECT room_id, count(room_id) AS count FROM {$wpdb->prefix}hotelier_rooms_bookings rb, {$wpdb->prefix}hotelier_bookings b WHERE rb.reservation_id = b.reservation_id AND (%s < b.checkout AND %s > b.checkin) GROUP by room_id", $checkin, $checkout );
		$reserved_rooms = $wpdb->get_results( $sql, ARRAY_A );

		$ids = array();

		if ( ! empty( $reserved_rooms ) ) {
			foreach ( $reserved_rooms as $id => $row ) {
				$_room = htl_get_room( $row[ 'room_id' ] );

				if ( $_room->exists() ) {
					if ( ! $_room->is_available( $checkin, $checkout ) ) {
						$ids[] = $row[ 'room_id' ];
					}
				}
			}
		}

		return apply_filters( 'hotelier_get_room_ids_unavailable', $ids );

	} catch ( Exception $e ) {
		if ( $e->getMessage() ) {
			htl_add_notice( $e->getMessage(), 'error' );
		}
		return false;
	}
}

/**
 * Function that returns an array containing the IDs of the rooms that are available
 * for bookings (htl_get_room_ids - htl_get_room_ids_unavailable). It returns an array
 * ready to use in the listing query.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @return array.
 */
function htl_get_available_room_ids( $checkin, $checkout ) {
	$all_rooms         = htl_get_room_ids(); // get all rooms
	$unavailable_rooms = htl_get_room_ids_unavailable( $checkin, $checkout ); // get unavailable rooms
	$filtered_ids      = array_diff( $all_rooms, $unavailable_rooms ); // subtract unavailable rooms

	// This will store the definitve (and filtered) array of room IDs
	$ids = array();


	// Pass all the IDs trough the is_available() method
	// This a final check and allows extensions to disable a room
	foreach ( $filtered_ids as $id ) {
		$_room = htl_get_room( $id );

		if ( $_room->exists() ) {
			if ( htl_get_option( 'room_unavailable_visibility', false ) || $_room->is_available( $checkin, $checkout ) ) {
				$ids[] = $id;
			}
		}
	}

	return apply_filters( 'hotelier_get_available_room_ids', $ids, $checkin, $checkout );
}

/**
 * Function that returns a WP Query object containing rooms that are related to a specific room.
 *
 * @access public
 * @param int $room_id
 * @param int $limit
 * @param string $order
 * @return array.
 */
function htl_get_related_rooms_query( $room_id, $limit, $order ) {
	$_room      = htl_get_room( $room_id );
	$categories = $_room->get_terms_related();

	// Return an empty array if there are not categories
	if ( sizeof( $categories ) === 0 ) {
		return array();
	}

	$args = apply_filters( 'hotelier_related_rooms_args', array(
		'post_type'            => 'room',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'posts_per_page'       => $limit,
		'orderby'              => $order,
		'post__not_in'         => array( $room_id ),
		'tax_query'            => array(
			array(
				'taxonomy' => 'room_cat',
				'field' => 'id',
				'terms' => $categories
			)
		)
	) );

	$rooms = new WP_Query( $args );

	return $rooms;
}

/**
 * Function that returns a WP Query object containing the rooms of the listing page.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @return array.
 */
function htl_get_listing_rooms_query( $checkin, $checkout, $room_id = false ) {
	// Get available rooms
	$room_ids = htl_get_available_room_ids( $checkin, $checkout );

	// Return an empty array if there are no rooms available
	if ( ! $room_ids ) {
		return array();
	}

	// Return early if only one room is available and it is the queried one
	if ( count( $room_ids ) === 1 && $room_ids[ 0 ] === $room_id ) {
		return array();
	}

	// Exclude room if we are querying a specific one
	if ( $room_id ) {
		$room_ids = array_diff( $room_ids, array( $room_id ) );
	}

	$query_args = array(
		'post_type'      => 'room',
		'post__in'       => $room_ids,
		'posts_per_page' => -1,
	);

	// Get sorting option
	$listing_sorting = htl_get_option( 'listing_sorting', 'menu_order' );

	// Sort query
	switch ( $listing_sorting ) {
		case 'date':
			$query_args[ 'orderby' ] = 'post_date';
			$query_args[ 'order' ]   = 'DESC';

			break;

		case 'title':
			$query_args[ 'orderby' ] = 'title';
			$query_args[ 'order' ]   = 'ASC';

			break;

		default:
			$query_args[ 'orderby' ] = 'menu_order';
			$query_args[ 'order' ]   = 'ASC';

			break;
	}

	// Filter rooms by category
	if ( isset( $_GET[ 'room_cat' ] ) ) {
		$room_cats = explode( ',', $_GET[ 'room_cat' ] );

		// Sanitize values
		$room_cats = array_map( 'absint', $room_cats );

		$query_args[ 'tax_query' ] = array(
			array(
				'taxonomy' => 'room_cat',
				'field'    => 'term_id',
				'terms'    => array_values( $room_cats )
			)
		);
	}

	// Filter rooms by rate
	if ( isset( $_GET[ 'room_rate' ] ) ) {
		$room_rates = explode( ',', $_GET[ 'room_rate' ] );

		// Sanitize values
		$room_rates = array_map( 'absint', $room_rates );

		$query_args[ 'tax_query' ][] = array(
			'taxonomy' => 'room_rate',
			'field'    => 'term_id',
			'terms'    => array_values( $room_rates )
		);
	}

	// Filter rooms by number of guests
	if ( isset( $_GET[ 'guests' ] ) ) {
		$guests = explode( ',', $_GET[ 'guests' ] );

		// Find higher value of 'guests' - We allow only one choice
		$guests = absint( max( $guests ) );

		$query_args[ 'meta_query' ][] = array(
			'key'     => '_max_guests',
			'value'   => $guests,
			'type'    => 'numeric',
			'compare' => '>=',
		);
	}

	// Filter rooms by number of children
	if ( isset( $_GET[ 'children' ] ) ) {
		$children = explode( ',', $_GET[ 'children' ] );

		// Find higher value of 'children' - We allow only one choice
		$children = absint( max( $children ) );

		$query_args[ 'meta_query' ][] = array(
			'key'     => '_max_children',
			'value'   => $children,
			'type'    => 'numeric',
			'compare' => '>=',
		);
	}

	$rooms = new WP_Query( apply_filters( 'hotelier_shortcode_room_list_query', $query_args ) );

	return $rooms;
}

/**
 * Gets a list of all rooms (in HTML).
 *
 * @access public
 * @param string $name
 * @return string
 */
function htl_get_list_of_rooms_html( $name ) {

	// Query args
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

	$select = '';

	$rooms = new WP_Query( $args );

	if ( $rooms->have_posts() ) {
		$select = '<select class="htl-ui-input htl-ui-input--select" name="' . esc_attr( $name ) . '">';

		while ( $rooms->have_posts() ) {
			$rooms->the_post();

			global $room;

			if ( $room->is_variable_room() ) {
				$varitations = $room->get_room_variations();

				$select .= '<optgroup label="' . get_the_title() . '">';

				foreach ( $varitations as $variation ) {

					$variation = new HTL_Room_Variation( $variation, $room->id );
					$select .= '<option value="'  . $room->id . '-' . $variation->get_room_index() . '">' . $variation->get_formatted_room_rate() . '</option>';
				}

				$select .= '</optgroup>';
			} else {
				$select .= '<option value="'  . $room->id . '-0">' . get_the_title() . '</option>';
			}
		}

		$select .= '</select>';

		wp_reset_postdata();
	}

	return $select;
}

/**
 * Get the price breakdown of a room.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @param int $room_id
 * @param int $rate_id
 * @return string
 */
function htl_get_room_price_breakdown( $checkin, $checkout, $room_id, $rate_id, $qty ) {
	$_room        = htl_get_room( $room_id );
	$is_variable  = $_room->is_variable_room() ? true : false;
	$checkin_obj  = new DateTime( $checkin );
	$checkout_obj = new DateTime( $checkout );
	$interval     = new DateInterval( 'P1D' );
	$daterange    = new DatePeriod( $checkin_obj, $interval ,$checkout_obj );

	$breakdown = array();

	if ( $is_variable ) {

		$_variation = $_room->get_room_variation( $rate_id );

		if ( $_variation->is_on_sale( $checkin, $checkout ) ) {

			if ( $_variation->is_price_per_day() ) {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_variation->variation[ 'sale_price_day' ][ $date->format( 'w' ) ] ) * $qty;
				}
			} else {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_variation->variation[ 'sale_price' ] ) * $qty;
				}
			}

		} else {

			if ( $_variation->has_seasonal_price() ) {
				// seasonal price schema
				$rules = htl_get_seasonal_prices_schema();

				if ( is_array( $rules ) ) {
					// Reverse the array, last rules have a higher precedence
					$rules = array_reverse( $rules );
				}

				foreach( $daterange as $date ) {
					$curr_date = $date->getTimestamp();

					if ( $rules ) {
						$has_seasonal_price = false;

						foreach ( $rules as $key => $rule ) {
							$begin = new DateTime( $rule[ 'from' ] );
							$end   = new DateTime( $rule[ 'to' ] );

							if ( $curr_date >= $begin->getTimestamp() && $curr_date <= $end->getTimestamp() ) {

								if ( isset( $_variation->variation[ 'seasonal_price' ][ $rule[ 'index' ] ] ) && $_variation->variation[ 'seasonal_price' ][ $rule[ 'index' ] ] > 0 ) {
									// Rule found, use seasonal price
									$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_variation->variation[ 'seasonal_price' ][ $rule[ 'index' ] ] ) * $qty;
									$has_seasonal_price = true;
								}

								break;
							}
						}

						if ( ! $has_seasonal_price ) {
							// Rule not found, use default price
							$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_variation->variation[ 'seasonal_base_price' ] ) * $qty;
						}
					}
				}
			} else if ( $_variation->is_price_per_day() ) {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_variation->variation[ 'price_day' ][ $date->format( 'w' ) ] ) * $qty;
				}
			} else {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_variation->variation[ 'regular_price' ] ) * $qty;
				}
			}
		}

	} else {

		if ( $_room->is_on_sale( $checkin, $checkout ) ) {

			if ( $_room->is_price_per_day() ) {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_room->sale_price_day[ $date->format( 'w' ) ] ) * $qty;
				}
			} else {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_room->sale_price ) * $qty;
				}
			}

		} else {

			if ( $_room->has_seasonal_price() ) {
				// seasonal price schema
				$rules = htl_get_seasonal_prices_schema();

				if ( is_array( $rules ) ) {
					// Reverse the array, last rules have a higher precedence
					$rules = array_reverse( $rules );
				}

				foreach( $daterange as $date ) {
					$curr_date = $date->getTimestamp();

					if ( $rules ) {
						$has_seasonal_price = false;

						foreach ( $rules as $key => $rule ) {
							$begin = new DateTime( $rule[ 'from' ] );
							$end   = new DateTime( $rule[ 'to' ] );

							if ( $curr_date >= $begin->getTimestamp() && $curr_date <= $end->getTimestamp() ) {

								if ( isset( $_room->seasonal_price[ $rule[ 'index' ] ] ) && $_room->seasonal_price[ $rule[ 'index' ] ] > 0 ) {
									// Rule found, use seasonal price
									$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_room->seasonal_price[ $rule[ 'index' ] ] ) * $qty;
									$has_seasonal_price = true;
								}

								break;
							}
						}

						if ( ! $has_seasonal_price ) {
							// Rule not found, use default price
							$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_room->seasonal_base_price ) * $qty;
						}
					}
				}
			} else if ( $_room->is_price_per_day() ) {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_room->regular_price_day[ $date->format( 'w' ) ] ) * $qty;
				}
			} else {
				foreach( $daterange as $date ) {
					$breakdown[ $date->format( 'Y-m-d' ) ] = absint( $_room->regular_price ) * $qty;
				}
			}
		}
	}

	return $breakdown;
}

/**
 * Function that returns an array containing the reservations of a room on the given dates.
 *
 * @access public
 * @param int $room_id
 * @param string $checkin
 * @param string $checkout
 * @return array (reservation_id, checkin, checkout and status)
 */
function htl_get_room_reservations( $room_id, $checkin, $checkout ) {
	global $wpdb;

	$sql          = $wpdb->prepare( "SELECT rb.reservation_id, b.checkin, b.checkout, b.status FROM {$wpdb->prefix}hotelier_rooms_bookings rb, {$wpdb->prefix}hotelier_bookings b WHERE rb.reservation_id = b.reservation_id AND rb.room_id = %d AND (%s <= b.checkout AND %s >= b.checkin) ORDER BY b.checkin", $room_id, $checkin, $checkout );
	$reservations = $wpdb->get_results( $sql, ARRAY_A );

	return $reservations;
}

/**
 * Function that display an info about the minimum/maximum nights stay.
 *
 * @access public
 * @param  int $min_nights
 * @param  int $max_nights
 * @return string
 */
function htl_get_room_min_max_info( $min_nights, $max_nights, $room ) {
	if ( $min_nights > 1 && $max_nights ) {
		$text = sprintf( __( '%s nights minimum stay and %s nights maximum stay', 'wp-hotelier' ), absint( $min_nights ), absint( $max_nights ) );
	} else if ( $min_nights > 1 ) {
		$text = sprintf( __( '%s nights minimum stay', 'wp-hotelier' ), absint( $min_nights ) );
	} else if ( $max_nights ) {
		$text = sprintf( _n( '%s night maximum stay', '%s nights maximum stay', $max_nights, 'wp-hotelier' ), absint( $max_nights ) );
	} else {
		$text = '';
	}

	$text = apply_filters( 'hotelier_get_room_min_max_info', $text, $min_nights, $max_nights, $room );

	return $text;
}


/**
 * Function that display a info about the minimum/maximum nights stay when not available.
 *
 * @access public
 * @param  int $min_nights
 * @param  int $max_nights
 * @return string
 */
function htl_get_room_not_available_min_max_info( $min_nights, $max_nights, $room ) {
	if ( $min_nights > 1 && $max_nights ) {
		$text = sprintf( __( 'Sorry, this room requires a minimum of %s nights and a maximum of %s nights.', 'wp-hotelier' ), absint( $min_nights ), absint( $max_nights ) );
	} else if ( $min_nights > 1 ) {
		$text = sprintf( __( 'Sorry, this room requires a minimum of %s nights.', 'wp-hotelier' ), absint( $min_nights ) );
	} else if ( $max_nights ) {
		$text = sprintf( _n( 'Sorry, this room requires a maximum of %s night.', 'Sorry, this room requires a maximum of %s nights.', $max_nights, 'wp-hotelier' ), absint( $max_nights ) );
	} else {
		$text = '';
	}

	return $text;
}

/**
 * Get the nice name of a rate name
 *
 * @param  string $rate
 * @return string
 */
function htl_get_formatted_room_rate( $rate ) {
	$term = term_exists( $rate, 'room_rate' );

	if ( $term !== 0 && $term !== null ) {
		$rate = get_term_by( 'id', $term[ 'term_id' ], 'room_rate' );
		$rate = $rate->name;
	} else {
		$rate = ucfirst( str_replace('-', ' ', $rate ) );
	}

	return $rate;
}

/**
 * Get rate ID from rate name
 */
function htl_get_room_rate_id_from_rate_name( $room, $rate_name ) {
	$rate_id = 0;

	if ( $room->is_variable_room() && $rate_name && $room->rate_term_exists( $rate_name ) ) {
		$variations = $room->get_room_variations();

		// Get first index
		foreach ( $variations as $key => $variation ) {
			if ( isset( $variation[ 'room_rate' ] ) && $variation[ 'room_rate' ] === $rate_name ) {
				$rate_id = $key;
				break;
			}
		}
	}

	return $rate_id;
}

/**
 * Calculate value of a fee.
 */
function htl_calculate_fee( $key, $value, $line_price, $checkin, $checkout, $room, $rate_id = 0) {
	$fee_to_add = apply_filters( 'hotelier_fee_to_add', 0, $key, $value, $line_price, $checkin, $checkout, $room, $rate_id );

	return $fee_to_add;
}

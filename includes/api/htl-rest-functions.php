<?php
/**
 * REST API Helper Functions.
 *
 * @author   Starter
 * @category API
 * @package  Hotelier/API
 * @version  2.18.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the minimum price for a room.
 *
 * Uses the HTL_Room::get_min_price() method.
 * Returns raw integer (e.g., 48000 for $480.00).
 *
 * @param HTL_Room $room Room object.
 * @return int|null Minimum price as integer or null if no price set.
 */
function htl_rest_get_room_min_price( $room ) {
	return $room->get_min_price();
}

/**
 * Get the minimum price for a room variation.
 *
 * Uses the HTL_Room_Variation::get_min_price() method.
 * Returns raw integer (e.g., 48000 for $480.00).
 *
 * @param HTL_Room_Variation $variation Variation object.
 * @return int|null Minimum price as integer or null if no price set.
 */
function htl_rest_get_variation_min_price( $variation ) {
	return $variation->get_min_price();
}

/**
 * Get date parameters schema for REST API.
 *
 * @return array Schema for checkin/checkout parameters.
 */
function htl_rest_get_date_params_schema() {
	return array(
		'checkin'  => array(
			'description' => __( 'Check-in date (YYYY-MM-DD).', 'wp-hotelier' ),
			'type'        => 'string',
			'format'      => 'date',
		),
		'checkout' => array(
			'description' => __( 'Check-out date (YYYY-MM-DD).', 'wp-hotelier' ),
			'type'        => 'string',
			'format'      => 'date',
		),
	);
}

/**
 * Get the featured image URL for a room.
 *
 * @param int    $room_id Room ID.
 * @param string $size    Image size.
 * @return string|null Image URL or null.
 */
function htl_rest_get_room_image_url( $room_id, $size = 'full' ) {
	$thumbnail_id = get_post_thumbnail_id( $room_id );

	if ( ! $thumbnail_id ) {
		return null;
	}

	$image = wp_get_attachment_image_src( $thumbnail_id, $size );

	return $image ? $image[0] : null;
}

/**
 * Get gallery images for a room.
 *
 * @param HTL_Room $room Room object.
 * @return array Array of image data.
 */
function htl_rest_get_room_gallery( $room ) {
	$gallery     = array();
	$gallery_ids = $room->get_gallery_attachment_ids();

	foreach ( $gallery_ids as $attachment_id ) {
		$full    = wp_get_attachment_image_src( $attachment_id, 'full' );
		$thumb   = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

		if ( $full ) {
			$gallery[] = array(
				'id'        => absint( $attachment_id ),
				'url'       => $full[0],
				'thumbnail' => $thumb ? $thumb[0] : $full[0],
				'alt'       => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			);
		}
	}

	return $gallery;
}

/**
 * Get room categories as array.
 *
 * @param int $room_id Room ID.
 * @return array Array of category data.
 */
function htl_rest_get_room_categories( $room_id ) {
	$categories = array();
	$terms      = get_the_terms( $room_id, 'room_cat' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$categories[] = array(
				'id'   => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
			);
		}
	}

	return $categories;
}

/**
 * Get room facilities as array.
 *
 * @param int $room_id Room ID.
 * @return array Array of facility data.
 */
function htl_rest_get_room_facilities( $room_id ) {
	$facilities = array();
	$terms      = get_the_terms( $room_id, 'room_facilities' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$facilities[] = array(
				'id'   => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
			);
		}
	}

	return $facilities;
}

/**
 * Get active hotelier extensions.
 *
 * @return array List of active extension identifiers.
 */
function htl_rest_get_active_extensions() {
	$extensions = array();

	if ( class_exists( 'HTL_APS_Room' ) ) {
		$extensions[] = 'advanced-pricing-system';
	}

	if ( class_exists( 'HTL_Disable_Dates' ) ) {
		$extensions[] = 'disable-dates';
	}

	if ( class_exists( 'HTL_Min_Max_Nights' ) ) {
		$extensions[] = 'min-max-nights';
	}

	if ( class_exists( 'HTL_Flat_Deposit' ) ) {
		$extensions[] = 'flat-deposit';
	}

	/**
	 * Filter the list of active extensions.
	 *
	 * @param array $extensions List of active extension identifiers.
	 */
	return apply_filters( 'hotelier_rest_active_extensions', $extensions );
}

/**
 * Calculate nights between two dates.
 *
 * @param string $checkin  Check-in date (YYYY-MM-DD).
 * @param string $checkout Check-out date (YYYY-MM-DD).
 * @return int Number of nights.
 */
function htl_rest_calculate_nights( $checkin, $checkout ) {
	$checkin_date  = new DateTime( $checkin );
	$checkout_date = new DateTime( $checkout );

	return $checkin_date->diff( $checkout_date )->days;
}

/**
 * Get extras data for a room or variation formatted for API response.
 *
 * @param HTL_Room $room    Room object.
 * @param int      $rate_id Rate ID (0 for standard room).
 * @return array Array of extras with pricing rules.
 */
function htl_rest_get_room_extras( $room, $rate_id = 0 ) {
	$extras_data = array();
	$extras_ids  = htl_get_room_extras_ids( $room, $rate_id );

	foreach ( $extras_ids as $extra_id ) {
		$extra = htl_get_extra( $extra_id );

		if ( ! $extra->exists() || ! $extra->is_enabled() ) {
			continue;
		}

		$extras_data[] = htl_rest_format_extra( $extra );
	}

	return $extras_data;
}

/**
 * Format an extra for API response.
 *
 * @param HTL_Extra $extra Extra object.
 * @return array Formatted extra data.
 */
function htl_rest_format_extra( $extra ) {
	$pricing_type = htl_rest_get_extra_pricing_type( $extra );

	$data = array(
		'id'          => absint( $extra->id ),
		'name'        => $extra->get_name(),
		'description' => $extra->get_description(),
		'optional'    => $extra->is_optional(),
		'pricing'     => array(
			'type'   => $pricing_type,
			'amount' => $extra->get_amount(),
		),
	);

	// Add amount_type for percentage-based extras.
	if ( $extra->get_amount_type() === 'percentage' ) {
		$data['pricing']['amount_type'] = 'percentage';
	}

	// Add selectable quantity info if enabled.
	if ( $extra->is_optional() && $extra->can_select_quantity() ) {
		$data['selectable_quantity'] = true;
		$data['max_quantity']        = $extra->get_max_quantity();
	}

	return $data;
}

/**
 * Get the pricing type string for an extra.
 *
 * @param HTL_Extra $extra Extra object.
 * @return string Pricing type (fixed, per_night, per_person, per_person_per_night).
 */
function htl_rest_get_extra_pricing_type( $extra ) {
	$type       = $extra->get_type(); // per_room or per_person
	$per_night  = $extra->calculate_per_night();

	if ( $type === 'per_room' ) {
		return $per_night ? 'per_night' : 'fixed';
	} else {
		return $per_night ? 'per_person_per_night' : 'per_person';
	}
}

/**
 * Calculate extra prices for a specific date range.
 *
 * @param HTL_Extra $extra    Extra object.
 * @param int       $nights   Number of nights.
 * @param int       $adults   Number of adults.
 * @param int       $children Number of children.
 * @return int Calculated price as integer.
 */
function htl_rest_calculate_extra_price( $extra, $nights, $adults = 1, $children = 0 ) {
	$price = 0;
	$amount = $extra->get_amount();
	$type = $extra->get_type();
	$per_night = $extra->calculate_per_night();

	if ( $extra->get_amount_type() === 'percentage' ) {
		// Percentage-based extras can't be calculated without room price.
		return 0;
	}

	if ( $type === 'per_room' ) {
		$price = $amount;
		if ( $per_night ) {
			$price = $amount * $nights;
		}
	} else {
		// Per person.
		$allowed_guest_type = $extra->get_allowed_guest_type();
		$guests = 0;

		if ( $allowed_guest_type === 'default' ) {
			$guests = $adults + $children;
		} elseif ( $allowed_guest_type === 'adults_only' ) {
			$guests = $adults;
		} elseif ( $allowed_guest_type === 'children_only' ) {
			$guests = $children;
		}

		$price = $amount * $guests;
		if ( $per_night ) {
			$price = $price * $nights;
		}
	}

	// Apply max cost if set.
	$max_cost = $extra->get_max_cost();
	if ( $max_cost > 0 && $price > $max_cost ) {
		$price = $max_cost;
	}

	return absint( $price );
}

/**
 * Calculate price with tax breakdown.
 *
 * @param int $price_excl_tax Price excluding tax.
 * @return array Array with price_excl_tax, tax, and price_incl_tax.
 */
function htl_rest_calculate_price_with_tax( $price_excl_tax ) {
	$price_excl_tax = absint( $price_excl_tax );
	$tax            = htl_calculate_tax( $price_excl_tax );

	return array(
		'price_excl_tax' => $price_excl_tax,
		'tax'            => $tax,
		'price_incl_tax' => $price_excl_tax + $tax,
	);
}

/**
 * Get required (non-optional) extras for a room/variation.
 *
 * @param HTL_Room $room    Room object.
 * @param int      $rate_id Rate ID (0 for standard room).
 * @return array Array of required extra IDs.
 */
function htl_rest_get_required_extras_ids( $room, $rate_id = 0 ) {
	$required   = array();
	$extras_ids = htl_get_room_extras_ids( $room, $rate_id );

	foreach ( $extras_ids as $extra_id ) {
		$extra = htl_get_extra( $extra_id );

		if ( $extra->exists() && $extra->is_enabled() && ! $extra->is_optional() ) {
			$required[] = $extra_id;
		}
	}

	return $required;
}

/**
 * Get optional extras for a room/variation.
 *
 * @param HTL_Room $room    Room object.
 * @param int      $rate_id Rate ID (0 for standard room).
 * @return array Array of optional extra IDs.
 */
function htl_rest_get_optional_extras_ids( $room, $rate_id = 0 ) {
	$optional   = array();
	$extras_ids = htl_get_room_extras_ids( $room, $rate_id );

	foreach ( $extras_ids as $extra_id ) {
		$extra = htl_get_extra( $extra_id );

		if ( $extra->exists() && $extra->is_enabled() && $extra->is_optional() ) {
			$optional[] = $extra_id;
		}
	}

	return $optional;
}

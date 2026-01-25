<?php
/**
 * REST API Availability Controller.
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
 * HTL_REST_Availability_Controller Class.
 *
 * Handles REST API requests for room availability with computed prices.
 */
class HTL_REST_Availability_Controller extends HTL_REST_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'availability';

	/**
	 * Register the routes for availability.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to read items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function get_items_permissions_check( $request ) {
		return HTL_REST_Authentication::check_public_permission();
	}

	/**
	 * Get availability for the specified date range.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$checkin  = $request->get_param( 'checkin' );
		$checkout = $request->get_param( 'checkout' );
		$room_id  = $request->get_param( 'room_id' );
		$adults   = $request->get_param( 'adults' );
		$children = $request->get_param( 'children' );

		// Validate required parameters.
		if ( empty( $checkin ) || empty( $checkout ) ) {
			return new WP_Error(
				'hotelier_rest_missing_dates',
				__( 'Both checkin and checkout parameters are required.', 'wp-hotelier' ),
				array( 'status' => 400 )
			);
		}

		// Validate date range.
		if ( strtotime( $checkout ) <= strtotime( $checkin ) ) {
			return new WP_Error(
				'hotelier_rest_invalid_dates',
				__( 'Check-out date must be after check-in date.', 'wp-hotelier' ),
				array( 'status' => 400 )
			);
		}

		// Validate checkin is not in the past.
		if ( strtotime( $checkin ) < strtotime( 'today' ) ) {
			return new WP_Error(
				'hotelier_rest_past_dates',
				__( 'Check-in date cannot be in the past.', 'wp-hotelier' ),
				array( 'status' => 400 )
			);
		}

		$nights = htl_rest_calculate_nights( $checkin, $checkout );

		// Single room mode.
		if ( $room_id ) {
			return $this->get_single_room_availability( $room_id, $checkin, $checkout, $nights, $adults, $children );
		}

		// List mode - get all available rooms.
		return $this->get_rooms_availability( $checkin, $checkout, $nights, $adults, $children );
	}

	/**
	 * Get availability for a single room.
	 *
	 * @param int    $room_id  Room ID.
	 * @param string $checkin  Check-in date.
	 * @param string $checkout Check-out date.
	 * @param int    $nights   Number of nights.
	 * @param int    $adults   Number of adults.
	 * @param int    $children Number of children.
	 * @return WP_REST_Response|WP_Error
	 */
	protected function get_single_room_availability( $room_id, $checkin, $checkout, $nights, $adults, $children ) {
		$room = htl_get_room( $room_id );

		if ( ! $room->exists() || 'publish' !== $room->post->post_status ) {
			return new WP_Error(
				'hotelier_rest_room_invalid_id',
				__( 'Invalid room ID.', 'wp-hotelier' ),
				array( 'status' => 404 )
			);
		}

		// Get availability with reason.
		$availability = $room->is_available_with_reason( $checkin, $checkout );

		$data = array(
			'checkin'  => $checkin,
			'checkout' => $checkout,
			'nights'   => $nights,
			'room'     => $this->prepare_room_availability_data( $room, $checkin, $checkout, $nights, $availability, $adults, $children ),
		);

		/**
		 * Filter single room availability data.
		 *
		 * @param array    $data     Availability data.
		 * @param HTL_Room $room     Room object.
		 * @param string   $checkin  Check-in date.
		 * @param string   $checkout Check-out date.
		 */
		$data = apply_filters( 'hotelier_rest_single_room_availability', $data, $room, $checkin, $checkout );

		return rest_ensure_response( $data );
	}

	/**
	 * Get availability for all rooms.
	 *
	 * @param string $checkin  Check-in date.
	 * @param string $checkout Check-out date.
	 * @param int    $nights   Number of nights.
	 * @param int    $adults   Number of adults.
	 * @param int    $children Number of children.
	 * @return WP_REST_Response
	 */
	protected function get_rooms_availability( $checkin, $checkout, $nights, $adults, $children ) {
		// Get available room IDs.
		$available_room_ids = htl_get_available_room_ids( $checkin, $checkout );

		$rooms           = array();
		$available_count = 0;

		foreach ( $available_room_ids as $room_id ) {
			$room = htl_get_room( $room_id );

			if ( ! $room->exists() ) {
				continue;
			}

			$availability = $room->is_available_with_reason( $checkin, $checkout );

			// Only include if available.
			if ( $availability['is_available'] ) {
				$rooms[] = $this->prepare_room_availability_data( $room, $checkin, $checkout, $nights, $availability, $adults, $children );
				$available_count++;
			}
		}

		$data = array(
			'checkin'         => $checkin,
			'checkout'        => $checkout,
			'nights'          => $nights,
			'available_count' => $available_count,
			'rooms'           => $rooms,
		);

		/**
		 * Filter rooms availability data.
		 *
		 * @param array  $data     Availability data.
		 * @param string $checkin  Check-in date.
		 * @param string $checkout Check-out date.
		 */
		$data = apply_filters( 'hotelier_rest_rooms_availability', $data, $checkin, $checkout );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare room availability data for response.
	 *
	 * @param HTL_Room $room         Room object.
	 * @param string   $checkin      Check-in date.
	 * @param string   $checkout     Check-out date.
	 * @param int      $nights       Number of nights.
	 * @param array    $availability Availability with reason.
	 * @param int      $adults       Number of adults.
	 * @param int      $children     Number of children.
	 * @return array Room availability data.
	 */
	protected function prepare_room_availability_data( $room, $checkin, $checkout, $nights, $availability, $adults, $children ) {
		// Resolve guest counts with room defaults as fallback.
		$guests = $this->resolve_guest_counts( $room, $adults, $children );
		$adults   = $guests['adults'];
		$children = $guests['children'];

		$data = array(
			'id'              => $room->id,
			'name'            => $room->get_title(),
			'is_variable'     => $room->is_variable_room(),
			'max_guests'      => $room->get_max_guests(),
			'max_children'    => $room->get_max_children(),
			'available_rooms' => $room->get_available_rooms( $checkin, $checkout ),
			'is_available'    => $availability['is_available'],
		);

		// Add reason if not available.
		if ( ! $availability['is_available'] && ! empty( $availability['reason'] ) ) {
			$data['reason'] = $availability['reason'];
		}

		// Calculate extra guest fees if APS extension is active.
		$extra_guest_fees = null;
		$extra_guest_fees_total = 0;

		if ( function_exists( 'hotelier_aps_calculate_extra_guest_fees' ) ) {
			$extra_guest_fees = hotelier_aps_calculate_extra_guest_fees( $room, $adults, $children, $nights );
			if ( $extra_guest_fees ) {
				$extra_guest_fees_total = $extra_guest_fees['total_fees'];

				// Add tax breakdown to adult fees.
				if ( isset( $extra_guest_fees['adults'] ) ) {
					$extra_guest_fees['adults']['total'] = htl_rest_calculate_price_with_tax( $extra_guest_fees['adults']['total'] );
				}

				// Add tax breakdown to children fees.
				if ( isset( $extra_guest_fees['children'] ) ) {
					$extra_guest_fees['children']['total'] = htl_rest_calculate_price_with_tax( $extra_guest_fees['children']['total'] );
				}

				// Remove internal total_fees key.
				unset( $extra_guest_fees['total_fees'] );

				$data['extra_guest_fees'] = $extra_guest_fees;
			}
		}

		if ( $room->is_variable_room() ) {
			$data['variations'] = $this->get_variations_availability( $room, $checkin, $checkout, $nights, $adults, $children, $extra_guest_fees_total );
		} else {
			// Standard room pricing.
			$room_price = $room->get_price( $checkin, $checkout );
			$data['room'] = htl_rest_calculate_price_with_tax( $room_price );

			// Get extras.
			$extras_result = $this->calculate_extras_for_room( $room, 0, $nights, $room_price, $adults, $children );
			$data['required_extras'] = $extras_result['required'];
			$data['optional_extras'] = $extras_result['optional'];

			// Calculate totals (room + required extras + extra guest fees).
			$totals_excl_tax = $room_price + $extras_result['required_total'] + $extra_guest_fees_total;
			$data['totals'] = htl_rest_calculate_price_with_tax( $totals_excl_tax );
		}

		return $data;
	}

	/**
	 * Resolve guest counts with room fallback.
	 *
	 * @param HTL_Room $room     Room object.
	 * @param int|null $adults   Requested adults (null if not provided).
	 * @param int|null $children Requested children (null if not provided).
	 * @return array Array with 'adults' and 'children' keys.
	 */
	protected function resolve_guest_counts( $room, $adults, $children ) {
		return array(
			'adults'   => $adults !== null ? $adults : $room->get_max_guests(),
			'children' => $children !== null ? $children : $room->get_max_children(),
		);
	}

	/**
	 * Get variations availability data.
	 *
	 * @param HTL_Room $room                  Room object.
	 * @param string   $checkin               Check-in date.
	 * @param string   $checkout              Check-out date.
	 * @param int      $nights                Number of nights.
	 * @param int      $adults                Number of adults.
	 * @param int      $children              Number of children.
	 * @param int      $extra_guest_fees_total Extra guest fees total.
	 * @return array Variations data.
	 */
	protected function get_variations_availability( $room, $checkin, $checkout, $nights, $adults, $children, $extra_guest_fees_total = 0 ) {
		$variations_data  = array();
		$variations       = $room->get_room_variations();
		$count_variations = count( $variations );

		for ( $i = 1; $i <= $count_variations; $i++ ) {
			$variation = $room->get_room_variation( $i );

			if ( ! $variation ) {
				continue;
			}

			$variation_price = $variation->get_price( $checkin, $checkout );

			$variation_data = array(
				'index'     => $variation->get_room_index(),
				'rate_name' => $variation->get_formatted_room_rate(),
				'room'      => htl_rest_calculate_price_with_tax( $variation_price ),
			);

			// Get extras for this variation.
			$extras_result = $this->calculate_extras_for_room( $room, $variation->get_room_index(), $nights, $variation_price, $adults, $children );
			$variation_data['required_extras'] = $extras_result['required'];
			$variation_data['optional_extras'] = $extras_result['optional'];

			// Calculate totals (room + required extras + extra guest fees).
			$totals_excl_tax = $variation_price + $extras_result['required_total'] + $extra_guest_fees_total;
			$variation_data['totals'] = htl_rest_calculate_price_with_tax( $totals_excl_tax );

			$variations_data[] = $variation_data;
		}

		return $variations_data;
	}

	/**
	 * Calculate extras for a room or variation.
	 *
	 * @param HTL_Room $room       Room object.
	 * @param int      $rate_id    Rate ID (0 for standard room).
	 * @param int      $nights     Number of nights.
	 * @param int      $room_price Room price for percentage calculations.
	 * @param int      $adults     Number of adults.
	 * @param int      $children   Number of children.
	 * @return array Array with 'required', 'optional', and 'required_total'.
	 */
	protected function calculate_extras_for_room( $room, $rate_id, $nights, $room_price, $adults, $children ) {
		$required        = array();
		$optional        = array();
		$required_total  = 0;
		$required_ids    = htl_rest_get_required_extras_ids( $room, $rate_id );
		$optional_ids    = htl_rest_get_optional_extras_ids( $room, $rate_id );

		// Process required extras - calculate their prices.
		foreach ( $required_ids as $extra_id ) {
			$extra = htl_get_extra( $extra_id );

			if ( ! $extra->exists() ) {
				continue;
			}

			// Calculate actual price for required extras.
			$extra_price = $this->calculate_extra_price_with_room( $extra, $nights, $room_price, $adults, $children );
			$required_total += $extra_price;

			$extra_data = array(
				'id'   => absint( $extra_id ),
				'name' => $extra->get_name(),
			);

			// Add price breakdown.
			$price_breakdown = htl_rest_calculate_price_with_tax( $extra_price );
			$extra_data['price_excl_tax'] = $price_breakdown['price_excl_tax'];
			$extra_data['tax']            = $price_breakdown['tax'];
			$extra_data['price_incl_tax'] = $price_breakdown['price_incl_tax'];

			$required[] = $extra_data;
		}

		// Process optional extras - show pricing rules (not calculated).
		foreach ( $optional_ids as $extra_id ) {
			$extra = htl_get_extra( $extra_id );

			if ( ! $extra->exists() ) {
				continue;
			}

			$optional[] = htl_rest_format_extra( $extra );
		}

		return array(
			'required'       => $required,
			'optional'       => $optional,
			'required_total' => $required_total,
		);
	}

	/**
	 * Calculate extra price including percentage-based extras.
	 *
	 * @param HTL_Extra $extra      Extra object.
	 * @param int       $nights     Number of nights.
	 * @param int       $room_price Room price (for percentage calculations).
	 * @param int       $adults     Number of adults.
	 * @param int       $children   Number of children.
	 * @return int Calculated price as integer.
	 */
	protected function calculate_extra_price_with_room( $extra, $nights, $room_price, $adults, $children ) {
		$price      = 0;
		$amount     = $extra->get_amount();
		$type       = $extra->get_type();
		$per_night  = $extra->calculate_per_night();
		$is_percent = $extra->get_amount_type() === 'percentage';

		if ( $type === 'per_room' ) {
			if ( $is_percent ) {
				$price = ( $room_price * $amount ) / 100;
			} else {
				$price = $amount;
				if ( $per_night ) {
					$price = $amount * $nights;
				}
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

			if ( $is_percent ) {
				$price = ( ( $room_price * $amount ) / 100 ) * $guests;
			} else {
				$price = $amount * $guests;
				if ( $per_night ) {
					$price = $price * $nights;
				}
			}
		}

		// Apply max cost if set.
		$max_cost = $extra->get_max_cost();
		if ( $max_cost > 0 && $price > $max_cost ) {
			$price = $max_cost;
		}

		return absint( ceil( $price ) );
	}

	/**
	 * Get the query params for availability.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'checkin'  => array(
				'description'       => __( 'Check-in date (YYYY-MM-DD). Required.', 'wp-hotelier' ),
				'type'              => 'string',
				'format'            => 'date',
				'required'          => true,
				'validate_callback' => array( $this, 'validate_date_param' ),
				'sanitize_callback' => array( $this, 'sanitize_date_param' ),
			),
			'checkout' => array(
				'description'       => __( 'Check-out date (YYYY-MM-DD). Required.', 'wp-hotelier' ),
				'type'              => 'string',
				'format'            => 'date',
				'required'          => true,
				'validate_callback' => array( $this, 'validate_date_param' ),
				'sanitize_callback' => array( $this, 'sanitize_date_param' ),
			),
			'room_id'  => array(
				'description'       => __( 'Check availability for a specific room.', 'wp-hotelier' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'adults'   => array(
				'description'       => __( 'Number of adults. Defaults to room max_guests if not provided.', 'wp-hotelier' ),
				'type'              => 'integer',
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'children' => array(
				'description'       => __( 'Number of children. Defaults to room max_children if not provided.', 'wp-hotelier' ),
				'type'              => 'integer',
				'minimum'           => 0,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Get the availability's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$price_schema = array(
			'type'       => 'object',
			'properties' => array(
				'price_excl_tax' => array( 'type' => 'integer' ),
				'tax'            => array( 'type' => 'integer' ),
				'price_incl_tax' => array( 'type' => 'integer' ),
			),
		);

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'availability',
			'type'       => 'object',
			'properties' => array(
				'checkin'         => array(
					'description' => __( 'Check-in date.', 'wp-hotelier' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'view' ),
				),
				'checkout'        => array(
					'description' => __( 'Check-out date.', 'wp-hotelier' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'view' ),
				),
				'nights'          => array(
					'description' => __( 'Number of nights.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'available_count' => array(
					'description' => __( 'Number of available rooms.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'rooms'           => array(
					'description' => __( 'Available rooms with pricing.', 'wp-hotelier' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'              => array( 'type' => 'integer' ),
							'name'            => array( 'type' => 'string' ),
							'is_variable'     => array( 'type' => 'boolean' ),
							'max_guests'      => array( 'type' => 'integer' ),
							'max_children'    => array( 'type' => 'integer' ),
							'available_rooms' => array( 'type' => 'integer' ),
							'is_available'    => array( 'type' => 'boolean' ),
							'room'            => $price_schema,
							'required_extras' => array( 'type' => 'array' ),
							'optional_extras' => array( 'type' => 'array' ),
							'totals'          => $price_schema,
							'variations'      => array( 'type' => 'array' ),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

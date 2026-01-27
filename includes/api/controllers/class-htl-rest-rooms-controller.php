<?php
/**
 * REST API Rooms Controller.
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
 * HTL_REST_Rooms_Controller Class.
 *
 * Handles REST API requests for rooms.
 */
class HTL_REST_Rooms_Controller extends HTL_REST_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'rooms';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'room';

	/**
	 * Register the routes for rooms.
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

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the room.', 'wp-hotelier' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
						'checkin'  => array(
							'description'       => __( 'Check-in date (YYYY-MM-DD) for price calculation.', 'wp-hotelier' ),
							'type'              => 'string',
							'format'            => 'date',
							'validate_callback' => array( $this, 'validate_date_param' ),
							'sanitize_callback' => array( $this, 'sanitize_date_param' ),
						),
						'checkout' => array(
							'description'       => __( 'Check-out date (YYYY-MM-DD) for price calculation.', 'wp-hotelier' ),
							'type'              => 'string',
							'format'            => 'date',
							'validate_callback' => array( $this, 'validate_date_param' ),
							'sanitize_callback' => array( $this, 'sanitize_date_param' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => array(
						'disabled_dates_type'   => array(
							'description' => __( 'Type of disabled dates: "global" or "custom".', 'wp-hotelier' ),
							'type'        => 'string',
							'enum'        => array( 'global', 'custom' ),
						),
						'disabled_dates_schema' => array(
							'description' => __( 'Array of disabled date rules (only for custom type).', 'wp-hotelier' ),
							'type'        => 'array',
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'from'       => array( 'type' => 'string', 'format' => 'date' ),
									'to'         => array( 'type' => array( 'string', 'null' ) ),
									'single_day' => array( 'type' => 'boolean' ),
									'every_year' => array( 'type' => 'boolean' ),
								),
							),
						),
					),
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
	 * Check if a given request has access to read an item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function get_item_permissions_check( $request ) {
		return HTL_REST_Authentication::check_public_permission();
	}

	/**
	 * Check if a given request has access to update an item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function update_item_permissions_check( $request ) {
		return HTL_REST_Authentication::check_edit_rooms_permission();
	}

	/**
	 * Get a collection of rooms.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$args = array(
			'post_type'      => $this->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $request->get_param( 'per_page' ),
			'paged'          => $request->get_param( 'page' ),
			'meta_query'     => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
			),
		);

		// Filter by room type (category).
		$room_type = $request->get_param( 'room_type' );
		if ( ! empty( $room_type ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'room_cat',
					'field'    => 'term_id',
					'terms'    => absint( $room_type ),
				),
			);
		}

		// Ordering.
		$orderby = $request->get_param( 'orderby' );
		$order   = $request->get_param( 'order' );

		switch ( $orderby ) {
			case 'date':
				$args['orderby'] = 'post_date';
				break;
			case 'title':
				$args['orderby'] = 'title';
				break;
			case 'id':
				$args['orderby'] = 'ID';
				break;
			case 'menu_order':
			default:
				$args['orderby'] = 'menu_order';
				break;
		}

		$args['order'] = strtoupper( $order ) === 'DESC' ? 'DESC' : 'ASC';

		/**
		 * Filter the query arguments for a request.
		 *
		 * @param array           $args    Key value array of query args.
		 * @param WP_REST_Request $request The request used.
		 */
		$args = apply_filters( 'hotelier_rest_rooms_query', $args, $request );

		$query       = new WP_Query( $args );
		$rooms       = array();
		$checkin     = $request->get_param( 'checkin' );
		$checkout    = $request->get_param( 'checkout' );

		// Validate date range if provided.
		if ( $checkin && $checkout ) {
			if ( strtotime( $checkout ) <= strtotime( $checkin ) ) {
				return new WP_Error(
					'hotelier_rest_invalid_dates',
					__( 'Check-out date must be after check-in date.', 'wp-hotelier' ),
					array( 'status' => 400 )
				);
			}
		}

		foreach ( $query->posts as $post ) {
			$room_data = $this->prepare_item_for_response( $post, $request );
			$rooms[]   = $this->prepare_response_for_collection( $room_data );
		}

		$response = rest_ensure_response( $rooms );

		// Add pagination headers.
		$response = $this->add_pagination_headers( $request, $response, $query->found_posts, $request->get_param( 'per_page' ) );

		return $response;
	}

	/**
	 * Get a single room.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$id   = absint( $request->get_param( 'id' ) );
		$post = get_post( $id );

		if ( ! $post || $this->post_type !== $post->post_type ) {
			return new WP_Error(
				'hotelier_rest_room_invalid_id',
				__( 'Invalid room ID.', 'wp-hotelier' ),
				array( 'status' => 404 )
			);
		}

		if ( 'publish' !== $post->post_status ) {
			return new WP_Error(
				'hotelier_rest_room_not_published',
				__( 'Room is not available.', 'wp-hotelier' ),
				array( 'status' => 404 )
			);
		}

		$checkin  = $request->get_param( 'checkin' );
		$checkout = $request->get_param( 'checkout' );

		// Validate date range if provided.
		if ( $checkin && $checkout ) {
			if ( strtotime( $checkout ) <= strtotime( $checkin ) ) {
				return new WP_Error(
					'hotelier_rest_invalid_dates',
					__( 'Check-out date must be after check-in date.', 'wp-hotelier' ),
					array( 'status' => 400 )
				);
			}
		}

		$data     = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Update a single room's disabled dates.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		// Check if Disable Dates extension is active
		if ( ! class_exists( 'Hotelier_Disable_Dates' ) ) {
			return new WP_Error(
				'hotelier_rest_extension_required',
				__( 'The Disable Dates extension is required to update room disabled dates.', 'wp-hotelier' ),
				array( 'status' => 400 )
			);
		}

		$id   = absint( $request->get_param( 'id' ) );
		$post = get_post( $id );

		if ( ! $post || $this->post_type !== $post->post_type ) {
			return new WP_Error(
				'hotelier_rest_invalid_id',
				__( 'Invalid room ID.', 'wp-hotelier' ),
				array( 'status' => 404 )
			);
		}

		$disabled_dates_type   = $request->get_param( 'disabled_dates_type' );
		$disabled_dates_schema = $request->get_param( 'disabled_dates_schema' );

		// Validate type
		if ( empty( $disabled_dates_type ) ) {
			return new WP_Error(
				'hotelier_rest_invalid_param',
				__( 'The disabled_dates_type parameter is required.', 'wp-hotelier' ),
				array( 'status' => 400 )
			);
		}

		if ( ! in_array( $disabled_dates_type, array( 'global', 'custom' ) ) ) {
			return new WP_Error(
				'hotelier_rest_invalid_param',
				__( 'Invalid disabled_dates_type. Use "global" or "custom".', 'wp-hotelier' ),
				array( 'status' => 400 )
			);
		}

		// If type is custom, validate the schema
		if ( $disabled_dates_type === 'custom' ) {
			if ( empty( $disabled_dates_schema ) || ! is_array( $disabled_dates_schema ) ) {
				return new WP_Error(
					'hotelier_rest_invalid_param',
					__( 'A disabled_dates_schema array is required when using custom type.', 'wp-hotelier' ),
					array( 'status' => 400 )
				);
			}

			// Validate each rule
			$validated_schema = array();
			$index            = 0;

			foreach ( $disabled_dates_schema as $rule ) {
				if ( ! isset( $rule['from'] ) || ! htl_rest_validate_date( $rule['from'] ) ) {
					return new WP_Error(
						'hotelier_rest_invalid_dates',
						__( 'Each rule must have a valid "from" date in YYYY-MM-DD format.', 'wp-hotelier' ),
						array( 'status' => 400 )
					);
				}

				$single_day = isset( $rule['single_day'] ) ? (bool) $rule['single_day'] : false;
				$to_date    = null;

				// Validate 'to' date if not a single day
				if ( ! $single_day ) {
					if ( isset( $rule['to'] ) && ! empty( $rule['to'] ) ) {
						if ( ! htl_rest_validate_date( $rule['to'] ) ) {
							return new WP_Error(
								'hotelier_rest_invalid_dates',
								__( 'Invalid "to" date format. Use YYYY-MM-DD.', 'wp-hotelier' ),
								array( 'status' => 400 )
							);
						}

						if ( strtotime( $rule['to'] ) < strtotime( $rule['from'] ) ) {
							return new WP_Error(
								'hotelier_rest_invalid_dates',
								__( 'The "to" date must be after or equal to the "from" date.', 'wp-hotelier' ),
								array( 'status' => 400 )
							);
						}

						$to_date = sanitize_text_field( $rule['to'] );
					}
				}

				$validated_rule = array(
					'index' => $index,
					'from'  => sanitize_text_field( $rule['from'] ),
					'to'    => $to_date,
				);

				// Only include single_day if true (extension uses isset() check)
				if ( $single_day ) {
					$validated_rule['single_day'] = 1;
				}

				// Only include every_year if true (extension uses isset() check)
				if ( isset( $rule['every_year'] ) && $rule['every_year'] ) {
					$validated_rule['every_year'] = 1;
				}

				$validated_schema[] = $validated_rule;

				$index++;
			}

			// Save custom schema
			update_post_meta( $id, '_disabled_dates_type', 'custom' );
			update_post_meta( $id, '_disabled_dates_schema', $validated_schema );
		} else {
			// Set to global
			update_post_meta( $id, '_disabled_dates_type', 'global' );
			delete_post_meta( $id, '_disabled_dates_schema' );
		}

		// Clear transient
		delete_transient( 'hotelier_room_disabled_dates' );
		delete_transient( 'hotelier_room_disabled_dates_' . $id );

		/**
		 * Fires after a room's disabled dates are updated via REST API.
		 *
		 * @param int             $id      The room ID.
		 * @param WP_REST_Request $request The request object.
		 */
		do_action( 'hotelier_rest_update_room_disabled_dates', $id, $request );

		// Return updated room
		$data     = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Prepare a single room output for response.
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response data.
	 */
	public function prepare_item_for_response( $post, $request ) {
		$room     = htl_get_room( $post );
		$checkin  = $request->get_param( 'checkin' );
		$checkout = $request->get_param( 'checkout' );

		$data = array(
			'id'                => $room->id,
			'name'              => $room->get_title(),
			'slug'              => $post->post_name,
			'permalink'         => get_permalink( $room->id ),
			'status'            => $post->post_status,
			'description'       => $post->post_content,
			'short_description' => $post->post_excerpt,
			'is_variable'       => $room->is_variable_room(),
			'max_guests'        => $room->get_max_guests(),
			'max_children'      => $room->get_max_children(),
			'stock_rooms'       => $room->get_stock_rooms(),
			'bed_size'          => $room->get_bed_size(),
			'beds'              => $room->get_beds(),
			'bathrooms'         => $room->get_bathrooms(),
			'room_size'         => $room->get_room_size(),
			'min_nights'        => $room->get_min_nights(),
			'max_nights'        => $room->get_max_nights(),
			'categories'        => htl_rest_get_room_categories( $room->id ),
			'facilities'        => htl_rest_get_room_facilities( $room->id ),
			'featured_image'    => $this->get_room_featured_image( $room->id ),
			'gallery'           => htl_rest_get_room_gallery( $room ),
		);

		if ( $room->is_variable_room() ) {
			// Variable room - variations contain deposit/cancellation info and extras.
			$data['variations'] = $this->get_room_variations( $room, $checkin, $checkout );
		} else {
			// Standard room - deposit/cancellation at room level.
			$data['min_price']           = htl_rest_get_room_min_price( $room );
			$data['requires_deposit']    = $room->needs_deposit();
			$data['deposit_percentage']  = $room->needs_deposit() ? absint( $room->get_deposit() ) : null;
			$data['is_cancellable']      = $room->is_cancellable();
			$data['conditions']          = $room->has_conditions() ? $room->get_room_conditions() : array();
			$data['extras']              = htl_rest_get_room_extras( $room, 0 );

			// Extension fields for standard room.
			$data = $this->add_extension_fields( $data, $room, null );
		}

		// Add links.
		$data['_links'] = $this->prepare_links( $data, $request );

		/**
		 * Filter room data before response.
		 *
		 * @param array           $data    Room data.
		 * @param HTL_Room        $room    Room object.
		 * @param WP_REST_Request $request Request object.
		 */
		$data = apply_filters( 'hotelier_rest_prepare_room', $data, $room, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Get room variations data.
	 *
	 * @param HTL_Room    $room     Room object.
	 * @param string|null $checkin  Check-in date.
	 * @param string|null $checkout Check-out date.
	 * @return array Variations data.
	 */
	protected function get_room_variations( $room, $checkin = null, $checkout = null ) {
		$variations_data  = array();
		$variations       = $room->get_room_variations();
		$count_variations = count( $variations );

		for ( $i = 1; $i <= $count_variations; $i++ ) {
			$variation = $room->get_room_variation( $i );

			if ( ! $variation ) {
				continue;
			}

			$variation_data = array(
				'index'              => $variation->get_room_index(),
				'rate_name'          => $variation->get_formatted_room_rate(),
				'rate_description'   => wp_strip_all_tags( $variation->get_room_description() ),
				'requires_deposit'   => $variation->needs_deposit(),
				'deposit_percentage' => $variation->needs_deposit() ? absint( $variation->get_deposit() ) : null,
				'is_cancellable'     => $variation->is_cancellable(),
				'conditions'         => $variation->has_conditions() ? $variation->get_room_conditions() : array(),
				'min_price'          => htl_rest_get_variation_min_price( $variation ),
				'extras'             => htl_rest_get_room_extras( $room, $variation->get_room_index() ),
			);

			// Extension fields for variation.
			$variation_data = $this->add_extension_fields( $variation_data, $room, $variation );

			$variations_data[] = $variation_data;
		}

		return $variations_data;
	}

	/**
	 * Add extension-specific fields.
	 *
	 * @param array                 $data      Current data array.
	 * @param HTL_Room              $room      Room object.
	 * @param HTL_Room_Variation|null $variation Variation object or null for standard room.
	 * @return array Modified data array.
	 */
	protected function add_extension_fields( $data, $room, $variation = null ) {
		// Flat Deposit extension.
		if ( class_exists( 'HTL_Flat_Deposit' ) ) {
			if ( $variation ) {
				$flat_deposit = isset( $variation->variation['flat_deposit_amount'] ) ? $variation->variation['flat_deposit_amount'] : null;
			} else {
				$flat_deposit = get_post_meta( $room->id, '_flat_deposit_amount', true );
			}

			if ( $flat_deposit ) {
				$data['flat_deposit_amount'] = absint( $flat_deposit );
			}
		}

		// Min/Max Nights extension (per-variation overrides).
		if ( class_exists( 'HTL_Min_Max_Nights' ) && $variation ) {
			// The extension hooks into filters, so variation already has computed values.
			$data['min_nights'] = $variation->get_min_nights();
			$data['max_nights'] = $variation->get_max_nights();
		}

		// Disable Dates extension (room level only).
		if ( class_exists( 'Hotelier_Disable_Dates' ) && ! $variation && function_exists( 'HTL_Disable_Dates' ) ) {
			$disabled_type = get_post_meta( $room->id, '_disabled_dates_type', true );
			$data['disabled_dates_type'] = $disabled_type ? $disabled_type : 'global';

			if ( $disabled_type === 'custom' ) {
				// Room has custom disabled dates.
				$schema = get_post_meta( $room->id, '_disabled_dates_schema', true );
				$data['disabled_dates'] = array_values( HTL_Disable_Dates()->get_disabled_dates_per_room( $room->id ) );
			} else {
				// Room uses global disabled dates.
				$data['disabled_dates'] = array_values( HTL_Disable_Dates()->get_global_disabled_dates() );
			}
		}

		// Advanced Pricing System extension.
		if ( class_exists( 'HTL_APS_Room' ) ) {
			$data['advanced_pricing_enabled'] = true;
		}

		return $data;
	}

	/**
	 * Format disabled dates schema.
	 *
	 * @param array $schema Raw disabled dates schema.
	 * @return array Formatted schema.
	 */
	protected function format_disabled_dates_schema( $schema ) {
		$formatted = array();

		if ( ! is_array( $schema ) ) {
			return $formatted;
		}

		foreach ( $schema as $rule ) {
			if ( isset( $rule['from'] ) ) {
				$formatted[] = array(
					'index'      => isset( $rule['index'] ) ? $rule['index'] : null,
					'from'       => $rule['from'],
					'to'         => isset( $rule['to'] ) && ! empty( $rule['to'] ) ? $rule['to'] : null,
					'single_day' => isset( $rule['single_day'] ) ? (bool) $rule['single_day'] : false,
					'every_year' => isset( $rule['every_year'] ) ? (bool) $rule['every_year'] : false,
				);
			}
		}

		return $formatted;
	}

	/**
	 * Get the featured image data for a room.
	 *
	 * @param int $room_id Room ID.
	 * @return array|null Featured image data or null.
	 */
	protected function get_room_featured_image( $room_id ) {
		$thumbnail_id = get_post_thumbnail_id( $room_id );

		if ( ! $thumbnail_id ) {
			return null;
		}

		$full  = wp_get_attachment_image_src( $thumbnail_id, 'full' );
		$thumb = wp_get_attachment_image_src( $thumbnail_id, 'room_thumbnail' );

		if ( ! $full ) {
			return null;
		}

		return array(
			'id'        => absint( $thumbnail_id ),
			'url'       => $full[0],
			'thumbnail' => $thumb ? $thumb[0] : $full[0],
			'alt'       => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ),
		);
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['room_type'] = array(
			'description'       => __( 'Filter by room category (term ID).', 'wp-hotelier' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['orderby'] = array(
			'description'       => __( 'Sort collection by attribute.', 'wp-hotelier' ),
			'type'              => 'string',
			'default'           => 'menu_order',
			'enum'              => array( 'date', 'title', 'menu_order', 'id' ),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['order'] = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'wp-hotelier' ),
			'type'              => 'string',
			'default'           => 'asc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['checkin'] = array(
			'description'       => __( 'Check-in date (YYYY-MM-DD) to filter by availability and calculate prices.', 'wp-hotelier' ),
			'type'              => 'string',
			'format'            => 'date',
			'validate_callback' => array( $this, 'validate_date_param' ),
			'sanitize_callback' => array( $this, 'sanitize_date_param' ),
		);

		$params['checkout'] = array(
			'description'       => __( 'Check-out date (YYYY-MM-DD) to filter by availability and calculate prices.', 'wp-hotelier' ),
			'type'              => 'string',
			'format'            => 'date',
			'validate_callback' => array( $this, 'validate_date_param' ),
			'sanitize_callback' => array( $this, 'sanitize_date_param' ),
		);

		/**
		 * Filter collection parameters for the rooms endpoint.
		 *
		 * @param array $params Collection parameters.
		 */
		return apply_filters( 'hotelier_rest_rooms_collection_params', $params );
	}

	/**
	 * Get the room's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'room',
			'type'       => 'object',
			'properties' => array(
				'id'                => array(
					'description' => __( 'Unique identifier for the room.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'name'              => array(
					'description' => __( 'Room name.', 'wp-hotelier' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'slug'              => array(
					'description' => __( 'Room slug.', 'wp-hotelier' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'permalink'         => array(
					'description' => __( 'Room URL.', 'wp-hotelier' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view' ),
				),
				'is_variable'       => array(
					'description' => __( 'Whether this room has rate variations.', 'wp-hotelier' ),
					'type'        => 'boolean',
					'context'     => array( 'view' ),
				),
				'max_guests'        => array(
					'description' => __( 'Maximum number of guests.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'max_children'      => array(
					'description' => __( 'Maximum number of children.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'stock_rooms'       => array(
					'description' => __( 'Number of rooms in stock.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'min_price'         => array(
					'description' => __( 'Minimum nightly price as integer.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'extras'            => array(
					'description' => __( 'Available extras with pricing rules.', 'wp-hotelier' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'          => array( 'type' => 'integer' ),
							'name'        => array( 'type' => 'string' ),
							'description' => array( 'type' => 'string' ),
							'optional'    => array( 'type' => 'boolean' ),
							'pricing'     => array(
								'type'       => 'object',
								'properties' => array(
									'type'   => array( 'type' => 'string' ),
									'amount' => array( 'type' => 'integer' ),
								),
							),
						),
					),
				),
				'variations'        => array(
					'description' => __( 'Room rate variations (for variable rooms).', 'wp-hotelier' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'index'            => array( 'type' => 'integer' ),
							'rate_name'        => array( 'type' => 'string' ),
							'rate_description' => array( 'type' => 'string' ),
							'min_price'        => array( 'type' => 'integer' ),
							'extras'           => array( 'type' => 'array' ),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

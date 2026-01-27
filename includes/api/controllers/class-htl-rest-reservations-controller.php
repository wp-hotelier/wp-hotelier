<?php
/**
 * REST API Reservations Controller.
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
 * HTL_REST_Reservations_Controller Class.
 *
 * Handles REST API requests for reservations (admin only).
 */
class HTL_REST_Reservations_Controller extends HTL_REST_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reservations';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'room_reservation';

	/**
	 * Register the routes for reservations.
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
						'description' => __( 'Unique identifier for the reservation.', 'wp-hotelier' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
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
		return HTL_REST_Authentication::check_manage_hotelier_permission();
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function get_item_permissions_check( $request ) {
		return HTL_REST_Authentication::check_manage_hotelier_permission();
	}

	/**
	 * Check if a given request has access to update an item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function update_item_permissions_check( $request ) {
		return HTL_REST_Authentication::check_manage_hotelier_permission();
	}

	/**
	 * Get a collection of reservations.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$args = array(
			'post_type'      => $this->post_type,
			'posts_per_page' => $request->get_param( 'per_page' ),
			'paged'          => $request->get_param( 'page' ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		// Filter by status.
		$status = $request->get_param( 'status' );
		if ( ! empty( $status ) ) {
			$args['post_status'] = 'htl-' . $status;
		} else {
			$args['post_status'] = array_keys( htl_get_reservation_statuses() );
		}

		// Filter by check-in date range.
		$checkin_after  = $request->get_param( 'checkin_after' );
		$checkin_before = $request->get_param( 'checkin_before' );

		if ( $checkin_after || $checkin_before ) {
			$args['meta_query'] = array();

			if ( $checkin_after ) {
				$args['meta_query'][] = array(
					'key'     => '_guest_checkin',
					'value'   => $checkin_after,
					'compare' => '>=',
					'type'    => 'DATE',
				);
			}

			if ( $checkin_before ) {
				$args['meta_query'][] = array(
					'key'     => '_guest_checkin',
					'value'   => $checkin_before,
					'compare' => '<=',
					'type'    => 'DATE',
				);
			}
		}

		/**
		 * Filter the query arguments for reservations.
		 *
		 * @param array           $args    Query arguments.
		 * @param WP_REST_Request $request Request object.
		 */
		$args = apply_filters( 'hotelier_rest_reservations_query', $args, $request );

		$query        = new WP_Query( $args );
		$reservations = array();

		foreach ( $query->posts as $post ) {
			$data           = $this->prepare_item_for_response( $post, $request );
			$reservations[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $reservations );

		// Add pagination headers.
		$response = $this->add_pagination_headers( $request, $response, $query->found_posts, $request->get_param( 'per_page' ) );

		return $response;
	}

	/**
	 * Get a single reservation.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$id   = absint( $request->get_param( 'id' ) );
		$post = get_post( $id );

		if ( ! $post || $this->post_type !== $post->post_type ) {
			return new WP_Error(
				'hotelier_rest_reservation_invalid_id',
				__( 'Invalid reservation ID.', 'wp-hotelier' ),
				array( 'status' => 404 )
			);
		}

		$data     = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Update a single reservation (status only).
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		$id   = absint( $request->get_param( 'id' ) );
		$post = get_post( $id );

		if ( ! $post || $this->post_type !== $post->post_type ) {
			return new WP_Error(
				'hotelier_rest_invalid_id',
				__( 'Invalid reservation ID.', 'wp-hotelier' ),
				array( 'status' => 404 )
			);
		}

		$reservation = htl_get_reservation( $id );

		$new_status = $request->get_param( 'status' );

		// Update status if provided
		if ( ! empty( $new_status ) ) {
			$current_status = $reservation->get_status();

			// Validate status
			$valid_statuses = array( 'pending', 'on-hold', 'confirmed', 'completed', 'cancelled', 'refunded', 'failed' );
			if ( ! in_array( $new_status, $valid_statuses ) ) {
				return new WP_Error(
					'hotelier_rest_invalid_status',
					__( 'Invalid reservation status.', 'wp-hotelier' ),
					array( 'status' => 400 )
				);
			}

			// Refunded reservations cannot be restored
			if ( $current_status === 'refunded' ) {
				return new WP_Error(
					'hotelier_rest_cannot_update',
					__( 'Refunded reservations cannot be modified.', 'wp-hotelier' ),
					array( 'status' => 400 )
				);
			}

			// Update the status
			$result = $reservation->update_status( $new_status, '', false, 'api' );

			if ( $result === false ) {
				return new WP_Error(
					'hotelier_rest_room_not_available',
					__( 'One or more rooms are no longer available for this reservation.', 'wp-hotelier' ),
					array( 'status' => 409 )
				);
			}
		}

		// Update last modified timestamp
		$reservation->update_last_modified();

		/**
		 * Fires after a reservation is updated via REST API.
		 *
		 * @param HTL_Reservation  $reservation The reservation object.
		 * @param WP_REST_Request  $request     The request object.
		 */
		do_action( 'hotelier_rest_update_reservation', $reservation, $request );

		// Return updated reservation
		$post     = get_post( $id );
		$data     = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}
	/**
	 * Prepare a single reservation output for response.
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response data.
	 */
	public function prepare_item_for_response( $post, $request ) {
		$reservation = new HTL_Reservation( $post );

		$data = array(
			'id'                    => $reservation->id,
			'reservation_number'    => $reservation->get_reservation_number(),
			'status'                => $reservation->get_status(),
			'date_created'          => $reservation->reservation_date,
			'checkin'               => $reservation->get_checkin(),
			'checkout'              => $reservation->get_checkout(),
			'nights'                => $reservation->get_nights(),
			'guest'                 => $this->get_guest_data( $reservation ),
			'payment_method'        => $reservation->get_payment_method(),
			'payment_method_title'  => $reservation->get_payment_method_title(),
			'currency'              => $reservation->get_reservation_currency(),
			'subtotal'              => $reservation->get_subtotal(),
			'tax_total'             => $reservation->get_tax_total(),
			'discount_total'        => $reservation->get_discount_total(),
			'total'                 => $reservation->get_total(),
			'deposit'               => $reservation->get_deposit(),
			'paid_deposit'          => $reservation->get_paid_deposit(),
			'remain_deposit_charge' => absint( $reservation->get_remain_deposit_charge() ),
			'balance_due'           => absint( $reservation->get_balance_due() ),
			'special_requests'      => $reservation->get_guest_special_requests(),
			'arrival_time'          => $reservation->get_formatted_arrival_time(),
			'items'                 => $this->get_reservation_items( $reservation ),
			'coupon_code'           => $reservation->get_coupon_code(),
			'is_marked_as_paid'     => (bool) $reservation->is_marked_as_paid(),
			'can_be_cancelled'      => $reservation->can_be_cancelled(),
		);

		// Add links.
		$data['_links'] = $this->prepare_links( $data, $request );

		/**
		 * Filter reservation data before response.
		 *
		 * @param array           $data        Reservation data.
		 * @param HTL_Reservation $reservation Reservation object.
		 * @param WP_REST_Request $request     Request object.
		 */
		$data = apply_filters( 'hotelier_rest_prepare_reservation', $data, $reservation, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Get guest data from reservation.
	 *
	 * @param HTL_Reservation $reservation Reservation object.
	 * @return array Guest data.
	 */
	protected function get_guest_data( $reservation ) {
		$address = $reservation->get_address();

		return array(
			'first_name' => $address['first_name'],
			'last_name'  => $address['last_name'],
			'email'      => $address['email'],
			'telephone'  => $address['telephone'],
			'address'    => array(
				'address_1' => $address['address1'],
				'address_2' => $address['address2'],
				'city'      => $address['city'],
				'state'     => $address['state'],
				'postcode'  => $address['postcode'],
				'country'   => $address['country'],
			),
		);
	}

	/**
	 * Get reservation items.
	 *
	 * @param HTL_Reservation $reservation Reservation object.
	 * @return array Items data.
	 */
	protected function get_reservation_items( $reservation ) {
		$items_data = array();
		$items      = $reservation->get_items();

		foreach ( $items as $item_id => $item ) {
			$room      = $reservation->get_room_from_item( $item );
			$item_data = array(
				'id'              => $item_id,
				'name'            => $item['name'],
				'room_id'         => isset( $item['room_id'] ) ? absint( $item['room_id'] ) : null,
				'quantity'        => isset( $item['qty'] ) ? absint( $item['qty'] ) : 1,
				'rate_name'       => isset( $item['rate_name'] ) ? $item['rate_name'] : null,
				'rate_id'         => isset( $item['rate_id'] ) ? absint( $item['rate_id'] ) : null,
				'max_guests'      => isset( $item['max_guests'] ) ? absint( $item['max_guests'] ) : null,
				'price'           => isset( $item['price'] ) ? absint( $item['price'] ) : null,
				'total'           => isset( $item['total'] ) ? absint( $item['total'] ) : null,
				'deposit'         => isset( $item['deposit'] ) ? absint( $item['deposit'] ) : 0,
				'percent_deposit' => isset( $item['percent_deposit'] ) ? absint( $item['percent_deposit'] ) : 0,
				'cancellable'     => isset( $item['is_cancellable'] ) ? (bool) $item['is_cancellable'] : true,
			);

			// Add guests info if available.
			if ( isset( $item['adults'] ) ) {
				$item_data['adults'] = maybe_unserialize( $item['adults'] );
			}

			if ( isset( $item['children'] ) ) {
				$item_data['children'] = maybe_unserialize( $item['children'] );
			}

			// Add extras if available.
			if ( isset( $item['extras'] ) ) {
				$extras = maybe_unserialize( $item['extras'] );
				if ( is_array( $extras ) && ! empty( $extras ) ) {
					$item_data['extras'] = $this->format_item_extras( $extras );
				}
			}

			// Add fees if available.
			if ( isset( $item['fees'] ) ) {
				$fees = maybe_unserialize( $item['fees'] );
				if ( is_array( $fees ) && ! empty( $fees ) ) {
					$item_data['fees'] = $this->format_item_fees( $fees );
				}
			}

			$items_data[] = $item_data;
		}

		return $items_data;
	}

	/**
	 * Format item extras for response.
	 *
	 * @param array $extras Raw extras data.
	 * @return array Formatted extras.
	 */
	protected function format_item_extras( $extras ) {
		$formatted = array();

		foreach ( $extras as $extra_id => $extra_data ) {
			if ( is_array( $extra_data ) ) {
				$formatted[] = array(
					'id'       => absint( $extra_id ),
					'name'     => isset( $extra_data['name'] ) ? $extra_data['name'] : '',
					'quantity' => isset( $extra_data['qty'] ) ? absint( $extra_data['qty'] ) : 1,
					'price'    => isset( $extra_data['price'] ) ? absint( $extra_data['price'] ) : 0,
				);
			}
		}

		return $formatted;
	}

	/**
	 * Format item fees for response.
	 *
	 * @param array $fees Raw fees data.
	 * @return array Formatted fees.
	 */
	protected function format_item_fees( $fees ) {
		$formatted = array();

		foreach ( $fees as $fee_key => $fee_data ) {
			if ( is_array( $fee_data ) ) {
				$formatted[] = array(
					'key'   => $fee_key,
					'name'  => isset( $fee_data['name'] ) ? $fee_data['name'] : '',
					'price' => isset( $fee_data['price'] ) ? absint( $fee_data['price'] ) : 0,
				);
			}
		}

		return $formatted;
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['status'] = array(
			'description'       => __( 'Filter by reservation status.', 'wp-hotelier' ),
			'type'              => 'string',
			'enum'              => array( 'pending', 'on-hold', 'confirmed', 'completed', 'cancelled', 'refunded', 'failed' ),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['checkin_after'] = array(
			'description'       => __( 'Filter reservations with check-in after this date (YYYY-MM-DD).', 'wp-hotelier' ),
			'type'              => 'string',
			'format'            => 'date',
			'validate_callback' => array( $this, 'validate_date_param' ),
			'sanitize_callback' => array( $this, 'sanitize_date_param' ),
		);

		$params['checkin_before'] = array(
			'description'       => __( 'Filter reservations with check-in before this date (YYYY-MM-DD).', 'wp-hotelier' ),
			'type'              => 'string',
			'format'            => 'date',
			'validate_callback' => array( $this, 'validate_date_param' ),
			'sanitize_callback' => array( $this, 'sanitize_date_param' ),
		);

		/**
		 * Filter collection parameters for the reservations endpoint.
		 *
		 * @param array $params Collection parameters.
		 */
		return apply_filters( 'hotelier_rest_reservations_collection_params', $params );
	}

	/**
	 * Get the reservation's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'reservation',
			'type'       => 'object',
			'properties' => array(
				'id'                   => array(
					'description' => __( 'Unique identifier for the reservation.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'reservation_number'   => array(
					'description' => __( 'Reservation number for display.', 'wp-hotelier' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'status'               => array(
					'description' => __( 'Reservation status.', 'wp-hotelier' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'date_created'         => array(
					'description' => __( 'Date the reservation was created.', 'wp-hotelier' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
				),
				'checkin'              => array(
					'description' => __( 'Check-in date.', 'wp-hotelier' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'view' ),
				),
				'checkout'             => array(
					'description' => __( 'Check-out date.', 'wp-hotelier' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'view' ),
				),
				'nights'               => array(
					'description' => __( 'Number of nights.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'guest'                => array(
					'description' => __( 'Guest information.', 'wp-hotelier' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
				),
				'subtotal'             => array(
					'description' => __( 'Subtotal amount (integer).', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'tax_total'            => array(
					'description' => __( 'Tax total (integer).', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'discount_total'       => array(
					'description' => __( 'Discount total (integer).', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'total'                => array(
					'description' => __( 'Total amount (integer).', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'deposit'              => array(
					'description' => __( 'Deposit amount (integer).', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'paid_deposit'          => array(
					'description' => __( 'Paid deposit amount (integer).', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'remain_deposit_charge' => array(
					'description' => __( 'Remain deposit charged manually (integer).', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'balance_due'           => array(
					'description' => __( 'Balance due (integer). Zero if marked as paid.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'items'                => array(
					'description' => __( 'Reservation items.', 'wp-hotelier' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

<?php
/**
 * REST API Room Types Controller.
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
 * HTL_REST_Room_Types_Controller Class.
 *
 * Handles REST API requests for room types (room_cat taxonomy).
 */
class HTL_REST_Room_Types_Controller extends HTL_REST_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'room-types';

	/**
	 * Taxonomy.
	 *
	 * @var string
	 */
	protected $taxonomy = 'room_cat';

	/**
	 * Register the routes for room types.
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
						'description' => __( 'Unique identifier for the room type.', 'wp-hotelier' ),
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
	 * Get a collection of room types.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$hide_empty = $request->get_param( 'hide_empty' );
		$orderby    = $request->get_param( 'orderby' );
		$order      = $request->get_param( 'order' );
		$parent     = $request->get_param( 'parent' );

		$args = array(
			'taxonomy'   => $this->taxonomy,
			'hide_empty' => $hide_empty,
			'orderby'    => $orderby,
			'order'      => strtoupper( $order ) === 'DESC' ? 'DESC' : 'ASC',
		);

		if ( $parent !== null ) {
			$args['parent'] = absint( $parent );
		}

		/**
		 * Filter the query arguments for room types.
		 *
		 * @param array           $args    Query arguments.
		 * @param WP_REST_Request $request Request object.
		 */
		$args = apply_filters( 'hotelier_rest_room_types_query', $args, $request );

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			return $terms;
		}

		$data = array();

		foreach ( $terms as $term ) {
			$item_data = $this->prepare_item_for_response( $term, $request );
			$data[]    = $this->prepare_response_for_collection( $item_data );
		}

		$response = rest_ensure_response( $data );

		// Add total header.
		$response->header( 'X-WP-Total', count( $terms ) );

		return $response;
	}

	/**
	 * Get a single room type.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$id   = absint( $request->get_param( 'id' ) );
		$term = get_term( $id, $this->taxonomy );

		if ( ! $term || is_wp_error( $term ) ) {
			return new WP_Error(
				'hotelier_rest_room_type_invalid_id',
				__( 'Invalid room type ID.', 'wp-hotelier' ),
				array( 'status' => 404 )
			);
		}

		$data     = $this->prepare_item_for_response( $term, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Prepare a single room type output for response.
	 *
	 * @param WP_Term         $term    Term object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response data.
	 */
	public function prepare_item_for_response( $term, $request ) {
		$data = array(
			'id'          => $term->term_id,
			'name'        => $term->name,
			'slug'        => $term->slug,
			'description' => $term->description,
			'count'       => $term->count,
			'parent'      => $term->parent,
		);

		// Get term image if set (via ACF or similar).
		$image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( $image_id ) {
			$image         = wp_get_attachment_image_src( $image_id, 'full' );
			$data['image'] = $image ? $image[0] : null;
		}

		// Add links.
		$data['_links'] = $this->prepare_term_links( $term );

		/**
		 * Filter room type data before response.
		 *
		 * @param array           $data    Room type data.
		 * @param WP_Term         $term    Term object.
		 * @param WP_REST_Request $request Request object.
		 */
		$data = apply_filters( 'hotelier_rest_prepare_room_type', $data, $term, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare links for term response.
	 *
	 * @param WP_Term $term Term object.
	 * @return array Links for the response.
	 */
	protected function prepare_term_links( $term ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $term->term_id ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		if ( $term->parent ) {
			$links['parent'] = array(
				'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $term->parent ) ),
			);
		}

		return $links;
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context'    => $this->get_context_param( array( 'default' => 'view' ) ),
			'hide_empty' => array(
				'description'       => __( 'Whether to hide categories with no rooms.', 'wp-hotelier' ),
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			),
			'orderby'    => array(
				'description'       => __( 'Sort collection by attribute.', 'wp-hotelier' ),
				'type'              => 'string',
				'default'           => 'name',
				'enum'              => array( 'id', 'name', 'slug', 'count', 'term_group' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'order'      => array(
				'description'       => __( 'Order sort attribute ascending or descending.', 'wp-hotelier' ),
				'type'              => 'string',
				'default'           => 'asc',
				'enum'              => array( 'asc', 'desc' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'parent'     => array(
				'description'       => __( 'Filter by parent term ID.', 'wp-hotelier' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Get the room type's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'room-type',
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'Unique identifier for the room type.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'name'        => array(
					'description' => __( 'Room type name.', 'wp-hotelier' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'slug'        => array(
					'description' => __( 'Room type slug.', 'wp-hotelier' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'description' => array(
					'description' => __( 'Room type description.', 'wp-hotelier' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'count'       => array(
					'description' => __( 'Number of rooms in this category.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'parent'      => array(
					'description' => __( 'Parent term ID.', 'wp-hotelier' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

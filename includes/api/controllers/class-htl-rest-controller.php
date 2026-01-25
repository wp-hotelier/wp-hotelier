<?php
/**
 * Abstract REST Controller.
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
 * HTL_REST_Controller Class.
 *
 * Abstract base class for all REST API controllers.
 */
abstract class HTL_REST_Controller extends WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'hotelier/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '';

	/**
	 * Add pagination headers to response.
	 *
	 * @param WP_REST_Request  $request  Request object.
	 * @param WP_REST_Response $response Response object.
	 * @param int              $total    Total items.
	 * @param int              $per_page Items per page.
	 * @return WP_REST_Response Modified response.
	 */
	protected function add_pagination_headers( $request, $response, $total, $per_page ) {
		$total       = absint( $total );
		$per_page    = absint( $per_page );
		$total_pages = $per_page > 0 ? ceil( $total / $per_page ) : 0;
		$page        = absint( $request->get_param( 'page' ) );

		if ( $page < 1 ) {
			$page = 1;
		}

		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', $total_pages );

		$base = add_query_arg( urlencode_deep( $request->get_query_params() ), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_page = $page - 1;
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}

		if ( $page < $total_pages ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Get collection parameters.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
			'page'     => array(
				'description'       => __( 'Current page of the collection.', 'wp-hotelier' ),
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-hotelier' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param mixed            $item    Item data.
	 * @param WP_REST_Request  $request Request object.
	 * @return array Links for the response.
	 */
	protected function prepare_links( $item, $request ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $item['id'] ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Validate a date parameter.
	 *
	 * @param string          $value   Date value (YYYY-MM-DD).
	 * @param WP_REST_Request $request Request object.
	 * @param string          $param   Parameter name.
	 * @return bool|WP_Error True if valid, WP_Error if not.
	 */
	public function validate_date_param( $value, $request, $param ) {
		if ( ! htl_rest_validate_date( $value ) ) {
			return new WP_Error(
				'rest_invalid_param',
				/* translators: %s: parameter name */
				sprintf( __( 'Invalid date format for %s. Use YYYY-MM-DD.', 'wp-hotelier' ), $param ),
				array( 'status' => 400 )
			);
		}

		return true;
	}

	/**
	 * Sanitize a date parameter.
	 *
	 * @param string $value Date value.
	 * @return string Sanitized date.
	 */
	public function sanitize_date_param( $value ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Get the room price type label.
	 *
	 * @param string $price_type Price type key.
	 * @return string Price type label.
	 */
	protected function get_price_type_label( $price_type ) {
		$types = array(
			'global'         => 'global',
			'per_day'        => 'per_day',
			'seasonal_price' => 'seasonal_price',
		);

		return isset( $types[ $price_type ] ) ? $types[ $price_type ] : 'global';
	}
}

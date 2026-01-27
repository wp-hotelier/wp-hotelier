<?php
/**
 * REST API Info Controller.
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
 * HTL_REST_Info_Controller Class.
 *
 * Handles REST API requests for hotel information and settings.
 */
class HTL_REST_Info_Controller extends HTL_REST_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'info';

	/**
	 * Register the routes for info.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_info' ),
					'permission_callback' => array( $this, 'get_info_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		// Route for updating global disabled dates
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/disabled-dates',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_disabled_dates' ),
					'permission_callback' => array( $this, 'update_disabled_dates_permissions_check' ),
					'args'                => array(
						'schema' => array(
							'description' => __( 'Array of disabled date rules.', 'wp-hotelier' ),
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
			)
		);
	}

	/**
	 * Check if a given request has access to read info.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function get_info_permissions_check( $request ) {
		return HTL_REST_Authentication::check_public_permission();
	}

	/**
	 * Check if a given request has access to update disabled dates.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function update_disabled_dates_permissions_check( $request ) {
		return HTL_REST_Authentication::check_manage_hotelier_permission();
	}

	/**
	 * Update global disabled dates settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_disabled_dates( $request ) {
		// Check if Disable Dates extension is active
		if ( ! class_exists( 'Hotelier_Disable_Dates' ) ) {
			return new WP_Error(
				'hotelier_rest_extension_required',
				__( 'The Disable Dates extension is required to update disabled dates settings.', 'wp-hotelier' ),
				array( 'status' => 400 )
			);
		}

		$schema = $request->get_param( 'schema' );

		// Validate and save schema if provided
		if ( $schema !== null ) {
			if ( ! is_array( $schema ) ) {
				return new WP_Error(
					'hotelier_rest_invalid_param',
					__( 'The schema parameter must be an array.', 'wp-hotelier' ),
					array( 'status' => 400 )
				);
			}

			$validated_schema = array();
			$index            = 0;

			foreach ( $schema as $rule ) {
				if ( ! isset( $rule['from'] ) || ! HTL_Formatting_Helper::is_valid_date( $rule['from'] ) ) {
					return new WP_Error(
						'hotelier_rest_invalid_dates',
						__( 'Each rule must have a valid "from" date in YYYY-MM-DD format.', 'wp-hotelier' ),
						array( 'status' => 400 )
					);
				}

				$single_day = isset( $rule['single_day'] ) ? (bool) $rule['single_day'] : false;
				$to_date    = '';

				// Validate 'to' date if not a single day
				if ( ! $single_day ) {
					if ( isset( $rule['to'] ) && ! empty( $rule['to'] ) ) {
						if ( ! HTL_Formatting_Helper::is_valid_date( $rule['to'] ) ) {
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

			// Save the schema
			htl_update_option( 'disabled_dates_schema', $validated_schema );
		}

		// Clear transients
		delete_transient( 'hotelier_global_disabled_dates' );
		delete_transient( 'hotelier_room_disabled_dates' );

		/**
		 * Fires after global disabled dates settings are updated via REST API.
		 *
		 * @param WP_REST_Request $request The request object.
		 */
		do_action( 'hotelier_rest_update_disabled_dates_settings', $request );

		// Return the updated disabled dates info
		$data = array(
			'success'        => true,
			'disabled_dates' => $this->get_disabled_dates_info(),
		);

		return rest_ensure_response( $data );
	}

	/**
	 * Get hotel information and settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response
	 */
	public function get_info( $request ) {
		$data = array(
			'hotel'           => $this->get_hotel_info(),
			'currency'        => $this->get_currency_info(),
			'booking'         => $this->get_booking_info(),
			'tax'             => $this->get_tax_info(),
			'seasonal_prices' => $this->get_seasonal_prices_info(),
			'extensions'      => htl_rest_get_active_extensions(),
		);

		// Add disabled dates info if extension is active.
		if ( class_exists( 'HTL_Disable_Dates' ) ) {
			$data['disabled_dates'] = $this->get_disabled_dates_info();
		}

		/**
		 * Filter hotel info data before response.
		 *
		 * @param array           $data    Info data.
		 * @param WP_REST_Request $request Request object.
		 */
		$data = apply_filters( 'hotelier_rest_prepare_info', $data, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Get hotel information.
	 *
	 * @return array Hotel info.
	 */
	protected function get_hotel_info() {
		return array(
			'name'           => HTL_Info::get_hotel_name(),
			'address'        => HTL_Info::get_hotel_address(),
			'postcode'       => HTL_Info::get_hotel_postcode(),
			'locality'       => HTL_Info::get_hotel_locality(),
			'telephone'      => HTL_Info::get_hotel_telephone(),
			'fax'            => HTL_Info::get_hotel_fax(),
			'email'          => HTL_Info::get_hotel_email(),
			'checkin_time'   => HTL_Info::get_hotel_checkin(),
			'checkout_time'  => HTL_Info::get_hotel_checkout(),
			'pets_message'   => HTL_Info::get_hotel_pets_message(),
			'accepted_cards' => HTL_Info::get_hotel_accepted_credit_cards(),
		);
	}

	/**
	 * Get currency information.
	 *
	 * @return array Currency info.
	 */
	protected function get_currency_info() {
		return array(
			'code'               => htl_get_currency(),
			'symbol'             => htl_get_currency_symbol(),
			'position'           => htl_get_option( 'currency_position', 'before' ),
			'thousand_separator' => htl_get_option( 'thousand_separator', ',' ),
			'decimal_separator'  => htl_get_option( 'decimal_separator', '.' ),
			'num_decimals'       => absint( htl_get_option( 'price_num_decimals', 2 ) ),
		);
	}

	/**
	 * Get booking information.
	 *
	 * @return array Booking info.
	 */
	protected function get_booking_info() {
		return array(
			'mode'                 => htl_get_option( 'booking_mode', 'instant-booking' ),
			'minimum_nights'       => absint( htl_get_option( 'booking_minimum_nights', 1 ) ),
			'maximum_nights'       => absint( htl_get_option( 'booking_maximum_nights', 0 ) ),
			'hold_minutes'         => absint( htl_get_option( 'booking_hold_minutes', 60 ) ),
			'arrival_time_enabled' => (bool) htl_get_option( 'booking_arrival_time', false ),
			'listing_disabled'     => (bool) htl_get_option( 'listing_disabled', false ),
			'datepicker_format'    => htl_get_option( 'datepicker_format', 'd MMMM yyyy' ),
		);
	}

	/**
	 * Get tax information.
	 *
	 * @return array Tax info.
	 */
	protected function get_tax_info() {
		return array(
			'enabled' => htl_is_tax_enabled(),
			'rate'    => htl_is_tax_enabled() ? floatval( htl_get_tax_rate() ) : 0,
		);
	}

	/**
	 * Get seasonal prices schema.
	 *
	 * @return array|null Seasonal prices schema or null if not set.
	 */
	protected function get_seasonal_prices_info() {
		$schema = htl_get_seasonal_prices_schema();

		if ( ! is_array( $schema ) || empty( $schema ) ) {
			return null;
		}

		$formatted = array();

		foreach ( $schema as $rule ) {
			if ( isset( $rule['from'] ) && isset( $rule['to'] ) ) {
				$formatted[] = array(
					'index'       => isset( $rule['index'] ) ? $rule['index'] : null,
					'from'        => $rule['from'],
					'to'          => $rule['to'],
					'every_year'  => isset( $rule['every_year'] ) ? (bool) $rule['every_year'] : false,
					'description' => isset( $rule['description'] ) ? $rule['description'] : '',
				);
			}
		}

		return $formatted;
	}

	/**
	 * Get global disabled dates info.
	 *
	 * @return array Disabled dates info.
	 */
	protected function get_disabled_dates_info() {
		$schema = htl_get_option( 'disabled_dates_schema', array() );

		$data = array(
			'allow_checkout_first_disabled_date' => (bool) htl_get_option( 'allow_checkout_first_disabled_date', true ),
			'hide_from_datepicker'               => (bool) htl_get_option( 'hide_disabled_dates_from_datepicker', true ),
			'schema'                             => array(),
			'dates'                              => array(),
		);

		// Format the schema (rules).
		if ( is_array( $schema ) && ! empty( $schema ) ) {
			foreach ( $schema as $rule ) {
				if ( isset( $rule['from'] ) ) {
					$formatted_rule = array(
						'index'      => isset( $rule['index'] ) ? $rule['index'] : null,
						'from'       => $rule['from'],
						'to'         => isset( $rule['to'] ) && ! empty( $rule['to'] ) ? $rule['to'] : null,
						'single_day' => isset( $rule['single_day'] ) ? (bool) $rule['single_day'] : false,
						'every_year' => isset( $rule['every_year'] ) ? (bool) $rule['every_year'] : false,
					);

					$data['schema'][] = $formatted_rule;
				}
			}
		}

		// Get calculated dates array.
		if ( function_exists( 'HTL_Disable_Dates' ) ) {
			$data['dates'] = array_values( HTL_Disable_Dates()->get_global_disabled_dates() );
		}

		return $data;
	}

	/**
	 * Get the info's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'info',
			'type'       => 'object',
			'properties' => array(
				'hotel'           => array(
					'description' => __( 'Hotel information.', 'wp-hotelier' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'properties'  => array(
						'name'           => array( 'type' => 'string' ),
						'address'        => array( 'type' => 'string' ),
						'postcode'       => array( 'type' => 'string' ),
						'locality'       => array( 'type' => 'string' ),
						'telephone'      => array( 'type' => 'string' ),
						'fax'            => array( 'type' => 'string' ),
						'email'          => array( 'type' => 'string' ),
						'checkin_time'   => array( 'type' => 'string' ),
						'checkout_time'  => array( 'type' => 'string' ),
						'pets_message'   => array( 'type' => 'string' ),
						'accepted_cards' => array( 'type' => 'array' ),
					),
				),
				'currency'        => array(
					'description' => __( 'Currency settings.', 'wp-hotelier' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'properties'  => array(
						'code'               => array( 'type' => 'string' ),
						'symbol'             => array( 'type' => 'string' ),
						'position'           => array( 'type' => 'string' ),
						'thousand_separator' => array( 'type' => 'string' ),
						'decimal_separator'  => array( 'type' => 'string' ),
						'num_decimals'       => array( 'type' => 'integer' ),
					),
				),
				'booking'         => array(
					'description' => __( 'Booking settings.', 'wp-hotelier' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'properties'  => array(
						'mode'           => array( 'type' => 'string' ),
						'minimum_nights' => array( 'type' => 'integer' ),
						'maximum_nights' => array( 'type' => 'integer' ),
						'hold_minutes'   => array( 'type' => 'integer' ),
					),
				),
				'tax'             => array(
					'description' => __( 'Tax settings.', 'wp-hotelier' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'properties'  => array(
						'enabled' => array( 'type' => 'boolean' ),
						'rate'    => array( 'type' => 'number' ),
					),
				),
				'seasonal_prices' => array(
					'description' => __( 'Seasonal prices schema.', 'wp-hotelier' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
				),
				'extensions'      => array(
					'description' => __( 'Active Hotelier extensions.', 'wp-hotelier' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

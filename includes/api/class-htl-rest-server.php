<?php
/**
 * REST API Server.
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
 * HTL_REST_Server Class.
 *
 * Main REST API server class that initializes all REST API controllers.
 */
class HTL_REST_Server {

	/**
	 * The single instance of the class.
	 *
	 * @var HTL_REST_Server
	 */
	private static $_instance = null;

	/**
	 * REST API namespaces and endpoints.
	 *
	 * @var array
	 */
	protected $controllers = array();

	/**
	 * Main HTL_REST_Server Instance.
	 *
	 * Ensures only one instance of HTL_REST_Server is loaded or can be loaded.
	 *
	 * @static
	 * @return HTL_REST_Server - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		// Authentication.
		include_once HTL_PLUGIN_DIR . 'includes/api/class-htl-rest-authentication.php';

		// Helper functions.
		include_once HTL_PLUGIN_DIR . 'includes/api/htl-rest-functions.php';

		// Abstract controller.
		include_once HTL_PLUGIN_DIR . 'includes/api/controllers/class-htl-rest-controller.php';

		// Controllers.
		include_once HTL_PLUGIN_DIR . 'includes/api/controllers/class-htl-rest-rooms-controller.php';
		include_once HTL_PLUGIN_DIR . 'includes/api/controllers/class-htl-rest-room-types-controller.php';
		include_once HTL_PLUGIN_DIR . 'includes/api/controllers/class-htl-rest-availability-controller.php';
		include_once HTL_PLUGIN_DIR . 'includes/api/controllers/class-htl-rest-reservations-controller.php';
		include_once HTL_PLUGIN_DIR . 'includes/api/controllers/class-htl-rest-info-controller.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		$this->controllers = array(
			'rooms'        => new HTL_REST_Rooms_Controller(),
			'room-types'   => new HTL_REST_Room_Types_Controller(),
			'availability' => new HTL_REST_Availability_Controller(),
			'reservations' => new HTL_REST_Reservations_Controller(),
			'info'         => new HTL_REST_Info_Controller(),
		);

		foreach ( $this->controllers as $controller ) {
			$controller->register_routes();
		}

		/**
		 * Fires after all REST API routes have been registered.
		 *
		 * This hook allows extensions to register additional routes.
		 *
		 * @param HTL_REST_Server $this The REST server instance.
		 */
		do_action( 'hotelier_rest_api_init', $this );
	}

	/**
	 * Get API namespaces.
	 *
	 * @return array
	 */
	public function get_namespaces() {
		return array( 'hotelier/v1' );
	}

	/**
	 * Get a specific controller.
	 *
	 * @param string $name Controller name.
	 * @return HTL_REST_Controller|null
	 */
	public function get_controller( $name ) {
		return isset( $this->controllers[ $name ] ) ? $this->controllers[ $name ] : null;
	}

	/**
	 * Get all controllers.
	 *
	 * @return array
	 */
	public function get_controllers() {
		return $this->controllers;
	}
}

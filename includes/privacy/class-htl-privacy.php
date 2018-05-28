<?php
/**
 * Privacy Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Privacy' ) ) :

/**
 * HTL_Privacy Class
 */
class HTL_Privacy {

	/**
	 * List of exporters.
	 *
	 * @var array
	 */
	protected $exporters = array();

	/**
	 * List of erasers.
	 *
	 * @var array
	 */
	protected $erasers = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		// Include supporting classes.
		include_once 'class-htl-privacy-erasers.php';
		include_once 'class-htl-privacy-exporters.php';

		$this->init();
	}

	/**
	 * Hook in events.
	 */
	protected function init() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ), 5 );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_erasers' ) );

		$this->add_exporter( 'hotelier-guest-reservations', __( 'Guest Reservations', 'wp-hotelier' ), array( 'HTL_Privacy_Exporters', 'reservation_data_exporter' ) );
		$this->add_eraser( 'hotelier-guest-reservations', __( 'Guest Reservations', 'wp-hotelier' ), array( 'HTL_Privacy_Erasers', 'reservation_data_eraser' ) );

		// Handles custom anonomization types not included in core.
		add_filter( 'wp_privacy_anonymize_data', array( $this, 'anonymize_custom_data_types' ), 10, 3 );
	}

	/**
	 * Integrate this exporter implementation within the WordPress core exporters.
	 *
	 * @param array $exporters List of exporter callbacks.
	 * @return array
	 */
	public function register_exporters( $exporters = array() ) {
		foreach ( $this->exporters as $id => $exporter ) {
			$exporters[ $id ] = $exporter;
		}

		return $exporters;
	}

	/**
	 * Integrate this eraser implementation within the WordPress core erasers.
	 *
	 * @param array $erasers List of eraser callbacks.
	 * @return array
	 */
	public function register_erasers( $erasers = array() ) {
		foreach ( $this->erasers as $id => $eraser ) {
			$erasers[ $id ] = $eraser;
		}

		return $erasers;
	}

	/**
	 * Add exporter to list of exporters.
	 *
	 * @param string $id       ID of the Exporter.
	 * @param string $name     Exporter name.
	 * @param string $callback Exporter callback.
	 */
	public function add_exporter( $id, $name, $callback ) {
		$this->exporters[ $id ] = array(
			'exporter_friendly_name' => $name,
			'callback'               => $callback,
		);

		return $this->exporters;
	}

	/**
	 * Add eraser to list of erasers.
	 *
	 * @param string $id       ID of the Eraser.
	 * @param string $name     Exporter name.
	 * @param string $callback Exporter callback.
	 */
	public function add_eraser( $id, $name, $callback ) {
		$this->erasers[ $id ] = array(
			'eraser_friendly_name' => $name,
			'callback'             => $callback,
		);

		return $this->erasers;
	}

	/**
	 * Handle some custom types of data and anonymize them.
	 *
	 * @param string $anonymous Anonymized string.
	 * @param string $type Type of data.
	 * @param string $data The data being anonymized.
	 * @return string Anonymized string.
	 */
	public function anonymize_custom_data_types( $anonymous, $type, $data ) {
		switch ( $type ) {
			case 'phone':
				$anonymous = preg_replace( '/\d/u', '0', $data );
				break;
			case 'numeric_id':
				$anonymous = 0;
				break;
		}

		return $anonymous;
	}
}

endif;

new HTL_Privacy();

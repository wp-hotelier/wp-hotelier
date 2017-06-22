<?php
/**
 * Hotelier Meta Boxes.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Meta_Boxes' ) ) :

/**
 * HTL_Admin_Meta_Boxes Class
 */
class HTL_Admin_Meta_Boxes {

	/**
	 * Room meta boxes.
	 */
	private $room_meta_boxes = array();

	/**
	 * Reservation meta boxes.
	 */
	private $reservation_meta_boxes = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->list_room_meta_boxes();
		$this->list_reservation_meta_boxes();

		// Actions
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
		add_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Data::save', 20, 2 );
		add_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Save::save', 30, 2 );

		// Filters
		add_filter( 'hotelier_meta_box_save_text', array( $this, 'sanitize_text' ) );
		add_filter( 'hotelier_meta_box_save_textarea', array( $this, 'sanitize_textarea' ) );
		add_filter( 'hotelier_meta_box_save_number', array( $this, 'sanitize_number' ) );
		add_filter( 'hotelier_meta_box_save_select', array( $this, 'sanitize_select' ) );
		add_filter( 'hotelier_meta_box_save_price', array( $this, 'sanitize_price' ) );
		add_filter( 'hotelier_meta_box_save_price_per_day', array( $this, 'sanitize_price_per_day' ) );
		add_filter( 'hotelier_meta_box_save_seasonal_price', array( $this, 'sanitize_seasonal_price' ) );
		add_filter( 'hotelier_meta_box_save_room_conditions', array( $this, 'sanitize_room_conditions' ) );
		add_filter( 'hotelier_meta_box_save_room_variations', array( $this, 'sanitize_room_variations' ) );

		$this->includes();
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		include_once( 'class-htl-meta-box-room-settings.php' );
		include_once( 'class-htl-meta-box-room-images.php' );
		include_once( 'class-htl-meta-box-room-excerpt.php' );
		include_once( 'class-htl-meta-box-reservation-data.php' );
		include_once( 'class-htl-meta-box-reservation-items.php' );
		include_once( 'class-htl-meta-box-reservation-save.php' );
		include_once( 'class-htl-meta-box-reservation-notes.php' );
	}

	/**
	 * Room meta boxes list.
	 */
	private function list_room_meta_boxes() {
		$fields = array(
			'_max_guests'              => 'select',
			'_max_children'            => 'select',
			'_bed_size'                => 'text',
			'_room_size'               => 'text',
			'_stock_rooms'             => 'select',
			'_room_type'               => 'select',
			'_price_type'              => 'select',
			'_regular_price'           => 'price',
			'_sale_price'              => 'price',
			'_regular_price_day'       => 'price_per_day',
			'_sale_price_day'          => 'price_per_day',
			'_seasonal_base_price'     => 'price',
			'_seasonal_price'          => 'seasonal_price',
			'_require_deposit'         => 'checkbox',
			'_deposit_amount'          => 'number',
			'_non_cancellable'         => 'checkbox',
			'_room_conditions'         => 'room_conditions',
			'_room_variations'         => 'room_variations',
			'_room_image_gallery'      => 'text',
			'_room_additional_details' => 'textarea',
		);

		$this->room_meta_boxes = apply_filters( 'hotelier_room_meta_boxes', $fields );
	}

	/**
	 * Reservation meta boxes list.
	 */
	private function list_reservation_meta_boxes() {
		$fields = array(
			'_guest_first_name' => 'text',
			'_guest_last_name'  => 'text',
			'_guest_email'      => 'text',
			'_guest_telephone'  => 'text',
			'_guest_country'    => 'text',
			'_guest_address1'   => 'text',
			'_guest_address2'   => 'text',
			'_guest_city'       => 'text',
			'_guest_state'      => 'text',
			'_guest_postcode'   => 'text'
		);

		$this->reservation_meta_boxes = apply_filters( 'hotelier_reservation_meta_boxes', $fields );
	}

	/**
	 * Add Hotelier meta boxes
	 */
	public function add_meta_boxes() {
		// Rooms
		add_meta_box( 'hotelier-room-settings', esc_html__( 'Room Settings', 'wp-hotelier' ), 'HTL_Meta_Box_Room_Settings::output', 'room', 'normal', 'high' );
		add_meta_box( 'hotelier-room-images', esc_html__( 'Room Gallery', 'wp-hotelier' ), 'HTL_Meta_Box_Room_Images::output', 'room', 'side', 'low' );
		add_meta_box( 'postexcerpt', esc_html__( 'Room Short Description', 'wp-hotelier' ), 'HTL_Meta_Box_Room_Excerpt::output', 'room', 'normal' );

		// Reservations
		add_meta_box( 'hotelier-reservation-data', esc_html__( 'Reservation Settings', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Data::output', 'room_reservation', 'normal', 'high' );
		add_meta_box( 'hotelier-reservation-items', esc_html__( 'Rooms', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Items::output', 'room_reservation', 'normal', 'high' );
		add_meta_box( 'hotelier-reservation-save', esc_html__( 'Save Reservation', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Save::output', 'room_reservation', 'side', 'high' );
		add_meta_box( 'hotelier-reservation-notes', esc_html__( 'Reservation Notes', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Notes::output', 'room_reservation', 'side', 'default' );
	}

	/**
	 * Save Hotelier meta boxes
	 */
	public function save_meta_boxes( $post_id, $post ) {

		// Check the nonce
		if ( empty( $_POST[ 'hotelier_meta_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'hotelier_meta_nonce' ], 'hotelier_save_data' ) ) {
			return;
		}
		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array();

		// Get room meta boxes
		if ( isset( $post->post_type ) && 'room' == $post->post_type ) {
			$fields = $this->room_meta_boxes;

			// clear transient
			delete_transient( 'hotelier_room_ids' );

		// Get reservation meta boxes
		} elseif ( isset( $post->post_type ) && 'room_reservation' == $post->post_type ) {
			$fields = $this->reservation_meta_boxes;
		}

		foreach ( $fields as $field => $type ) {
			if ( ! empty( $_POST[ $field ] ) ) {
				// Each field is passed to a custom filter that validates the input
				$data = apply_filters( 'hotelier_meta_box_save_' . $type, $_POST[ $field ] );

				// In the previous line, data is passed to a filter. Malicious code can
				// use that filter to alter the data. So we do a final validation here.
				$data = HTL_Formatting_Helper::clean_input( $data );

				update_post_meta( $post_id, $field, $data );
			} else {
				delete_post_meta( $post_id, $field );
			}
		}

		if ( isset( $post->post_type ) && 'room_reservation' == $post->post_type ) {
			do_action( 'hotelier_process_room_reservation_meta', $post_id, $post );
		}
	}

	/**
	 * Sanitize text input
	 */
	public function sanitize_text( $data ) {
		return sanitize_text_field( $data );
    }

    /**
	 * Sanitize textarea input
	 */
	public function sanitize_textarea( $data ) {
		return sanitize_text_field( $data );
	}

	/**
	 * Sanitize number
	 */
	public function sanitize_number( $number ) {
		return absint( $number );
	}

	/**
	 * Sanitize select input
	 */
	public function sanitize_select( $data ) {
		return sanitize_key( $data );
	}

	/**
	 * Sanitize price amount
	 */
	public function sanitize_price( $price ) {
		if ( empty( $price ) ) {
			return;
		}

		return HTL_Formatting_Helper::sanitize_amount( $price );
	}

	/**
	 * Sanitize price per day
	 */
	public function sanitize_price_per_day( $prices ) {
		if ( is_array( $prices ) ) {
			foreach ( $prices as $key => $value ) {
				if ( empty( $prices[ $key ] ) ) {
					return;
				}
				$prices[ $key ] = HTL_Formatting_Helper::sanitize_amount( $value );
			}
		}

		return $prices;
	}

	/**
	 * Sanitize seasonal price
	 */
	public function sanitize_seasonal_price( $prices ) {
		if ( is_array( $prices ) ) {
			foreach ( $prices as $key => $value ) {
				$prices[ $key ] = HTL_Formatting_Helper::sanitize_amount( $value );
			}
		}

		return $prices;
	}

	/**
	 * Sanitize room conditions
	 */
	public function sanitize_room_conditions( $conditions ) {
		if ( is_array( $conditions ) ) {
			$count = count( $conditions );

			// ensures conditions are correctly mapped to an array starting with an index of 1
			uasort( $conditions, function( $a, $b ) {
				return $a[ 'index' ] - $b[ 'index' ];
			});

			$conditions = array_combine( range( 1, count( $conditions ) ), array_values( $conditions ) );

			foreach ( $conditions as $id => $condition ) {
				if ( empty( $condition[ 'name' ] ) && ( $count > 1 ) ) {
					unset( $conditions[ $id ] );
					$count --;
					continue;
				}

				$conditions[ $id ][ 'name' ] = $this->sanitize_text( $condition[ 'name' ] );
			}

		}

		return array_combine( range( 1, count( $conditions ) ), array_values( $conditions ) );
	}

	/**
	 * Sanitize variations
	 */
	public function sanitize_room_variations( $variations ) {
		global $post;

		// Store an array of rated IDs (terms of the taxonomy 'room_rate')
		$term_ids = array();

		// First check if we are saving a variable room
		if ( $_POST[ '_room_type' ] === 'variable_room' ) {

			if ( is_array( $variations ) ) {

				// Ensures variations are correctly mapped to an array starting with an index of 1
				uasort( $variations, function( $a, $b ) {
					return $a[ 'index' ] - $b[ 'index' ];
				});

				$variations = array_combine( range( 1, count( $variations ) ), array_values( $variations ) );

				foreach ( $variations as $id => $variation ) {
					$keys = apply_filters( 'hotelier_room_variation_keys', array(
						'price_type'          => 'select',
						'regular_price'       => 'price',
						'sale_price'          => 'price',
						'price_day'           => 'price_per_day',
						'sale_price_day'      => 'price_per_day',
						'seasonal_base_price' => 'price',
						'seasonal_price'      => 'seasonal_price',
						'room_conditions'     => 'room_conditions',
						'room_rate'           => 'select',
						'deposit_amount'      => 'select',
					) );

					foreach ( $keys as $key => $validation ) {
						if ( isset( $variations[ $id ][ $key ] ) ) {
							$variations[ $id ][ $key ] = call_user_func( array( $this, 'sanitize_' . $validation ), $variations[ $id ][ $key ] );
						}
					}

					// Get room_rate ID ('term_id')
					$term = term_exists( $variations[ $id ][ 'room_rate' ], 'room_rate' );
					$term = absint( $term[ 'term_id' ] );

					if ( $term !== 0 && $term !== null ) {
						$term_ids[] = $term;
					}
				}
			}

		} else {
			$variations = array();
		}

		// Assign rate terms to the post (empty if it is standard room)
		wp_set_object_terms( $post->ID, $term_ids, 'room_rate' );

		return $variations;
	}
}

endif;

new HTL_Admin_Meta_Boxes();

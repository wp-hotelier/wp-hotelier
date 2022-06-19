<?php
/**
 * Hotelier Meta Boxes.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.6.0
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
	 * Coupon meta boxes.
	 */
	private $coupon_meta_boxes = array();

	/**
	 * Extra meta boxes.
	 */
	private $extra_meta_boxes = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->list_room_meta_boxes();
		$this->list_reservation_meta_boxes();
		$this->list_coupon_meta_boxes();
		$this->list_extra_meta_boxes();

		// Actions
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
		add_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Data::save', 20, 2 );
		add_action( 'hotelier_process_room_reservation_meta', 'HTL_Meta_Box_Reservation_Save::save', 30, 2 );
		add_action( 'admin_notices', 'HTL_Meta_Box_Reservation_Save::print_notices' );

		// Filters
		add_filter( 'hotelier_meta_box_save_text', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_text' ) );
		add_filter( 'hotelier_meta_box_save_textarea', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_textarea' ) );
		add_filter( 'hotelier_meta_box_save_number', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_number' ) );
		add_filter( 'hotelier_meta_box_save_select', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_select' ) );
		add_filter( 'hotelier_meta_box_save_multiselect', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_multiselect' ) );
		add_filter( 'hotelier_meta_box_save_checkbox', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_checkbox' ) );
		add_filter( 'hotelier_meta_box_save_price', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_price' ) );
		add_filter( 'hotelier_meta_box_save_price_per_day', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_price_per_day' ) );
		add_filter( 'hotelier_meta_box_save_switch', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_switch' ) );
		add_filter( 'hotelier_meta_box_save_seasonal_price', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_seasonal_price' ) );
		add_filter( 'hotelier_meta_box_save_multi_text', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_multi_text' ) );
		add_filter( 'hotelier_meta_box_save_room_variations', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_room_variations' ) );
		add_filter( 'hotelier_meta_box_save_date', array( 'HTL_Admin_Meta_Boxes_Validation', 'sanitize_date' ) );

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
		include_once( 'class-htl-meta-box-coupon-settings.php' );
		include_once( 'class-htl-meta-box-extra-settings.php' );
	}

	/**
	 * Room meta boxes list.
	 */
	private function list_room_meta_boxes() {
		$fields = array(
			'_room_type'               => 'switch',
			'_max_guests'              => 'number',
			'_max_children'            => 'number',
			'_bed_size'                => 'text',
			'_room_size'               => 'number',
			'_beds'                    => 'number',
			'_bathrooms'               => 'number',
			'_stock_rooms'             => 'number',
			'_show_extra_settings'     => 'checkbox',
			'_room_additional_details' => 'textarea',
			'_price_type'              => 'switch',
			'_regular_price'           => 'price',
			'_sale_price'              => 'price',
			'_regular_price_day'       => 'price_per_day',
			'_sale_price_day'          => 'price_per_day',
			'_seasonal_base_price'     => 'price',
			'_seasonal_price'          => 'seasonal_price',
			'_require_deposit'         => 'checkbox',
			'_deposit_amount'          => 'select',
			'_non_cancellable'         => 'checkbox',
			'_room_conditions'         => 'multi_text',
			'_room_variations'         => 'room_variations',
			'_room_image_gallery'      => 'text',
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
	 * Coupon meta boxes list.
	 */
	private function list_coupon_meta_boxes() {
		$fields = array(
			'_coupon_enabled'           => 'switch',
			'_coupon_code'              => 'text',
			'_coupon_description'       => 'textarea',
			'_coupon_type'              => 'switch',
			'_coupon_amount_percentage' => 'number',
			'_coupon_amount_fixed'      => 'price',
			'_coupon_expiration_date'   => 'date',
		);

		$this->coupon_meta_boxes = apply_filters( 'hotelier_coupon_meta_boxes', $fields );
	}

	/**
	 * Extra meta boxes list.
	 */
	private function list_extra_meta_boxes() {
		$fields = array(
			'_extra_enabled'             => 'switch',
			'_extra_name'                => 'text',
			'_extra_description'         => 'textarea',
			'_extra_amount_type'         => 'switch',
			'_extra_amount_fixed'        => 'price',
			'_extra_amount_percentage'   => 'number',
			'_extra_type'                => 'switch',
			'_extra_guest_type'          => 'select',
			'_extra_calculate_per_night' => 'checkbox',
			'_extra_max_cost'            => 'price',
		);

		$this->extra_meta_boxes = apply_filters( 'hotelier_extra_meta_boxes', $fields );
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
		add_meta_box( 'hotelier-reservation-data', esc_html__( 'Reservation Details', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Data::output', 'room_reservation', 'normal', 'high' );
		add_meta_box( 'hotelier-reservation-items', esc_html__( 'Rooms', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Items::output', 'room_reservation', 'normal', 'high' );
		add_meta_box( 'hotelier-reservation-save', esc_html__( 'Save Reservation', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Save::output', 'room_reservation', 'side', 'high' );
		add_meta_box( 'hotelier-reservation-notes', esc_html__( 'Reservation Notes', 'wp-hotelier' ), 'HTL_Meta_Box_Reservation_Notes::output', 'room_reservation', 'side', 'default' );

		// Coupons
		add_meta_box( 'hotelier-coupon-settings', esc_html__( 'Coupon Settings', 'wp-hotelier' ), 'HTL_Meta_Box_Coupon_Settings::output', 'coupon', 'normal', 'high' );

		// Extras
		add_meta_box( 'hotelier-extra-settings', esc_html__( 'Extra Settings', 'wp-hotelier' ), 'HTL_Meta_Box_Extra_Settings::output', 'extra', 'normal', 'high' );
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

		// Get coupon meta boxes
		} elseif ( isset( $post->post_type ) && 'coupon' == $post->post_type ) {
			$fields = $this->coupon_meta_boxes;

		// Get extra meta boxes
		} elseif ( isset( $post->post_type ) && 'extra' == $post->post_type ) {
			$fields = $this->extra_meta_boxes;

			// clear transient
			delete_transient( 'hotelier_extras_ids' );
		}

		foreach ( $fields as $field => $type ) {
			if ( ! empty( $_POST[ $field ] ) ) {
				// Each field is passed to a custom filter that validates the input
				$data = apply_filters( 'hotelier_meta_box_save_' . $type, $_POST[ $field ] );

				update_post_meta( $post_id, $field, $data );
			} else {
				delete_post_meta( $post_id, $field );
			}
		}

		if ( isset( $post->post_type ) && 'room_reservation' == $post->post_type ) {
			do_action( 'hotelier_process_room_reservation_meta', $post_id, $post );
		}
	}
}

endif;

new HTL_Admin_Meta_Boxes();

<?php
/**
 * Registers post types and taxonomies.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Post_Types' ) ) :

/**
 * HTL_Post_Types Class
 */
class HTL_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 5 );
		add_action( 'init', array( $this, 'register_post_types' ), 5 );
		add_action( 'init', array( $this, 'register_post_status' ), 9 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'block_editor_can_edit_post_type' ), 10, 2 );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'block_editor_can_edit_post_type' ), 10, 2 );
	}

	/**
	 * Register core post types.
	 */
	public function register_post_types() {
		if ( post_type_exists( 'room' ) ) {
			return;
		}

		$archives = defined( 'HTL_DISABLE_ARCHIVE' ) && HTL_DISABLE_ARCHIVE ? false : true;
		$slug     = defined( 'HTL_SLUG' ) ? HTL_SLUG : 'rooms';
		$rewrite  = defined( 'HTL_DISABLE_REWRITE' ) && HTL_DISABLE_REWRITE ? false : array( 'slug' => $slug, 'with_front' => false );

		do_action( 'hotelier_register_post_type' );

		// Room Post Type
		$room_labels =  apply_filters( 'hotelier_room_labels', array(
			'name'               => esc_html_x( 'Rooms', 'room post type name', 'wp-hotelier' ),
			'singular_name'      => esc_html_x( 'Room', 'singular room post type name', 'wp-hotelier' ),
			'add_new'            => esc_html__( 'Add New', 'wp-hotelier' ),
			'add_new_item'       => esc_html__( 'Add New Room', 'wp-hotelier' ),
			'edit_item'          => esc_html__( 'Edit Room', 'wp-hotelier' ),
			'new_item'           => esc_html__( 'New Room', 'wp-hotelier' ),
			'all_items'          => esc_html__( 'All Rooms', 'wp-hotelier' ),
			'view_item'          => esc_html__( 'View Room', 'wp-hotelier' ),
			'search_items'       => esc_html__( 'Search Rooms', 'wp-hotelier' ),
			'not_found'          => esc_html__( 'No Rooms found', 'wp-hotelier' ),
			'not_found_in_trash' => esc_html__( 'No Rooms found in Trash', 'wp-hotelier' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html_x( 'Rooms', 'room post type menu name', 'wp-hotelier' )
		) );

		$room_args = array(
			'labels'             => $room_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_position'      => 46,
			'query_var'          => true,
			'rewrite'            => $rewrite,
			'capability_type'    => 'room',
			'map_meta_cap'       => true,
			'has_archive'        => $archives,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'publicize' ),
		);
		register_post_type( 'room', apply_filters( 'hotelier_room_post_type_args', $room_args ) );

		// Reservation Post Type
		$reservation_labels = apply_filters( 'hotelier_reservation_labels', array(
			'name'               => esc_html_x( 'Reservations', 'post type general name', 'wp-hotelier' ),
			'singular_name'      => esc_html_x( 'Reservation', 'post type singular name', 'wp-hotelier' ),
			'add_new'            => esc_html__( 'Add Reservation', 'wp-hotelier' ),
			'add_new_item'       => esc_html__( 'Add New Reservation', 'wp-hotelier' ),
			'edit'               => esc_html__( 'Edit', 'wp-hotelier' ),
			'edit_item'          => esc_html__( 'Edit Reservation', 'wp-hotelier' ),
			'new_item'           => esc_html__( 'New Reservation', 'wp-hotelier' ),
			'view'               => esc_html__( 'View Reservation', 'wp-hotelier' ),
			'view_item'          => esc_html__( 'View Reservation', 'wp-hotelier' ),
			'search_items'       => esc_html__( 'Search Reservations', 'wp-hotelier' ),
			'not_found'          => esc_html__( 'No Reservations found', 'wp-hotelier' ),
			'not_found_in_trash' => esc_html__( 'No Reservations found in Trash', 'wp-hotelier' ),
			'parent'             => esc_html__( 'Parent Reservation', 'wp-hotelier' ),
			'menu_name'          => esc_html_x( 'Reservations', 'admin menu name', 'wp-hotelier' )
		) );

		$reservation_args = array(
			'labels'              => $reservation_labels,
			'description'         => esc_html__( 'This is where hotel reservations are stored.', 'wp-hotelier' ),
			'public'              => false,
			'show_ui'             => true,
			'query_var'           => false,
			'rewrite'             => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => current_user_can( 'manage_hotelier' ) ? 'hotelier-settings' : true,
			'capability_type'     => 'room_reservation',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'supports'            => array( 'title', 'comments' ),
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			)
		);
		register_post_type( 'room_reservation', apply_filters( 'hotelier_reservation_post_type_args', $reservation_args ) );

		// Coupon Post Type
		if ( htl_get_option( 'enable_coupons' ) ) {
			$coupon_labels = apply_filters( 'hotelier_coupon_labels', array(
				'name'               => esc_html_x( 'Coupons', 'post type general name', 'wp-hotelier' ),
				'singular_name'      => esc_html_x( 'Coupon', 'post type singular name', 'wp-hotelier' ),
				'add_new'            => esc_html__( 'Add Coupon', 'wp-hotelier' ),
				'add_new_item'       => esc_html__( 'Add New Coupon', 'wp-hotelier' ),
				'edit'               => esc_html__( 'Edit', 'wp-hotelier' ),
				'edit_item'          => esc_html__( 'Edit Coupon', 'wp-hotelier' ),
				'new_item'           => esc_html__( 'New Coupon', 'wp-hotelier' ),
				'view'               => esc_html__( 'View Coupon', 'wp-hotelier' ),
				'view_item'          => esc_html__( 'View Coupon', 'wp-hotelier' ),
				'search_items'       => esc_html__( 'Search Coupons', 'wp-hotelier' ),
				'not_found'          => esc_html__( 'No Coupons found', 'wp-hotelier' ),
				'not_found_in_trash' => esc_html__( 'No Coupons found in Trash', 'wp-hotelier' ),
				'parent'             => esc_html__( 'Parent Coupon', 'wp-hotelier' ),
				'menu_name'          => esc_html_x( 'Coupons', 'admin menu name', 'wp-hotelier' )
			) );

			$coupon_args = array(
				'labels'              => $coupon_labels,
				'description'         => esc_html__( 'This is where you can add new coupons that guests can use to book a room.', 'wp-hotelier' ),
				'public'              => false,
				'show_ui'             => true,
				'query_var'           => false,
				'rewrite'             => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => current_user_can( 'manage_hotelier' ) ? 'hotelier-settings' : true,
				'capability_type'     => 'coupon',
				'map_meta_cap'        => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'has_archive'         => false,
				'supports'            => array( 'title' ),
			);
			register_post_type( 'coupon', apply_filters( 'hotelier_coupon_post_type_args', $coupon_args ) );
		}

		// Extra Post Type
		$extra_labels = apply_filters( 'hotelier_extra_labels', array(
			'name'               => esc_html_x( 'Extras', 'post type general name', 'wp-hotelier' ),
			'singular_name'      => esc_html_x( 'Extra', 'post type singular name', 'wp-hotelier' ),
			'add_new'            => esc_html__( 'Add Extra', 'wp-hotelier' ),
			'add_new_item'       => esc_html__( 'Add New Extra', 'wp-hotelier' ),
			'edit'               => esc_html__( 'Edit', 'wp-hotelier' ),
			'edit_item'          => esc_html__( 'Edit Extra', 'wp-hotelier' ),
			'new_item'           => esc_html__( 'New Extra', 'wp-hotelier' ),
			'view'               => esc_html__( 'View Extra', 'wp-hotelier' ),
			'view_item'          => esc_html__( 'View Extra', 'wp-hotelier' ),
			'search_items'       => esc_html__( 'Search Extras', 'wp-hotelier' ),
			'not_found'          => esc_html__( 'No Extras found', 'wp-hotelier' ),
			'not_found_in_trash' => esc_html__( 'No Extras found in Trash', 'wp-hotelier' ),
			'parent'             => esc_html__( 'Parent Extra', 'wp-hotelier' ),
			'menu_name'          => esc_html_x( 'Extras', 'admin menu name', 'wp-hotelier' )
		) );

		$extra_args = array(
			'labels'              => $extra_labels,
			'description'         => esc_html__( 'This is where you can add new extras.', 'wp-hotelier' ),
			'public'              => false,
			'show_ui'             => true,
			'query_var'           => false,
			'rewrite'             => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => current_user_can( 'manage_hotelier' ) ? 'hotelier-settings' : true,
			'capability_type'     => 'extra',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'supports'            => array( 'title', 'page-attributes' ),
		);
		register_post_type( 'extra', apply_filters( 'hotelier_extra_post_type_args', $extra_args ) );
	}

	/**
	 * Register post statuses, used for reservation status.
	 */
	public function register_post_status() {
		register_post_status( 'htl-pending', array(
			'label'                     => esc_html_x( 'Pending Payment', 'Reservation status', 'wp-hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wp-hotelier' )
		) );
		register_post_status( 'htl-on-hold', array(
			'label'                     => esc_html_x( 'On Hold', 'Reservation status', 'wp-hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>', 'wp-hotelier' )
		) );
		register_post_status( 'htl-confirmed', array(
			'label'                     => esc_html_x( 'Confirmed', 'Reservation status', 'wp-hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'wp-hotelier' )
		) );
		register_post_status( 'htl-completed', array(
			'label'                     => esc_html_x( 'Completed', 'Reservation status', 'wp-hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'wp-hotelier' )
		) );
		register_post_status( 'htl-cancelled', array(
			'label'                     => esc_html_x( 'Cancelled', 'Reservation status', 'wp-hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'wp-hotelier' )
		) );
		register_post_status( 'htl-failed', array(
			'label'                     => esc_html_x( 'Failed', 'Reservation status', 'wp-hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'wp-hotelier' )
		) );
		register_post_status( 'htl-refunded', array(
			'label'                     => esc_html_x( 'Refunded', 'Reservation status', 'wp-hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'wp-hotelier' )
		) );

	}

	/**
	 * Register core taxonomies.
	 */
	public function register_taxonomies() {
		if ( taxonomy_exists( 'room_cat' ) ) {
			return;
		}

		$slug     = defined( 'HTL_ROOM_CAT_SLUG' ) ? HTL_ROOM_CAT_SLUG : 'room-type';
		$rewrite  = defined( 'HTL_DISABLE_ROOM_CAT_REWRITE' ) && HTL_DISABLE_ROOM_CAT_REWRITE ? false : array( 'slug' => $slug, 'with_front' => false );

		do_action( 'hotelier_register_taxonomy' );

		register_taxonomy( 'room_cat',
			apply_filters( 'hotelier_taxonomy_objects_room_cat', array( 'room' ) ),
			apply_filters( 'hotelier_taxonomy_args_room_cat', array(
				'hierarchical'          => true,
				'labels' => array(
						'name'              => esc_html__( 'Room Categories', 'wp-hotelier' ),
						'singular_name'     => esc_html__( 'Room Category', 'wp-hotelier' ),
						'menu_name'         => esc_html__( 'Categories', 'wp-hotelier' ),
						'search_items'      => esc_html__( 'Search Room Categories', 'wp-hotelier' ),
						'all_items'         => esc_html__( 'All Room Categories', 'wp-hotelier' ),
						'edit_item'         => esc_html__( 'Edit Room Category', 'wp-hotelier' ),
						'update_item'       => esc_html__( 'Update Room Category', 'wp-hotelier' ),
						'add_new_item'      => esc_html__( 'Add New Room Category', 'wp-hotelier' ),
						'new_item_name'     => esc_html__( 'New Room Category Name', 'wp-hotelier' ),
					),
				'show_ui'               => true,
				'show_admin_column'     => true,
				'query_var'             => true,
				'capabilities'          => array(
						'manage_terms' => 'manage_room_terms',
						'edit_terms'   => 'edit_room_terms',
						'delete_terms' => 'delete_room_terms',
						'assign_terms' => 'assign_room_terms',
				),
				'rewrite' 				=> $rewrite,
			) )
		);

		register_taxonomy( 'room_rate',
			apply_filters( 'hotelier_taxonomy_objects_room_rate', array( 'room' ) ),
			apply_filters( 'hotelier_taxonomy_args_room_rate', array(
				'hierarchical'       => false,
				'labels' => array(
						'name'              => esc_html_x( 'Rates', 'taxonomy general name', 'wp-hotelier' ),
						'singular_name'     => esc_html_x( 'Rate', 'taxonomy singular name', 'wp-hotelier' ),
						'menu_name'         => esc_html_x( 'Rates', 'admin menu name', 'wp-hotelier' ),
						'search_items'      => esc_html__( 'Search Rates', 'wp-hotelier' ),
						'all_items'         => esc_html__( 'All Rates', 'wp-hotelier' ),
						'edit_item'         => esc_html__( 'Edit Rate', 'wp-hotelier' ),
						'update_item'       => esc_html__( 'Update Rate', 'wp-hotelier' ),
						'add_new_item'      => esc_html__( 'Add New Rate', 'wp-hotelier' ),
						'new_item_name'     => esc_html__( 'New Rate Name', 'wp-hotelier' )
					),
				'public'             => false,
				'show_ui'            => true,
				'show_in_nav_menus'  => false,
				'show_in_quick_edit' => false,
				'query_var'          => false,
				'capabilities'       => array(
						'manage_terms' => 'manage_room_terms',
						'edit_terms'   => 'edit_room_terms',
						'delete_terms' => 'delete_room_terms',
						'assign_terms' => 'assign_room_terms',
				)
			) )
		);

		register_taxonomy( 'room_facilities',
			apply_filters( 'hotelier_taxonomy_objects_room_facilities', array( 'room' ) ),
			apply_filters( 'hotelier_taxonomy_args_room_facilities', array(
				'hierarchical'          => false,
				'labels' => array(
						'name'              => esc_html_x( 'Facilities', 'taxonomy general name', 'wp-hotelier' ),
						'singular_name'     => esc_html_x( 'Facility', 'taxonomy singular name', 'wp-hotelier' ),
						'menu_name'         => esc_html_x( 'Facilities', 'admin menu name', 'wp-hotelier' ),
						'search_items'      => esc_html__( 'Search Facilities', 'wp-hotelier' ),
						'all_items'         => esc_html__( 'All Facilities', 'wp-hotelier' ),
						'edit_item'         => esc_html__( 'Edit Facility', 'wp-hotelier' ),
						'update_item'       => esc_html__( 'Update Facility', 'wp-hotelier' ),
						'add_new_item'      => esc_html__( 'Add New Facility', 'wp-hotelier' ),
						'new_item_name'     => esc_html__( 'New Facility Name', 'wp-hotelier' )
					),
				'public'                => false,
				'show_ui'               => true,
				'show_in_nav_menus'  	=> false,
				'query_var'             => false,
				'capabilities'          => array(
						'manage_terms' => 'manage_room_terms',
						'edit_terms'   => 'edit_room_terms',
						'delete_terms' => 'delete_room_terms',
						'assign_terms' => 'assign_room_terms',
				)
			) )
		);

	}

	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages[ 'room' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Room updated. <a href="%s">View Room</a>', 'wp-hotelier' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => esc_html__( 'Custom field updated.', 'wp-hotelier' ),
			3 => esc_html__( 'Custom field deleted.', 'wp-hotelier' ),
			4 => esc_html__( 'Room updated.', 'wp-hotelier' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Room restored to revision from %s', 'wp-hotelier' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Room published. <a href="%s">View Room</a>', 'wp-hotelier' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Room saved.', 'wp-hotelier' ),
			8 => sprintf( __( 'Room submitted. <a target="_blank" href="%s">Preview Room</a>', 'wp-hotelier' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Room scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Room</a>', 'wp-hotelier' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-hotelier' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Room draft updated. <a target="_blank" href="%s">Preview Room</a>', 'wp-hotelier' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		$messages[ 'room_reservation' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => esc_html__( 'Reservation updated.', 'wp-hotelier' ),
			2 => esc_html__( 'Custom field updated.', 'wp-hotelier' ),
			3 => esc_html__( 'Custom field deleted.', 'wp-hotelier' ),
			4 => esc_html__( 'Reservation updated.', 'wp-hotelier' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Reservation restored to revision from %s', 'wp-hotelier' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => esc_html__( 'Reservation updated.', 'wp-hotelier' ),
			7 => esc_html__( 'Reservation saved.', 'wp-hotelier' ),
			8 => esc_html__( 'Reservation submitted.', 'wp-hotelier' ),
			9 => sprintf( __( 'Reservation scheduled for: <strong>%1$s</strong>.', 'wp-hotelier' ),
			date_i18n( __( 'M j, Y @ G:i', 'wp-hotelier' ), strtotime( $post->post_date ) ) ),
			10 => esc_html__( 'Reservation draft updated.', 'wp-hotelier' ),
			11 => esc_html__( 'Reservation updated and email sent.', 'wp-hotelier' ),
			12 => esc_html__( 'Reservation updated. Please reload this page again.', 'wp-hotelier' ),
		);

		$messages[ 'coupon' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => esc_html__( 'Coupon updated.', 'wp-hotelier' ),
			2 => esc_html__( 'Custom field updated.', 'wp-hotelier' ),
			3 => esc_html__( 'Custom field deleted.', 'wp-hotelier' ),
			4 => esc_html__( 'Coupon updated.', 'wp-hotelier' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Coupon restored to revision from %s', 'wp-hotelier' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => esc_html__( 'Coupon updated.', 'wp-hotelier' ),
			7 => esc_html__( 'Coupon saved.', 'wp-hotelier' ),
			8 => esc_html__( 'Coupon submitted.', 'wp-hotelier' ),
			9 => sprintf( __( 'Coupon scheduled for: <strong>%1$s</strong>.', 'wp-hotelier' ),
			date_i18n( __( 'M j, Y @ G:i', 'wp-hotelier' ), strtotime( $post->post_date ) ) ),
			10 => esc_html__( 'Coupon draft updated.', 'wp-hotelier' ),
			11 => esc_html__( 'Coupon updated and email sent.', 'wp-hotelier' ),
			12 => esc_html__( 'Coupon updated. Please reload this page again.', 'wp-hotelier' ),
		);

		$messages[ 'extra' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => esc_html__( 'Extra updated.', 'wp-hotelier' ),
			2 => esc_html__( 'Custom field updated.', 'wp-hotelier' ),
			3 => esc_html__( 'Custom field deleted.', 'wp-hotelier' ),
			4 => esc_html__( 'Extra updated.', 'wp-hotelier' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Extra restored to revision from %s', 'wp-hotelier' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => esc_html__( 'Extra updated.', 'wp-hotelier' ),
			7 => esc_html__( 'Extra saved.', 'wp-hotelier' ),
			8 => esc_html__( 'Extra submitted.', 'wp-hotelier' ),
			9 => sprintf( __( 'Extra scheduled for: <strong>%1$s</strong>.', 'wp-hotelier' ),
			date_i18n( __( 'M j, Y @ G:i', 'wp-hotelier' ), strtotime( $post->post_date ) ) ),
			10 => esc_html__( 'Extra draft updated.', 'wp-hotelier' ),
			11 => esc_html__( 'Extra updated and email sent.', 'wp-hotelier' ),
			12 => esc_html__( 'Extra updated. Please reload this page again.', 'wp-hotelier' ),
		);

		return $messages;
	}

	/**
	 * Disable block editor for rooms.
	 *
	 * @param bool   $can_edit Whether the post type can be edited or not.
	 * @param string $post_type The post type being checked.
	 * @return bool
	 */
	public static function block_editor_can_edit_post_type( $can_edit, $post_type ) {
		if ( 'room' === $post_type ) {
			return false;
		}

		return $can_edit;
	}
}

endif;

return new HTL_Post_Types();

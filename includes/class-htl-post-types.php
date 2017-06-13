<?php
/**
 * Registers post types and taxonomies.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.0.0
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
			'name'               => esc_html_x( 'Rooms', 'room post type name', 'hotelier' ),
			'singular_name'      => esc_html_x( 'Room', 'singular room post type name', 'hotelier' ),
			'add_new'            => esc_html__( 'Add New', 'hotelier' ),
			'add_new_item'       => esc_html__( 'Add New Room', 'hotelier' ),
			'edit_item'          => esc_html__( 'Edit Room', 'hotelier' ),
			'new_item'           => esc_html__( 'New Room', 'hotelier' ),
			'all_items'          => esc_html__( 'All Rooms', 'hotelier' ),
			'view_item'          => esc_html__( 'View Room', 'hotelier' ),
			'search_items'       => esc_html__( 'Search Rooms', 'hotelier' ),
			'not_found'          => esc_html__( 'No Rooms found', 'hotelier' ),
			'not_found_in_trash' => esc_html__( 'No Rooms found in Trash', 'hotelier' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html_x( 'Rooms', 'room post type menu name', 'hotelier' )
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
			'name'               => esc_html_x( 'Reservations', 'post type general name', 'hotelier' ),
			'singular_name'      => esc_html_x( 'Reservation', 'post type singular name', 'hotelier' ),
			'add_new'            => esc_html__( 'Add Reservation', 'hotelier' ),
			'add_new_item'       => esc_html__( 'Add New Reservation', 'hotelier' ),
			'edit'               => esc_html__( 'Edit', 'hotelier' ),
			'edit_item'          => esc_html__( 'Edit Reservation', 'hotelier' ),
			'new_item'           => esc_html__( 'New Reservation', 'hotelier' ),
			'view'               => esc_html__( 'View Reservation', 'hotelier' ),
			'view_item'          => esc_html__( 'View Reservation', 'hotelier' ),
			'search_items'       => esc_html__( 'Search Reservations', 'hotelier' ),
			'not_found'          => esc_html__( 'No Reservations found', 'hotelier' ),
			'not_found_in_trash' => esc_html__( 'No Reservations found in Trash', 'hotelier' ),
			'parent'             => esc_html__( 'Parent Reservation', 'hotelier' ),
			'menu_name'          => esc_html_x( 'Reservations', 'admin menu name', 'hotelier' )
		) );

		$reservation_args = array(
			'labels'              => apply_filters( 'hotelier_reservation_labels', $reservation_labels ),
			'description'         => esc_html__( 'This is where hotel reservations are stored.', 'hotelier' ),
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
	}

	/**
	 * Register post statuses, used for reservation status.
	 */
	public function register_post_status() {
		register_post_status( 'htl-pending', array(
			'label'                     => esc_html_x( 'Pending Payment', 'Reservation status', 'hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'hotelier' )
		) );
		register_post_status( 'htl-on-hold', array(
			'label'                     => esc_html_x( 'On Hold', 'Reservation status', 'hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>', 'hotelier' )
		) );
		register_post_status( 'htl-confirmed', array(
			'label'                     => esc_html_x( 'Confirmed', 'Reservation status', 'hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'hotelier' )
		) );
		register_post_status( 'htl-completed', array(
			'label'                     => esc_html_x( 'Completed', 'Reservation status', 'hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'hotelier' )
		) );
		register_post_status( 'htl-cancelled', array(
			'label'                     => esc_html_x( 'Cancelled', 'Reservation status', 'hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'hotelier' )
		) );
		register_post_status( 'htl-failed', array(
			'label'                     => esc_html_x( 'Failed', 'Reservation status', 'hotelier' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'hotelier' )
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
						'name'              => esc_html__( 'Room Categories', 'hotelier' ),
						'singular_name'     => esc_html__( 'Room Category', 'hotelier' ),
						'menu_name'         => esc_html__( 'Categories', 'hotelier' ),
						'search_items'      => esc_html__( 'Search Room Categories', 'hotelier' ),
						'all_items'         => esc_html__( 'All Room Categories', 'hotelier' ),
						'edit_item'         => esc_html__( 'Edit Room Category', 'hotelier' ),
						'update_item'       => esc_html__( 'Update Room Category', 'hotelier' ),
						'add_new_item'      => esc_html__( 'Add New Room Category', 'hotelier' ),
						'new_item_name'     => esc_html__( 'New Room Category Name', 'hotelier' ),
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
						'name'              => esc_html_x( 'Rates', 'taxonomy general name', 'hotelier' ),
						'singular_name'     => esc_html_x( 'Rate', 'taxonomy singular name', 'hotelier' ),
						'menu_name'         => esc_html_x( 'Rates', 'admin menu name', 'hotelier' ),
						'search_items'      => esc_html__( 'Search Rates', 'hotelier' ),
						'all_items'         => esc_html__( 'All Rates', 'hotelier' ),
						'edit_item'         => esc_html__( 'Edit Rate', 'hotelier' ),
						'update_item'       => esc_html__( 'Update Rate', 'hotelier' ),
						'add_new_item'      => esc_html__( 'Add New Rate', 'hotelier' ),
						'new_item_name'     => esc_html__( 'New Rate Name', 'hotelier' )
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
						'name'              => esc_html_x( 'Facilities', 'taxonomy general name', 'hotelier' ),
						'singular_name'     => esc_html_x( 'Facility', 'taxonomy singular name', 'hotelier' ),
						'menu_name'         => esc_html_x( 'Facilities', 'admin menu name', 'hotelier' ),
						'search_items'      => esc_html__( 'Search Facilities', 'hotelier' ),
						'all_items'         => esc_html__( 'All Facilities', 'hotelier' ),
						'edit_item'         => esc_html__( 'Edit Facility', 'hotelier' ),
						'update_item'       => esc_html__( 'Update Facility', 'hotelier' ),
						'add_new_item'      => esc_html__( 'Add New Facility', 'hotelier' ),
						'new_item_name'     => esc_html__( 'New Facility Name', 'hotelier' )
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
			1 => sprintf( __( 'Room updated. <a href="%s">View Room</a>', 'hotelier' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => esc_html__( 'Custom field updated.', 'hotelier' ),
			3 => esc_html__( 'Custom field deleted.', 'hotelier' ),
			4 => esc_html__( 'Room updated.', 'hotelier' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Room restored to revision from %s', 'hotelier' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Room published. <a href="%s">View Room</a>', 'hotelier' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Room saved.', 'hotelier' ),
			8 => sprintf( __( 'Room submitted. <a target="_blank" href="%s">Preview Room</a>', 'hotelier' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Room scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Room</a>', 'hotelier' ),
			  date_i18n( __( 'M j, Y @ G:i', 'hotelier' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Room draft updated. <a target="_blank" href="%s">Preview Room</a>', 'hotelier' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		$messages[ 'room_reservation' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => esc_html__( 'Reservation updated.', 'hotelier' ),
			2 => esc_html__( 'Custom field updated.', 'hotelier' ),
			3 => esc_html__( 'Custom field deleted.', 'hotelier' ),
			4 => esc_html__( 'Reservation updated.', 'hotelier' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Reservation restored to revision from %s', 'hotelier' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => esc_html__( 'Reservation updated.', 'hotelier' ),
			7 => esc_html__( 'Reservation saved.', 'hotelier' ),
			8 => esc_html__( 'Reservation submitted.', 'hotelier' ),
			9 => sprintf( __( 'Reservation scheduled for: <strong>%1$s</strong>.', 'hotelier' ),
			date_i18n( __( 'M j, Y @ G:i', 'hotelier' ), strtotime( $post->post_date ) ) ),
			10 => esc_html__( 'Reservation draft updated.', 'hotelier' ),
			11 => esc_html__( 'Reservation updated and email sent.', 'hotelier' ),
		);

		return $messages;
	}
}

endif;

return new HTL_Post_Types();

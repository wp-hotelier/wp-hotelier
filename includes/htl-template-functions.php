<?php
/**
 * Functions for the templating system.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handle redirects before content is output.
 */
function htl_template_redirect() {
	global $wp_query, $wp;

	// Empty cart in listing page
	if ( is_listing() ) {

		HTL()->cart->empty_cart();
	}

	// When on the booking with an empty cart, redirect to listing page
	elseif ( is_booking() && HTL()->cart->is_empty() && empty( $wp->query_vars[ 'pay-reservation' ] ) && ! isset( $wp->query_vars[ 'reservation-received' ] ) ) {

		$url = apply_filters( 'hotelier_empty_cart_redirect_url', htl_get_page_permalink( 'listing' ) );

		wp_redirect( $url );
		exit;
	}

	// Booking page handling
	elseif ( is_booking() ) {
		// Buffer the booking page
		ob_start();

		// Ensure gateways are loaded early
		HTL()->payment_gateways();
	}
}
add_action( 'template_redirect', 'htl_template_redirect' );

/**
 * When the_post is called, put room data into a global.
 *
 * @param mixed $post
 * @return HTL_Room
 */
function htl_setup_room_data( $post ) {
	unset( $GLOBALS[ 'room' ] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->post_type ) || ! ( 'room' == $post->post_type ) ) {
		return;
	}

	$GLOBALS[ 'room' ] = htl_get_room( $post );

	return $GLOBALS[ 'room' ];
}
add_action( 'the_post', 'htl_setup_room_data' );

if ( ! function_exists( 'hotelier_reset_loop' ) ) {

	/**
	 * Reset the loop's index and columns when we're done outputting a room loop.
	 *
	 * @subpackage	Loop
	 */
	function hotelier_reset_loop() {
		global $hotelier_loop;

		// Reset loop/columns globals when starting a new loop
		$hotelier_loop[ 'loop' ] = $hotelier_loop[ 'columns' ] = '';
	}
}
add_filter( 'loop_end', 'hotelier_reset_loop' );

/**
 * Output Hotelier generator tag.
 *
 * @access public
 */
function htl_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="Easy WP Hotelier ' . esc_attr( HTL_VERSION ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="Easy WP Hotelier ' . esc_attr( HTL_VERSION ) . '" />';
			break;
	}
	return $gen;
}

/**
 * Add body classes for HTL pages.
 *
 * @param  array $classes
 * @return array
 */
function htl_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_hotelier() ) {
		$classes[] = 'hotelier';
		$classes[] = 'hotelier-page';
	}

	elseif ( is_booking() ) {
		$classes[] = 'hotelier-booking';
		$classes[] = 'hotelier-page';
	}

	elseif ( is_listing() ) {
		$classes[] = 'hotelier-listing';
		$classes[] = 'hotelier-page';
	}

	foreach ( HTL()->query->query_vars as $key => $value ) {
		if ( is_htl_endpoint_url( $key ) ) {
			$classes[] = 'hotelier-' . sanitize_html_class( $key );
		}
	}

	return array_unique( $classes );
}

/***** Global ***************************/

if ( ! function_exists( 'hotelier_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 */
	function hotelier_output_content_wrapper() {
		htl_get_template( 'global/wrapper-start.php' );
	}

}

if ( ! function_exists( 'hotelier_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 */
	function hotelier_output_content_wrapper_end() {
		htl_get_template( 'global/wrapper-end.php' );
	}

}

if ( ! function_exists( 'hotelier_get_sidebar' ) ) {

	/**
	 * Get the sidebar template.
	 */
	function hotelier_get_sidebar() {
		htl_get_template( 'global/sidebar.php' );
	}
}

/***** Loop Archive Room ***************************/

if ( ! function_exists( 'hotelier_taxonomy_archive_description' ) ) {

	/**
	 * Show an archive description on taxonomy archives.
	 */
	function hotelier_taxonomy_archive_description() {
		if ( is_tax( 'room_cat' ) && get_query_var( 'paged' ) == 0 ) {
			$description = do_shortcode( shortcode_unautop( wpautop( term_description() ) ) );

			if ( $description ) {
				echo '<div class="taxonomy-description page__description">' . $description . '</div>';
			}
		}
	}

}

if ( ! function_exists( 'hotelier_before_archive_room_loop' ) ) {

	/**
	 * Output the start of the loop wrapper.
	 */
	function hotelier_before_archive_room_loop() {
		htl_get_template( 'loop/loop-wrapper-start.php' );
	}

}

if ( ! function_exists( 'hotelier_output_loop_wrapper_end' ) ) {

	/**
	 * Output the end of the loop wrapper.
	 */
	function hotelier_output_loop_wrapper_end() {
		htl_get_template( 'loop/loop-wrapper-end.php' );
	}

}

if ( ! function_exists( 'hotelier_page_title' ) ) {

	/**
	 * Display page title.
	 */
	function hotelier_page_title() {

		if ( is_search() ) {
			$page_title = sprintf( esc_html__( 'Search results for: "%s"', 'wp-hotelier' ), '<span>' . get_search_query() . '</span>' );

			if ( get_query_var( 'paged' ) )
				$page_title .= sprintf( esc_html__( '&nbsp;&ndash; Page %s', 'wp-hotelier' ), get_query_var( 'paged' ) );

		} elseif ( is_tax() ) {

			$page_title = single_term_title( "", false );

		} else {

			$page_title = esc_html__( 'Rooms archive', 'wp-hotelier' );
			$page_title = apply_filters( 'hotelier_rooms_archive_page_title', $page_title );
		}

		$page_title = apply_filters( 'hotelier_page_title', $page_title );

		htl_get_template( 'archive/page-title.php', array( 'page_title' => $page_title ) );
	}
}

if ( ! function_exists( 'hotelier_template_archive_room_image' ) ) {

	/**
	 * Show the room image in the archive room page.
	 */
	function hotelier_template_archive_room_image() {
		htl_get_template( 'archive/content/room-image.php' );
	}

}

if ( ! function_exists( 'hotelier_template_archive_room_title' ) ) {

	/**
	 * Show the room title in the archive room page.
	 */
	function hotelier_template_archive_room_title() {
		htl_get_template( 'archive/content/room-title.php' );
	}

}

if ( ! function_exists( 'hotelier_template_archive_room_description' ) ) {

	/**
	 * Show the room description in the archive room page.
	 */
	function hotelier_template_archive_room_description() {
		htl_get_template( 'archive/content/room-description.php' );
	}

}

if ( ! function_exists( 'hotelier_template_archive_room_price' ) ) {

	/**
	 * Show the room price in the archive room page.
	 */
	function hotelier_template_archive_room_price() {
		htl_get_template( 'archive/content/room-price.php' );
	}

}

if ( ! function_exists( 'hotelier_template_archive_room_more' ) ) {

	/**
	 * Show the more button price in the archive room page.
	 */
	function hotelier_template_archive_room_more() {
		htl_get_template( 'archive/content/room-more-button.php' );
	}

}

/***** Single Room ***************************/

if ( ! function_exists( 'hotelier_template_single_room_image' ) ) {

	/**
	 * Show the room image in the single room page.
	 */
	function hotelier_template_single_room_image() {
		htl_get_template( 'single-room/image.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_gallery' ) ) {

	/**
	 * Show the room gallery in the single room page.
	 */
	function hotelier_template_single_room_gallery() {
		htl_get_template( 'single-room/gallery.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_title' ) ) {

	/**
	 * Show the room title in the single room page.
	 */
	function hotelier_template_single_room_title() {
		htl_get_template( 'single-room/title.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_datepicker' ) ) {

	/**
	 * Show the datepicker in the single room page.
	 */
	function hotelier_template_single_room_datepicker() {
		// show the datepicker only when the booking is enabled
		if ( htl_get_option( 'booking_mode' ) != 'no-booking' ) {

			$checkin  = HTL()->session->get( 'checkin' );
			$checkout = HTL()->session->get( 'checkout' );

			htl_get_template( 'global/datepicker.php', array( 'checkin' => $checkin, 'checkout' => $checkout ) );
		}
	}

}

if ( ! function_exists( 'hotelier_template_single_room_price' ) ) {

	/**
	 * Show the room price in the single room page.
	 */
	function hotelier_template_single_room_price() {
		htl_get_template( 'single-room/price.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_non_cancellable_info' ) ) {

	/**
	 * Show info in the single room page when the room is not cancellable.
	 */
	function hotelier_template_single_room_non_cancellable_info() {
		htl_get_template( 'single-room/non-cancellable-info.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_deposit' ) ) {

	/**
	 * Show the required deposit in the single room page.
	 */
	function hotelier_template_single_room_deposit() {
		htl_get_template( 'single-room/deposit.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_min_max_info' ) ) {

	/**
	 * Show minimum/maximum required nights in the single room page.
	 */
	function hotelier_template_single_room_min_max_info() {
		global $room;

		if ( $room->is_variable_room() ) {
			return;
		}

		$min_nights = $room->get_min_nights();
		$max_nights = $room->get_max_nights();

		$text = htl_get_room_min_max_info( $min_nights, $max_nights );

		if ( $text ) {
			htl_get_template( 'global/min-max-info.php', array( 'info' => $text, 'location' => 'single' ) );
		}
	}

}

if ( ! function_exists( 'hotelier_template_single_room_category' ) ) {

	/**
	 * Show the room category in the single room page.
	 */
	function hotelier_template_single_room_category() {
		htl_get_template( 'single-room/category.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_conditions' ) ) {

	/**
	 * Show the room conditions in the single room page.
	 */
	function hotelier_template_single_room_conditions() {
		htl_get_template( 'single-room/conditions.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_meta' ) ) {

	/**
	 * Show the room meta in the single room page.
	 */
	function hotelier_template_single_room_meta() {
		htl_get_template( 'single-room/meta.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_facilities' ) ) {

	/**
	 * Show the room facilities in the single room page.
	 */
	function hotelier_template_single_room_facilities() {
		htl_get_template( 'single-room/facilities.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_sharing' ) ) {

	/**
	 * Show the room sharer in the single room page.
	 */
	function hotelier_template_single_room_sharing() {
		htl_get_template( 'single-room/sharer.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_description' ) ) {

	/**
	 * Show the room description in the single room page.
	 */
	function hotelier_template_single_room_description() {
		htl_get_template( 'single-room/description.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rates' ) ) {

	/**
	 * Show the room rates in the single room page.
	 */
	function hotelier_template_single_room_rates() {
		htl_get_template( 'single-room/room-rates.php' );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_single_rate' ) ) {

	/**
	 * Show the single rate in the single room page.
	 */
	function hotelier_template_single_room_single_rate( $variation ) {
		htl_get_template( 'single-room/rate.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_name' ) ) {

	/**
	 * Show the rate name.
	 */
	function hotelier_template_single_room_rate_name( $variation ) {
		htl_get_template( 'single-room/rate/rate-name.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_description' ) ) {

	/**
	 * Show the rate description.
	 */
	function hotelier_template_single_room_rate_description( $variation ) {
		htl_get_template( 'single-room/rate/rate-description.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_conditions' ) ) {

	/**
	 * Show the rate conditions.
	 */
	function hotelier_template_single_room_rate_conditions( $variation ) {
		htl_get_template( 'single-room/rate/rate-conditions.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_min_max_info' ) ) {

	/**
	 * Show minimum/maximum required nights in the single room page.
	 */
	function hotelier_template_single_room_rate_min_max_info( $variation ) {
		$min_nights = $variation->get_min_nights();
		$max_nights = $variation->get_max_nights();

		$text = htl_get_room_min_max_info( $min_nights, $max_nights );

		if ( $text ) {
			htl_get_template( 'global/min-max-info.php', array( 'info' => $text, 'location' => 'rate-single' ) );
		}
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_price' ) ) {

	/**
	 * Show the rate price.
	 */
	function hotelier_template_single_room_rate_price( $variation ) {
		htl_get_template( 'single-room/rate/rate-price.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_non_cancellable_info' ) ) {

	/**
	 * Show info when the rate is not cancellable.
	 */
	function hotelier_template_single_room_rate_non_cancellable_info( $variation ) {
		htl_get_template( 'single-room/rate/rate-non-cancellable-info.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_check_availability' ) ) {

	/**
	 * Show the rate check availability button.
	 */
	function hotelier_template_single_room_rate_check_availability( $variation ) {
		htl_get_template( 'single-room/rate/rate-button.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_single_room_rate_deposit' ) ) {

	/**
	 * Show the rate deposit.
	 */
	function hotelier_template_single_room_rate_deposit( $variation ) {
		htl_get_template( 'single-room/rate/rate-deposit.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_related_rooms' ) ) {

	/**
	 * Output the related rooms.
	 */
	function hotelier_template_related_rooms() {
		global $room, $hotelier_loop;

		$args = apply_filters( 'hotelier_output_related_rooms_args', array(
			'posts_per_page' => 3,
			'columns'        => 3,
			'orderby'        => 'rand'
		) );

		$hotelier_loop[ 'columns' ] = $args[ 'columns' ];

		$related_rooms = htl_get_related_rooms_query( $room->id, $args[ 'posts_per_page' ], $args[ 'orderby' ] );

		htl_get_template( 'single-room/related.php', array( 'related_rooms' => $related_rooms, 'columns' => $args[ 'columns' ] ) );
	}
}

/***** Loop ***************************/

if ( ! function_exists( 'hotelier_room_loop_start' ) ) {

	/**
	 * Output the start of a room loop. By default this is a UL
	 *
	 * @param bool $echo
	 * @return string
	 */
	function hotelier_room_loop_start( $echo = true ) {
		ob_start();
		htl_get_template( 'loop/loop-start.php' );
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

}

if ( ! function_exists( 'hotelier_room_loop_end' ) ) {

	/**
	 * Output the end of a room loop. By default this is a UL
	 *
	 * @param bool $echo
	 * @return string
	 */
	function hotelier_room_loop_end( $echo = true ) {
		ob_start();
		htl_get_template( 'loop/loop-end.php' );
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

}

/***** Room List Page ***************************/

if ( ! function_exists( 'hotelier_room_list_start' ) ) {

	/**
	 * Output the start of the room list loop. By default this is a UL
	 *
	 * @param bool $echo
	 * @return string
	 */
	function hotelier_room_list_start( $echo = true ) {
		ob_start();
		htl_get_template( 'loop/loop-room-list-start.php' );
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

}

if ( ! function_exists( 'hotelier_room_list_end' ) ) {

	/**
	 * Output the end of the room list loop. By default this is a UL
	 *
	 * @param bool $echo
	 * @return string
	 */
	function hotelier_room_list_end( $echo = true ) {
		ob_start();
		htl_get_template( 'loop/loop-room-list-end.php' );
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

}

if ( ! function_exists( 'hotelier_template_datepicker' ) ) {

	/**
	 * Show the datepicker form in the room list loop.
	 */
	function hotelier_template_datepicker() {

		$checkin  = HTL()->session->get( 'checkin' );
		$checkout = HTL()->session->get( 'checkout' );

		htl_get_template( 'global/datepicker.php', array( 'checkin' => $checkin, 'checkout' => $checkout ) );
	}

}

if ( ! function_exists( 'hotelier_template_selected_nights' ) ) {

	/**
	 * Show the selected nights in the room list loop.
	 */
	function hotelier_template_selected_nights() {
		$checkin  = new DateTime( HTL()->session->get( 'checkin' ) );
		$checkout = new DateTime( HTL()->session->get( 'checkout' ) );
		$nights   = $checkin->diff( $checkout )->days;

		htl_get_template( 'room-list/selected-nights.php', array( 'nights' => $nights ) );
	}

}

if ( ! function_exists( 'hotelier_template_room_list_content' ) ) {

	/**
	 * The template for displaying room content in the listing loop.
	 */
	function hotelier_template_room_list_content( $single = false ) {
		htl_get_template( 'room-list/room-content.php', array( 'is_single' => $single ) );
	}

}

if ( ! function_exists( 'hotelier_template_rooms_left' ) ) {

	/**
	 * Show rooms left message in the room list loop.
	 */
	function hotelier_template_rooms_left( $is_available, $checkin, $checkout ) {
		htl_get_template( 'room-list/content/rooms-left.php', array( 'is_available' => $is_available, 'checkin' => $checkin, 'checkout' => $checkout ) );
	}

}

if ( ! function_exists( 'hotelier_template_room_list_title' ) ) {

	/**
	 * Show the room title in the room list loop.
	 */
	function hotelier_template_room_list_title() {
		htl_get_template( 'room-list/content/title.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_image' ) ) {

	/**
	 * Show the room featured image in the room list loop.
	 */
	function hotelier_template_loop_room_image() {
		htl_get_template( 'room-list/content/image.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_thumbnails' ) ) {

	/**
	 * Show the room thumbnails in the room list loop.
	 */
	function hotelier_template_loop_room_thumbnails() {
		htl_get_template( 'room-list/content/thumbnails.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_short_description' ) ) {

	/**
	 * Show the room description in the room list loop.
	 */
	function hotelier_template_loop_room_short_description() {
		htl_get_template( 'room-list/content/short-description.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_facilities' ) ) {

	/**
	 * Show the room facilities in the room list loop.
	 */
	function hotelier_template_loop_room_facilities() {
		htl_get_template( 'room-list/content/facilities.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_meta' ) ) {

	/**
	 * Show the room meta in the room list loop.
	 */
	function hotelier_template_loop_room_meta() {
		htl_get_template( 'room-list/content/meta.php' );
	}

}


if ( ! function_exists( 'hotelier_template_loop_room_conditions' ) ) {

	/**
	 * Show the room conditions in the room list loop.
	 */
	function hotelier_template_loop_room_conditions() {
		htl_get_template( 'room-list/content/room-conditions.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_deposit' ) ) {

	/**
	 * Show the room deposit in the room list loop.
	 */
	function hotelier_template_loop_room_deposit() {
		htl_get_template( 'room-list/content/deposit.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_guests' ) ) {

	/**
	 * Show the room guests in the room list loop.
	 */
	function hotelier_template_loop_room_guests() {
		htl_get_template( 'room-list/content/max-guests.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_price' ) ) {

	/**
	 * Show the room price in the room list loop.
	 */
	function hotelier_template_loop_room_price( $checkin, $checkout ) {
		htl_get_template( 'room-list/content/price.php', array( 'checkin' => $checkin, 'checkout' => $checkout ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_not_available_info' ) ) {

	/**
	 * Show info when a room is not available.
	 */
	function hotelier_template_loop_room_not_available_info( $is_available ) {
		htl_get_template( 'room-list/content/not-available-info.php', array( 'is_available' => $is_available ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_min_max_info' ) ) {

	/**
	 * Show minimum/maximum required nights.
	 */
	function hotelier_template_loop_room_min_max_info() {
		global $room;


		if ( $room->is_variable_room() ) {
			return;
		}

		$min_nights = $room->get_min_nights();
		$max_nights = $room->get_max_nights();

		$text = htl_get_room_min_max_info( $min_nights, $max_nights );

		if ( $text ) {
			htl_get_template( 'global/min-max-info.php', array( 'info' => $text, 'location' => 'listing' ) );
		}
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_non_cancellable_info' ) ) {

	/**
	 * Show info in the room list loop when the room is not cancellable.
	 */
	function hotelier_template_loop_room_non_cancellable_info() {
		htl_get_template( 'room-list/content/non-cancellable-info.php' );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_add_to_cart' ) ) {

	/**
	 * Show the room add to cart button in the room list loop.
	 */
	function hotelier_template_loop_room_add_to_cart( $is_available ) {
		htl_get_template( 'room-list/content/add-to-cart.php', array( 'is_available' => $is_available ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate' ) ) {

	/**
	 * Output the rate in the room list loop.
	 */
	function hotelier_template_loop_room_rate( $variation, $is_available, $checkin, $checkout ) {
		htl_get_template( 'room-list/room-rate.php', array( 'variation' => $variation, 'is_available' => $is_available, 'checkin' => $checkin, 'checkout' => $checkout ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_name' ) ) {

	/**
	 * Show the rate name.
	 */
	function hotelier_template_loop_room_rate_name( $variation ) {
		htl_get_template( 'room-list/content/rate/rate-name.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_description' ) ) {

	/**
	 * Show the rate description.
	 */
	function hotelier_template_loop_room_rate_description( $variation ) {
		htl_get_template( 'room-list/content/rate/rate-description.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_conditions' ) ) {

	/**
	 * Show the rate conditions.
	 */
	function hotelier_template_loop_room_rate_conditions( $variation ) {
		htl_get_template( 'room-list/content/rate/rate-conditions.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_deposit' ) ) {

	/**
	 * Show the rate deposit.
	 */
	function hotelier_template_loop_room_rate_deposit( $variation ) {
		htl_get_template( 'room-list/content/rate/rate-deposit.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_min_max_info' ) ) {

	/**
	 * Show rate minimum/maximum required nights in the listing page.
	 */
	function hotelier_template_loop_room_rate_min_max_info( $variation ) {
		$min_nights = $variation->get_min_nights();
		$max_nights = $variation->get_max_nights();

		$text = htl_get_room_min_max_info( $min_nights, $max_nights );

		if ( $text ) {
			htl_get_template( 'global/min-max-info.php', array( 'info' => $text, 'location' => 'rate-listing' ) );
		}
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_non_cancellable_info' ) ) {

	/**
	 * Show info in the room list loop when the rate is not cancellable.
	 */
	function hotelier_template_loop_room_rate_non_cancellable_info( $variation ) {
		htl_get_template( 'room-list/content/rate/rate-non-cancellable-info.php', array( 'variation' => $variation ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_price' ) ) {

	/**
	 * Show the rate price.
	 */
	function hotelier_template_loop_room_rate_price( $variation, $is_available, $checkin, $checkout ) {
		htl_get_template( 'room-list/content/rate/rate-price.php', array( 'variation' => $variation, 'is_available' => $is_available, 'checkin' => $checkin, 'checkout' => $checkout ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_rate_add_to_cart' ) ) {

	/**
	 * Show the rate add to cart button.
	 */
	function hotelier_template_loop_room_rate_add_to_cart( $variation, $is_available, $checkin, $checkout ) {
		htl_get_template( 'room-list/content/rate/rate-add-to-cart.php', array( 'variation' => $variation, 'is_available' => $is_available, 'checkin' => $checkin, 'checkout' => $checkout ) );
	}

}

if ( ! function_exists( 'hotelier_template_loop_room_reserve_button' ) ) {

	/**
	 * Show the reserve button in the room list loop.
	 */
	function hotelier_template_loop_room_reserve_button() {
		htl_get_template( 'room-list/reserve-button.php' );
	}

}

if ( ! function_exists( 'hotelier_pagination' ) ) {

	/**
	 * Output the pagination.
	 */
	function hotelier_pagination() {
		htl_get_template( 'loop/pagination.php' );
	}

}

/***** Booking Page ***************************/

if ( ! function_exists( 'hotelier_template_terms_checkbox' ) ) {

	/**
	 * Show the terms and consitions checkbox.
	 */
	function hotelier_template_terms_checkbox() {
		htl_get_template( 'booking/terms.php' );
	}

}

if ( ! function_exists( 'hotelier_reservation_table_guests' ) ) {

	/**
	 * Show selects for adults and children.
	 */
	function hotelier_reservation_table_guests( $room, $item_key, $quantity ) {
		$max_adults   = $room->get_max_guests();
		$max_children = $room->get_max_children();

		htl_get_template( 'booking/reservation-table-guests.php', array(
			'adults'   => $max_adults,
			'children' => $max_children,
			'item_key' => $item_key,
			'quantity' => $quantity,
		) );
	}

}

/***** Reservation ***************************/

if ( ! function_exists( 'hotelier_template_reservation_table' ) ) {

	/**
	 * Displays reservation table.
	 *
	 * @param mixed $reservation_id
	 */
	function hotelier_template_reservation_table( $reservation_id ) {
		if ( ! $reservation_id ) {
			return;
		}

		htl_get_template( 'reservation/reservation-table.php', array(
			'reservation_id' => $reservation_id
		) );
	}
}

if ( ! function_exists( 'hotelier_template_reservation_details' ) ) {

	/**
	 * Displays reservation details.
	 *
	 * @param mixed $reservation
	 */
	function hotelier_template_reservation_details( $reservation ) {
		if ( ! $reservation ) {
			return;
		}

		htl_get_template( 'reservation/reservation-details.php', array(
			'reservation' => $reservation
		) );
	}
}

if ( ! function_exists( 'hotelier_template_guest_details' ) ) {

	/**
	 * Displays guest details.
	 *
	 * @param mixed $reservation
	 */
	function hotelier_template_guest_details( $reservation ) {
		if ( ! $reservation ) {
			return;
		}

		htl_get_template( 'reservation/guest-details.php', array(
			'reservation' => $reservation
		) );
	}
}

if ( ! function_exists( 'hotelier_template_cancel_reservation' ) ) {

	/**
	 * Displays cancel reservation button.
	 *
	 * @param mixed $reservation
	 */
	function hotelier_template_cancel_reservation( $reservation ) {
		if ( ! $reservation ) {
			return;
		}

		if ( $reservation->can_be_cancelled() && $reservation->has_status( apply_filters( 'hotelier_valid_reservation_statuses_for_cancel', array( 'pending', 'confirmed', 'failed' ) ) ) ) {
			htl_get_template( 'reservation/cancel-reservation.php', array(
				'reservation' => $reservation
			) );
		}
	}
}

/***** Forms ***************************/

if ( ! function_exists( 'htl_form_field' ) ) {

	/**
	 * Outputs a booking form field.
	 *
	 * @param string $key
	 * @param mixed $args
	 */
	function htl_form_field( $key, $args, $value = null ) {
		$defaults = array(
			'type'        => 'text',
			'label'       => '',
			'desc'        => '',
			'placeholder' => '',
			'required'    => false,
			'id'          => $key,
			'class'       => array(),
			'options'     => array(),
			'validate'    => array(),
			'default'     => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args[ 'required' ] ) {
			$args[ 'class' ][] = 'form-row--validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wp-hotelier'  ) . '">*</abbr>';
		} else {
			$required = '';
		}

		if ( is_null( $value ) ) {
			$value = $args[ 'default' ];
		}

		if ( ! empty( $args[ 'validate' ] ) ) {
			foreach( $args[ 'validate' ] as $validate ) {
				$args[ 'class' ][] = 'form-row--validate-' . $validate;
			}
		}

		$field = '';
		$label_id = $args[ 'id' ];
		$field_wrapper = '<p class="form-row %1$s" id="%2$s">%3$s</p>';

		switch ( $args[ 'type' ] ) {
			case 'textarea' :

				$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" rows="4" cols="5">'. esc_textarea( $value  ) .'</textarea>';
				break;

			case 'text' :

				$field .= '<input type="text" class="input-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args[ 'id' ] ) . '" placeholder="' . esc_attr( $args[ 'placeholder' ] ) . '" value="' . esc_attr( $value ) . '" />';

				break;

			case 'number' :

				$field .= '<input type="number" class="input-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args[ 'id' ] ) . '" placeholder="' . esc_attr( $args[ 'placeholder' ] ) . '" value="' . absint( $value ) . '" />';

				break;

			case 'email' :

				$field .= '<input type="email" class="input-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args[ 'id' ] ) . '" placeholder="' . esc_attr( $args[ 'placeholder' ] ) . '" value="' . esc_attr( $value ) . '" />';

				break;

			case 'tel' :

				$field .= '<input type="tel" class="input-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args[ 'id' ] ) . '" placeholder="' . esc_attr( $args[ 'placeholder' ] ) . '" value="' . esc_attr( $value ) . '" />';

				break;

			case 'select' :

				$options = $field = '';

				if ( ! empty( $args[ 'options' ] ) ) {
					$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args[ 'id' ] ) . '" class="select">';

					foreach ( $args[ 'options' ] as $k => $v ) {
						$field .= '<option value="' . esc_attr( $k ) . '" '. selected( $value, $k, false ) . '>' . esc_attr( $v ) .'</option>';
					}

					$field .= '</select>';
				}

				break;
		}

		if ( ! empty( $field ) ) {
			$field_html = '';

			if ( $args[ 'label' ] ) {
				$field_html .= '<label class="form-row__label" for="' . esc_attr( $label_id ) . '">' . $args[ 'label' ] . $required . '</label>';
			}

			$field_html .= $field;

			if ( $args[ 'desc' ] ) {
				$field_html .= '<span class="form-row__description">' . esc_attr( $args[ 'desc' ] ) . '</span>';
			}

			$wrapper_class = esc_attr( implode( ' ', $args[ 'class' ] ) );
			$wrapper_id = esc_attr( $args[ 'id' ] ) . '_field';

			$field = sprintf( $field_wrapper, $wrapper_class, $wrapper_id, $field_html );
		}

		$field = apply_filters( 'hotelier_form_field_' . $args[ 'type' ], $field, $key, $args, $value );

		echo $field;
	}
}

if ( ! function_exists( 'hotelier_quantity_input' ) ) {

	/**
	 * Output the quantity input for add to cart forms.
	 *
	 * @param  array $args Args for the input
	 * @param  HTL_Room|null $room
	 * @param  boolean $echo Whether to return or echo|string
	 */
	function hotelier_quantity_input( $args = array(), $room = null, $echo = true ) {
		if ( is_null( $room ) )
			$room = $GLOBALS[ 'room' ];

		$defaults = array(
			'id'          => '',
			'input_name'  => 'quantity',
			'input_value' => '1',
			'max_value'   => apply_filters( 'hotelier_quantity_input_max', '', $room ),
			'min_value'   => apply_filters( 'hotelier_quantity_input_min', '', $room ),
			'step'        => apply_filters( 'hotelier_quantity_input_step', '1', $room )
		);

		$args = apply_filters( 'hotelier_quantity_input_args', wp_parse_args( $args, $defaults ), $room );

		ob_start();

		htl_get_template( 'global/quantity-input.php', $args );

		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}
}

/***** Photoswipe ***************************/

if ( ! function_exists( 'htl_photoswipe_markup' ) ) {
	/**
	 * Output PhotoSwipe (.pswp) element to DOM.
	 */
	function htl_photoswipe_markup() {
		if ( htl_get_option( 'room_lightbox', true ) && ( is_listing() || is_room() ) ) {
			$html = '<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"><div class="pswp__bg"></div><div class="pswp__scroll-wrap"><div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"><div class="pswp__top-bar"><div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button><button class="pswp__button pswp__button--share" title="Share"></button><button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button><button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button><div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"><div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button><button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button><div class="pswp__caption"><div class="pswp__caption__center"></div></div></div></div></div>';

			echo $html;
		}
	}
}
add_action( 'wp_footer', 'htl_photoswipe_markup' );

/**
 * Replaces privacy placeholder with a link to the privacy policy page.
 */
function hotelier_replace_policy_page_link_placeholder( $text ) {
	$privacy_page_id = get_option( 'wp_page_for_privacy_policy', 0 );
	$privacy_link    = $privacy_page_id ? '<a href="' . esc_url( get_permalink( $privacy_page_id ) ) . '" class="privacy-policy-text__link" target="_blank">' . __( 'privacy policy', 'wp-hotelier' ) . '</a>' : __( 'privacy policy', 'wp-hotelier' );
	$text            = str_replace( '[privacy_policy]', $privacy_link, $text );

	return $text;
}

/**
 * Output privacy policy text.
 */
function hotelier_privacy_policy_text() {
	$snippet_text = htl_get_option( 'privacy_settings_snippet', 'Your personal data will be used to support your experience throughout this website, to process your reservations, and for other purposes described in our [privacy_policy].' );

	if ( ! get_option( 'wp_page_for_privacy_policy', 0 ) || ! $snippet_text ) {
		return;
	}

	$text = wp_kses_post( wpautop( hotelier_replace_policy_page_link_placeholder( $snippet_text ) ) );

	echo '<div class="privacy-policy-text">' . $text . '</div>';
}

/**
 * Gets the url to remove an item from the cart.
 */
function htl_get_cart_remove_url( $cart_item_key ) {
	$listing_page_url = htl_get_page_permalink( 'listing' );

	return $listing_page_url ? wp_nonce_url( add_query_arg( 'remove_room', $cart_item_key, $listing_page_url ) ) : '';
}

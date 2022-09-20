<?php
/**
 * Functions for the templating system.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'body_class', 'htl_body_class' );
add_filter( 'post_class', 'htl_post_class' );

/**
 * Output Hotelier generator tag
 */
add_action( 'get_the_generator_html', 'htl_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'htl_generator_tag', 10, 2 );

/**
 * Global
 */
add_action( 'hotelier_before_main_content', 'hotelier_output_content_wrapper', 10 );
add_action( 'hotelier_after_main_content', 'hotelier_output_content_wrapper_end', 10 );
add_action( 'hotelier_sidebar', 'hotelier_get_sidebar', 10 );
add_action( 'hotelier_pagination', 'hotelier_pagination', 10 );

/**
 * Single Room
 */
add_action( 'hotelier_single_room_images', 'hotelier_template_single_room_image', 10 );
add_action( 'hotelier_single_room_images', 'hotelier_template_single_room_gallery', 20 );
add_action( 'hotelier_single_room_title', 'hotelier_template_single_room_title', 10 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_datepicker', 5 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_price', 10 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_non_cancellable_info', 15 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_deposit', 20 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_min_max_info', 25 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_meta', 30 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_facilities', 40 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_conditions', 50 );
add_action( 'hotelier_single_room_details', 'hotelier_template_single_room_sharing', 60 );
add_action( 'hotelier_single_room_description', 'hotelier_template_single_room_description', 10 );
add_action( 'hotelier_single_room_rates', 'hotelier_template_single_room_rates', 10 );
add_action( 'hotelier_single_room_single_rate', 'hotelier_template_single_room_single_rate', 10 );

// Rate content
add_action( 'hotelier_single_room_rate_content', 'hotelier_template_single_room_rate_name', 10 );
add_action( 'hotelier_single_room_rate_content', 'hotelier_template_single_room_rate_description', 15 );
add_action( 'hotelier_single_room_rate_content', 'hotelier_template_single_room_rate_conditions', 20 );
add_action( 'hotelier_single_room_rate_content', 'hotelier_template_single_room_rate_min_max_info', 25 );
add_action( 'hotelier_single_room_rate_actions', 'hotelier_template_single_room_rate_price', 10 );
add_action( 'hotelier_single_room_rate_actions', 'hotelier_template_single_room_rate_non_cancellable_info', 15 );
add_action( 'hotelier_single_room_rate_actions', 'hotelier_template_single_room_rate_check_availability', 20 );
add_action( 'hotelier_single_room_rate_actions', 'hotelier_template_single_room_rate_deposit', 25 );
add_action( 'hotelier_output_related_rooms', 'hotelier_template_related_rooms', 10 );

/**
 * Archive Loop Items
 */
add_action( 'hotelier_archive_description', 'hotelier_taxonomy_archive_description', 10 );
add_action( 'hotelier_before_archive_room_loop', 'hotelier_before_archive_room_loop', 10 );
add_action( 'hotelier_after_archive_room_loop', 'hotelier_output_loop_wrapper_end', 10 );
add_action( 'hotelier_archive_item_room', 'hotelier_template_archive_room_image', 5 );
add_action( 'hotelier_archive_item_room', 'hotelier_template_archive_room_title', 10 );
add_action( 'hotelier_archive_item_room', 'hotelier_template_archive_room_description', 20 );
add_action( 'hotelier_archive_item_room', 'hotelier_template_archive_room_price', 30 );
add_action( 'hotelier_archive_item_room', 'hotelier_template_archive_room_more', 40 );

/**
 * Listing Items
 */
add_action( 'hotelier_room_list_datepicker', 'hotelier_template_datepicker', 10 );
add_action( 'hotelier_room_list_selected_nights', 'hotelier_template_selected_nights', 10 );
add_action( 'hotelier_room_list_item_content', 'hotelier_template_room_list_content', 10, 2 );
add_action( 'hotelier_room_list_item_title', 'hotelier_template_rooms_left', 10, 4 );
add_action( 'hotelier_room_list_item_title', 'hotelier_template_room_list_title', 20, 4 );
add_action( 'hotelier_room_list_item_images', 'hotelier_template_loop_room_image', 10 );
add_action( 'hotelier_room_list_item_images', 'hotelier_template_loop_room_thumbnails', 20 );
add_action( 'hotelier_room_list_item_description', 'hotelier_template_loop_room_short_description', 10 );
add_action( 'hotelier_room_list_item_meta', 'hotelier_template_loop_room_facilities', 10 );
add_action( 'hotelier_room_list_item_meta', 'hotelier_template_loop_room_meta', 15 );
add_action( 'hotelier_room_list_item_meta', 'hotelier_template_loop_room_conditions', 20 );
add_action( 'hotelier_room_list_item_deposit', 'hotelier_template_loop_room_deposit', 10 );
add_action( 'hotelier_room_list_item_guests', 'hotelier_template_loop_room_guests', 10 );
add_action( 'hotelier_room_list_item_price', 'hotelier_template_loop_room_price', 10, 3 );
add_action( 'hotelier_room_list_not_available_info', 'hotelier_template_loop_room_not_available_info', 10, 2 );
add_action( 'hotelier_room_list_min_max_info', 'hotelier_template_loop_room_min_max_info', 10 );
add_action( 'hotelier_room_list_item_before_add_to_cart', 'hotelier_template_loop_room_non_cancellable_info', 10 );
add_action( 'hotelier_room_list_after_standard_content', 'hotelier_template_loop_room_fees', 10 );
add_action( 'hotelier_room_list_print_toggle_rates_button', 'hotelier_template_loop_toggle_rates_button', 10 );

// Hide book button when booking_mode is set to 'no-booking'
if ( htl_get_option( 'booking_mode' ) != 'no-booking' ) {
	add_action( 'hotelier_room_list_item_add_to_cart', 'hotelier_template_loop_room_add_to_cart', 10, 2 );
	add_action( 'hotelier_reserve_button', 'hotelier_template_loop_room_reserve_button', 10 );
}

add_action( 'hotelier_room_list_item_rate', 'hotelier_template_loop_room_rate', 10, 5 );
add_action( 'hotelier_room_list_item_rate_content', 'hotelier_template_loop_room_rate_name', 10, 2 );
add_action( 'hotelier_room_list_item_rate_content', 'hotelier_template_loop_room_rate_description', 15, 2 );
add_action( 'hotelier_room_list_item_rate_content', 'hotelier_template_loop_room_rate_conditions', 20, 2 );
add_action( 'hotelier_room_list_item_rate_content', 'hotelier_template_loop_room_rate_deposit', 25, 2 );
add_action( 'hotelier_room_list_item_rate_content', 'hotelier_template_loop_room_rate_min_max_info', 30, 2 );
add_action( 'hotelier_room_list_item_rate_content', 'hotelier_template_loop_room_rate_fees', 40, 2 );
add_action( 'hotelier_room_list_item_rate_actions', 'hotelier_template_loop_room_rate_price', 10, 4 );
add_action( 'hotelier_room_list_item_rate_actions', 'hotelier_template_loop_room_rate_non_cancellable_info', 12, 4 );

// Hide book button when booking_mode is set to 'no-booking'
if ( htl_get_option( 'booking_mode' ) != 'no-booking' ) {
	add_action( 'hotelier_room_list_item_rate_actions', 'hotelier_template_loop_room_rate_add_to_cart', 15, 5 );
}

/**
 * Listing Items (card)
 */
add_action( 'hotelier_room_list_card_room_gallery', 'hotelier_template_room_card_image', 10 );
add_action( 'hotelier_room_list_card_room_content', 'hotelier_template_room_card_title', 10, 4 );
add_action( 'hotelier_room_list_card_room_content', 'hotelier_template_room_card_meta', 20, 4 );
add_action( 'hotelier_room_list_card_room_content', 'hotelier_template_room_card_description', 30, 4 );
add_action( 'hotelier_room_list_card_room_content', 'hotelier_template_room_card_facilities', 40, 4 );
add_action( 'hotelier_room_list_card_room_content', 'hotelier_template_room_card_max_guests_info', 50, 4 );
add_action( 'hotelier_room_list_card_room_content', 'hotelier_template_room_card_not_available_info', 60, 4 );
add_action( 'hotelier_room_list_card_room_action_content', 'hotelier_template_room_card_deposit', 10, 4 );
add_action( 'hotelier_room_list_card_room_action_content', 'hotelier_template_room_card_conditions', 20, 4 );
add_action( 'hotelier_room_list_card_room_action_content', 'hotelier_template_room_card_min_max_info', 30, 4 );
add_action( 'hotelier_room_list_card_room_action_content', 'hotelier_template_room_card_non_cancellable_info', 40, 4 );
add_action( 'hotelier_room_list_card_room_action_content', 'hotelier_template_room_card_fees', 50, 4 );
add_action( 'hotelier_room_list_card_room_action_button', 'hotelier_template_room_card_price', 10, 4 );

// Hide book button when booking_mode is set to 'no-booking'
if ( htl_get_option( 'booking_mode' ) != 'no-booking' ) {
	add_action( 'hotelier_room_list_card_room_action_button', 'hotelier_template_loop_room_add_to_cart', 20, 4 );
}

add_action( 'hotelier_room_list_card_rate_action_content', 'hotelier_template_room_card_rate_name', 10, 5 );
add_action( 'hotelier_room_list_card_rate_action_content', 'hotelier_template_room_card_rate_description', 20, 5 );
add_action( 'hotelier_room_list_card_rate_action_content', 'hotelier_template_room_card_rate_deposit', 30, 5 );
add_action( 'hotelier_room_list_card_rate_action_content', 'hotelier_template_room_card_rate_conditions', 40, 5 );
add_action( 'hotelier_room_list_card_rate_action_content', 'hotelier_template_room_card_rate_min_max_info', 50, 5 );
add_action( 'hotelier_room_list_card_rate_action_content', 'hotelier_template_room_card_rate_non_cancellable_info', 60, 5 );
add_action( 'hotelier_room_list_card_rate_action_content', 'hotelier_template_room_card_rate_fees', 70, 5 );

add_action( 'hotelier_room_list_card_rate_action_button', 'hotelier_template_room_card_rate_price', 10, 5 );

// Hide book button when booking_mode is set to 'no-booking'
if ( htl_get_option( 'booking_mode' ) != 'no-booking' ) {
	add_action( 'hotelier_room_list_card_rate_action_button', 'hotelier_template_loop_room_rate_add_to_cart', 20, 5 );
}

/**
 * Booking and form pay page
 */
add_action( 'hotelier_booking_before_submit', 'hotelier_privacy_policy_text', 5 );
add_action( 'hotelier_booking_before_submit', 'hotelier_template_terms_checkbox', 10 );
add_action( 'hotelier_form_pay_before_submit', 'hotelier_privacy_policy_text', 5 );
add_action( 'hotelier_form_pay_before_submit', 'hotelier_template_terms_checkbox', 10 );
add_action( 'hotelier_reservation_table_guests', 'hotelier_reservation_table_guests', 10, 3 );
add_action( 'hotelier_reservation_table_extras', 'hotelier_reservation_table_extras', 10, 3 );
add_action( 'hotelier_reservation_table_coupon_form', 'hotelier_reservation_show_coupon_form', 10 );

/**
 * Reservation details
 */
add_action( 'hotelier_received', 'hotelier_template_reservation_table', 10 );
add_action( 'hotelier_reservation_details', 'hotelier_template_reservation_details', 10 );
add_action( 'hotelier_after_reservation_table', 'hotelier_template_guest_details', 10 );
add_action( 'hotelier_after_reservation_table', 'hotelier_template_cancel_reservation', 10 );

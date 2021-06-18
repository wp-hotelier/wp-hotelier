<?php
/**
 * Hotelier Uninstall
 *
 * Uninstalling Hotelier deletes user roles, pages, tables, and options.
 *
 * @author      Benito Lopez <hello@lopezb.com>
 * @category    Core
 * @package     HTL/Uninstaller
 * @version     2.6.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$hotelier_options = get_option( 'hotelier_settings' );

if ( ! empty( $hotelier_options[ 'remove_data_uninstall' ] ) ) {

	// Roles + caps
	include_once( 'includes/class-htl-roles.php' );
	$roles = new HTL_Roles;
	$roles->remove_roles();

	global $wpdb;

	// Pages.
	wp_trash_post( get_option( 'hotelier_booking_page_id' ) );
	wp_trash_post( get_option( 'hotelier_listing_page_id' ) );

	// Tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}hotelier_bookings" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}hotelier_reservation_itemmeta" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}hotelier_reservation_items" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}hotelier_rooms_bookings" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}hotelier_sessions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}hotelier_bookings" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}hotelier_bookings" );

	// Delete options
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'hotelier\_%';");

	// Delete posts + meta.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'room', 'room_reservation', 'coupon', 'extra' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

	// Delete cron jobs when uninstalling
	wp_clear_scheduled_hook( 'hotelier_cancel_pending_reservations' );
	wp_clear_scheduled_hook( 'hotelier_process_completed_reservations' );
	wp_clear_scheduled_hook( 'hotelier_cleanup_sessions' );
	wp_clear_scheduled_hook( 'hotelier_check_license_cron' );
}

<?php
/**
 * Displayed when no rooms are found matching the current query.
 *
 * Override this template by copying it to yourtheme/hotelier/room-list/no-rooms-available.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="hotelier-notice hotelier-notice--info hotelier-notice--no-rooms-available"><?php esc_html_e( 'We are sorry, there are no rooms available on your requested dates. Please try again with some different dates.', 'wp-hotelier' ); ?></p>

<?php
	/**
	 * hotelier_room_list_datepicker hook
	 *
	 * @hooked hotelier_template_datepicker - 10
	 */
	do_action( 'hotelier_room_list_datepicker' );
?>

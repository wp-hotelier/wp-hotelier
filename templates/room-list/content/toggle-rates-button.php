<?php
/**
 * Toggle rates button
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/toggle-rates-button.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<a href="#room-variations-<?php echo absint( $room->id ); ?>" data-closed="<?php esc_html_e( 'Show rates', 'wp-hotelier' ); ?>" data-open="<?php esc_html_e( 'Hide rates', 'wp-hotelier' ); ?>" class="button button--toggle-rates"><?php esc_html_e( 'Hide rates', 'wp-hotelier' ); ?></a>

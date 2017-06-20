<?php
/**
 * Displayed when no rooms are found matching the current query
 *
 * This template can be overridden by copying it to yourtheme/hotelier/loop/no-rooms-found.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="no-rooms-found"><?php esc_html_e( 'No rooms were found matching your selection.', 'wp-hotelier' ); ?></p>

<?php
/**
 * The more button
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/content/room-more-button.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<a class="button button--view-room-details" href="<?php the_permalink() ?>"><?php esc_html_e( 'View room details', 'wp-hotelier' ); ?></a>

<?php
/**
 * The room description
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/content/room-description.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$description = apply_filters( 'hotelier_short_description', $post->post_excerpt );

if ( ! $description ) {
	return;
}
?>

<div class="room__description room__description--loop">
	<?php echo wp_kses_post( $description ); ?>
</div>

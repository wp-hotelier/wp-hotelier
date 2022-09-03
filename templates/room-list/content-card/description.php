<?php
/**
 * Room short description
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/short-description.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>

<div class="room-card__description">
	<?php echo apply_filters( 'hotelier_short_description', $post->post_excerpt ) ?>
</div>

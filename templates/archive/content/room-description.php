<?php
/**
 * The room description
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/content/room-description.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<div class="room__description room__description--loop">
	<?php echo apply_filters( 'hotelier_short_description', $post->post_excerpt ) ?>
</div>

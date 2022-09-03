<?php
/**
 * Room rate description
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/rate/rate-description.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $description = $variation->get_room_description() ) : ?>

<div class="room-card__rate-description"><?php echo wp_kses( $variation->get_room_description(), array( 'p' => array() ) ); ?></div>

<?php endif; ?>

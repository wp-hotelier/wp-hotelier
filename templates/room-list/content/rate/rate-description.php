<?php
/**
 * Room rate description
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/rate/rate-description.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $description = $variation->get_room_description() ) : ?>

<div class="rate__description rate__description--listing"><?php echo wp_kses( $variation->get_room_description(), array( 'p' => array() ) ); ?></div>

<?php endif; ?>

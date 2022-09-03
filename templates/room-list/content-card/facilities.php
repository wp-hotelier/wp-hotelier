<?php
/**
 * Room facilities
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/facilities.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<?php if ( $facilities = $room->get_facilities() ) : ?>

	<div class="room-card__facilities"><?php echo $facilities; ?></div>

<?php endif; ?>

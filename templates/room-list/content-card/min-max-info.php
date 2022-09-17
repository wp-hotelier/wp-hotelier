<?php
/**
 * Show info when a room requires min/max nights.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/min-max-info.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="room-card__min-max-stay room-card__info">
	<?php echo wp_kses_post( $info ); ?>
</div>

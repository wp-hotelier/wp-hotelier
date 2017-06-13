<?php
/**
 * Show info when a room requires min/max nights
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/min-max-info.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="room__min-max-stay room__min-max-stay--listing">
	<?php echo wp_kses_post( $info ); ?>
</div>

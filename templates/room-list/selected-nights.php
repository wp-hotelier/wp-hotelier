<?php
/**
 * Selected nights
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/selected-nights.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nights = absint( $nights );

?>

<?php if ( $nights > 0 ) : ?>

	<p class="selected-nights"><?php printf( _nx( '%s-night stay', '%s-nights stay', $nights, 'selected_nights', 'wp-hotelier' ), $nights ); ?></p>

<?php endif; ?>

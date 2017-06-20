<?php
/**
 * Room rate conditions
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/content/rate/rate-conditions.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $variation->has_conditions() ) {
	return;
}

?>

<div class="rate__conditions rate__conditions--single">

	<span class="rate__conditions-title rate__conditions-title--single"><?php esc_html_e( 'Rate conditions:', 'wp-hotelier' ) ?></span>

	<ul class="rate__conditions-list rate__conditions-list--single">

	<?php foreach ( $variation->get_room_conditions() as $condition ) : ?>

		<li class="rate__conditions-item rate__conditions-item--single"><?php echo esc_html( $condition ); ?></li>

	<?php endforeach; ?>

	</ul>

</div>

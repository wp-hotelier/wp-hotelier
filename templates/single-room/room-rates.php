<?php
/**
 * Room rates.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/room-rates.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->is_variable_room() ) {
	return;
}

$variations = $room->get_room_variations();

?>

<div class="room__rates room__rates--single">

	<?php if ( apply_filters( 'hotelier_single_room_rates_show_title', true ) ) : ?>
		<h3 class="room__rates-title"><?php esc_html_e( 'Available rates', 'wp-hotelier' ); ?></h3>
	<?php endif; ?>

	<ul id="room-rates-<?php echo absint( get_the_ID() ); ?>" class="room__rates-list">

		<?php
		// Print room rates
		foreach ( $variations as $variation ) :
			$variation = new HTL_Room_Variation( $variation, $room->id ); ?>

			<?php
				/**
				 * hotelier_single_room_single_rate hook.
				 *
				 * @hooked hotelier_template_single_room_single_rate - 10
				 */
				do_action( 'hotelier_single_room_single_rate', $variation );
			?>

		<?php endforeach; ?>
	</ul>

</div>

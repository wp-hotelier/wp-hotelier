<?php
/**
 * Single rate.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/rate.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<li class="room__rate room__rate--single">

	<div class="rate__description-wrapper">

		<?php
			/**
			 * hotelier_single_room_rate_content hook
			 *
			 * @hooked hotelier_template_single_room_rate_name - 10
			 * @hooked hotelier_template_single_room_rate_description - 15
			 * @hooked hotelier_template_single_room_rate_conditions - 20
			 * @hooked hotelier_template_single_room_rate_min_max_info - 25
			 */
			do_action( 'hotelier_single_room_rate_content', $variation );
		?>

	</div>

	<div class="rate__actions rate__actions--single">

		<?php
			/**
			 * hotelier_single_room_rate_actions hook
			 *
			 * @hooked hotelier_template_single_room_rate_price - 10
			 * @hooked hotelier_template_single_room_rate_non_cancellable_info - 15
			 * @hooked hotelier_template_single_room_rate_check_availability - 20
			 * @hooked hotelier_template_single_room_rate_deposit - 25
			 */
			do_action( 'hotelier_single_room_rate_actions', $variation );
		?>

	</div>

</li>

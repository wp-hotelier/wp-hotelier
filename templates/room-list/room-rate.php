<?php
/**
 * The template for displaying room variation in the listing loop
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/room-rate.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<div class="room__rate room__rate--listing">
	<div class="rate__content rate__content--listing">
		<?php
			/**
			 * hotelier_room_list_item_rate_content hook
			 *
			 * @hooked hotelier_template_loop_room_rate_name - 10
			 * @hooked hotelier_template_loop_room_rate_description - 15
			 * @hooked hotelier_template_loop_room_rate_conditions - 20
			 * @hooked hotelier_template_loop_room_rate_deposit - 25
			 * @hooked hotelier_template_loop_room_rate_min_max_info - 30
			 * @hooked hotelier_template_loop_room_rate_fees - 40
			 */
			do_action( 'hotelier_room_list_item_rate_content', $variation, $shortcode_atts );
		?>
	</div><!-- .rate__content -->

	<div class="rate__actions rate__actions--listing">
		<?php
			/**
			 * hotelier_room_list_item_rate_actions hook
			 *
			 * @hooked hotelier_template_loop_room_rate_price - 10
			 * @hooked hotelier_template_loop_room_rate_non_cancellable_info - 12
			 * @hooked hotelier_template_loop_room_rate_add_to_cart - 15
			 */
			do_action( 'hotelier_room_list_item_rate_actions', $variation, $is_available, $checkin, $checkout, $shortcode_atts );
		?>
	</div><!-- .rate__actions -->

	<?php do_action( 'hotelier_room_list_after_item_rate', $variation, $is_available, $checkin, $checkout, $shortcode_atts ); ?>
</div>

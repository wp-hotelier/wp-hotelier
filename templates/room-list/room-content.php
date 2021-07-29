<?php
/**
 * The template for displaying room content in the listing loop
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/room-content.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$checkin      = HTL()->session->get( 'checkin' );
$checkout     = HTL()->session->get( 'checkout' );
$is_available = $room->is_available( $checkin, $checkout );

$listing_room_classes = array(
	'listing__room'
);

if ( $is_single ) {
	$listing_room_classes[] = 'listing__room--queried';
}
?>

<li <?php post_class( $listing_room_classes ); ?>>

	<div class="room__content-wrapper">

		<div class="room__content room__content--listing">

			<?php
				/**
				 * hotelier_room_list_item_title hook
				 *
				 * @hooked hotelier_template_rooms_left - 10
				 * @hooked hotelier_template_loop_room_title - 20
				 */
				do_action( 'hotelier_room_list_item_title', $is_available, $checkin, $checkout, $shortcode_atts );
			?>

			<div class="room__gallery room__gallery--listing">

				<?php
					/**
					 * hotelier_room_list_item_thumb hook
					 *
					 * @hooked hotelier_template_loop_room_image - 10
					 * @hooked hotelier_template_loop_room_thumbnails - 20
					 */
					do_action( 'hotelier_room_list_item_images' );
				?>

			</div>

			<?php
				/**
				 * hotelier_room_list_item_description hook
				 *
				 * @hooked hotelier_template_loop_room_short_description - 10
				 */
				do_action( 'hotelier_room_list_item_description' );
			?>

			<div id="room-details-<?php echo esc_attr( $room->id ); ?>" class="room__details room__details--listing">

				<?php
					/**
					 * hotelier_room_list_item_meta hook
					 *
					 * @hooked hotelier_template_loop_room_facilities - 10
					 * @hooked hotelier_template_loop_room_meta - 15
					 * @hooked hotelier_template_loop_room_conditions - 20
					 */
					do_action( 'hotelier_room_list_item_meta' );
				?>

			</div>

			<?php
				/**
				 * hotelier_room_list_item_deposit hook
				 *
				 * @hooked hotelier_template_loop_room_deposit - 10
				 */
				do_action( 'hotelier_room_list_item_deposit' );
			?>

			<?php
				/**
				 * hotelier_room_list_item_guests hook
				 *
				 * @hooked hotelier_template_loop_room_guests - 10
				 */
				do_action( 'hotelier_room_list_item_guests' );
			?>

			<?php
				/**
				 * hotelier_room_list_not_available_info hook
				 *
				 * @hooked hotelier_template_loop_room_not_available_info - 10
				 */
				do_action( 'hotelier_room_list_not_available_info', $is_available );
			?>

			<?php
				/**
				 * hotelier_room_list_min_max_info hook
				 *
				 * @hooked hotelier_template_loop_room_min_max_info - 10
				 */
				do_action( 'hotelier_room_list_min_max_info' );
			?>

			<?php do_action( 'hotelier_room_list_after_content' ); ?>

			<?php if ( ! $room->is_variable_room() ) : ?>

				<?php
					/**
					 * hotelier_room_list_after_standard_content hook
					 *
					 * @hooked hotelier_template_loop_room_fees - 10
					 */
					do_action( 'hotelier_room_list_after_standard_content' );
				?>

			<?php endif; ?>

		</div><!-- .room__content -->

		<div class="room__actions">

		<?php
			/**
			 * hotelier_room_list_item_price hook
			 *
			 * @hooked hotelier_template_loop_room_price - 10
			 */
			do_action( 'hotelier_room_list_item_price', $checkin, $checkout );
		?>

		<?php if ( $room->is_variable_room() ) : ?>

				<?php
					/**
					 * hotelier_room_list_print_toggle_rates_button hook
					 *
					 * @hooked hotelier_template_loop_toggle_rates_button - 10
					 */
					do_action( 'hotelier_room_list_print_toggle_rates_button', $shortcode_atts );
				?>

				</div><!-- .room__actions -->

			</div><!-- .room__content-wrapper -->

			<div class="clear"></div>

			<div id="room-variations-<?php echo absint( $room->id ); ?>" class="room__rates room__rates--listing">

				<?php
				$varitations = $room->get_room_variations();

				// Print room rates
				foreach ( $varitations as $variation ) :
					$variation = new HTL_Room_Variation( $variation, $room->id ); ?>

					<?php if ( apply_filters( 'hotelier_room_list_show_item_rate', true, $variation, $checkin, $checkout ) ) : ?>

						<?php
							/**
							 * hotelier_room_list_item_rate hook
							 *
							 * @hooked hotelier_template_loop_room_rate - 10
							 */
							do_action( 'hotelier_room_list_item_rate', $variation, $is_available, $checkin, $checkout, $shortcode_atts );
						?>

					<?php endif; ?>

				<?php endforeach; ?>

			</div><!-- .room__rates -->

		<?php else : ?>

				<?php
					/**
					 * hotelier_room_list_item_before_add_to_cart hook
					 *
					 * @hooked hotelier_template_loop_room_non_cancellable_info - 10
					 */
					do_action( 'hotelier_room_list_item_before_add_to_cart' );
				?>

				<?php
					/**
					 * hotelier_room_list_item_add_to_cart hook
					 *
					 * @hooked hotelier_template_loop_room_add_to_cart - 10
					 */
					do_action( 'hotelier_room_list_item_add_to_cart', $is_available, $shortcode_atts );
				?>

				</div><!-- .room__actions -->

			</div><!-- .room__content-wrapper -->

		<?php endif; ?>

		<?php do_action( 'hotelier_room_list_after_content_wrapper', $is_available, $checkin, $checkout, $shortcode_atts ); ?>
</li>

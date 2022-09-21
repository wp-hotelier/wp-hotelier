<?php
/**
 * The template for displaying the room content card in the listing loop
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/room-content-card.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
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

	<?php do_action( 'hotelier_room_list_before_card_content_wrapper', $is_available, $checkin, $checkout, $shortcode_atts ); ?>

	<div class="room-card-wrapper">

		<?php
			/**
			 * hotelier_room_list_card_room_gallery hook
			 *
			 */
			do_action( 'hotelier_room_list_card_room_gallery', $shortcode_atts );
		?>

		<div class="room-card__content">

			<div class="room-card__details">

				<?php
					/**
					 * hotelier_room_list_card_room_content hook
					 *
					 */
					do_action( 'hotelier_room_list_card_room_content', $is_available, $checkin, $checkout, $shortcode_atts );
				?>

			</div><!-- .room-card__details -->

			<div class="room-card__actions">

				<?php if ( ! $room->is_variable_room() ) : ?>

					<div class="room-card__action">

						<div class="room-card__action-content">

							<?php
								/**
								 * hotelier_room_list_card_room_action_content hook
								 *
								 */
								do_action( 'hotelier_room_list_card_room_action_content', $is_available, $checkin, $checkout, $shortcode_atts );
							?>

						</div><!-- .room-card__action-content -->

						<div class="room-card__action-button">

							<?php
								/**
								 * hotelier_room_list_card_room_action_button hook
								 *
								 */
								do_action( 'hotelier_room_list_card_room_action_button', $is_available, $checkin, $checkout, $shortcode_atts );
							?>

						</div><!-- .room-card__action-button -->

					</div><!-- .room-card__action -->

					<?php
						/**
						 * hotelier_room_list_card_after_room_action hook
						 *
						 */
						do_action( 'hotelier_room_list_card_after_room_action', $is_available, $checkin, $checkout, $shortcode_atts );
					?>

				<?php else : ?>

					<?php
					$varitations = $room->get_room_variations();

					// Print room rates
					foreach ( $varitations as $variation ) :
						$variation = new HTL_Room_Variation( $variation, $room->id ); ?>

						<?php if ( apply_filters( 'hotelier_room_list_show_item_rate', true, $variation, $checkin, $checkout ) ) : ?>

							<div class="room-card__action">

								<div class="room-card__action-content room-card__action-content--rate">

									<?php
										/**
										 * hotelier_room_list_card_rate_action_content hook
										 *
										 */
										do_action( 'hotelier_room_list_card_rate_action_content', $variation, $is_available, $checkin, $checkout, $shortcode_atts );
									?>

								</div><!-- .room-card__action-text -->

								<div class="room-card__action-button room-card__action-button--rate">

									<?php
										/**
										 * hotelier_room_list_card_rate_action_button hook
										 *
										 */
										do_action( 'hotelier_room_list_card_rate_action_button', $variation, $is_available, $checkin, $checkout, $shortcode_atts );
									?>

								</div><!-- .room-card__action-button -->

							</div><!-- .room-card__action -->

							<?php
								/**
								 * hotelier_room_list_card_after_rate_action hook
								 *
								 */
								do_action( 'hotelier_room_list_card_after_rate_action', $variation, $is_available, $checkin, $checkout, $shortcode_atts );
							?>

						<?php endif; ?>

					<?php endforeach; ?>

				<?php endif; ?>

			</div><!-- .room-card__actions -->

		</div><!-- .room-card__content -->

	</div><!-- .room-card-wrapper -->

	<?php do_action( 'hotelier_room_list_after_card_content_wrapper', $is_available, $checkin, $checkout, $shortcode_atts ); ?>

</li>

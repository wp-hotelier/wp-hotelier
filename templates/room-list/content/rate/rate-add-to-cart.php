<?php
/**
 * Room rate add to cart
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/rate/rate-add-to-cart.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$key             = htl_generate_item_key( $room->id, $variation->get_room_index() );
$available_rooms = absint( $room->get_available_rooms( $checkin, $checkout ) );

?>

<?php if ( $available_rooms > 0 && $is_available && apply_filters( 'hotelier_show_rate_add_to_cart_button', true, $checkin, $checkout, $variation ) ) : ?>

	<div class="add-to-cart-wrapper add-to-cart-wrapper--rate">

			<?php do_action( 'hotelier_before_add_to_cart_button' ); ?>

			<?php
				hotelier_quantity_input( array(
					'id'          => 'add-to-cart-room[' . esc_attr( $key ) . ']',
					'min_value'   => apply_filters( 'hotelier_quantity_input_min', 0, $room ),
					'max_value'   => apply_filters( 'hotelier_quantity_input_max', $room->get_stock_rooms(), $room ),
					'input_value' => 0,
					'input_name'  => "quantity[{$key}]"
				) );
			?>

			<input type="hidden" name="add_to_cart_room[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( apply_filters( 'hotelier_add_to_cart_room_id', $room->id, $room ) ); ?>" />
			<input type="hidden" name="rate_id[<?php echo esc_attr( $key ); ?>]" value="<?php echo absint( $variation->get_room_index() ); ?>" />

			<a href="#reserve-button" data-selected-text-singular="<?php echo esc_attr_x( 'room selected', 'book now button text: singular', 'wp-hotelier' ); ?>" data-selected-text-plural="<?php echo esc_attr_x( 'rooms selected', 'book now button text: plural', 'wp-hotelier' ); ?>" class="button button--add-to-cart"><?php esc_html_e( 'Book now', 'wp-hotelier' ); ?></a>

			<?php do_action( 'hotelier_after_add_to_cart_button' ); ?>

	</div>

<?php endif; ?>

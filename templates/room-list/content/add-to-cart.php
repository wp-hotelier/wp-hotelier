<?php
/**
 * Room add to cart
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content/add-to-cart.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$key             = htl_generate_item_key( $room->id, 0 );
$checkin         = HTL()->session->get( 'checkin' );
$checkout        = HTL()->session->get( 'checkout' );
$available_rooms = absint( $room->get_available_rooms( $checkin, $checkout ) );

?>

<div class="add-to-cart-wrapper">

	<?php if ( $available_rooms > 0 && $is_available && apply_filters( 'hotelier_show_add_to_cart_button', true, $room, $checkin, $checkout ) ) : ?>

		<?php do_action( 'hotelier_before_add_to_cart_button' ); ?>

		<?php
		$quantity_input_args = array(
			'id'          => 'add-to-cart-room[' . esc_attr( $key ) . ']',
			'min_value'   => apply_filters( 'hotelier_quantity_input_min', 0, $room ),
			'max_value'   => apply_filters( 'hotelier_quantity_input_max', $room->get_stock_rooms(), $room ),
			'input_value' => 0,
			'input_name'  => "quantity[{$key}]",
		);

		if ( $show_guests_selection ) {
			$max_adults     = $room->get_max_guests();
			$adults_options = array();

			for ( $i = 1; $i <= $max_adults; $i++ ) {
				$adults_options[ $i ] = $i;
			}

			$adults_std = apply_filters( 'hotelier_room_list_guests_default_selection_adults', $max_adults );

			$quantity_input_args['adults_args'] = array(
				'input_name' => "adults[{$key}]",
				'label'      => esc_html__( 'Adults', 'wp-hotelier' ),
				'default'    => $adults_std,
				'options'    => $adults_options
			);

			$max_children = $room->get_max_children();

			if ( $max_children > 0 ) {
				$children_options = array();

				for ( $i = 0; $i <= $max_children; $i++ ) {
					$children_options[ $i ] = $i;
				}

				$children_std = apply_filters( 'hotelier_room_list_guests_default_selection_children', 0 );

				$quantity_input_args['children_args'] = array(
					'input_name' => "children[{$key}]",
					'type'       => 'select',
					'label'      => esc_html__( 'Children', 'wp-hotelier' ),
					'class'      => array(),
					'default'    => $children_std,
					'options'    => $children_options
				);
			}
		}

		hotelier_quantity_input( $quantity_input_args );
		?>

		<input type="hidden" name="add_to_cart_room[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( apply_filters( 'hotelier_add_to_cart_room_id', $room->id, $room ) ); ?>" />
		<input type="hidden" name="rate_id[<?php echo esc_attr( $key ); ?>]" value="0" />

		<a href="#reserve-button" data-selected-text-singular="<?php echo esc_attr_x( 'room selected', 'book now button text: singular', 'wp-hotelier' ); ?>" data-selected-text-plural="<?php echo esc_attr_x( 'rooms selected', 'book now button text: plural', 'wp-hotelier' ); ?>" class="button button--add-to-cart"><?php esc_html_e( 'Book now', 'wp-hotelier' ); ?></a>

		<?php do_action( 'hotelier_after_add_to_cart_button' ); ?>

	<?php endif; ?>

</div>

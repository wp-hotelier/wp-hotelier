<?php
/**
 * The template for displaying the AJAX Room Booking widget result (after the AJAX call)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/ajax-room-booking-result.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $room->is_variable_room() ) :
	$varitations      = $room->get_room_variations();
	$prices           = array();
	$varitations_data = array();
	?>

	<?php ob_start(); ?>
	<?php foreach ( $varitations as $variation ) : ?>
		<?php
		$variation = new HTL_Room_Variation( $variation, $room->id );
		$varitations_data[$variation->get_room_index()] = $variation->get_formatted_room_rate();
		?>

		<?php
		htl_get_template( 'widgets/ajax-room-booking/rate/description.php',
			array(
				'room'                  => $room,
				'variation'             => $variation,
				'show_rate_description' => $show_rate_description,
			)
		);
		?>

		<?php
		htl_get_template( 'widgets/ajax-room-booking/rate/conditions.php',
			array(
				'room'                 => $room,
				'variation'            => $variation,
				'show_room_conditions' => $show_room_conditions,
			)
		);
		?>

		<?php
		htl_get_template( 'widgets/ajax-room-booking/rate/fees.php',
			array(
				'room'      => $room,
				'variation' => $variation,
			)
		);
		?>

		<?php do_action( 'hotelier_widget_ajax_room_booking_before_rate_price', $room, $checkin, $checkout, $variation ); ?>

		<?php
		htl_get_template( 'widgets/ajax-room-booking/rate/deposit.php',
			array(
				'room'              => $room,
				'variation'         => $variation,
				'show_room_deposit' => $show_room_deposit,
			)
		);
		?>

		<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--price widget-ajax-room-booking__data--rate" data-rate-id="<?php echo esc_attr( $variation->get_room_index() ); ?>">
			<?php echo $variation->get_price_html( $checkin, $checkout ) ?>
		</p>

		<?php
		htl_get_template( 'widgets/ajax-room-booking/rate/non-cancellable-info.php',
			array(
				'room'      => $room,
				'variation' => $variation,
			)
		);
		?>

	<?php endforeach; ?>
	<?php $result_html = ob_get_clean(); ?>

	<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__row--rates">
		<label class="form-row__label widget-ajax-room-booking__label"><?php echo esc_html_e( 'Select rate', 'wp-hotelier' ); ?></label>

		<select class="form-row__input select" name="rate" id="rate">
			<?php foreach ( $varitations_data as $variation_data_key => $variation_data_value ) : ?>
				<option value="<?php echo esc_attr( $variation_data_key ); ?>"><?php echo esc_html( $variation_data_value ); ?></option>
			<?php endforeach ?>
		</select>
	</p>

	<?php echo $result_html; ?>

<?php else : ?>

	<?php
	htl_get_template( 'widgets/ajax-room-booking/room/conditions.php',
		array(
			'room'                 => $room,
			'show_room_conditions' => $show_room_conditions,
		)
	);
	?>

	<?php
	htl_get_template( 'widgets/ajax-room-booking/room/fees.php',
		array(
			'room' => $room,
		)
	);
	?>

	<?php do_action( 'hotelier_widget_ajax_room_booking_before_room_price', $room, $checkin, $checkout ); ?>

	<?php
	htl_get_template( 'widgets/ajax-room-booking/room/deposit.php',
		array(
			'room'              => $room,
			'show_room_deposit' => $show_room_deposit,
		)
	);
	?>

	<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--price">
		<?php echo $room->get_price_html( $checkin, $checkout ); ?>
	</p>

	<?php
	htl_get_template( 'widgets/ajax-room-booking/room/non-cancellable-info.php',
		array(
			'room' => $room,
		)
	);
	?>
<?php endif; ?>

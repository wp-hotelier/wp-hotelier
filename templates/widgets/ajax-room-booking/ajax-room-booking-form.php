<?php
/**
 * The template for displaying the AJAX Room Booking widget form
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/ajax-room-booking-form.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$datepicker_class = array();

if ( is_array( $datepicker_atts ) ) {
	if ( isset( $datepicker_atts['inline'] ) ) {
		$datepicker_class[] = 'datepicker-form--inline';
	}

	if ( isset( $datepicker_atts['rounded'] ) ) {
		$datepicker_class[] = 'datepicker-form--rounded';
	}

	if ( isset( $datepicker_atts['disabled_style'] ) ) {
		$datepicker_class[] = 'datepicker-form--disabled-style-' . $datepicker_atts['disabled_style'];
	}

	if ( isset( $datepicker_atts['bar'] ) && $datepicker_atts['bar'] === 'bottom' ) {
		$datepicker_class[] = 'datepicker-form--bottom-bar';
	}
}

$datepicker_class = implode( ' ', $datepicker_class );

do_action( 'hotelier_before_widget_ajax_room_booking' );
?>

<div class="widget-ajax_room_booking__wrapper">
	<form class="form--widget-ajax-room-booking <?php echo esc_attr( $datepicker_class ); ?>" id="widget-ajax-room-booking-form" name="widget-ajax-room-booking-form" method="post" data-room-id="<?php echo absint( $room->id ); ?>" data-show-rate-description="<?php echo $show_rate_desc ? 'true' : 'false'; ?>" data-show-room-conditions="<?php echo $show_room_conditions ? 'true' : 'false'; ?>">
		<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__row--dates">
			<label class="form-row__label widget-ajax-room-booking__label"><?php echo esc_html_e( 'Check-in / Check-out', 'wp-hotelier' ); ?></label>

			<span class="datepicker-input-select-wrapper">
				<input class="datepicker-input-select" type="text" value="">
			</span>

			<input type="text" class="datepicker-input datepicker-input--checkin" name="checkin" value="<?php echo esc_attr( $checkin ); ?>" style="display: none;">
			<input type="text" class="datepicker-input datepicker-input--checkout" name="checkout" value="<?php echo esc_attr( $checkout ); ?>" style="display: none;">
		</p>

		<?php if ( $show_quantity ) : ?>
			<?php
			$min_value = apply_filters( 'hotelier_quantity_input_min', 0, $room );
			$max_value = apply_filters( 'hotelier_quantity_input_max', $room->get_stock_rooms(), $room );
			?>
			<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__row--quantity widget-ajax-room-booking__row--pre">
				<label class="form-row__label widget-ajax-room-booking__label"><?php echo esc_html_e( 'Nr. rooms', 'wp-hotelier' ); ?></label>

				<input type="number" id="quantity" step="1" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( $max_value ); ?>" name="quantity" value="1" title="<?php echo esc_attr_x( 'Qty', 'Room quantity input tooltip', 'wp-hotelier' ) ?>" class="form-row__input input-text" data-default="1" />
			</p>
		<?php endif ?>

		<?php
		$print_adults_selection   = false;
		$print_children_selection = false;

		if ( $show_guests_selection && apply_filters( 'hotelier_booking_show_number_of_guests_selection', true, $room ) ) {
			$max_adults = $room->get_max_guests();

			if ( $max_adults > 0 ) {
				$print_adults_selection = true;
				$adults_options         = array();

				for ( $i = 1; $i <= $max_adults; $i++ ) {
					$adults_options[ $i ] = $i;
				}

				$adults_std = apply_filters( 'hotelier_widget_ajax_room_booking_guests_default_selection_adults', $max_adults );
			}

			$max_children = $room->get_max_children();

			if ( $max_children > 0 ) {
				$print_children_selection = true;
				$children_options         = array();

				for ( $i = 0; $i <= $max_children; $i++ ) {
					$children_options[ $i ] = $i;
				}

				$children_std = apply_filters( 'hotelier_widget_ajax_room_booking_guests_default_selection_children', 0 );
			}
		}
		?>

		<?php if ( $print_adults_selection ) : ?>
			<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__row--adults widget-ajax-room-booking__row--pre">
				<label class="form-row__label widget-ajax-room-booking__label"><?php echo esc_html_e( 'Adults', 'wp-hotelier' ); ?></label>

				<select class="form-row__input select" name="adults" id="adults" data-default="<?php echo esc_attr( $adults_std ); ?>">
					<?php foreach ( $adults_options as $adults_option_key => $adults_option_value ): ?>
						<option value="<?php echo esc_attr( $adults_option_key ); ?>" <?php selected( $adults_option_key, $adults_std ); ?>><?php echo esc_html( $adults_option_value ); ?></option>
					<?php endforeach ?>
				</select>
			</p>
		<?php endif; ?>

		<?php if ( $print_children_selection ) : ?>
			<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__row--children widget-ajax-room-booking__row--pre">
				<label class="form-row__label widget-ajax-room-booking__label"><?php echo esc_html_e( 'Children', 'wp-hotelier' ); ?></label>

				<select class="form-row__input select" name="children" id="children" data-default="<?php echo esc_attr( $children_std ); ?>">
					<?php foreach ( $children_options as $children_option_key => $children_option_value ): ?>
							<option value="<?php echo esc_attr( $children_option_key ); ?>" <?php selected( $children_option_key, $children_std ); ?>><?php echo esc_html( $children_option_value ); ?></option>
						<?php endforeach ?>
				</select>
			</p>
		<?php endif; ?>

		<?php // Don't remove this! ?>
		<div class="widget-ajax_room_booking__result"></div>

		<p class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__row--submit">
			<?php echo apply_filters( 'hotelier_widget_ajax_room_booking_html', '<input type="submit" class="button button--widget-ajax-room-booking" name="hotelier_widget_ajax_room_booking_button" id="widget-ajax-room-booking-button" value="' . esc_attr__( 'Check availability', 'wp-hotelier' ) . '" />' ); ?>

			<span id="widget-ajax-room-booking-reset" class="reset--widget-ajax-room-booking" style="display: none;"><?php echo esc_html__( 'Reset selection', 'wp-hotelier' ); ?></span>

			<input type="hidden" name="show_rate_desc" value="<?php echo esc_attr ( $show_rate_desc ); ?>">
			<input type="hidden" name="show_room_conditions" value="<?php echo esc_attr ( $show_room_conditions ); ?>">
			<input type="hidden" name="show_room_deposit" value="<?php echo esc_attr ( $show_room_deposit ); ?>">
		</p>
	</form>
</div>

<?php
do_action( 'hotelier_after_widget_ajax_room_booking' );

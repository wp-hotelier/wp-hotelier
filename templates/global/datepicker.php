<?php
/**
 * Datepicker form
 *
 * This template can be overridden by copying it to yourtheme/hotelier/global/datepicker.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcode_atts = isset( $shortcode_atts ) ? $shortcode_atts : array();

$room_id = is_room() ? get_the_ID() : false;

// extensions can hook into here to add their own pages
$datepicker_form_url = apply_filters( 'hotelier_datepicker_form_url', HTL()->cart->get_room_list_form_url( $room_id ) ); ?>

<?php do_action( 'hotelier_before_datepicker', $shortcode_atts ); ?>

<form name="hotelier_datepicker" method="post" id="hotelier-datepicker" class="datepicker-form" action="<?php echo esc_url( $datepicker_form_url ); ?>" enctype="multipart/form-data">

	<?php do_action( 'hotelier_datepicker_before_input', $shortcode_atts ); ?>

	<span class="datepicker-input-select-wrapper"><input class="datepicker-input-select" type="text" id="hotelier-datepicker-select" value=""></span>
	<input type="text" id="hotelier-datepicker-checkin" class="datepicker-input datepicker-input--checkin" name="checkin" value="<?php echo esc_attr( $checkin ); ?>" style="display: none;">
	<input type="text" id="hotelier-datepicker-checkout" class="datepicker-input datepicker-input--checkout" name="checkout" value="<?php echo esc_attr( $checkout ); ?>" style="display: none;">

	<?php echo apply_filters( 'hotelier_datepicker_button_html', '<input type="submit" class="button button--datepicker" name="hotelier_datepicker_button" id="datepicker-button" value="' . esc_attr__( 'Check availability', 'wp-hotelier' ) . '" />' ); ?>
</form>

<?php do_action( 'hotelier_after_datepicker', $shortcode_atts );


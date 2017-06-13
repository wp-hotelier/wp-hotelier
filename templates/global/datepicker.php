<?php
/**
 * Datepicker form
 *
 * This template can be overridden by copying it to yourtheme/hotelier/global/datepicker.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// extensions can hook into here to add their own pages
$datepicker_form_url = apply_filters( 'hotelier_datepicker_form_url', HTL()->cart->get_room_list_form_url() ); ?>

<form name="hotelier_datepicker" method="post" id="hotelier-datepicker" class="datepicker-form" action="<?php echo esc_url( $datepicker_form_url ); ?>" enctype="multipart/form-data">

	<span class="datepicker-input-select-wrapper"><input class="datepicker-input-select" type="text" id="hotelier-datepicker-select" value=""></span>
	<input type="text" id="hotelier-datepicker-checkin" class="datepicker-input datepicker-input--checkin" name="checkin" value="<?php echo esc_attr( $checkin ); ?>">
	<input type="text" id="hotelier-datepicker-checkout" class="datepicker-input datepicker-input--checkout" name="checkout" value="<?php echo esc_attr( $checkout ); ?>">

	<?php echo apply_filters( 'hotelier_datepicker_button_html', '<input type="submit" class="button button--datepicker" name="hotelier_datepicker_button" id="datepicker-button" value="' . esc_attr__( 'Check availability', 'hotelier' ) . '" />' ); ?>
</form>

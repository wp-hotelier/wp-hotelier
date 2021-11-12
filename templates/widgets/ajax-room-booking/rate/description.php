<?php
/**
 * Rate description
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/rate/description.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $show_rate_description ) : ?>
	<div class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--description widget-ajax-room-booking__data--rate" data-rate-id="<?php echo esc_attr( $variation->get_room_index() ); ?>">
		<?php echo wp_kses( $variation->get_room_description(), array( 'p' => array() ) ); ?>
	</div>
<?php endif; ?>

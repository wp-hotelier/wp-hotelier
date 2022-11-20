<?php
/**
 * Booking Payment Section
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/payment.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="payment" class="booking__section booking__section--payment">
	<header class="section-header">
		<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_booking_section_payment_title', __( 'Payment method', 'wp-hotelier' ) ) ); ?></h3>
	</header>

	<ul class="payment-methods">
		<?php
			if ( ! empty( $available_gateways ) ) {
				$single = ( count( $available_gateways ) == 1 ) ? true : false;

				foreach ( $available_gateways as $gateway ) {
					htl_get_template( 'booking/payment-method.php', array( 'gateway' => $gateway, 'single' => $single ) );
				}
			} else {
				echo '<li class="payment-method payment-method--error">' . esc_html__( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance.', 'wp-hotelier' ) . '</li>';
			}
		?>
	</ul>
</div>

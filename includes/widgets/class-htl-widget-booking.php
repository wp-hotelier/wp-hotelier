<?php
/**
 * Booking Widget.
 *
 * Displays current rooms selected and booking details.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Widgets
 * @package  Hotelier/Widgets
 * @version  1.0.0
 * @extends  HTL_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HTL_Widget_Booking extends HTL_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--hotelier widget-booking';
		$this->widget_description = __( 'Displays current rooms selected and booking details. Visible only in the "listing" and "booking" pages.', 'wp-hotelier' );
		$this->widget_id          = 'hotelier-widget-booking';
		$this->widget_name        = __( 'Hotelier Booking', 'wp-hotelier' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Your stay', 'wp-hotelier' ),
				'label' => __( 'Title', 'wp-hotelier' )
			)
		);

		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( ( ! is_listing() && ! is_booking() ) || is_reservation_received_page() || is_pay_reservation_page() ) {
			return;
		}

		$checkin  = HTL()->session->get( 'checkin' );
		$checkout = HTL()->session->get( 'checkout' );

		$this->widget_start( $args, $instance );

		do_action( 'hotelier_before_widget_booking' );
		?>

		<div class="widget-booking__wrapper">

			<?php if ( is_booking() && ! htl_get_option( 'listing_disabled', false ) ) : ?>
				<p class="widget-booking__change-cart"><a href="<?php echo esc_url( HTL()->cart->get_room_list_form_url() ); ?>" class="widget-booking__change-cart-link"><?php esc_html_e( 'Modify', 'wp-hotelier' ); ?></a></p>
			<?php endif; ?>

			<?php ob_start(); ?>

			<div class="widget-booking__dates">
				<div class="widget-booking__date-block widget-booking__date-block--checkin">
					<span class="widget-booking__date-label"><?php esc_html_e( 'Check-in', 'wp-hotelier' ); ?></span>

					<div class="widget-booking__date">
						<span class="widget-booking__month-year"><?php echo date_i18n( 'M Y', strtotime( $checkin ) ); ?></span>
						<span class="widget-booking__day"><?php echo date_i18n( 'd', strtotime( $checkin ) ); ?></span>
						<span class="widget-booking__day-name"><?php echo date_i18n( 'D', strtotime( $checkin ) ); ?></span>
					</div>
				</div>

				<div class="widget-booking__date-block widget-booking__date-block--checkout">
					<span class="widget-booking__date-label"><?php esc_html_e( 'Check-out', 'wp-hotelier' ); ?></span>

					<div class="widget-booking__date">
						<span class="widget-booking__month-year"><?php echo date_i18n( 'M Y', strtotime( $checkout ) ); ?></span>
						<span class="widget-booking__day"><?php echo date_i18n( 'd', strtotime( $checkout ) ); ?></span>
						<span class="widget-booking__day-name"><?php echo date_i18n( 'D', strtotime( $checkout ) ); ?></span>
					</div>
				</div>
			</div>

			<?php

			$html = ob_get_clean();
			echo apply_filters( 'hotelier_widget_booking_dates', $html, $checkin, $checkout );

			if ( HTL()->session->get( 'cart' ) ) : ?>

				<ul class="widget-booking__rooms-list">

				<?php foreach ( HTL()->cart->get_cart() as $cart_item_key => $cart_item ) :
					$_room    = $cart_item[ 'data' ];
					$_room_id = $cart_item[ 'room_id' ];

					if ( $_room && $_room->exists() && $cart_item[ 'quantity' ] > 0 ) : ?>

						<li class="widget-booking__room-item">
							<a class="widget-booking__room-link" href="<?php echo esc_url( get_permalink( $_room_id ) ); ?>"><?php echo esc_html( $_room->get_title() ); ?> <?php echo $cart_item[ 'quantity' ] > 1 ? '&times; ' . absint( $cart_item[ 'quantity' ] ) : ''; ?></a>
							<?php if ( $cart_item[ 'rate_name' ] ) : ?>
								<small class="widget-booking__room-rate"><?php printf( esc_html__( 'Rate: %s', 'wp-hotelier' ), htl_get_formatted_room_rate( $cart_item[ 'rate_name' ] ) ); ?></small>
							<?php endif; ?>
						</li>

					<?php endif;
				endforeach; ?>

				</ul>

				<span class="widget-booking__cart-total"><strong><?php esc_html_e( 'Total', 'wp-hotelier' ); ?></strong><?php echo htl_cart_formatted_total(); ?></span>

			<?php endif; ?>

		</div>

		<?php
		do_action( 'hotelier_after_widget_booking' );

		$this->widget_end( $args );
	}
}

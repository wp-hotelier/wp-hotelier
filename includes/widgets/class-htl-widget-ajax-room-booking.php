<?php
/**
 * AJAX Room Booking Widget.
 *
 * Book your room directly from its page.
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

class HTL_Widget_Ajax_Room_Booking extends HTL_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--hotelier widget-ajax-room-booking';
		$this->widget_description = __( 'Allows you to book your room directly from its page. Works in room pages only.', 'wp-hotelier' );
		$this->widget_id          = 'hotelier-widget-ajax-room-booking';
		$this->widget_name        = __( 'Hotelier AJAX Room Booking', 'wp-hotelier' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => __( 'Title', 'wp-hotelier' )
			),
			'show_quantity' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => __( 'Show quantity selection or book one room by default', 'wp-hotelier' )
			),
			'show_guests_selection' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => __( 'Show number of guests selection?', 'wp-hotelier' )
			),
			'show_rate_desc' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => __( 'Show rate description (variable rooms)', 'wp-hotelier' )
			),
			'show_room_conditions' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => __( 'Show room conditions', 'wp-hotelier' )
			),
			'show_room_deposit' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => __( 'Show room deposit', 'wp-hotelier' )
			),
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
		if ( ! is_room() ) {
			return;
		}

		// Widget settings
		$show_guests_selection = isset( $instance[ 'show_guests_selection' ] ) && $instance[ 'show_guests_selection' ] ? true : false;
		$show_quantity         = isset( $instance[ 'show_quantity' ] ) && $instance[ 'show_quantity' ] ? true : false;
		$show_rate_desc        = isset( $instance[ 'show_rate_desc' ] ) && $instance[ 'show_rate_desc' ] ? true : false;
		$show_room_conditions  = isset( $instance[ 'show_room_conditions' ] ) && $instance[ 'show_room_conditions' ] ? true : false;
		$show_room_deposit     = isset( $instance[ 'show_room_deposit' ] ) && $instance[ 'show_room_deposit' ] ? true : false;

		$checkin  = HTL()->session->get( 'checkin' );
		$checkout = HTL()->session->get( 'checkout' );

		$datepicker_atts = array();

		$this->widget_start( $args, $instance );

		wp_enqueue_script( 'hotelier-ajax-room-booking' );

		ob_start();

		htl_get_template( 'widgets/ajax-room-booking/ajax-room-booking-form.php', array(
			'checkin'               => $checkin,
			'checkout'              => $checkout,
			'show_guests_selection' => $show_guests_selection,
			'show_quantity'         => $show_quantity,
			'show_rate_desc'        => $show_rate_desc,
			'show_room_conditions'  => $show_room_conditions,
			'show_room_deposit'     => $show_room_deposit,
			'datepicker_atts'       => $datepicker_atts,
		) );

		echo ob_get_clean();

		$this->widget_end( $args );
	}
}

<?php
/**
 * Rooms Filter Widget.
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

class HTL_Widget_Rooms_Filter extends HTL_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--hotelier widget-rooms-filter';
		$this->widget_description = __( 'Displays a custom filter which lets you narrow down the list of rooms. Visible only in the "listing" page.', 'wp-hotelier' );
		$this->widget_id          = 'hotelier-widget-rooms-filter';
		$this->widget_name        = __( 'Hotelier Rooms Filter', 'wp-hotelier' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Refine results', 'wp-hotelier' ),
				'label' => __( 'Title', 'wp-hotelier' )
			),
			'max_guests' => array(
				'type'  => 'number',
				'std'   => 7,
				'label' => __( 'Max guests (type "0" to hide this filter)', 'wp-hotelier' )
			),
			'max_children' => array(
				'type'  => 'number',
				'std'   => 3,
				'label' => __( 'Max children (type "0" to hide this filter)', 'wp-hotelier' )
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
		if ( ! is_listing() ) {
			return;
		}

		$max_guests   = isset( $instance[ 'max_guests' ] ) && $instance[ 'max_guests' ] != '' ? absint( $instance[ 'max_guests' ] ) : $this->settings[ 'max_guests' ][ 'std' ];
		$max_children = isset( $instance[ 'max_children' ] ) && $instance[ 'max_children' ] != '' ? absint( $instance[ 'max_children' ] ) : $this->settings[ 'max_children' ][ 'std' ];
		$link         = HTL()->cart->get_room_list_form_url();

		$this->widget_start( $args, $instance );

		ob_start();

		htl_get_template( 'widgets/rooms-filter.php', array(
			'link'          => $link,
			'max_guests'    => $max_guests,
			'max_children'  => $max_children,
			'wrapper_class' => '',
		) );

		echo ob_get_clean();

		$this->widget_end( $args );
	}
}

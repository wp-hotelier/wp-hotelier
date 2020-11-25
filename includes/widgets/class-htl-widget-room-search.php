<?php
/**
 * Room Search Widget.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Widgets
 * @package  Hotelier/Widgets
 * @version  2.3.0
 * @extends  HTL_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HTL_Widget_Room_Search extends HTL_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--hotelier widget-room-search';
		$this->widget_description = __( 'A search box for rooms only.', 'wp-hotelier' );
		$this->widget_id          = 'hotelier-widget-room-search';
		$this->widget_name        = __( 'Hotelier Room Search', 'wp-hotelier' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
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
		$this->widget_start( $args, $instance );

		htl_get_template( 'widgets/room-searchform.php' );

		$this->widget_end( $args );
	}
}

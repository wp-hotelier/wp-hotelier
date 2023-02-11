<?php
/**
 * Room Search Widget.
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

class HTL_Widget_Rooms extends HTL_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--hotelier widget-rooms';
		$this->widget_description = __( 'Display a list of rooms on your site.', 'wp-hotelier' );
		$this->widget_id          = 'hotelier-widget-rooms';
		$this->widget_name        = __( 'Hotelier Rooms', 'wp-hotelier' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Rooms', 'wp-hotelier' ),
				'label' => __( 'Title', 'wp-hotelier' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of rooms to show', 'wp-hotelier' )
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'date',
				'label' => __( 'Order by?', 'wp-hotelier' ),
				'options' => array(
					'date' => __( 'Date', 'wp-hotelier' ),
					'cat'  => __( 'Category', 'wp-hotelier' ),
					'ids'  => __( 'IDs', 'wp-hotelier' ),
				)
			),
			'cats'  => array(
				'type'        => 'text',
				'label'       => __( 'Category IDs', 'wp-hotelier' ),
				'std'         => '',
				'description' => __( 'List of category IDs separated by commas (eg. 1,5,8). Works only when the order type is set to "Category"', 'wp-hotelier' )
			),
			'ids'  => array(
				'type'        => 'text',
				'label'       => __( 'Room IDs', 'wp-hotelier' ),
				'std'         => '',
				'description' => __( 'List of room IDs separated by commas (eg. 1,5,8). Works only when the order type is set to "IDs"', 'wp-hotelier' )
			),
		);

		parent::__construct();
	}

	/**
	 * Query the rooms and return them.
	 * @param  array $args
	 * @param  array $instance
	 * @return WP_Query
	 */
	public function get_rooms( $args, $instance ) {
		$number  = isset( $instance[ 'number' ] ) && $instance[ 'number' ] ? absint( $instance[ 'number' ] ) : $this->settings[ 'number' ][ 'std' ];
		$order    = isset( $instance[ 'order' ] ) && $instance[ 'order' ] ? sanitize_title( $instance[ 'order' ] ) : $this->settings[ 'order' ][ 'std' ];
		$cats = isset( $instance[ 'cats' ] ) && $instance[ 'cats' ] ? HTL_Formatting_Helper::sanitize_ids( $instance[ 'cats' ] ) : array();
		$ids = isset( $instance[ 'ids' ] ) && $instance[ 'ids' ] ? HTL_Formatting_Helper::sanitize_ids( $instance[ 'ids' ] ) : array();

		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => 'room',
			'no_found_rows'  => 1,
			'meta_query'     => array()
		);

		switch ( $order ) {
			case 'cat' :
				$query_args[ 'posts_per_page' ] = $number;
				$query_args[ 'tax_query' ]      = array(
					array(
						'taxonomy' => 'room_cat',
						'field'    => 'term_id',
						'terms'    => $cats,
					) );
				break;

			case 'ids' :
				$ids = explode( ',', $ids );
				$query_args[ 'post__in' ] = $ids;
				$query_args[ 'orderby' ]  = 'post__in';
				break;

			default :
				$query_args[ 'posts_per_page' ] = $number;
				break;
		}

		return new WP_Query( apply_filters( 'hotelier_rooms_widget_query_args', $query_args ) );
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		if ( ( $rooms = $this->get_rooms( $args, $instance ) ) && $rooms->have_posts() ) {
			$this->widget_start( $args, $instance );

			echo apply_filters( 'hotelier_before_widget_room_list', '<ul class="widget-rooms__list">' );

			while ( $rooms->have_posts() ) {
				$rooms->the_post();
				htl_get_template( 'widgets/content-widget-room.php' );
			}

			echo apply_filters( 'hotelier_after_widget_room_list', '</ul>' );

			$this->widget_end( $args );
		}

		wp_reset_postdata();

		echo $this->cache_widget( $args, ob_get_clean() );
	}
}

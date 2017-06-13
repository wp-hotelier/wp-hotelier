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

class HTL_Widget_Room_Search extends HTL_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--hotelier widget-room-search';
		$this->widget_description = __( 'A search box for rooms only.', 'hotelier' );
		$this->widget_id          = 'hotelier-widget-room-search';
		$this->widget_name        = __( 'Hotelier Room Search', 'hotelier' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'hotelier' )
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

		do_action( 'hotelier_before_widget_room_search' );
		?>

		<form role="search" method="get" class="form--room-search room-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
			<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'hotelier' ); ?></label>
			<input type="search" class="room-search__input" placeholder="<?php echo esc_attr_x( 'Search rooms&hellip;', 'placeholder', 'hotelier' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'hotelier' ); ?>" />
			<input class="button button--room-search" type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'hotelier' ); ?>" />
			<input type="hidden" name="post_type" value="room" />

			<?php do_action( 'hotelier_after_widget_room_search_fields' ); ?>
		</form>

		<?php
		do_action( 'hotelier_after_widget_room_search' );

		$this->widget_end( $args );
	}
}

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

		$this->widget_start( $args, $instance );

		do_action( 'hotelier_before_widget_rooms_filter' );
		?>

		<div class="widget-rooms-filter__wrapper">

			<?php
			$link            = HTL()->cart->get_room_list_form_url();
			$room_cats       = get_terms( 'room_cat' );
			$room_rates      = get_terms( 'room_rate' );
			$default_filters = apply_filters( 'hotelier_widget_rooms_filter_default_filters',
				array(
					'room_cat',
					'room_rate',
					'guests',
					'children',
				)
			);

			// Sanitize default filters
			$default_filters = array_map( 'esc_attr', $default_filters );

			// Array of all current filters
			$filters = array();

			foreach ( $default_filters as $filter ) {
				if ( isset( $_GET[ $filter ] ) ) {
					$filters[ $filter ] = explode( ',', $_GET[ $filter ] );

					// Sanitize values
					$filters[ $filter ] = array_map( 'esc_attr', $filters[ $filter ] );
				}
			}

			// Extensions can hook into here to add their own options
			do_action( 'hotelier_before_widget_rooms_filter', $default_filters );

			if ( ! empty( $room_cats ) && ! is_wp_error( $room_cats ) ) :
				$current_cats = isset( $filters[ 'room_cat' ] ) ? $filters[ 'room_cat' ] : array();
				?>

				<div class="widget-rooms-filter__group widget-rooms-filter__group--room-type">

					<span class="widget-rooms-filter__group-label"><?php esc_html_e( 'Room type', 'wp-hotelier' ); ?></span>

					<ul class="widget-rooms-filter__group-list widget-rooms-filter__group-list--room-type">

						<?php foreach ( $room_cats as $room_cat ) :

							$current_filter = $current_cats;
							$link_cat       = $link;
							$class          = 'widget-rooms-filter__group-item';

							if ( ! in_array( $room_cat->term_id, $current_filter ) ) {
								$current_filter[] = $room_cat->term_id;
							} else {
								$current_filter = array_diff( $current_filter, array( $room_cat->term_id ) );
								$class          .= ' widget-rooms-filter__group-item--chosen';
							}

							if ( count( $current_filter ) > 0 ) {
								$link_cat = add_query_arg( 'room_cat', implode( ',', $current_filter ) , $link );
							}

							foreach ( $filters as $name => $data ) {
								if ( $name != 'room_cat' ) {
									$link_cat = add_query_arg( $name, implode( ',', $data ) , $link_cat );
								}
							}

							$link_cat = str_replace( '%2C', ',', $link_cat );
							?>

							<li class="<?php echo esc_attr( $class ); ?>"><a class="widget-rooms-filter__group-link" href="<?php echo esc_url( $link_cat ); ?>"><?php echo esc_html( $room_cat->name ); ?></a></li>

						<?php endforeach; ?>

					</ul>

				</div>

			<?php endif;

			if ( ! empty( $room_rates ) && ! is_wp_error( $room_rates ) ) :
				$current_rates = isset( $filters[ 'room_rate' ] ) ? $filters[ 'room_rate' ] : array();
				?>

				<div class="widget-rooms-filter__group widget-rooms-filter__group--room-rate">

					<span class="widget-rooms-filter__group-label"><?php esc_html_e( 'Room rate', 'wp-hotelier' ); ?></span>

					<ul class="widget-rooms-filter__group-list widget-rooms-filter__group-list--room-rate">

						<?php foreach ( $room_rates as $room_rate ) :

							$current_filter = $current_rates;
							$link_rate      = $link;
							$class          = 'widget-rooms-filter__group-item';

							if ( ! in_array( $room_rate->term_id, $current_filter ) ) {
								$current_filter[] = $room_rate->term_id;
							} else {
								$current_filter = array_diff( $current_filter, array( $room_rate->term_id ) );
								$class          .= ' widget-rooms-filter__group-item--chosen';
							}

							if ( count( $current_filter ) > 0 ) {
								$link_rate = add_query_arg( 'room_rate', implode( ',', $current_filter ) , $link );
							}

							foreach ( $filters as $name => $data ) {
								if ( $name != 'room_rate' ) {
									$link_rate = add_query_arg( $name, implode( ',', $data ) , $link_rate );
								}
							}

							$link_rate = str_replace( '%2C', ',', $link_rate );
							?>

							<li class="<?php echo esc_attr( $class ); ?>"><a class="widget-rooms-filter__group-link" href="<?php echo esc_url( $link_rate ); ?>"><?php echo esc_html( $room_rate->name ); ?></a></li>

						<?php endforeach; ?>

					</ul>

				</div>

			<?php endif; ?>

			<?php if ( $max_guests > 1 ) : ?>

				<div class="widget-rooms-filter__group widget-rooms-filter__group--guests">

					<span class="widget-rooms-filter__group-label"><?php esc_html_e( 'Guests', 'wp-hotelier' ); ?></span>

					<?php $guests =  range( 2, $max_guests ); ?>

					<ul class="widget-rooms-filter__group-list widget-rooms-filter__group-list--guests">
						<?php
						$current_guests = isset( $filters[ 'guests' ] ) ? $filters[ 'guests' ] : array();

						// Find higher value of 'guests' - We allow only one choice
						$current_guests = $current_guests ? absint( max( $current_guests ) ) : false;

						foreach ( $guests as $guest ) :

							$link_guests    = $link;
							$class          = 'widget-rooms-filter__group-item';

							if ( $guest === $current_guests ) {
								$class       .= ' widget-rooms-filter__group-item--chosen';
							} else {
								$link_guests = add_query_arg( 'guests', $guest, $link );
							}

							foreach ( $filters as $name => $data ) {
								if ( $name != 'guests' ) {
									$link_guests = add_query_arg( $name, implode( ',', $data ) , $link_guests );
								}
							}

							$link_guests = str_replace( '%2C', ',', $link_guests );
							?>

							<li class="<?php echo esc_attr( $class ); ?>"><a class="widget-rooms-filter__group-link" href="<?php echo esc_url( $link_guests ); ?>"><?php echo esc_html( $guest ); ?></a></li>

						<?php endforeach; ?>
					</ul>

				</div>

			<?php endif; ?>

			<?php if ( $max_children > 0 ) : ?>

				<div class="widget-rooms-filter__group widget-rooms-filter__group--children">

					<span class="widget-rooms-filter__group-label"><?php esc_html_e( 'Children', 'wp-hotelier' ); ?></span>

					<?php $children =  range( 1, $max_children ); ?>

					<ul class="widget-rooms-filter__group-list widget-rooms-filter__group-list--children">
						<?php
						$current_children = isset( $filters[ 'children' ] ) ? $filters[ 'children' ] : array();

						// Find higher value of 'children' - We allow only one choice
						$current_children = $current_children ? absint( max( $current_children ) ) : false;

						foreach ( $children as $children_value ) :

							$link_children  = $link;
							$class          = 'widget-rooms-filter__group-item';

							if ( $children_value === $current_children ) {
								$class         .= ' widget-rooms-filter__group-item--chosen';
							} else {
								$link_children = add_query_arg( 'children', $children_value, $link );
							}

							foreach ( $filters as $name => $data ) {
								if ( $name != 'children' ) {
									$link_children = add_query_arg( $name, implode( ',', $data ) , $link_children );
								}

							}

							$link_children = str_replace( '%2C', ',', $link_children );
							?>

							<li class="<?php echo esc_attr( $class ); ?>"><a class="widget-rooms-filter__group-link" href="<?php echo esc_url( $link_children ); ?>"><?php echo esc_html( $children_value ); ?></a></li>

						<?php endforeach; ?>
					</ul>

				</div>

			<?php endif; ?>

		</div>

		<?php
		// Extensions can hook into here to add their own options
		do_action( 'hotelier_after_widget_rooms_filter', $default_filters );

		$this->widget_end( $args );
	}
}

<?php
/**
 * Hotelier Shortcode Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Shortcodes
 * @package  Hotelier/Classes
 * @version  2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Shortcodes' ) ) :

/**
 * HTL_Shortcodes Class
 */
class HTL_Shortcodes {

	/**
	 * Init shortcodes
	 */
	public static function init() {
		self::includes();

		$shortcodes = array(
			'hotelier_recent_rooms' => __CLASS__ . '::recent_rooms',
			'hotelier_rooms'        => __CLASS__ . '::rooms',
			'hotelier_room_type'    => __CLASS__ . '::room_type',
			'hotelier_booking'      => __CLASS__ . '::booking',
			'hotelier_listing'      => __CLASS__ . '::room_list',
			'hotelier_datepicker'   => __CLASS__ . '::datepicker',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Include required files
	 */
	public static function includes() {
		include_once( 'class-htl-shortcode-booking.php' );
		include_once( 'class-htl-shortcode-room-list.php' );
		include_once( 'class-htl-shortcode-datepicker.php' );
	}

	/**
	 * Shortcode Wrapper
	 *
	 * @param string[] $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'hotelier',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		$wrapper[ 'class' ] = apply_filters( 'hotelier_shortcode_wrapper_class', $wrapper[ 'class' ] );

		echo empty( $wrapper[ 'before' ] ) ? '<div class="' . esc_attr( $wrapper[ 'class' ] ) . '">' : $wrapper[ 'before' ];
		call_user_func( $function, $atts );
		echo empty( $wrapper[ 'after' ] ) ? '</div>' : $wrapper[ 'after' ];

		return ob_get_clean();
	}

	/**
	 * Loop over found rooms.
	 * @param  array $query_args
	 * @param  array $atts
	 * @param  string $loop_name
	 * @return string
	 */
	private static function room_loop( $query_args, $atts, $loop_name ) {
		global $hotelier_loop;

		$has_pagination = isset( $atts['paginate'] ) && $atts['paginate'] === 'true' ? true : false;

		if ( $has_pagination ) {
			$query_args['paged'] = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
			unset( $query_args['offset'] );
		}

		$rooms                      = new WP_Query( apply_filters( 'hotelier_shortcode_rooms_query', $query_args, $atts ) );
		$columns                    = absint( $atts[ 'columns' ] );
		$hotelier_loop[ 'columns' ] = $columns;

		ob_start();

		global $wp_query;

		// Backup query object.
		$previous_wp_query = $wp_query;
		$wp_query          = $rooms;

		if ( $rooms->have_posts() ) : ?>

			<?php do_action( "hotelier_shortcode_before_{$loop_name}_loop", $columns ); ?>

			<?php hotelier_room_loop_start(); ?>

				<?php while ( $rooms->have_posts() ) : $rooms->the_post(); ?>

					<?php htl_get_template_part( 'archive/content', 'room' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php hotelier_room_loop_end(); ?>

			<?php do_action( "hotelier_shortcode_after_{$loop_name}_loop", $columns ); ?>

			<?php
			if ( $has_pagination ) {
				do_action( "hotelier_pagination" );
			}
			?>

		<?php endif;

		// Restore $previous_wp_query and reset post data.
		$wp_query = $previous_wp_query;
		hotelier_reset_loop();
		wp_reset_postdata();

		$custom_class = apply_filters( 'hotelier_shortcode_room_loop_wrapper_class', '', $columns );

		$html = ob_get_clean();

		if ( apply_filters( 'hotelier_shortcode_room_loop_has_wrapper', true ) ) {
			$html = '<div class="hotelier room-loop room-loop--shortcode-rooms room-loop--columns-' . $columns . ' ' . esc_attr( $custom_class ) . '">' . $html . '</div>';
		}

		return $html;
	}

	/**
	 * Booking page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function booking( $atts ) {
		return self::shortcode_wrapper( array( 'HTL_Shortcode_Booking', 'output' ), $atts );
	}

	/**
	 * Room list shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function room_list( $atts ) {
		return self::shortcode_wrapper( array( 'HTL_Shortcode_Room_List', 'output' ), $atts );
	}

	/**
	 * Booking page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function datepicker( $atts ) {
		return self::shortcode_wrapper( array( 'HTL_Shortcode_Datepicker', 'output' ), $atts );
	}

	/**
	 * Recent rooms shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function recent_rooms( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => '9',
			'columns'  => '3',
			'orderby'  => 'date',
			'order'    => 'desc',
			'paginate' => 'false',
			'offset'   => 0,
		), $atts );

		$query_args = array(
			'post_type'           => 'room',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts[ 'per_page' ],
			'orderby'             => $atts[ 'orderby' ],
			'order'               => $atts[ 'order' ],
			'offset'              => absint( $atts[ 'offset' ] ),
			'meta_query'          => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				),
			),
		);

		return self::room_loop( $query_args, $atts, 'recent_rooms' );
	}

	/**
	 * List rooms in a category shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function room_type( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => '9',
			'columns'  => '3',
			'paginate' => 'false',
			'orderby'  => 'title',
			'order'    => 'desc',
			'offset'   => 0,
			'category' => '',  // Slugs
			'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
		), $atts );

		if ( ! $atts[ 'category' ] ) {
			return '';
		}

		$query_args = array(
			'post_type'           => 'room',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts[ 'per_page' ],
			'orderby'             => $atts[ 'orderby' ],
			'order'               => $atts[ 'order' ],
			'offset'              => absint( $atts[ 'offset' ] ),
			'meta_query'          => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				),
			),
		);

		$query_args[ 'tax_query' ] = array(
			array(
				'taxonomy' => 'room_cat',
				'terms'    => array_map( 'sanitize_title', explode( ',', $atts[ 'category' ] ) ),
				'field'    => 'slug',
				'operator' => $atts[ 'operator' ]
			)
		);

		return self::room_loop( $query_args, $atts, 'room_type' );
	}

	/**
	 * Multiple rooms shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function rooms( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'         => '-1',
			'columns'          => '3',
			'orderby'          => 'title',
			'order'            => 'asc',
			'paginate'         => 'false',
			'order'            => 'asc',
			'ids'              => '',
			'exclude_ids'      => '',
			'category'         => '',
			'exclude_category' => '',
			'offset'           => 0,
		), $atts );

		$query_args = array(
			'post_type'           => 'room',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts[ 'per_page' ],
			'orderby'             => $atts[ 'orderby' ],
			'order'               => $atts[ 'order' ],
			'offset'              => absint( $atts[ 'offset' ] ),
			'meta_query'          => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				),
			),
		);

		if ( ! empty( $atts[ 'ids' ] ) ) {
			$ids = array_map( 'trim', explode( ',', $atts[ 'ids' ] ) );
			$ids = array_map( 'absint', $ids );
			$query_args[ 'post__in' ] = $ids;
		}

		if ( ! empty( $atts[ 'exclude_ids' ] ) ) {
			$ids = array_map( 'trim', explode( ',', $atts[ 'exclude_ids' ] ) );
			$ids = array_map( 'absint', $ids );
			$query_args[ 'post__not_in' ] = $ids;
		}

		if ( ! empty( $atts[ 'category' ] ) ) {
			$tax_query_args = array(
				'operator' => 'IN',
				'taxonomy' => 'room_cat',
				'terms'    => array_map( 'absint', explode( ',', $atts[ 'category' ] ) ),
				'field'    => 'term_id',
			);
			if ( isset( $query_args[ 'tax_query' ] ) ) {
				$query_args[ 'tax_query' ][] = $tax_query_args;
			} else {
				$query_args[ 'tax_query' ] = array(
					$tax_query_args
				);
			}
		}

		if ( ! empty( $atts[ 'exclude_category' ] ) ) {
			$tax_query_args = array(
				'operator' => 'NOT IN',
				'taxonomy' => 'room_cat',
				'terms'    => array_map( 'absint', explode( ',', $atts[ 'exclude_category' ] ) ),
				'field'    => 'term_id',
			);
			if ( isset( $query_args[ 'tax_query' ] ) ) {
				$query_args[ 'tax_query' ][] = $tax_query_args;
			} else {
				$query_args[ 'tax_query' ] = array(
					$tax_query_args
				);
			}
		}

		return self::room_loop( $query_args, $atts, 'rooms' );
	}
}

endif;

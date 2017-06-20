<?php
/**
 * Pagination
 *
 * This template can be overridden by copying it to yourtheme/hotelier/loop/pagination.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

// Don't print empty markup if there's only one page.
if ( $wp_query->max_num_pages < 2 ) {
	return;
}

?>

<nav class="hotelier-pagination pagination">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'wp-hotelier' ); ?></h2>

	<?php
	$big = 999999999; // need an unlikely integer

	$pages = paginate_links( apply_filters( 'hotelier_pagination_args', array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'add_args'     => '',
		'current'      => max( 1, get_query_var( 'paged' ) ),
		'total'        => $wp_query->max_num_pages,
		'prev_text'    => '&larr;',
		'next_text'    => '&rarr;',
		'type'         => 'array',
		'end_size'     => 3,
		'mid_size'     => 3
	) ) );
	?>

	<ul class="pagination__list">
		<?php foreach ( $pages as $page ) :
			// Add BEM classes but maintain default WP classes for backward compatibility
			$page = str_replace( 'page-numbers', 'page-numbers pagination__link', $page );
			$page = str_replace( 'current', 'current pagination__link--current', $page );
			$page = str_replace( 'next', 'next pagination__link--next', $page );
			$page = str_replace( 'dots', 'dots pagination__link--dots', $page );
			?>
			<li class="pagination__item"><?php echo $page; // WPCS: XSS OK. ?></li>
		<?php endforeach; ?>
	</ul><!-- .pagination__list -->
</nav><!-- .navigation -->

<?php
/**
 * The template for displaying the archive page title
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/page-title.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$archive_header_classes = apply_filters(
	'hotelier_archive_header_classes', array(
		'page-header',
		'page__header'
	)
);

?>

<header class="<?php echo esc_attr( implode( ' ', $archive_header_classes ) ) ?>">
	<?php do_action( 'hotelier_before_page_title' ); ?>

	<h1 class="page-title page__title"><?php echo wp_kses_post( $page_title ); ?></h1>

	<?php do_action( 'hotelier_after_page_title' ); ?>
</header>

<?php
/**
 * The template for displaying the archive page title
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/page-title.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<header class="page-header page__header">
	<?php do_action( 'hotelier_before_page_title' ); ?>

	<h1 class="page-title page__title"><?php echo esc_html( $page_title ); ?></h1>

	<?php do_action( 'hotelier_after_page_title' ); ?>
</header>

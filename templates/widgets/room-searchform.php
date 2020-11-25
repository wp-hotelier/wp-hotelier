<?php
/**
 * The template for displaying room widget search
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/room-searchform.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'hotelier_before_widget_room_search' ); ?>

<form role="search" method="get" class="form--room-search room-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'wp-hotelier' ); ?></label>
	<input type="search" class="room-search__input" placeholder="<?php echo esc_attr_x( 'Search rooms&hellip;', 'placeholder', 'wp-hotelier' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'wp-hotelier' ); ?>" />
	<input class="button button--room-search" type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'wp-hotelier' ); ?>" />
	<input type="hidden" name="post_type" value="room" />

	<?php do_action( 'hotelier_after_widget_room_search_fields' ); ?>
</form>

<?php do_action( 'hotelier_after_widget_room_search' );

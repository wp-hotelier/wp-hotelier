<?php
/**
 * The template for displaying room widget entries
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/content-widget-room.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<li class="widget-rooms__item">
	<a class="widget-rooms__link" href="<?php echo esc_url( get_permalink( $room->id ) ); ?>" title="<?php echo esc_attr( $room->get_title() ); ?>">
		<?php echo $room->get_image( 'room_thumbnail', array( 'class' => 'widget-rooms__thumbnail' ) ); ?>
		<span class="widget-rooms__name"><?php echo $room->get_title(); ?></span>
	</a>
	<span class="widget-rooms__price"><?php echo $room->get_min_price_html(); ?></span>
</li>

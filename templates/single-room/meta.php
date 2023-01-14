<?php
/**
 * Room meta.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/meta.php.
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

<div class="room__meta room__meta--single">

	<?php if ( apply_filters( 'hotelier_single_room_meta_inline', false ) ) : ?>

		<?php if ( $max_guests = $room->get_max_guests() ) : ?>
			<span class="room__meta-item room__meta-item--guests"><?php echo esc_html( sprintf( _n( '%s adult', '%s adults', $max_guests, 'wp-hotelier' ), absint( $max_guests ) ) ); ?></span>
		<?php endif; ?>

		<?php if ( $max_children = $room->get_max_children() ) : ?>
			<span class="room__meta-item room__meta-item--children"><?php echo esc_html( sprintf( __( '%s children', 'wp-hotelier' ), $max_guests ) ); ?></span>
		<?php endif; ?>

		<?php if ( $bed_size = $room->get_bed_size() ) : ?>
			<span class="room__meta-item room__meta-item--beds"><?php echo esc_html( $bed_size ); ?></span>
		<?php endif; ?>

		<?php if ( $bathrooms = $room->get_bathrooms() ) : ?>
			<span class="room__meta-item room__meta-item--bathrooms"><?php echo esc_html( sprintf( _n( '%s bathroom', '%s bathrooms', $bathrooms, 'wp-hotelier' ), absint( $bathrooms ) ) ); ?></span>
		<?php endif; ?>

		<?php if ( $room->get_room_size() ) : ?>
			<span class="room__meta-item room__meta-item--size"><?php echo esc_html( $room->get_formatted_room_size() ); ?></span>
		<?php endif; ?>

	<?php else:  ?>

		<?php if ( apply_filters( 'hotelier_single_room_meta_show_title', true ) ) : ?>
			<h3 class="room__meta-title room__meta-title--single"><?php esc_html_e( 'Room details', 'wp-hotelier' ); ?></h3>
		<?php endif; ?>

		<ul class="room__meta-list room__meta-list--single">
			<li class="room__meta-item room__meta-item--guests"><strong><?php esc_html_e( 'Guests:', 'wp-hotelier' ); ?></strong> <?php echo absint( $room->get_max_guests() ); ?></li>

			<?php if ( $room->get_max_children() ) : ?>
				<li class="room__meta-item room__meta-item--children"><strong><?php esc_html_e( 'Children:', 'wp-hotelier' ); ?></strong> <?php echo absint( $room->get_max_children() ); ?></li>
			<?php endif; ?>

			<?php if ( $room->get_formatted_room_size() ) : ?>
				<li class="room__meta-item room__meta-item--size"><strong><?php esc_html_e( 'Room size:', 'wp-hotelier' ); ?></strong> <?php echo esc_html( $room->get_formatted_room_size() ); ?></li>
			<?php endif; ?>

			<?php if ( $room->get_bed_size() ) : ?>
				<li class="room__meta-item room__meta-item--bed-size"><strong><?php esc_html_e( 'Bed size(s):', 'wp-hotelier' ) ?></strong> <?php echo esc_html( $room->get_bed_size() ); ?></li>
			<?php endif; ?>

			<?php echo $room->get_categories( ', ', '<li class="room__meta-item room__meta-item--type"><strong>' . esc_html__( 'Room type:', 'wp-hotelier' ) . '</strong> ', '</li>' ); ?>
		</ul>

	<?php endif; ?>
</div>

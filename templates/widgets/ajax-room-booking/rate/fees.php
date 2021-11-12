<?php
/**
 * Room fees
 *
 * This template can be overridden by copying it to yourtheme/hotelier/widgets/ajax-room-booking/rate/fees.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fees = apply_filters( 'hotelier_get_room_fees', array(), $room, $variation );
?>

<?php if ( is_array( $fees ) && count( $fees ) > 0 ) : ?>
<div class="form-row form-row--wide widget-ajax-room-booking__row widget-ajax-room-booking__data widget-ajax-room-booking__data--fees widget-ajax-room-booking__data--rate room__fees" data-rate-id="<?php echo esc_attr( $variation->get_room_index() ); ?>">
	<?php $key = htl_generate_item_key( $room->id, $variation->get_room_index() ); ?>

	<?php foreach ( $fees as $fee ) : ?>
		<div class="room-fee">

			<?php if ( isset( $fee[ 'title' ] ) ) : ?>
				<strong class="room-fee__title"><?php echo esc_html( $fee[ 'title' ] ); ?></strong>
			<?php endif; ?>

			<?php if ( isset( $fee[ 'options' ] ) && is_array( $fee[ 'options' ] ) ) : ?>
				<?php foreach ( $fee[ 'options' ] as $option ) : ?>
					<?php
					$checked = isset( $option[ 'checked' ] ) && $option[ 'checked' ] ? true : false;
					$label   = $option[ 'value' ] == 0 ? $option[ 'label' ] : sprintf( esc_html__( 'Add %s', 'wp-hotelier' ), $option[ 'label' ] );
					?>

					<label class="room-fee__label"><input type="<?php echo esc_attr( $fee[ 'type' ] ); ?>" name="fees[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $option[ 'key' ] ); ?>]" value="<?php echo esc_attr( $option[ 'value' ] ); ?>" <?php echo $checked ? 'checked' : ''; ?>><?php echo wp_kses_post( $label ); ?></label>

				<?php endforeach; ?>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
</div>

<?php endif; ?>

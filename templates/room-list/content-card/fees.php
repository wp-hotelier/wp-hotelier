<?php
/**
 * Room fees
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/fees.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$fees = apply_filters( 'hotelier_get_room_fees', array(), $room );
?>

<?php if ( is_array( $fees ) && count( $fees ) > 0 ) : ?>

<div class="room-card__fees">
	<?php $key = htl_generate_item_key( $room->id, 0 ); ?>

	<?php foreach ( $fees as $fee ) : ?>
		<div class="room-fee">

			<?php if ( isset( $fee[ 'title' ] ) ) : ?>
				<strong class="room-fee__title"><?php echo esc_html( $fee[ 'title' ] ); ?></strong>
			<?php endif; ?>

			<?php if ( isset( $fee[ 'options' ] ) && is_array( $fee[ 'options' ] ) ) : ?>
				<?php foreach ( $fee[ 'options' ] as $option ) : ?>
					<?php $checked = isset( $option[ 'checked' ] ) && $option[ 'checked' ] ? true : false; ?>

					<label class="room-fee__label"><input type="<?php echo esc_attr( $fee[ 'type' ] ); ?>" name="fees[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $option[ 'key' ] ); ?>]" value="<?php echo esc_attr( $option[ 'value' ] ); ?>" <?php echo $checked ? 'checked' : ''; ?>><?php echo wp_kses_post( $option[ 'label' ] ); ?></label>

				<?php endforeach; ?>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
</div>

<?php endif; ?>

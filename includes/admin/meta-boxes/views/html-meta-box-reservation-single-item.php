<?php
/**
 * Shows a reservation item
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<tr class="item">
	<td class="thumb">
		<?php if ( $_room ) : ?>
			<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_room->id ) . '&action=edit' ) ); ?>"><?php echo $_room->get_image( 'room_thumbnail', array( 'title' => '' ) ); ?></a>
		<?php else : ?>
			<?php echo htl_placeholder_img( 'room_thumbnail' ); ?>
		<?php endif; ?>
	</td>
	<td class="name">
		<?php if ( $_room ) : ?>
			<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_room->id ) . '&action=edit' ) ); ?>">
				<?php echo esc_html( $item[ 'name' ] ); ?>
			</a>
			<?php if ( isset( $item[ 'rate_name' ] ) ) : ?>
				<span><?php echo esc_html__( 'Rate', 'hotelier' ) . ': '; ?><span class="rate"><?php echo htl_get_formatted_room_rate( $item[ 'rate_name' ] ); ?></span></span>
			<?php else : ?>
				<span><?php echo esc_html__( 'Standard room', 'hotelier' ); ?></span>
			<?php endif; ?>
		<?php else : ?>
			<?php echo esc_html( $item[ 'name' ] ); ?>
			<?php if ( isset( $item[ 'rate_name' ] ) ) : ?>
				<?php echo htl_get_formatted_room_rate( $item[ 'rate_name' ] ); ?>
			<?php else : ?>
				<span><?php echo esc_html__( 'Standard room', 'hotelier' ); ?></span>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( isset( $item[ 'is_cancellable' ] ) && ! $item[ 'is_cancellable' ] ) : ?>
			<span class="non-refundable"><?php echo esc_html__( 'Non-refundable', 'hotelier' ); ?></span>
		<?php endif; ?>
	</td>
	<td class="guests">
	<?php
		$room_max_guests = absint( $item[ 'max_guests' ] );

		for( $i = 0; $i < $room_max_guests; $i++ ) {
			echo '<i class="dashicons dashicons-admin-users"></i>';
		} ?>
	</td>
	<td class="price">
		<?php echo htl_price( htl_convert_to_cents( $item[ 'price' ] ), $reservation->get_reservation_currency() ); ?>
	</td>
	<td class="qty">
		<?php echo esc_html( $item[ 'qty' ] ); ?>
	</td>
	<td class="total">
		<?php echo htl_price( htl_convert_to_cents( $item[ 'total' ] ), $reservation->get_reservation_currency() ); ?>
	</td>
</tr>

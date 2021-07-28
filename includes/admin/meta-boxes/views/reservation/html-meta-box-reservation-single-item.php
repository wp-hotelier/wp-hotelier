<?php
/**
 * Shows a reservation item
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$item_has_extras = false;

if ( isset( $item[ 'extras' ] ) ) {
	$extras = maybe_unserialize( $item[ 'extras' ] );

	if ( is_array( $extras ) && count( $extras ) > 0 ) {
		$item_has_extras = true;
	}
}

?>

<tr class="htl-ui-table__row htl-ui-table__row--body">
	<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-thumb">
		<?php if ( $_room ) : ?>
			<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_room->id ) . '&action=edit' ) ); ?>"><?php echo $_room->get_image( 'room_thumbnail', array( 'title' => '' ) ); ?></a>
		<?php else : ?>
			<?php echo htl_placeholder_img( 'room_thumbnail' ); ?>
		<?php endif; ?>
	</td>

	<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-name">
		<?php if ( $_room ) : ?>
			<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_room->id ) . '&action=edit' ) ); ?>">
				<?php echo esc_html( $item[ 'name' ] ); ?>
			</a>

			<?php if ( isset( $item[ 'rate_name' ] ) ) : ?>
				<span class="reservation-items-rate"><?php echo wp_kses_post( sprintf( __( 'Rate: %s', 'wp-hotelier' ), '<span class="reservation-items-rate__name">' . htl_get_formatted_room_rate( $item[ 'rate_name' ] ) . '</span>' ) ); ?></span>
			<?php else : ?>
				<span class="reservation-items-rate"><span class="reservation-items-rate__name"><?php echo esc_html__( 'Standard room', 'wp-hotelier' ); ?></span></span>
			<?php endif; ?>

		<?php else : ?>
			<a href="#"><?php echo esc_html( $item[ 'name' ] ); ?></a>

			<?php if ( isset( $item[ 'rate_name' ] ) ) : ?>
				<span class="reservation-items-rate"><?php echo wp_kses_post( sprintf( __( 'Rate: %s', 'wp-hotelier' ), '<span class="reservation-items-rate__name">' . htl_get_formatted_room_rate( $item[ 'rate_name' ] ) . '</span>' ) ); ?></span>
			<?php else : ?>
				<span class="reservation-items-rate"><span class="reservation-items-rate__name"><?php echo esc_html__( 'Standard room', 'wp-hotelier' ); ?></span></span>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( isset( $item[ 'is_cancellable' ] ) && ! $item[ 'is_cancellable' ] ) : ?>
			<span class="non-refundable"><?php echo esc_html__( 'Non-refundable', 'wp-hotelier' ); ?></span>
		<?php endif; ?>

		<?php if ( htl_get_option( 'booking_number_of_guests_selection', true ) ) :

			$adults   = isset( $item[ 'adults' ] ) ? maybe_unserialize( $item[ 'adults' ] ) : false;
			$children = isset( $item[ 'children' ] ) ? maybe_unserialize( $item[ 'children' ] ) : false;

			for ( $q = 0; $q < $item[ 'qty' ]; $q++ ) :
				$line_adults   = isset( $adults[ $q ] ) && ( $adults[ $q ] > 0 ) ? $adults[ $q ] : false;
				$line_children = isset( $children[ $q ] ) && ( $children[ $q ] > 0 ) ? $children[ $q ] : false;
				?>

				<?php if ( $line_adults || $line_children ) : ?>

					<div class="adults-children">

						<?php if ( $item[ 'qty' ] > 1 ) : ?>
							<span class="adults-children__label"><?php echo sprintf( esc_html__( 'Number of guests (Room %d):', 'wp-hotelier' ), $q + 1 ); ?></span>
						<?php else : ?>
							<span class="adults-children__label"><?php esc_html_e( 'Number of guests:', 'wp-hotelier' ); ?></span>
						<?php endif; ?>

						<?php if ( $line_adults ) : ?>
							<span class="adults-children__adults"><?php echo sprintf( _n( '%s Adult', '%s Adults', $line_adults, 'wp-hotelier' ), $line_adults ); ?></span>
						<?php endif; ?>

						<?php if ( $line_children ) : ?>
							<span class="adults-children__children"><?php echo sprintf( esc_html__( '%d Children', 'wp-hotelier' ), $line_children ); ?></span>
						<?php endif; ?>
					</div>

				<?php endif; ?>

			<?php endfor; ?>
		<?php endif; ?>

		<?php do_action( 'hotelier_reservation_item_after_name', $_room, $item ); ?>

	</td>

	<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-guests">
	<?php
		$room_max_guests = absint( $item[ 'max_guests' ] );

		for( $i = 0; $i < $room_max_guests; $i++ ) {
			echo '<i class="fas fa-male"></i>';
		} ?>
	</td>

	<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-price">
		<?php
		$item_price = $item_has_extras && isset( $item[ 'price_without_extras' ] ) ? $item[ 'price_without_extras' ] : $item[ 'price' ];
		?>
		<?php echo htl_price( htl_convert_to_cents( $item_price ), $reservation->get_reservation_currency() ); ?>
	</td>

	<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-qty">
		<?php echo esc_html( $item[ 'qty' ] ); ?>
	</td>

	<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-total">
		<?php
		$item_total = $item_has_extras && isset( $item[ 'total_without_extras' ] ) ? $item[ 'total_without_extras' ] : $item[ 'total' ];
		?>
		<?php echo htl_price( htl_convert_to_cents( $item_total ), $reservation->get_reservation_currency() ); ?>
	</td>
</tr>

<?php if ( $item_has_extras ) : ?>
	<?php foreach ( $extras as $extra_id => $extra_data ) : ?>
		<?php
		$extra       = htl_get_extra( $extra_id );
		$extra_qty   = isset( $extra_data['qty'] ) ? $extra_data['qty'] : 1;
		$extra_price = $item[ 'qty' ] * $extra_data['price'] * $extra_qty;
		?>

		<tr class="htl-ui-table__row htl-ui-table__row--body">
			<td colspan="5" class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-extra">
				<div class="extra">
					<strong class="extra__name"><?php echo esc_html( $extra->get_name() ); ?></strong>
					<?php if ( $extra_descritpion = $extra->get_description() ) : ?>
						<span class="extra__description"><?php echo esc_html( $extra_descritpion ); ?></span>
					<?php endif; ?>
				</div>
			</td>

			<td class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-room-total"><?php echo htl_price( htl_convert_to_cents( $extra_price ) ) ?></td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ( ! $_room ) : ?>
	<tr class="htl-ui-table__row htl-ui-table__row--body">
		<td colspan="6" class="htl-ui-table__cell htl-ui-table__cell--body htl-ui-table__cell--reservation-items-no-room">
			<?php htl_ui_print_notice( __( 'This room does not exist anymore', 'wp-hotelier' ), 'error' ); ?>
		</td>
	</tr>
<?php endif; ?>

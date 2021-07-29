<?php
/**
 * Reservation item details
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/item-extras.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$item_has_extras = false;

if ( isset( $item[ 'extras' ] ) ) {
	$extras = maybe_unserialize( $item[ 'extras' ] );

	if ( is_array( $extras ) && count( $extras ) > 0 ) {
		$item_has_extras = true;
	}
}

if ( ! $item_has_extras ) {
	return;
}
?>

<?php foreach ( $extras as $extra_id => $extra_data ) : ?>
	<?php
	$extra       = htl_get_extra( $extra_id );
	$extra_qty   = isset( $extra_data['qty'] ) ? $extra_data['qty'] : 1;
	$extra_price = $item[ 'qty' ] * $extra_data['price'] * $extra_qty;
	?>

	<tr class="reservation-table__row reservation-table__row--body">
		<td class="reservation-table__room-extra reservation-table__room-extra--body">
			<div class="extra">
				<strong class="extra__name"><?php echo esc_html( $extra->get_name() ); ?></strong>
				<?php if ( $extra_descritpion = $extra->get_description() ) : ?>
					<span class="extra__description"><?php echo esc_html( $extra_descritpion ); ?></span>
				<?php endif; ?>
			</div>
		</td>
		<td class="reservation-table__room-qty reservation-table__room-qty--body">&nbsp;</td>
		<td class="reservation-table__room-extra-cost reservation-table__room-extra-cost--body"><?php echo htl_price( htl_convert_to_cents( $extra_price ) ) ?></td>
	</tr>
<?php endforeach; ?>

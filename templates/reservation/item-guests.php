<?php
/**
 * Reservation item details
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/item-guests.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! htl_get_option( 'booking_number_of_guests_selection', true ) ) {
	return;
}

?>

<?php for ( $q = 0; $q < $quantity; $q++ ) :

	$line_adults   = isset( $adults[ $q ] ) && ( $adults[ $q ] > 0 ) ? $adults[ $q ] : false;
	$line_children = isset( $children[ $q ] ) && ( $children[ $q ] > 0 ) ? $children[ $q ] : false;
	?>

	<?php if ( $line_adults || $line_children ) : ?>

		<div class="reservation-table__room-guests">

			<?php if ( $quantity > 1 ) : ?>
				<span class="reservation-table__room-guests-label"><?php echo sprintf( esc_html__( 'Number of guests (Room %d):', 'wp-hotelier' ), $q + 1 ); ?></span>
			<?php else : ?>
				<span class="reservation-table__room-guests-label"><?php esc_html_e( 'Number of guests:', 'wp-hotelier' ); ?></span>
			<?php endif; ?>

			<?php if ( $line_adults ) : ?>
				<span class="reservation-table__room-guests-adults"><?php echo sprintf( _n( '%s Adult', '%s Adults', $line_adults, 'wp-hotelier' ), $line_adults ); ?></span>
			<?php endif; ?>

			<?php if ( $line_children ) : ?>
				<span class="reservation-table__room-guests-children"><?php echo sprintf( esc_html__( '%d Children', 'wp-hotelier' ), $line_children ); ?></span>
			<?php endif; ?>
		</div>

	<?php endif; ?>

<?php endfor; ?>



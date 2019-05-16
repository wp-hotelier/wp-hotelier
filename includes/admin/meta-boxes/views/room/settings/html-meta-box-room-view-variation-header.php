<?php
/**
 * View: variation header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="room-variation__header">
	<label class="htl-ui-label htl-ui-label--room-rate"><?php esc_html_e( 'Room rate:', 'wp-hotelier' ); ?></label>

	<select class="htl-ui-input htl-ui-input--select" name="_room_variations[<?php echo absint( $loop ); ?>][room_rate]">
		<?php
		$room_rate_selected = is_array( $variations ) && isset( $variations[ absint( $loop ) ][ 'room_rate' ] ) ? $variations[ absint( $loop ) ][ 'room_rate' ] : false;

		foreach ( $room_rates as $room_rate ) {
			echo '<option value="' . esc_attr( $room_rate->slug ) . '" ' . selected( esc_attr( $room_rate_selected ), esc_attr( $room_rate->slug ), false ) . ' >' . esc_html( $room_rate->name ) . '</option>';
		}
		?>
	</select>

	<div class="room-variation__actions">
		<span class="htl-ui-icon htl-ui-icon--no-text htl-ui-icon--clone-variation"><?php esc_html_e( 'Clone variation', 'wp-hotelier' ); ?></span>

		<span class="htl-ui-icon htl-ui-icon--no-text htl-ui-icon--delete-variation"><?php esc_html_e( 'Delete variation', 'wp-hotelier' ); ?></span>

		<span class="htl-ui-icon htl-ui-icon--no-text htl-ui-icon--drag-variation"><?php esc_html_e( 'Drag variation', 'wp-hotelier' ); ?></span>

		<span class="htl-ui-icon htl-ui-icon--no-text htl-ui-icon--toggle-variation"><?php esc_html_e( 'Toggle variation', 'wp-hotelier' ); ?></span>
	</div>

	<input type="hidden" class="htl-ui-input htl-ui-input--hidden htl-ui-input--room-variation-index" name="_room_variations[<?php echo absint( $loop ); ?>][index]" value="<?php echo absint( $loop ); ?>">
</div>

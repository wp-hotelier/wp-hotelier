<?php
/**
 * Shows the room conditions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="room-conditions">

	<span class="label-text"><?php esc_html_e( 'Conditions:', 'hotelier' ); ?></span>

	<table class="widefat">

		<tbody>

			<?php
			$conditions = get_post_meta( $thepostid, '_room_conditions', true );
			$condition_has_value = ( ( $conditions ) && ( is_array( $conditions ) ) ) ? true : false;
			$conditions_lenght = $conditions ? count( $conditions ) : 1;

			for ( $i = 1; $i <= $conditions_lenght; $i++ ) :
				$condition_value = $condition_has_value ? $conditions[ absint( $i ) ][ 'name' ] : '';
				?>

				<tr class="room-condition" data-key="<?php echo absint( $i ); ?>">
					<td class="condition-name">
						<input type="text" name="_room_conditions[<?php echo absint( $i ); ?>][name]" value="<?php echo esc_attr( $condition_value ); ?>" placeholder="<?php esc_html_e( 'Special condition here', 'hotelier' ); ?>" />
						<input type="hidden" class="condition-index" name="_room_conditions[<?php echo absint( $i ); ?>][index]" value="<?php echo absint( $i ); ?>">
					</td>

					<td><button type="button" class="remove-condition button"><?php esc_html_e( 'Remove', 'hotelier' ); ?></button></td>

					<td class="sort-conditions"><i class="htl-icon htl-bars"></i></td>
				</tr>

			<?php endfor; ?>

		</tbody>

		<tfoot>
			<tr>
				<th colspan="3"><button type="button" class="add-condition button"><?php esc_html_e( 'Add new condition', 'hotelier' ); ?></button></th>
			</tr>
		</tfoot>

	</table>

</div>

<?php
/**
 * Admin View: New reservation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$today    = new Datetime();
$today    = $today->format( 'Y-m-d' );
$tomorrow = new Datetime();
$tomorrow = $tomorrow->modify( '+1 day' );
$tomorrow = $tomorrow->format( 'Y-m-d' );
?>

<div class="wrap hotelier">
	<?php
	// Check if we have at least one room
	$args = array(
		'post_type'           => 'room',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'orderby'             => 'title',
		'order'               => 'ASC',
		'meta_query'          => array(
			array(
				'key'     => '_stock_rooms',
				'value'   => 0,
				'compare' => '>',
			),
		),
	);

	do_action( 'hotelier_admin_add_new_reservation_before_rooms_query' );

	$rooms = new WP_Query( apply_filters( 'hotelier_admin_add_new_reservation_rooms_query_args', $args ) );

	if ( $rooms->have_posts() ) : ?>

		<h1><?php esc_html_e( 'Add new reservation', 'wp-hotelier' ); ?></h1>

		<form method="post" class="add-new-reservation-form">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Select room:', 'wp-hotelier' ); ?></th>
						<td>
							<div class="add-new-room-row" data-key="0">
								<?php echo htl_get_list_of_rooms_html( 'room[0]' ); ?>
								<input type="number" name="room_qty[0]" value="1">
								<button type="button" class="button remove-room" disabled><?php esc_html_e( 'Remove room', 'wp-hotelier' ); ?></button>
							</div>
							<button type="button" class="button button-primary add-new-room"><?php esc_html_e( 'Add another room', 'wp-hotelier' ); ?></button>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Check-in:', 'wp-hotelier' ); ?></th>
						<td>
							<input class="date-from" type="text" placeholder="YYYY-MM-DD" name="from" value="<?php echo esc_attr( $today ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Check-out:', 'wp-hotelier' ); ?></th>
						<td>
							<input class="date-to" type="text" placeholder="YYYY-MM-DD" name="to" value="<?php echo esc_attr( $tomorrow ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><strong><?php esc_html_e( 'Guest details', 'wp-hotelier' ); ?></strong></th>
						<td><hr></td>
					</tr>
					<?php foreach ( HTL_Meta_Box_Reservation_Data::get_guest_details_fields() as $key => $field ) :
						$type     = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';
						$required = isset( $field[ 'required' ] ) ? ' * ' : '';
						?>
						<tr>
							<th scope="row"><?php echo esc_html( $field[ 'label' ] ) . $required ; ?></th>
							<td><input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $key ); ?>"></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>

			<p class="submit">
				<input type="submit" name="hotelier_admin_add_new_reservation" class="button button-primary add-new-reservation" value="<?php esc_attr_e( 'Save reservation', 'wp-hotelier' ); ?>">
				<?php wp_nonce_field( 'hotelier_admin_process_new_reservation' ); ?>
			</p>
		</form>

	<?php else : ?>

		<div class="error"><p><?php esc_html_e( 'In order to create a reservation, you need to have at least one room.', 'wp-hotelier' ); ?></p></div>

		<p><?php printf( wp_kses( __( 'Create a new room <a href="%s">here</a>.', 'wp-hotelier' ), array( 'a' => array( 'href' => array() ) ) ), 'post-new.php?post_type=room' ); ?></p>

	<?php endif; ?>

	<?php do_action( 'hotelier_admin_add_new_reservation_after_rooms_query' ); ?>
</div>

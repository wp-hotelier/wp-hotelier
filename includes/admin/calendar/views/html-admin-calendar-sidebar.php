<?php
/**
 * Calendar page sidebar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="booking-calendar-filter">
	<ul class="booking-calendar-filter__list">
		<?php foreach ( htl_get_reservation_statuses() as $id => $name ) : ?>
			<?php
			$id     = esc_attr( str_replace( 'htl-', '', $id ) );
			$status = ! empty( $_GET[ $id ] ) && 'false' == $_GET[ $id ] ? 'true' : 'false';
			?>

			<li class="booking-calendar-filter__item booking-calendar-filter__item--<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $name ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

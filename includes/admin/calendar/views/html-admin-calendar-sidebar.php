<?php
/**
 * Calendar page sidebar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="booking-calendar-filters">
	<ul class="booking-calendar-filters__list">
		<?php foreach ( htl_get_reservation_statuses() as $id => $name ) : ?>
			<?php
			$id         = esc_attr( str_replace( 'htl-', '', $id ) );
			$not_active = in_array( $id, $default_disabled_statuses ) ? true : false;
			?>

			<li class="booking-calendar-filters__item booking-calendar-filters__item--<?php echo esc_attr( $id ); ?> <?php echo $not_active ? 'not-active' : ''; ?>" data-status="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $name ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

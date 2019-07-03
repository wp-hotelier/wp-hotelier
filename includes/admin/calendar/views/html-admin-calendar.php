<?php
/**
 * Calendar page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$today     = new Datetime();
$today     = $today->format( 'Y-m-d' );
$next_week = clone( $marker );
$next_week = $next_week->modify( '+7 days' )->format( 'Y-m-d' );
$prev_week = clone( $marker );
$prev_week = $prev_week->modify( '-7 days' )->format( 'Y-m-d' );
?>

<div class="wrap htl-ui-scope booking-calendar">
	<h3 class="booking-calendar__title"><?php esc_html_e( 'Booking calendar', 'wp-hotelier' ); ?></h3>

	<div class="booking-calendar__panel">
		<div class="booking-calendar__header">
			<?php include_once HTL_PLUGIN_DIR . 'includes/admin/calendar/views/html-admin-calendar-header.php'; ?>
		</div>

		<div class="booking-calendar__sidebar">
			<?php include_once HTL_PLUGIN_DIR . 'includes/admin/calendar/views/html-admin-calendar-sidebar.php'; ?>
		</div>

		<div class="booking-calendar__content">
			<?php include_once HTL_PLUGIN_DIR . 'includes/admin/calendar/views/html-admin-calendar-table.php'; ?>
		</div>
	</div>
</div>

<?php
/**
 * Calendar page header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="booking-calendar-navigation">
	<ul class="booking-calendar-navigation__list">
		<li class="booking-calendar-navigation__item">
			<a class="htl-ui-button booking-calendar-navigation-button booking-calendar-navigation-button--prev" href="<?php echo esc_url( add_query_arg( 'marker', $prev_week ) ); ?>"><?php esc_html_e( 'Previous', 'wp-hotelier' ); ?></a>
		</li>

		<li class="booking-calendar-navigation__item">
			<a class="htl-ui-button booking-calendar-navigation-button booking-calendar-navigation-button--next" href="<?php echo esc_url( add_query_arg( 'marker', $next_week ) ); ?>"><?php esc_html_e( 'Next', 'wp-hotelier' ); ?></a>
		</li>
	</ul>
</div>

<div class="booking-calendar-weeks-navigation">
	<ul class="booking-calendar-weeks-navigation__list">
		<?php for ( $i = 1; $i < 5; $i++ ) : ?>
			<li class="booking-calendar-weeks-navigation__item booking-calendar-weeks-navigation__item--<?php echo absint( $i ); ?>">
				<a class="htl-ui-button booking-calendar-weeks-navigation-button booking-calendar-weeks-navigation-button--<?php echo absint( $i ); ?>" href="<?php echo esc_url( add_query_arg( 'weeks', $i ) ); ?>"><?php echo absint( $i * 7 ); ?></a>
			</li>
		<?php endfor; ?>

		<li class="booking-calendar-weeks-navigation__item booking-calendar-weeks-navigation__item--today">
			<a class="htl-ui-button booking-calendar-weeks-navigation-button booking-calendar-weeks-navigation-button--today" href="<?php echo esc_url( add_query_arg( 'marker', $today ) ); ?>"><?php esc_html_e( 'Today', 'wp-hotelier' ); ?></a>
		</li>
	</ul>
</div>

<div class="booking-calendar-datepicker">
	<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get" class="htl-ui-form htl-ui-form--booking-calendar">
		<input type="hidden" name="page" value="hotelier-calendar">
		<input type="hidden" name="weeks" value="<?php echo absint( $weeks ); ?>">

		<span class="htl-ui-datepicker-wrapper">
			<input class="htl-ui-input htl-ui-input--datepicker" type="text" placeholder="YYYY-MM-DD" name="marker" value="<?php echo esc_attr( $marker->format( 'Y-m-d' ) ); ?>" autocomplete="off">
		</span>

		<input type="submit" class="htl-ui-button" value="<?php esc_attr_e( 'Go to date', 'wp-hotelier' ); ?>">
	</form>
</div>

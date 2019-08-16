<?php
/**
 * Pro features description
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php if ( false ) : // temp hide this banner ?>

<div class="hotelier-settings-pro-features pro-features">
	<div class="pro-features__header">
		<h3 class="htl-ui-heading"><?php esc_html_e( 'Pro features', 'wp-hotelier' ); ?></h3>
		<a href="https://wphotelier.com/" class="htl-ui-button htl-ui-button--upgrade-to-pro"><?php esc_html_e( 'Upgrade to Pro version', 'wp-hotelier' ); ?></a>
	</div>

	<ul class="pro-features__list">
		<li class="pro-features__item">
			<h4 class="htl-ui-heading pro-features__item__title"><?php esc_html_e( 'Disable dates', 'wp-hotelier' ); ?></h4>
			<p class="htl-ui-heading pro-features__item__description"><?php esc_html_e( 'Stop bookings being made for a certain date. Select one (or more) date range or a single date that need to be blocked. And optionally, you can make it repeat every year.', 'wp-hotelier' ); ?></p>
		</li>

		<li class="pro-features__item">
			<h4 class="htl-ui-heading pro-features__item__title"><?php esc_html_e( 'iCalendar Importer/Exporter', 'wp-hotelier' ); ?></h4>
			<p class="htl-ui-heading pro-features__item__description"><?php esc_html_e( 'Import your reservations from popular external services including Airbnb and Google Calendar. And generate automatically a public iCal file for each room.', 'wp-hotelier' ); ?></p>
		</li>

		<li class="pro-features__item">
			<h4 class="htl-ui-heading pro-features__item__title"><?php esc_html_e( 'Stripe Payment Gateway', 'wp-hotelier' ); ?></h4>
			<p class="htl-ui-heading pro-features__item__description"><?php esc_html_e( 'Take payments on your site and accept all major credit cards from customers in every country. Without living your site, using a pre-built, conversion-optimized form.', 'wp-hotelier' ); ?></p>
		</li>

		<li class="pro-features__item">
			<h4 class="htl-ui-heading pro-features__item__title"><?php esc_html_e( 'Always improving', 'wp-hotelier' ); ?></h4>
			<p class="htl-ui-heading pro-features__item__description"><?php esc_html_e( 'An always-improving toolkit for hotel managers that gains new features every month. Actively developed and fast bug fixing.', 'wp-hotelier' ); ?></p>
		</li>
	</ul>
</div>

<?php endif; ?>

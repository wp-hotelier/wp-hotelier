<?php
/**
 * Page - Status Logs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wrap htl-ui-scope hotelier-settings hotelier-settings--logs">
	<?php
	$settings   = new HTL_Admin_Settings();
	$tabs       = $settings->get_settings_tabs();
	$active_tab = 'logs';

	$notice_wrapper_class = array(
		'htl-ui-setting',
		'htl-ui-setting--section-description'
	);

	$notice_class = array(
		'htl-ui-setting--section-description__text'
	);

	$notice_text = $logs ? __( 'Logs are useful to debug events and errors. Use the dropdown below to select the log you are interested in.', 'wp-hotelier' ) : __( 'There are currently no logs to view.', 'wp-hotelier' );

	include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-navigation.php'; ?>

	<div class="hotelier-settings-wrapper">
		<?php include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-header.php'; ?>

		<div class="hotelier-settings-panel">

			<?php if ( $logs ) : ?>

				<?php htl_ui_print_notice( $notice_text, 'info', $notice_wrapper_class, $notice_class ); ?>

				<strong class="selected-log-title"><?php esc_html_e( 'Current selected log file:', 'wp-hotelier' ); ?></strong>
				<span class="selected-log-file"><?php printf( '%s (%s)', esc_html( $viewed_log ), date_i18n( get_option( 'date_format') . ' ' . get_option( 'time_format'), filemtime( HTL_LOG_DIR . $viewed_log ) ) ); ?></span>

				<form class="htl-ui-form htl-ui-form--logs" action="<?php echo esc_url( admin_url( 'admin.php?page=hotelier-logs' ) ); ?>" method="post">
					<select class="htl-ui-input htl-ui-input--select" name="log_file">
						<?php foreach ( $logs as $log_key => $log_file ) : ?>
							<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( sanitize_title( $viewed_log ), $log_key ); ?>><?php echo esc_html( $log_file ); ?> (<?php echo date_i18n( get_option( 'date_format') . ' ' . get_option( 'time_format'), filemtime( HTL_LOG_DIR . $log_file ) ); ?>)</option>
						<?php endforeach; ?>
					</select>
					<input type="submit" class="htl-ui-button htl-ui-button--view-log" value="<?php esc_attr_e( 'View', 'wp-hotelier' ); ?>" />
				</form>

				<textarea class="htl-ui-input htl-ui-input--textarea htl-ui-input--logs" cols="70" rows="25"><?php echo esc_textarea( file_get_contents( HTL_LOG_DIR . $viewed_log ) ); ?></textarea>

			<?php else : ?>

				<?php htl_ui_print_notice( $notice_text, 'info', $notice_wrapper_class, $notice_class ); ?>

			<?php endif; ?>

		</div>

		<?php include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-pro-section.php'; ?>

	</div>

</div><!-- .wrap -->

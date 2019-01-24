<?php
/**
 * Settings header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="hotelier-settings-header">
	<div class="hotelier-settings-header__brand">
		<h3 class="hotelier-settings-header__brand--title"><?php esc_attr_e( 'Easy WP Hotelier', 'wp-hotelier' ); ?></h3>
	</div>

	<a href="<?php echo esc_url( admin_url( 'admin.php?page=hotelier-calendar' ) ) ?>" class="htl-ui-button htl-ui-button--view-calendar"><?php esc_attr_e( 'View calendar', 'wp-hotelier' ); ?></a>

	<span class="hotelier-settings-header__version-number"><strong><?php esc_attr_e( 'Easy WP Hotelier', 'wp-hotelier' ); ?></strong><?php echo sprintf( esc_html__( 'Version %s', 'wp-hotelier' ), HTL_VERSION ); ?></span>

	<?php $is_hotelier_pro_installed = class_exists( 'HotelierPro' ) ? true : false; ?>

	<span class="hotelier-settings-header__version-info <?php echo $is_hotelier_pro_installed ? 'hotelier-settings-header__version-info--pro' : ''; ?>">
		<?php if ( $is_hotelier_pro_installed ) : ?>
			<?php esc_attr_e( 'Pro', 'wp-hotelier' ); ?>
		<?php else : ?>
			<a href="https://wphotelier.com/"><?php esc_attr_e( 'Upgrade to pro version', 'wp-hotelier' ); ?></a>
		<?php endif; ?>
	</span>
</div>

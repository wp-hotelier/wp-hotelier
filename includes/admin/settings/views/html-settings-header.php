<?php
/**
 * Settings header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="hotelier-settings-header">
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=hotelier-calendar' ) ) ?>" class="htl-ui-button htl-ui-button--view-calendar"><?php esc_attr_e( 'View calendar', 'wp-hotelier' ); ?></a>

	<span class="hotelier-settings-header__version-number"><strong><?php esc_attr_e( 'WP Hotelier', 'wp-hotelier' ); ?></strong><?php echo sprintf( esc_html__( 'Version %s', 'wp-hotelier' ), HTL_VERSION ); ?></span>

	<?php if ( false ) : // temp hide this banner ?>

		<?php $is_hotelier_pro_installed = class_exists( 'HotelierPro' ) ? true : false; ?>

		<span class="hotelier-settings-header__version-info <?php echo $is_hotelier_pro_installed ? 'hotelier-settings-header__version-info--pro-version' : 'hotelier-settings-header__version-info--free-version'; ?>">
			<?php if ( $is_hotelier_pro_installed ) : ?>
				<?php esc_attr_e( 'Pro', 'wp-hotelier' ); ?>
			<?php else : ?>
				<a class="see-pro-version-features" href="https://wphotelier.com/"><?php esc_attr_e( 'Upgrade to Pro version', 'wp-hotelier' ); ?></a>
			<?php endif; ?>
		</span>

	<?php endif; ?>
</div>

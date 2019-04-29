<?php
/**
 * Settings navigation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<nav class="hotelier-settings-navigation">
	<h3 class="hotelier-settings-navigation__title"><?php esc_html_e( 'Dashboard', 'wp-hotelier' ); ?></h3>

	<span class="htl-ui-text-icon htl-ui-text-icon--show-settings-navigation" data-hide-text="<?php esc_html_e( 'Hide navigation', 'wp-hotelier' ); ?>"><?php esc_html_e( 'Show navigation', 'wp-hotelier' ); ?></span>

	<ul class="hotelier-settings-navigation__list">
		<?php
		foreach( $tabs as $tab_id => $tab_name ) {

			$tab_url = add_query_arg(
				array(
					'settings-updated' => false,
					'tab'              => $tab_id
				) ,
				admin_url( 'admin.php?page=hotelier-settings' )
			);

			$active_class = $active_tab == $tab_id ? 'active' : '';
			?>

			<li class="hotelier-settings-navigation__item hotelier-settings-navigation__item--<?php	echo esc_attr( $tab_id ); ?> <?php echo esc_attr( $active_class ); ?>">
				<a href="<?php echo esc_url( $tab_url ); ?>" title="<?php echo esc_attr( $tab_name ); ?>" class="hotelier-settings-navigation__link hotelier-settings-navigation__link--<?php echo esc_attr( $tab_id ); ?> <?php echo esc_attr( $active_class ); ?>"><?php echo esc_html( $tab_name ); ?></a>
			</li>
		<?php } ?>

		<li class="hotelier-settings-navigation__item hotelier-settings-navigation__item--new-reservation <?php echo $active_tab == 'new-reservation' ? 'active' : ''; ?>">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=hotelier-add-reservation' ) ); ?>" title="<?php esc_attr_e( 'Logs', 'wp-hotelier' ); ?>" class="hotelier-settings-navigation__link hotelier-settings-navigation__link--new-reservation <?php echo $active_tab == 'new-reservation' ? 'active' : ''; ?>"><?php esc_attr_e( 'Add manual reservation', 'wp-hotelier' ); ?></a>
		</li>

		<li class="hotelier-settings-navigation__item hotelier-settings-navigation__item--logs <?php echo $active_tab == 'logs' ? 'active' : ''; ?>">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=hotelier-logs' ) ); ?>" title="<?php esc_attr_e( 'Logs', 'wp-hotelier' ); ?>" class="hotelier-settings-navigation__link hotelier-settings-navigation__link--logs <?php echo $active_tab == 'logs' ? 'active' : ''; ?>"><?php esc_attr_e( 'Logs', 'wp-hotelier' ); ?></a>
		</li>
	</ul>
</nav>

<?php
/**
 * Settings navigation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<nav class="hotelier-settings-navigation">
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

		<li class="hotelier-settings-navigation__item hotelier-settings-navigation__item--logs <?php echo $active_tab == 'logs' ? 'active' : ''; ?>">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=hotelier-logs' ) ); ?>" title="<?php esc_attr_e( 'Logs', 'wp-hotelier' ); ?>" class="hotelier-settings-navigation__link hotelier-settings-navigation__link--logs <?php echo $active_tab == 'logs' ? 'active' : ''; ?>"><?php esc_attr_e( 'Logs', 'wp-hotelier' ); ?></a>
		</li>
	</ul>
</nav>

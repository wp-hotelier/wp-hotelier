<?php
/**
 * Wrapper for server info fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="htl-ui-setting htl-ui-setting--server-info htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<?php do_action( 'hotelier_settings_info_' . $args[ 'id' ] ); ?>
</div>

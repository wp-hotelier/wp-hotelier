<?php
/**
 * Wrapper for server info fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="server-info">
	<?php do_action( 'hotelier_settings_info_' . $args[ 'id' ] ); ?>
</p>

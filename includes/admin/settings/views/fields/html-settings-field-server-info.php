<?php
/**
 * Server info field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class = isset( $class ) ? 'htl-ui-setting--server-info-text--' . $class : '';

?>

<span class="htl-ui-setting--server-info-text <?php echo esc_attr( $class ); ?>">
	<?php echo wp_kses_post( $info ); ?>
</span>

<?php
/**
 * Field "Tool Button"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="htl-ui-setting htl-ui-setting--tool-button htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<a class="htl-ui-button htl-ui-button--tool-button" href="<?php echo esc_url( $url ) ?>"><?php echo esc_html( $label ) ?></a>
	<span class="htl-ui-setting__description htl-ui-setting__description--tool-button"><?php echo esc_html( $args[ 'desc' ] ); ?></span>

	<?php do_action( 'hotelier_settings_hook_' . $args[ 'id' ] ); ?>
</div>

<?php
/**
 * Field "Tool Button"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p>
	<a class="button" href="<?php echo esc_url( $url ) ?>"><?php echo esc_html( $label ) ?></a>
	<span class="description"><?php echo esc_html( $args[ 'desc' ] ); ?></span>
</p>

<?php do_action( 'hotelier_settings_hook_' . $args[ 'id' ] ); ?>

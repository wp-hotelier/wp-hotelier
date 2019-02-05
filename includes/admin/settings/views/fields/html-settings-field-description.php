<?php
/**
 * Field "Description"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="htl-ui-setting htl-ui-setting--section-description htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<p class="htl-ui-setting--section-description__text"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></p>
</div>

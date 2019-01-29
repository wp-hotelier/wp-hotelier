<?php
/**
 * Field "Description"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="section-description"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></p>

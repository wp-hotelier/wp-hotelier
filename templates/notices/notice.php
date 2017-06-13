<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/hotelier/notices/notice.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="hotelier-notice hotelier-notice--info"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach; ?>

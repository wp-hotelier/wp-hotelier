<?php
/**
 * Room description (post content).
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/description.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( apply_filters( 'hotelier_single_room_description_show_title', false ) ) : ?>
	<h3 class="room__description-title room__description-title--single"><?php esc_html_e( 'Description', 'wp-hotelier' ); ?></h3>
<?php endif; ?>

<?php the_content(); ?>

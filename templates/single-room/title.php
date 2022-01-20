<?php
/**
 * Room title.
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/title.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$single_room_header_classes = apply_filters(
	'hotelier_single_room_header_classes', array(
		'entry-header',
		'room__header',
	)
);

?>

<header class="<?php echo esc_attr( implode( ' ', $single_room_header_classes ) ) ?>">
	<h1 class="entry-title room__title room__title--single"><?php the_title(); ?></h1>

	<?php do_action( 'hotelier_after_room_title' ); ?>
</header>

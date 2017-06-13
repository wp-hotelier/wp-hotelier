<?php
/**
 * The room name
 *
 * This template can be overridden by copying it to yourtheme/hotelier/archive/content/room-title.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h3 class="room__name room__name--loop"><a class="room__link room__link--loop" href="<?php the_permalink() ?>" rel="bookmark"><?php echo the_title(); ?></a></h3>

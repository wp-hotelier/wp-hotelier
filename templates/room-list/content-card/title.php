<?php
/**
 * Room name
 *
 * This template can be overridden by copying it to yourtheme/hotelier/room-list/content-card/title.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h3 class="room-card__name"><a class="room-card__link" href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>

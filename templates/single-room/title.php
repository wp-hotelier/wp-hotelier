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

?>

<header class="entry-header room__header">
	<h1 class="entry-title room__title room__title--single"><?php the_title(); ?></h1>
</header>

<?php
/**
 * Theme Support Functions. Basic support for WordPress default themes.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( get_template() === 'twentyseventeen' ) {
	include_once( 'class-htl-twenty-seventeen.php' );
} else if ( get_template() === 'twentytwentyone' ) {
	include_once( 'class-htl-twenty-twentyone.php' );
}

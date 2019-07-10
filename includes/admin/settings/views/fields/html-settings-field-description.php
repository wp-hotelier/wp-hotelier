<?php
/**
 * Field "Description"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notice_wrapper_class = array(
	'htl-ui-setting',
	'htl-ui-setting--section-description',
	'htl-ui-setting--' . $args[ 'id' ]
);

$notice_class = array(
	'htl-ui-setting--section-description__text'
);

htl_ui_print_notice( $args[ 'desc' ], 'info', $notice_wrapper_class, $notice_class );

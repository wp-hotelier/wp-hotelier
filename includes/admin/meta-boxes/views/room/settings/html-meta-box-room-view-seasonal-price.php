<?php
/**
 * View: seasonal price
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $thepostid, $post;

$thepostid                = empty( $thepostid ) ? $post->ID : $thepostid;
$default_price_input_name = $settings[ 'default_price_input_name' ] ? $settings[ 'default_price_input_name' ] : '';
$default_price_value      = $settings[ 'default_price_value' ] ? $settings[ 'default_price_value' ] : '';
$schema_price_input_name  = $settings[ 'schema_price_input_name' ] ? $settings[ 'schema_price_input_name' ] : '';
$schema_price_value       = $settings[ 'schema_price_value' ] ? $settings[ 'schema_price_value' ] : array();
$seasonal_prices_schema   = htl_get_seasonal_prices_schema();

$seasonal_price_notice = $seasonal_prices_schema ? sprintf( wp_kses( __( 'When the seasonal price is activated the system will calculate the final price using the date ranges defined on your seasonal schema. You can change your schema whenever you want <a href="%1$s">here</a>.', 'wp-hotelier' ), array( 'a' => array( 'href' => array() ) ) ), 'admin.php?page=hotelier-settings&tab=seasonal-prices' ) : sprintf( wp_kses( __( 'There are no seasonal ranges defined. Add some date ranges <a href="%1$s">here</a> and then edit again this room to define your price system.', 'wp-hotelier' ), array( 'a' => array( 'href' => array() ) ) ), 'admin.php?page=hotelier-settings&tab=seasonal-prices' );

htl_ui_print_notice( $seasonal_price_notice );

HTL_Meta_Boxes_Helper::price_input(
	array(
		'id'          => $default_price_input_name,
		'value'       => $default_price_value,
		'label'       => esc_html__( 'Default price:', 'wp-hotelier' ),
		'description' => esc_html__( 'This is the default price of one night when no rules are applicable.', 'wp-hotelier' )
	)
);

if ( is_array( $seasonal_prices_schema ) ) {

	foreach ( $seasonal_prices_schema as $key => $rule ) {
		$seasonal_price_current_value = isset( $schema_price_value[ $key ] ) ? $schema_price_value[ $key ] : '';
		$every_year                   = isset( $seasonal_prices_schema[ $key ][ 'every_year' ] ) ? 1 : 0;
		$after_label                  = isset( $rule[ 'season_name' ] ) && $rule[ 'season_name' ] ? '<strong class="htl-ui-setting__title-description__season-name">' . $rule[ 'season_name' ] . '</strong>' : '';
		$after_label                  .= sprintf( __( 'From %s to %s', 'wp-hotelier' ), $rule[ 'from' ], $rule[ 'to' ] );

		$input_args = array(
			'id'          => $schema_price_input_name . '[' . esc_attr( $key ) . ']',
			'label'       => esc_html__( 'Price of this range:', 'wp-hotelier' ),
			'after_label' => $after_label,
			'description' => esc_html__( 'This is the price of one night on this date range.', 'wp-hotelier' ),
			'value'       => $seasonal_price_current_value
		);

		if ( $every_year ) {
			$input_args[ 'after_input' ] = __( '(Every year)', 'wp-hotelier' );
		}

		HTL_Meta_Boxes_Helper::price_input( $input_args );
	}
}

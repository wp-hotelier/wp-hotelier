<?php
/**
 * Shows the room price
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="room-price-panel">

	<?php

	// room price

	$price_type = array(
		'global'         => esc_html__( 'Global', 'wp-hotelier' ),
		'per_day'        => esc_html__( 'Price per day', 'wp-hotelier' ),
		'seasonal_price' => esc_html__( 'Seasonal price', 'wp-hotelier' )
	);

	HTL_Meta_Boxes_Helper::select_input(
		array(
			'id'      => '_price_type',
			'label'   => esc_html__( 'Price:', 'wp-hotelier' ),
			'class'   => 'room-price',
			'options' => $price_type
		)
	);

	?>

	<div class="price-panel price-panel-global">

		<?php

		HTL_Meta_Boxes_Helper::text_input(
			array(
				'id'            => '_regular_price',
				'label'         => esc_html__( 'Regular price:', 'wp-hotelier' ),
				'wrapper_class' => 'price',
				'data_type'     => 'price',
				'placeholder'   => self::get_price_placeholder(),
				'desc_tip'      => 'true',
				'description'   => esc_html__( 'Same price for all days of the week.', 'wp-hotelier' )
			)
		);

		HTL_Meta_Boxes_Helper::text_input(
			array(
				'id'            => '_sale_price',
				'label'         => esc_html__( 'Sale price:', 'wp-hotelier' ),
				'wrapper_class' => 'price',
				'data_type'     => 'price',
				'placeholder'   => self::get_price_placeholder(),
				'desc_tip'      => 'true',
				'description'   => esc_html__( 'Same price for all days of the week.', 'wp-hotelier' )
			)
		);

		?>

	</div><!-- .global-price -->

	<div class="price-panel price-panel-per_day">

		<?php

		HTL_Meta_Boxes_Helper::price_per_day(
			array(
				'id'          => '_regular_price_day',
				'label'       => esc_html__( 'Regular price:', 'wp-hotelier' ),
				'desc_tip'    => 'true',
				'description' => esc_html__( 'The regular price of the room per day.', 'wp-hotelier' )
			)
		);

		HTL_Meta_Boxes_Helper::price_per_day(
			array(
				'id'          => '_sale_price_day',
				'label'       => esc_html__( 'Sale price:', 'wp-hotelier' ),
				'desc_tip'    => 'true',
				'description' => esc_html__( 'The sale price of the room per day.', 'wp-hotelier' )
			)
		);

		?>

	</div><!-- .price-per-day -->

	<div class="price-panel price-panel-seasonal_price">

		<?php

		HTL_Meta_Boxes_Helper::text_input(
			array(
				'id'            => '_seasonal_base_price',
				'label'         => esc_html__( 'Default price:', 'wp-hotelier' ),
				'wrapper_class' => 'price',
				'data_type'     => 'price',
				'placeholder'   => self::get_price_placeholder(),
				'desc_tip'      => 'true',
				'description'   => esc_html__( 'Default room price. Used when no rules are found.', 'wp-hotelier' )
			)
		);


		if ( ( $seasonal_prices_schema = htl_get_seasonal_prices_schema() ) && is_array( $seasonal_prices_schema ) ) {

			$seasonal_price_value = get_post_meta( $thepostid, '_seasonal_price', true );

			foreach ( $seasonal_prices_schema as $key => $rule ) {
				$seasonal_price_current_value = isset( $seasonal_price_value[ $key ] ) ? $seasonal_price_value[ $key ] : '';
				$every_year = isset( $seasonal_prices_schema[ $key ][ 'every_year' ] ) ? 1 : 0;

				$input_args = array(
					'id'            => '_seasonal_price[' . esc_attr( $key ) . ']',
					'label'         => sprintf( __( 'Price from %s to %s:', 'wp-hotelier' ), '<em>' . esc_html( $rule[ 'from' ] ) . '</em>', '<em>' . esc_html( $rule[ 'to' ] ) . '</em>' ),
					'wrapper_class' => 'price',
					'data_type'     => 'price',
					'placeholder'   => self::get_price_placeholder(),
					'value'         => $seasonal_price_current_value
				);

				if ( $every_year ) {
					$input_args[ 'after_input' ] = __( '(Every year)', 'wp-hotelier' );
				}

				HTL_Meta_Boxes_Helper::text_input( $input_args );
			}

			echo '<p class="change-seasonal-prices-rules"><a href="admin.php?page=hotelier-settings&tab=seasonal-prices">' . esc_html( 'Change seasonal prices schema', 'wp-hotelier' ) . '</a></p>';

		} else {
			echo '<p class="message no-seasonal-prices-rules">' . sprintf( wp_kses( __( 'There are no seasonal prices defined. Add some date ranges <a href="%1$s">here</a>.', 'wp-hotelier' ), array( 'a' => array( 'href' => array() ) ) ), 'admin.php?page=hotelier-settings&tab=seasonal-prices' ) . '</p>';
		} ?>

	</div><!-- .seasonal-price -->

</div><!-- .room-price-panel -->

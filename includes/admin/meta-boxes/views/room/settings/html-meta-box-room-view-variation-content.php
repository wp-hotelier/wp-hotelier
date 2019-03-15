<?php
/**
 * View: variation header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="room-variation__content">

	<div class="htl-ui-setting-group htl-ui-setting-group--metabox htl-ui-setting-group--price-options">
		<?php
		$price_type = array(
			'global'         => esc_html__( 'Global', 'wp-hotelier' ),
			'per_day'        => esc_html__( 'Price per day', 'wp-hotelier' ),
			'seasonal_price' => esc_html__( 'Seasonal price', 'wp-hotelier' )
		);

		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_room_variations[' . absint( $loop ) . '][price_type]',
				'value'                => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'price_type', $loop ),
				'label'                => esc_html__( 'Price type:', 'wp-hotelier' ),
				'options'              => apply_filters( 'hotelier_room_price_type', $price_type ),
				'std'                  => 'global',
				'conditional'          => true,
				'conditional-selector' => 'price-type',
			)
		);
		?>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--price-type" data-type="global">
			<?php
			HTL_Meta_Boxes_Helper::price_input(
				array(
					'name'        => '_room_variations[' . absint( $loop ) . '][regular_price]',
					'value'       => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'regular_price', $loop ),
					'label'       => esc_html__( 'Regular price:', 'wp-hotelier' ),
					'description' => 'Same price for all days of the week.',
				)
			);

			HTL_Meta_Boxes_Helper::price_input(
				array(
					'name'        => '_room_variations[' . absint( $loop ) . '][sale_price]',
					'value'       => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'sale_price', $loop ),
					'label'       => esc_html__( 'Sale price:', 'wp-hotelier' ),
					'description' => 'Same price for all days of the week.',
				)
			);
			?>
		</div>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--price-type" data-type="per_day">
			<?php
			HTL_Meta_Boxes_Helper::price_per_day(
				array(
					'name'        => '_room_variations[' . absint( $loop ) . '][price_day]',
					'value'       => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'price_day', $loop ),
					'label'       => esc_html__( 'Regular price:', 'wp-hotelier' ),
					'description' => 'The regular price of the room per day.',
				)
			);

			HTL_Meta_Boxes_Helper::price_per_day(
				array(
					'name'        => '_room_variations[' . absint( $loop ) . '][sale_price_day]',
					'value'       => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'sale_price_day', $loop ),
					'label'       => esc_html__( 'Sale price:', 'wp-hotelier' ),
					'description' => 'The sale price of the room per day.',
				)
			);
			?>
		</div>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--price-type" data-type="seasonal_price">
			<?php
			HTL_Meta_Boxes_Views::seasonal_price(
				array(
					'default_price_input_name' => '_room_variations[' . absint( $loop ) . '][seasonal_base_price]',
					'default_price_value'      => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'seasonal_base_price', $loop ),
					'schema_price_input_name'  => '_room_variations[' . absint( $loop ) . '][seasonal_price]',
					'schema_price_value'       => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'seasonal_price', $loop ),
				)
			);
			?>
		</div>

		<?php
		/**
		 * A filter is provided to allow extensions to add their own price settings
		 */
		do_action( 'hotelier_room_price_settings_variation', HTL_Meta_Box_Room_Settings::get_price_placeholder() ); ?>
	</div>

	<?php do_action( 'hotelier_room_variation_settings_after_price' ); ?>

	<div class="htl-ui-setting-group htl-ui-setting-group--metabox htl-ui-setting-group--deposit-options">
		<?php
		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'              => '_room_variations[' . absint( $loop ) . '][require_deposit]',
				'value'             => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'require_deposit', $loop ),
				'label'             => esc_html__( 'Require deposit?', 'wp-hotelier' ),
				'options'           => array(
					'yes' => esc_html__( 'Yes', 'wp-hotelier' ),
					'no'  => esc_html__( 'No', 'wp-hotelier' ),
				),
				'std'               => 'no',
				'checkbox-fallback' => true,
				'show-if'           => 'yes',
				'show-element'      => 'deposit-settings',
				'description'       => esc_html__( 'When enabled, a deposit is required at the time of booking.', 'wp-hotelier' )
			)
		);
		?>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--deposit-settings" data-type="deposit-settings">
			<?php
			HTL_Meta_Boxes_Helper::select_input(
				array(
					'name'    => '_room_variations[' . absint( $loop ) . '][deposit_amount]',
					'value'   => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'deposit_amount', $loop ),
					'label'   => esc_html__( 'Deposit amount:', 'wp-hotelier' ),
					'options' => HTL_Meta_Box_Room_Settings::get_deposit_options(),
					'class'   => 'deposit-amount-select',
				)
			);
			?>
		</div>

		<?php
		/**
		 * A filter is provided to allow extensions to add their own deposit options
		 */
		do_action( 'hotelier_room_variation_deposit_options' ); ?>
	</div>

	<?php
	HTL_Meta_Boxes_Helper::switch_input(
		array(
			'name'              => '_room_variations[' . absint( $loop ) . '][non_cancellable]',
			'value'             => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'non_cancellable', $loop ),
			'label'             => esc_html__( 'Non cancellable?', 'wp-hotelier' ),
			'options'           => array(
				'yes' => esc_html__( 'Yes', 'wp-hotelier' ),
				'no'  => esc_html__( 'No', 'wp-hotelier' ),
			),
			'std'               => 'no',
			'checkbox-fallback' => true,
			'description'       => esc_html__( 'When enabled, reservations that include this room will be non cancellable and non refundable.', 'wp-hotelier' )
		)
	);

	HTL_Meta_Boxes_Helper::multi_text(
		array(
			'name'         => '_room_variations[' . absint( $loop ) . '][room_conditions]',
			'value'        => HTL_Meta_Boxes_Helper::get_variation_field_value( $variations, 'room_conditions', $loop ),
			'label'        => esc_html__( 'Conditions:', 'wp-hotelier' ),
			'placeholder'  => esc_html__( 'Special condition here', 'wp-hotelier' ),
			'button_label' => esc_html__( 'Add new condition', 'wp-hotelier' ),
		)
	);
	?>

	<?php
	/**
	 * A filter is provided to allow extensions to add their own room variation settings
	 */
	do_action( 'hotelier_room_variation_settings' ); ?>

</div>

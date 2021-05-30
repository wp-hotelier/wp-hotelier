<?php
/**
 * Fee settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

?>

<?php wp_nonce_field( 'hotelier_save_data', 'hotelier_meta_nonce' ); ?>

<div class="fee-settings htl-ui-scope">

	<?php do_action( 'hotelier_fee_before_fee_general_settings' ); ?>

	<div class="fee-settings__general htl-ui-settings-wrap">
		<h3 class="htl-ui-heading htl-ui-heading--section-header"><?php esc_html_e( 'General settings', 'wp-hotelier' ); ?></h3>

		<?php
		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_fee_enabled',
				'value'                => get_post_meta( $thepostid, '_fee_enabled', true ),
				'label'                => esc_html__( 'Enabled?', 'wp-hotelier' ),
				'description'          => esc_html__( 'Activate this fee.', 'wp-hotelier' ),
				'options'              => array(
					'enabled'  => esc_html__( 'Enabled', 'wp-hotelier' ),
					'disabled' => esc_html__( 'Disabled', 'wp-hotelier' )
				),
				'std'                  => 'enabled',
			)
		);

		HTL_Meta_Boxes_Helper::text_input(
			array(
				'id'          => '_fee_name',
				'value'       => get_post_meta( $thepostid, '_fee_name', true ),
				'label'       => esc_html__( 'Fee name:', 'wp-hotelier' ),
				'placeholder' => esc_html__( 'Fee name', 'wp-hotelier' ),
				'description' => esc_html__( 'The name of the fee displayed to the user.', 'wp-hotelier' ),
			)
		);

		HTL_Meta_Boxes_Helper::textarea_input(
			array(
				'id'          => '_fee_description',
				'value'       => get_post_meta( $thepostid, '_fee_description', true ),
				'label'       => esc_html__( 'Fee description:', 'wp-hotelier' ),
				'placeholder' => esc_html__( 'Fee description (optional)', 'wp-hotelier' ),
				'description' => esc_html__( 'Enter a description for this fee (optional). For internal uses but some themes may use it.', 'wp-hotelier' ),
			)
		);

		HTL_Meta_Boxes_Helper::price_input(
			array(
				'id'          => '_fee_price',
				'value'       => get_post_meta( $thepostid, '_fee_price', true ),
				'label'       => esc_html__( 'Fee price:', 'wp-hotelier' ),
				'description' => esc_html__( 'Enter the price of the fee.', 'wp-hotelier' ),
			)
		);

		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_fee_type',
				'value'                => get_post_meta( $thepostid, '_fee_type', true ),
				'label'                => esc_html__( 'Fee type:', 'wp-hotelier' ),
				'description'          => esc_html__( 'The type of fee.', 'wp-hotelier' ),
				'options'              => array(
					'per_room'   => esc_html__( 'Per room', 'wp-hotelier' ),
					'per_person' => esc_html__( 'Per person', 'wp-hotelier' ),
				),
				'std'                  => 'per_room',
				'show-if'              => 'per_person',
				'show-element'         => 'fee-guest-type',
			)
		);
		?>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--fee-guest-type" data-type="fee-guest-type">
			<?php
			HTL_Meta_Boxes_Helper::select_input(
				array(
					'name'                 => '_fee_guest_type',
					'value'                => get_post_meta( $thepostid, '_fee_guest_type', true ),
					'label'                => esc_html__( 'Guest type:', 'wp-hotelier' ),
					'description'          => esc_html__( 'You can restrict the use of this fee to a type of guest.', 'wp-hotelier' ),
					'options'              => array(
						'default'       => esc_html__( 'Adults and children', 'wp-hotelier' ),
						'adults_only'   => esc_html__( 'Adults only', 'wp-hotelier' ),
						'children_only' => esc_html__( 'Children only', 'wp-hotelier' ),
					),
				)
			);
			?>
		</div>

		<?php
		HTL_Meta_Boxes_Helper::checkbox_input(
			array(
				'id'           => '_fee_calculate_per_night',
				'value'        => get_post_meta( $thepostid, '_fee_calculate_per_night', true ),
				'label'        => esc_html__( 'Multiply per night?', 'wp-hotelier' ),
				'toggle'       => true,
				'show-if'      => true,
				'show-element' => 'fee-max-cost',
				'description'  => esc_html__( 'Enable to multiply the price of the fee for the nights of staying.', 'wp-hotelier' ),
			)
		);
		?>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--fee-max-cost" data-type="fee-max-cost">
			<?php
			HTL_Meta_Boxes_Helper::price_input(
				array(
					'id'          => '_fee_max_cost',
					'value'       => get_post_meta( $thepostid, '_fee_max_cost', true ),
					'label'       => esc_html__( 'Maximum cost:', 'wp-hotelier' ),
					'description' => esc_html__( 'Set a maximum cost that the fee can reach.', 'wp-hotelier' ),
				)
			);
			?>
		</div>

		<?php
		/**
		 * A filter is provided to allow extensions to add their own standard fee settings
		 */
		do_action( 'hotelier_fee_general_settings' ); ?>
	</div>

	<?php do_action( 'hotelier_fee_after_fee_general_settings' ); ?>

</div>

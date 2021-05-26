<?php
/**
 * Coupon settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

?>

<?php wp_nonce_field( 'hotelier_save_data', 'hotelier_meta_nonce' ); ?>

<div class="coupon-settings htl-ui-scope">

	<?php do_action( 'hotelier_coupon_before_coupon_general_settings' ); ?>

	<div class="coupon-settings__general htl-ui-settings-wrap">
		<h3 class="htl-ui-heading htl-ui-heading--section-header"><?php esc_html_e( 'General settings', 'wp-hotelier' ); ?></h3>

		<?php
		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_coupon_enabled',
				'value'                => get_post_meta( $thepostid, '_coupon_enabled', true ),
				'label'                => esc_html__( 'Enabled?', 'wp-hotelier' ),
				'description'          => esc_html__( 'Activate this coupon.', 'wp-hotelier' ),
				'options'              => array(
					'enabled'  => esc_html__( 'Enabled', 'wp-hotelier' ),
					'disabled' => esc_html__( 'Disabled', 'wp-hotelier' )
				),
				'std'                  => 'enabled',
			)
		);

		HTL_Meta_Boxes_Helper::text_input(
			array(
				'id'          => '_coupon_code',
				'value'       => get_post_meta( $thepostid, '_coupon_code', true ),
				'label'       => esc_html__( 'Coupon code:', 'wp-hotelier' ),
				'placeholder' => esc_html__( 'Coupon code', 'wp-hotelier' ),
				'description' => esc_html__( 'Enter a code for this coupon, such as 30SALE. Only alphanumeric characters are allowed.', 'wp-hotelier' ),
			)
		);

		HTL_Meta_Boxes_Helper::textarea_input(
			array(
				'id'          => '_coupon_description',
				'value'       => get_post_meta( $thepostid, '_coupon_description', true ),
				'label'       => esc_html__( 'Coupon description:', 'wp-hotelier' ),
				'placeholder' => esc_html__( 'Coupon description (optional)', 'wp-hotelier' ),
				'description' => esc_html__( 'Enter a description for this coupon (optional). For internal uses but some themes may use it.', 'wp-hotelier' ),
			)
		);

		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_coupon_type',
				'value'                => get_post_meta( $thepostid, '_coupon_type', true ),
				'label'                => esc_html__( 'Coupon type:', 'wp-hotelier' ),
				'description'          => esc_html__( 'The type of discount to apply for this coupon.', 'wp-hotelier' ),
				'options'              => array(
					'percentage' => esc_html__( 'Percentage', 'wp-hotelier' ),
					'fixed'      => esc_html__( 'Fixed', 'wp-hotelier' )
				),
				'std'                  => 'percentage',
				'conditional'          => true,
				'conditional-selector' => 'coupon-amount-type',
			)
		);
		?>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--coupon-amount-type" data-type="percentage">
			<?php
				HTL_Meta_Boxes_Helper::number_input(
					array(
						'id'          => '_coupon_amount_percentage',
						'value'       => get_post_meta( $thepostid, '_coupon_amount_percentage', true ),
						'label'       => esc_html__( 'Amount:', 'wp-hotelier' ),
						'description' => esc_html__( 'Enter the discount percentage. 30 = 30%.', 'wp-hotelier' ),
					)
				);
			?>
		</div>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--coupon-amount-type" data-type="fixed">
			<?php
				HTL_Meta_Boxes_Helper::price_input(
					array(
						'id'          => '_coupon_amount_fixed',
						'value'       => get_post_meta( $thepostid, '_coupon_amount_fixed', true ),
						'label'       => esc_html__( 'Amount:', 'wp-hotelier' ),
						'description' => esc_html__( 'Enter the discount amount.', 'wp-hotelier' ),
					)
				);
			?>
		</div>

		<?php
		HTL_Meta_Boxes_Helper::datepicker(
			array(
				'name'        => '_coupon_expiration_date',
				'value'       => get_post_meta( $thepostid, '_coupon_expiration_date', true ),
				'label'       => esc_html__( 'Expiration date:', 'wp-hotelier' ),
				'description' => esc_html__( 'Enter the expiration date for this coupon code. The coupon will expire at 00:00:00 of this date. Leave blank for no expiration.', 'wp-hotelier' ),
			)
		);

		/**
		 * A filter is provided to allow extensions to add their own standard coupon settings
		 */
		do_action( 'hotelier_coupon_general_settings' ); ?>
	</div>

	<?php do_action( 'hotelier_coupon_after_coupon_general_settings' ); ?>

</div>

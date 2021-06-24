<?php
/**
 * Extra settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

?>

<?php wp_nonce_field( 'hotelier_save_data', 'hotelier_meta_nonce' ); ?>

<div class="extra-settings htl-ui-scope">

	<?php do_action( 'hotelier_extra_before_extra_general_settings' ); ?>

	<div class="extra-settings__general htl-ui-settings-wrap">
		<h3 class="htl-ui-heading htl-ui-heading--section-header"><?php esc_html_e( 'General settings', 'wp-hotelier' ); ?></h3>

		<?php
		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_extra_enabled',
				'value'                => get_post_meta( $thepostid, '_extra_enabled', true ),
				'label'                => esc_html__( 'Enabled?', 'wp-hotelier' ),
				'description'          => esc_html__( 'Activate this extra.', 'wp-hotelier' ),
				'options'              => array(
					'enabled'  => esc_html__( 'Enabled', 'wp-hotelier' ),
					'disabled' => esc_html__( 'Disabled', 'wp-hotelier' )
				),
				'std'                  => 'enabled',
			)
		);

		HTL_Meta_Boxes_Helper::text_input(
			array(
				'id'          => '_extra_name',
				'value'       => get_post_meta( $thepostid, '_extra_name', true ),
				'label'       => esc_html__( 'Extra name:', 'wp-hotelier' ),
				'placeholder' => esc_html__( 'Extra name', 'wp-hotelier' ),
				'description' => esc_html__( 'The name of the extra displayed to the user.', 'wp-hotelier' ),
			)
		);

		HTL_Meta_Boxes_Helper::textarea_input(
			array(
				'id'          => '_extra_description',
				'value'       => get_post_meta( $thepostid, '_extra_description', true ),
				'label'       => esc_html__( 'Extra description:', 'wp-hotelier' ),
				'placeholder' => esc_html__( 'Extra description (optional)', 'wp-hotelier' ),
				'description' => esc_html__( 'Enter a description for this extra.', 'wp-hotelier' ),
			)
		);

		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_extra_amount_type',
				'value'                => get_post_meta( $thepostid, '_extra_amount_type', true ),
				'label'                => esc_html__( 'Extra amount type:', 'wp-hotelier' ),
				'description'          => esc_html__( 'The type of amount to apply for this extra.', 'wp-hotelier' ),
				'options'              => array(
					'fixed'      => esc_html__( 'Fixed', 'wp-hotelier' ),
					'percentage' => esc_html__( 'Percentage', 'wp-hotelier' ),
				),
				'std'                  => 'fixed',
				'conditional'          => true,
				'conditional-selector' => 'extra-amount-type',
			)
		);
		?>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--extra-amount-type" data-type="fixed">
			<?php
				HTL_Meta_Boxes_Helper::price_input(
					array(
						'id'          => '_extra_amount_fixed',
						'value'       => get_post_meta( $thepostid, '_extra_amount_fixed', true ),
						'label'       => esc_html__( 'Amount:', 'wp-hotelier' ),
						'description' => esc_html__( 'Enter the fixed amount.', 'wp-hotelier' ),
					)
				);
				HTL_Meta_Boxes_Helper::checkbox_input(
					array(
						'id'           => '_extra_calculate_per_night',
						'value'        => get_post_meta( $thepostid, '_extra_calculate_per_night', true ),
						'label'        => esc_html__( 'Multiply per night?', 'wp-hotelier' ),
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => 'extra-max-cost',
						'description'  => esc_html__( 'Enable to multiply the price of the extra for the nights of staying.', 'wp-hotelier' ),
					)
				);
			?>

			<div class="htl-ui-setting-conditional htl-ui-setting-conditional--extra-max-cost" data-type="extra-max-cost">
				<?php
				HTL_Meta_Boxes_Helper::price_input(
					array(
						'id'          => '_extra_max_cost',
						'value'       => get_post_meta( $thepostid, '_extra_max_cost', true ),
						'label'       => esc_html__( 'Maximum cost:', 'wp-hotelier' ),
						'description' => esc_html__( 'Set a maximum cost that the extra can reach or leave empty to disable.', 'wp-hotelier' ),
					)
				);
				?>
			</div>
		</div>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--extra-amount-type" data-type="percentage">
			<?php
				HTL_Meta_Boxes_Helper::number_input(
					array(
						'id'          => '_extra_amount_percentage',
						'value'       => get_post_meta( $thepostid, '_extra_amount_percentage', true ),
						'label'       => esc_html__( 'Amount:', 'wp-hotelier' ),
						'description' => esc_html__( 'Enter the amount percentage. 30 = 30%.', 'wp-hotelier' ),
					)
				);
			?>
		</div>

		<?php
		HTL_Meta_Boxes_Helper::switch_input(
			array(
				'name'                 => '_extra_type',
				'value'                => get_post_meta( $thepostid, '_extra_type', true ),
				'label'                => esc_html__( 'Extra type:', 'wp-hotelier' ),
				'description'          => esc_html__( 'The type of extra.', 'wp-hotelier' ),
				'options'              => array(
					'per_room'   => esc_html__( 'Per room', 'wp-hotelier' ),
					'per_person' => esc_html__( 'Per person', 'wp-hotelier' ),
				),
				'std'                  => 'per_room',
				'show-if'              => 'per_person',
				'show-element'         => 'extra-guest-type',
			)
		);
		?>

		<div class="htl-ui-setting-conditional htl-ui-setting-conditional--extra-guest-type" data-type="extra-guest-type">
			<?php
			if ( htl_get_option( 'book_now_redirect_to_booking_page', 0 ) && ! htl_get_option( 'book_now_allow_quantity_selection', 0 ) ) {
				$notice_text = sprintf( __( '<strong>Please note:</strong> It is recommended to enable the <a href="%s">Allow quantity selection</a> option when using the <strong>"Per person"</strong> type of extra, to allow the user to select the number of guests. If left off, the system will calculate the maximum number of adults that the room allows as the number of guests.', 'wp-hotelier' ), admin_url( 'admin.php?page=hotelier-settings&tab=rooms-and-reservations' ) );
				htl_ui_print_notice( $notice_text );
			}

			HTL_Meta_Boxes_Helper::select_input(
				array(
					'name'                 => '_extra_guest_type',
					'value'                => get_post_meta( $thepostid, '_extra_guest_type', true ),
					'label'                => esc_html__( 'Guest type:', 'wp-hotelier' ),
					'description'          => esc_html__( 'You can restrict the use of this extra to a type of guest.', 'wp-hotelier' ),
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
		/**
		 * A filter is provided to allow extensions to add their own standard extra settings
		 */
		do_action( 'hotelier_extra_general_settings' ); ?>
	</div>

	<?php do_action( 'hotelier_extra_after_extra_general_settings' ); ?>

</div>

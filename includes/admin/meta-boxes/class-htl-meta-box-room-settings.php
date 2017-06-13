<?php
/**
 * Room Settings.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Room_Settings' ) ) :

/**
 * HTL_Meta_Box_Room_Settings Class
 */
class HTL_Meta_Box_Room_Settings {

	/**
	 * Get max guests option.
	 */
	public static function get_guests() {
		$max_guests = apply_filters( 'hotelier_max_guests', 10 );
		$guests = array_combine( range( 1, $max_guests ), range( 1, $max_guests ) );

		return $guests;
	}

	/**
	 * Get max children option.
	 */
	public static function get_children() {
		$max_children = apply_filters( 'hotelier_max_children', 5 );
		$children = range( 0, $max_children );

		return $children;
	}

	/**
	 * Get room stock option.
	 */
	public static function get_stock_rooms() {
		$stock_rooms = apply_filters( 'hotelier_stock_rooms', 15 );
		$quantity = range( 0, $stock_rooms );

		return $quantity;
	}

	/**
	 * Get price placeholder.
	 */
	public static function get_price_placeholder() {
		$thousands_sep = htl_get_price_thousand_separator();
		$decimal_sep   = htl_get_price_decimal_separator();
		$decimals      = htl_get_price_decimals();

		$placeholder = number_format( '0', $decimals, $decimal_sep, $thousands_sep );

		return $placeholder;
	}

	/**
	 * Get deposit options.
	 */
	public static function get_deposit_options() {
		$options =  array(
			'100' => '100%',
			'90'  => '90%',
			'80'  => '80%',
			'70'  => '70%',
			'60'  => '60%',
			'50'  => '50%',
			'40'  => '40%',
			'30'  => '30%',
			'20'  => '20%',
			'10'  => '10%',
		);

		// extensions can hook into here to add their own options
		return apply_filters( 'hotelier_deposit_options', $options );
	}

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		wp_nonce_field( 'hotelier_save_data', 'hotelier_meta_nonce' );
		?>

		<div class="panel-wrap room-settings">

			<div class="room-general-settings">

				<?php

				// room type
				HTL_Meta_Boxes_Helper::select_input(
					array(
						'id'      => '_room_type',
						'show_id' => true,
						'label'   => esc_html__( 'Room type:', 'hotelier' ),
						'options' => array(
							'standard_room' => esc_html__( 'Standard room', 'hotelier' ),
							'variable_room' => esc_html__( 'Variable room', 'hotelier' )
						)
					)
				);

				// guests
				HTL_Meta_Boxes_Helper::select_input(
					array(
						'id'      => '_max_guests',
						'show_id' => true,
						'label'   => esc_html__( 'Guests:', 'hotelier' ),
						'options' => self::get_guests()
					)
				);

				// children
				HTL_Meta_Boxes_Helper::select_input(
					array(
						'id'      => '_max_children',
						'show_id' => true,
						'label'   => esc_html__( 'Children:', 'hotelier' ),
						'options' => self::get_children()
					)
				);

				// bed size(s)
				HTL_Meta_Boxes_Helper::text_input(
					array(
						'id'          => '_bed_size',
						'show_id'     => true,
						'label'       => esc_html__( 'Bed size(s):', 'hotelier' ),
						'placeholder' => esc_html__( '1 king', 'hotelier' )
					)
				);

				// room size
				$hotelier_dimension_unit = htl_get_option( 'room_size_unit', 'm²' );
				HTL_Meta_Boxes_Helper::text_input(
					array(
						'id'          => '_room_size',
						'show_id'     => true,
						'label'       => esc_html__( 'Room size', 'hotelier' ) . ' (' . $hotelier_dimension_unit . '):' ,
						'placeholder' => '10'
					)
				);

				// available rooms
				HTL_Meta_Boxes_Helper::select_input(
					array(
						'id'          => '_stock_rooms',
						'show_id'     => true,
						'label'       => esc_html__( 'Stock rooms?', 'hotelier' ),
						'options'     => self::get_stock_rooms(),
						'description' => esc_html__( 'This is the total number of rooms available in the structure.', 'hotelier' )
					)
				);

				// additional settings button
				?>
				<p class="form-field"><a id="view-room-additional-settings" href="#room-additional-settings" class="button button-primary"><?php esc_html_e( 'Additional settings', 'hotelier' ) ?></a></p>

				<?php
				/**
				 * A filter is provided to allow extensions to add their own room general settings
				 */
				do_action( 'hotelier_room_general_settings' ); ?>

			</div><!-- .room-general-settings -->

			<div class="room-advanced-settings">

				<div class="standard-room-panel">

					<h4><?php esc_html_e( 'Standard room', 'hotelier' ); ?></h4>

					<?php

					// room price

					include( 'views/html-meta-box-room-price.php' );

					?>

					<?php do_action( 'hotelier_room_standard_settings_after_price' ); ?>

					<div class="room-deposit">

						<?php
						HTL_Meta_Boxes_Helper::checkbox_input(
							array(
								'id'          => '_require_deposit',
								'show_id'     => true,
								'class'       => 'require-deposit',
								'label'       => esc_html__( 'Require deposit?', 'hotelier' ),
								'description' => esc_html__( 'When selected, a deposit is required at the time of booking.', 'hotelier' )
							)
						); ?>

						<div class="room-deposit-amount">

							<?php
							HTL_Meta_Boxes_Helper::select_input(
								array(
									'id'      => '_deposit_amount',
									'show_id' => true,
									'label'   => esc_html__( 'Deposit amount:', 'hotelier' ),
									'options' => self::get_deposit_options()
								)
							);
							?>

							<?php
							/**
							 * A filter is provided to allow extensions to add their own deposit options
							 */
							do_action( 'hotelier_room_standard_deposit_options' ); ?>

						</div><!-- .room-deposit-amount -->

					</div><!-- .room-deposit -->

					<div class="room-cancellation">
						<?php
						HTL_Meta_Boxes_Helper::checkbox_input(
							array(
								'id'          => '_non_cancellable',
								'show_id'     => true,
								'label'       => esc_html__( 'Non cancellable?', 'hotelier' ),
								'description' => esc_html__( 'When checked, reservations that include this room will be non cancellable and non refundable.', 'hotelier' )
							)
						); ?>
					</div>

					<?php

					// room conditions

					include( 'views/html-meta-box-room-conditions.php' );

					?>

					<?php
					/**
					 * A filter is provided to allow extensions to add their own standard room settings
					 */
					do_action( 'hotelier_room_standard_settings' ); ?>

				</div><!-- .standard-room-panel -->

				<div class="variation-room-panel">

					<h4><?php esc_html_e( 'Variable room', 'hotelier' ); ?></h4>

					<div class="toolbar">
						<a href="#" class="expand-all"><?php esc_html_e( 'Expand all', 'hotelier' ); ?></a>
						<a href="#" class="close-all"><?php esc_html_e( 'Close all', 'hotelier' ); ?></a>
						<button type="button" class="add-variation button button-primary"><?php esc_html_e( 'Add room rate', 'hotelier' ); ?></button>
					</div>

					<?php
					$get_room_rates = get_terms( 'room_rate', 'hide_empty=0' );

					if ( ! empty( $get_room_rates ) && ! is_wp_error( $get_room_rates ) ) : ?>

						<div class="room-variations">

							<?php
							$variations = maybe_unserialize( get_post_meta( $thepostid, '_room_variations', true ) );

							$loop_lenght = $variations ? count( $variations ) : 1;

							$variation_state_class = $variations ? 'closed' : '';

							for ( $loop = 1; $loop <= $loop_lenght; $loop++ ) : ?>

								<div class="room-variation <?php echo esc_attr( $variation_state_class ); ?>" data-key="<?php echo absint( $loop ); ?>">

									<div class="room-variation-header">

										<?php

										// room rate

										echo '<label><strong>' . esc_html__( 'Room rate:', 'hotelier' ) . '</strong><select class="room-rates" name="_room_variations[' . absint( $loop ) . '][room_rate]">';

										$room_rate_selected = is_array( $variations ) && isset( $variations[ absint( $loop ) ][ 'room_rate' ] ) ? $variations[ absint( $loop ) ][ 'room_rate' ] : false;

										foreach ( $get_room_rates as $room_rate ) {
											echo '<option value="' . esc_attr( $room_rate->slug ) . '" ' . selected( esc_attr( $room_rate_selected ), esc_attr( $room_rate->slug ), false ) . ' >' . esc_html( $room_rate->name ) . '</option>';
										}
										echo '</select></label>';

										?>

										<button type="button" class="remove-variation button"><?php esc_html_e( 'Remove', 'hotelier' ); ?></button>

										<input type="hidden" class="variation-index" name="_room_variations[<?php echo absint( $loop ); ?>][index]" value="<?php echo absint( $loop ); ?>">

									</div><!-- .room-variation-header -->

									<div class="room-variation-content">

										<?php

										// room price

										include( 'views/html-meta-box-room-price-variation.php' );

										?>

										<?php do_action( 'hotelier_room_variation_settings_after_price', absint( $loop ) ); ?>

										<div class="room-deposit">

											<?php
											HTL_Meta_Boxes_Helper::checkbox_input(
												array(
													'id'          => '_room_variations',
													'name'        => '_room_variations[' . absint( $loop ) . '][require_deposit]',
													'depth'       => array( absint( $loop ), 'require_deposit' ),
													'class'       => 'require-deposit',
													'label'       => esc_html__( 'Require deposit?', 'hotelier' ),
													'description' => esc_html__( 'When selected, a deposit is required at the time of booking.', 'hotelier' )
												)
											); ?>

											<div class="room-deposit-amount">

												<?php
												HTL_Meta_Boxes_Helper::select_input(
													array(
														'id'      => '_room_variations',
														'name'    => '_room_variations[' . absint( $loop ) . '][deposit_amount]',
														'depth'   => array( absint( $loop ), 'deposit_amount' ),
														'label'   => esc_html__( 'Deposit amount:', 'hotelier' ),
														'options' => self::get_deposit_options()
													)
												);
												?>

												<?php
												/**
												 * A filter is provided to allow extensions to add their own deposit options
												 */
												do_action( 'hotelier_room_variation_deposit_options' ); ?>

											</div><!-- .room-deposit-amount -->

										</div><!-- .room-deposit -->

										<div class="room-cancellation">
											<?php
											HTL_Meta_Boxes_Helper::checkbox_input(
												array(
													'id'          => '_room_variations',
													'name'        => '_room_variations[' . absint( $loop ) . '][non_cancellable]',
													'depth'       => array( absint( $loop ), 'non_cancellable' ),
													'label'       => esc_html__( 'Non cancellable?', 'hotelier' ),
													'description' => esc_html__( 'When checked, reservations that include this room will be non cancellable and non refundable.', 'hotelier' )
												)
											); ?>
										</div>

										<?php
										// room conditions

										include( 'views/html-meta-box-room-conditions-variation.php' );

										?>

										<?php
										/**
										 * A filter is provided to allow extensions to add their own room variation settings
										 */
										do_action( 'hotelier_room_variation_settings' ); ?>

									</div><!-- .room-variation-content -->

								</div><!-- .room-variation -->

							<?php endfor; ?>

						</div><!-- .room-variations -->

					<?php else : ?>

						<p class="message empty-variations"><?php printf( wp_kses( __( 'Before adding variations, add and save some <a href="%1$s">room rates</a>.', 'hotelier' ), array( 'a' => array( 'href' => array() ) ) ), 'edit-tags.php?taxonomy=room_rate&post_type=room' ); ?></p>

					<?php endif; ?>

					<div class="toolbar">
						<a href="#" class="expand-all"><?php esc_html_e( 'Expand all', 'hotelier' ); ?></a>
						<a href="#" class="close-all"><?php esc_html_e( 'Close all', 'hotelier' ); ?></a>
						<button type="button" class="add-variation button button-primary"><?php esc_html_e( 'Add room rate', 'hotelier' ); ?></button>
					</div>

				</div><!-- .variation-room-panel -->

			</div><!-- .room-advanced-settings -->

			<div id="room-additional-settings" class="room-additional-settings">
				<h4><?php esc_html_e( 'Additional room settings', 'hotelier' ); ?></h4>

				<p class="form-field room-additional-settings-close-button">
					<a href="#hotelier-room-settings" id="close-room-additional-settings" class="button"><?php esc_html_e( 'Back to the room settings', 'hotelier' ); ?></a>
				</p>

				<?php
				/**
				 * A filter is provided to allow extensions to add their own room additional settings
				 */
				do_action( 'hotelier_room_before_additional_settings' );

				// additional details
				HTL_Meta_Boxes_Helper::textarea_input(
					array(
						'id'          => '_room_additional_details',
						'show_id'     => true,
						'label'       => esc_html__( 'Additional details', 'hotelier' ),
						'description' => esc_html__( 'These details are not prominent by default; however, some themes may show them.', 'hotelier' )
					)
				);

				/**
				 * A filter is provided to allow extensions to add their own room additional settings
				 */
				do_action( 'hotelier_room_after_additional_settings' );
				?>
			</div>

		</div><!-- .room-settings -->

		<?php
	}
}

endif;

<?php
/**
 * The template for displaying room content in the single-room.php template
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/content-single-room.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php
	/**
	 * hotelier_before_single_room hook.
	 *
	 * @hooked htl_print_notices - 10
	 */
	do_action( 'hotelier_before_single_room' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
?>

<?php if ( apply_filters( 'hotelier_print_single_room_wrapper', true ) ) : ?>
	<<?php echo esc_attr( apply_filters( 'hotelier_single_room_wrapper_tag', 'div' ) ); ?> id="room-<?php echo absint( get_the_ID() ); ?>" <?php post_class(); ?>>
<?php endif; ?>

	<?php if ( apply_filters( 'hotelier_single_room_print_hooks', true ) ) : ?>

		<?php
			/**
			 * hotelier_single_room_title hook.
			 *
			 * @hooked hotelier_template_single_room_title - 10
			 */
			do_action( 'hotelier_single_room_title' );
		?>

		<?php do_action( 'hotelier_single_room_before_content' ); ?>

		<?php
			/**
			 * hotelier_single_room_images hook.
			 *
			 * @hooked hotelier_template_single_room_image - 10
			 * @hooked hotelier_template_single_room_gallery - 20
			 */
			do_action( 'hotelier_single_room_images' );
		?>

		<div class="entry-content room__content room__content--single">

			<div class="room__details room__details--single">

				<?php
					/**
					 * hotelier_single_room_details hook.
					 *
					 * @hooked hotelier_template_single_room_datepicker - 5
					 * @hooked hotelier_template_single_room_price - 10
					 * @hooked hotelier_template_single_room_non_cancellable_info - 15
					 * @hooked hotelier_template_single_room_deposit - 20
					 * @hooked hotelier_template_single_room_min_max_info - 25
					 * @hooked hotelier_template_single_room_meta - 30
					 * @hooked hotelier_template_single_room_facilities - 40
					 * @hooked hotelier_template_single_room_conditions - 50
					 * @hooked hotelier_template_single_room_sharing - 60
					 */
					do_action( 'hotelier_single_room_details' );
				?>

			</div>

			<div class="room__description room__description--single">

				<?php
					/**
					 * hotelier_single_room_description hook.
					 *
					 * @hooked hotelier_template_single_room_description - 10
					 */
					do_action( 'hotelier_single_room_description' );
				?>

			</div>

			<?php
				/**
				 * hotelier_single_room_rates hook.
				 *
				 * @hooked hotelier_template_single_room_rates - 10
				 */
				do_action( 'hotelier_single_room_rates' );
			?>

			<?php
				/**
				 * hotelier_output_related_rooms hook.
				 *
				 * @hooked hotelier_template_related_rooms - 10
				 */
				do_action( 'hotelier_output_related_rooms' );
			?>

		</div>

		<?php do_action( 'hotelier_single_room_after_content' ); ?>

	<?php else : ?>

		<?php the_content(); ?>

	<?php endif; ?>

<?php if ( apply_filters( 'hotelier_print_single_room_wrapper', true ) ) : ?>
	</<?php echo esc_attr( apply_filters( 'hotelier_single_room_wrapper_tag', 'div' ) ); ?>>
<?php endif; ?>

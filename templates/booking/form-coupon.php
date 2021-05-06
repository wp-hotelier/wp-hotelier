<?php
/**
 * Coupon Form
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/form-coupon.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$has_coupon            = HTL()->cart->get_coupon_id() > 0 && HTL()->cart->get_discount_total() > 0 ? true : false;
$coupon_button_classes = apply_filters( 'hotelier_form_coupon_button_classes', array() );
?>

<tr class="reservation-table__row reservation-table__row--footer reservation-table__row--coupon-form">
	<td colspan="3" class="reservation-table__coupon-form">
		<div class="coupon-form">

			<div class="coupon-form-input-wrapper">
				<input type="text" class="input-text coupon-form__input" name="coupon" id="coupon" placeholder="<?php esc_attr_e( 'Gift or discount code', 'wp-hotelier' ); ?>" value="">
				<button type="button" class="coupon-form__apply button <?php echo esc_attr( implode( ' ', $coupon_button_classes ) ); ?>"><?php esc_html_e( 'Apply', 'wp-hotelier' ); ?></button>
			</div>

			<?php if ( $has_coupon ) : ?>
				<?php
				$coupon = htl_get_coupon( HTL()->cart->get_coupon_id() );
				?>

				<div class="coupon-form__card coupon-card">
					<div class="coupon-card__info">
						<span class="coupon-card__title"><?php echo esc_html( $coupon->get_code() ); ?></span>

						<?php if ( $coupon_descritpion = $coupon->get_description() ) : ?>
							<span class="coupon-card__description"><?php echo esc_html( $coupon_descritpion ); ?></span>
						<?php endif; ?>
					</div>
					<div class="coupon-card__total">
						<strong><?php echo htl_cart_formatted_discount(); ?></strong>
						<button type="button" class="coupon-form__remove button <?php echo esc_attr( implode( ' ', $coupon_button_classes ) ); ?>"><?php esc_html_e( 'Remove', 'wp-hotelier' ); ?></button>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</td>
</tr>

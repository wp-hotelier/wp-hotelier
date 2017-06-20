<?php
/**
 * Email guest details (HTML)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/email-guest-details.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<tr>
	<td style="text-align:left;font-size:16px;line-height:40px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><?php esc_html_e( 'Guest details', 'wp-hotelier' ); ?></td>
</tr>

<?php
foreach ( $fields as $type => $value ) {
	if ( $type == 'guest_telephone' ) {
		if ( isset( $value[ 'label' ] ) && isset( $value[ 'value' ] ) && $value[ 'value' ] ) {
			?>
			<tr>
				<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><strong style="color:#444444;"><?php echo esc_html( $value[ 'label' ] ); ?>:</strong> <a href="tel:<?php echo esc_attr( $value[ 'value' ] ); ?>" value="<?php echo esc_attr( $value[ 'value' ] ); ?>" target="_blank" style="color:#5CC8FF"><?php echo esc_html( $value[ 'value' ] ); ?></a></td>
			</tr>
			<?php
		}
	} elseif ( $type == 'guest_email' ) {
		if ( isset( $value[ 'label' ] ) && isset( $value[ 'value' ] ) && $value[ 'value' ] ) {
			?>
			<tr>
				<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><strong style="color:#444444;"><?php echo esc_html( $value[ 'label' ] ); ?>:</strong> <a href="mailto:<?php echo esc_attr( $value[ 'value' ] ); ?>" target="_blank" style="color:#5CC8FF"><?php echo esc_html( $value[ 'value' ] ); ?></a></td>
			</tr>
			<?php
		}
	} else {
		if ( isset( $value[ 'label' ] ) && isset( $value[ 'value' ] ) && $value[ 'value' ] ) {
			?>
			<tr>
				<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><strong style="color:#444444;"><?php echo esc_html( $value[ 'label' ] ); ?>:</strong> <?php echo esc_html( $value[ 'value' ] ); ?></td>
			</tr>
			<?php
		}
	}
} ?>

<tr>
	<td>&nbsp;</td>
</tr>

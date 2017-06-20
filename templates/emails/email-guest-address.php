<?php
/**
 * Email guest address (HTML)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/plain/email-guest-address.php
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
	<td style="text-align:left;font-size:16px;line-height:40px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><?php esc_html_e( 'Guest address', 'wp-hotelier' ); ?></td>
</tr>
<tr>
	<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><?php echo $reservation->get_formatted_guest_address(); ?></td>
</tr>

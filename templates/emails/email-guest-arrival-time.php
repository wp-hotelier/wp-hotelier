<?php
/**
 * Email guest arrival time (HTML)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/email-guest-arrival-time.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<tr>
	<td style="text-align:left;font-size:16px;line-height:40px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><?php echo esc_html( $label ); ?></td>
</tr>
<tr>
	<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><?php echo esc_html( $guest_arrival_time ); ?></td>
</tr>

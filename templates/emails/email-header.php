<?php
/**
 * Email Header (HTML)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/email-header.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" bgcolor="#F0F0F0" style="margin:0px;padding:0px;border:0px;margin:0;padding:0;background-color:#F0F0F0;font-family:Helvetica,Arial;">
		<tbody>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" height="100%" width="580px" style="width:580px;margin:0px;padding:0px;border:0px;margin:0;padding:0;font-family:Helvetica,Arial;">
						<tbody>
							<tr>
								<td align="left" valign="top">
									<table border="0" cellpadding="30" cellspacing="0" width="100%" height="100%" style="margin:0px;padding:0px;border:0px;margin:0;padding:0;font-family:Helvetica,Arial;">
										<tbody>
											<tr>
												<td bgcolor="#FFFFFF" style="background:#fff;border-radius:6px;font-family:Helvetica,Arial;">
													<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="margin:0px;padding:0px;border:0px;margin:0;padding:0;font-family:Helvetica,Arial;">
														<tbody>
															<?php if ( htl_get_option( 'emails_logo' ) ) : ?>
															<tr>
																<td style="text-align:left;font-size:22px;line-height:32px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><img src="<?php echo esc_url( htl_get_option( 'emails_logo' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></td>
															</tr>
															<tr>
																<td>&nbsp;</td>
															</tr>
															<?php endif; ?>
															<tr>
																<td style="text-align:left;font-size:22px;line-height:32px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><?php echo esc_html( $email_heading ); ?></td>
															</tr>

<?php
/**
 * Email Footer (HTML)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/email-footer.php
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
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td style="border-bottom: solid 1px #e9e9e9; background: #ffffff" bgcolor="ffffff" width="100%">&nbsp;</td>
															</tr>
															<tr>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td style="text-align:left;font-size:13px;line-height:17px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><?php echo esc_html( htl_get_option( 'emails_footer_text' ), 'Powered by WP Hotelier' ); ?></td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</tbody>
	</table>
</body>
</html>

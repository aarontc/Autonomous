<?php

	require ('autonomous.inc.php'); 
	
	// Get file mtime and cache rules
	clearstatcache ();
	$_SESSION["RulesFile"]["path"] = SHOREWALL_RULES_FILE;
	$_SESSION["RulesFile"]["mtime"] = filemtime ( SHOREWALL_RULES_FILE );
	$_SESSION["Rules"] = ShorewallGetRules ( SHOREWALL_RULES_FILE );

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<title>autonomous</title>
		<link rel="STYLESHEET" href="css/style.css" type="text/css" />
	</head>
	<body bgcolor="FFFFFF">
		<form action="test.php" method="post">
			<div align="center">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="3" width="100%">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="100%" colspan="3">
									<span style="font-size:28px;color:85C329">
										Autonomous
									</span>
									</td>
								</tr>
								<tr>
									<td width="50%">
										<span style="font-size:20px;color:CCCCCC">
											Self-Governing Routing
										</span>
									</td>
									<td width="50%" align="right">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td align="right" height="20">
													<span style="color:AAAAAA">
														search for term&nbsp;
													</span>
													<input type="text" onFocus="this.style.background='FFFFFF';" onBlur="this.style.background='F9F9F9';">&nbsp;
													<input type="submit" style="padding:0;background-color:85C329;border:0;color:FFFFFF;width:25;font-weight:bold" value="GO">
												</td>
											</tr>
										</table>
									</td>
									<td width="20"></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15" bgcolor="FFFFFF"></td>
					</tr>
					<tr>
						<td colspan="3" height="1" class="hsep"></td>
					</tr>
					<tr>
						<td colspan="3" height="10" bgcolor="FFFFFF"></td>
					</tr>
					<tr>
						<td width="170" bgcolor="FFFFFF" valign="top">
							<span style="font-size:6px"><br /></span>
							<div align="center">
								<table width="140" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><a href="rules.php">Port Forwarding</a></td>
									</tr>
									<tr>
										<td>Forward ports to LAN machines</td>
									</tr>
									<tr>
										<td height="5" bgcolor="FFFFFF"></td>
									</tr>
									<!--<tr>
										<td><a href="#">company overview</a></td>
									</tr>
									<tr>
										<td>about our company</td>
									</tr>
									<tr>
										<td height="5" bgcolor="FFFFFF"></td>
									</tr>
									<tr>
										<td><a href="#">services &amp; products</a></td>
									</tr>
									<tr>
										<td>what we provide</td>
									</tr>
									<tr>
										<td height="5" bgcolor="FFFFFF"></td>
									</tr>
									<tr>
										<td><a href="#">partnerships</a></td>
									</tr>
									<tr>
										<td>look at all our partners</td>
									</tr>
									<tr>
										<td height="5" bgcolor="FFFFFF"></td>
									</tr>
									<tr>
										<td><a href="#">online portfolio</a></td>
									</tr>
									<tr>
										<td>see all of our clients</td>
									</tr>
									<tr>
										<td height="5" bgcolor="FFFFFF"></td>
									</tr>
									<tr>
										<td><a href="#">registration</a></td>
									</tr>
									<tr>
										<td>how you can register</td>
									</tr>
									<tr>
										<td height="5" bgcolor="FFFFFF"></td>
									</tr>
									<tr>
										<td><a href="#">contacting us</a></td>
									</tr>
									<tr>
										<td>how you can reach us</td>
									</tr>-->
								</table>
								<!--<br /><span style="font-size:6px"><br /></span>
								<table width="140" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="3" height="1" bgcolor="AAAAAA"></td>
									</tr>
									<tr>
										<td width="1" bgcolor="AAAAAA" rowspan="5"></td>
										<td width="138" height="26" bgcolor="F9F9F9" style="color:85C329;font-size:14px">
											&nbsp;&nbsp;announcements
										</td>
										<td width="1" bgcolor="AAAAAA" rowspan="5"></td>
									</tr>
									<tr>
										<td height="1" bgcolor="AAAAAA"></td>
									</tr>
									<tr>
										<td bgcolor="FFFFFF">
											<table width="138" border="0" cellpadding="7" cellspacing="0">
												<tr>
													<td style="color:AAAAAA">
														4/30 - idea spawned
														<br /><span style="font-size:6px"><br /></span>
														5/1 - design created
														<br /><span style="font-size:6px"><br /></span>
														5/1 - design submitted to OSWD for approval
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td height="1" bgcolor="AAAAAA"></td>
									</tr>
								</table>
								<span style="font-size:6px"><br /></span>-->
							</div>
						</td>
						<td width="1" class='vsep'></td>
						<td width="83%" valign="top">
							<span style="font-size:6px"><br /></span>
							<div align="center">
								<?php
									foreach ( $_SESSION["Rules"] as $rule ) {
								?>
								<table cellpadding='0' cellspacing='0' width='90%'>
									<tr>
										<td rowspan='2' colspan='2' class='rulehead'>
											<table cellpadding='0' cellspacing='0' width='100%'>
												<tr>
													<td width='95%'>
														<span style='padding-left: 3px;'>
															<input type='text' class='desc' value='<?= htmlentities ( $rule->comment ) ?>' name="Description[<?= $rule->id ?>]" />
														</span>
													</td>
													<td align='right'>
														<span style='padding-right: 1px; padding-top: 2px;'>
															<input name="Action[<?= $rule->id ?>]" value="Delete" type='image' src='images/delete-15x15.png' class='delimg' title='Remove' alt='Remove' />
														</span>
													</td>
												</tr>
											</table>
										</td>
										<td></td>
									</tr>
									<tr>
										<td class='shadow' width='5'></td>
									</tr>
									<tr>
										<td colspan='2' class='rulebody'>
											<table width="80%" border="0" cellpadding="17" cellspacing="0">
												<tr>
													<td style="padding:12px">
														<div align="justify">
															<table cellpadding='3' cellspacing='0' border='0'>
																<tr>
																	<th align='right'>LAN Computer IP</th>
																	<td><input type='text' name="LAN IP[<?= $rule->id ?>]" value="<?= htmlentities ( $rule->destination[1] ) ?>" size='15' /></td>
																	<td width='20' rowspan='2'></td><!-- separator -->
																	<th align='right'>Protocol</th>
																	<td>
																		<select name="Protocol[<?= $rule->id ?>]">
																		<option value="tcp" <?php echo ( $rule->protocol == "tcp" ? "selected" : "" ); ?>>TCP</option>
																		<option value="udp" <?php echo ( $rule->protocol == "udp" ? "selected" : "" ); ?>>UDP</option>
																		</select>
																	</td>
																</tr>
																<tr>
																	<th align='right'>LAN Port</th>
																	<td colspan='1'>
																		<input type='text' name="LAN Port Start[<?= $rule->id ?>]" value="<?= htmlentities ( $rule->destination[2] ) ?>" size='5' />
																		<input type='text' name="LAN Port End[<?= $rule->id ?>]" value="<?= htmlentities ( $rule->destination[3] ) ?>" size='5' />
																	</td>
																	<th align='right'>Internet Port</th>
																	<td colspan='1' align='left'>
																		<input type='text' name="WAN Port Start[<?= $rule->id ?>]" value="<?= htmlentities ( $rule->destination_ports[0] ) ?>" size='5' />
																		<input type='text' name="WAN Port End[<?= $rule->id ?>]" value="<?= htmlentities ( $rule->destination_ports[1] ) ?>" size='5' />
																	</td>
																</tr>
															</table>
														</div>
													</td>
												</tr>
											</table>
										</td>
										<td class='shadow'></td>
									</tr>
									<tr>
										<td width='4' height='5'></td>
										<td class='shadow' width='99%'></td>
										<td class='shadow'></td>
									<tr>
									</tr>
								</table>
								<br />
								<?php
								}
								?>
								<span style="font-size:6px"><br /></span>
								<input type='submit' value='Update' />&nbsp;&nbsp;<input type='reset' name='reset' value='Retry' />
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10" bgcolor="FFFFFF"></td>
					</tr>
					<tr>
						<td colspan="3" height="1" class="hsep"></td>
					</tr>
					<tr>
						<td colspan="3" height="5" bgcolor="FFFFFF"></td>
					</tr>
					<tr>
						<td colspan="3" bgcolor="FFFFFF" align="right">
							Copyright &#0169; 2008, <a href="http://www.principleofdesign.com" style="font-size:12px;color:AAAAAA">Scott Deutsch, Ben Mann, Aaron Ten Clay</a>. All Rights Reserved.
						</td>
					</tr>
				</table>
			</div>
		</form>
	</body>
</html>
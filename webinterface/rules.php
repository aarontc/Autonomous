<?php require ('autonomous.inc.php');

if(IsDBEmpty())
{
	header('Location: ius.php');
	exit;
}

if(!IsGoodSession())
{
	header('Location: login.php');
	exit;
}

if(IsRulesTableEmpty())
{
	//add initial hashes
	foreach($_SESSION['Rules'] as $rule)
	{
		AddRuleToDB($rule->checkSum);
		AttachOwnerToRule("-1",$rule->checkSum);
	}
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous</title>
		<meta content="">
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
		<?php
		if ( $_SERVER['REQUEST_METHOD'] == "POST" )
			UpdateRules();

		$owned_rules = GetOwnedRulesFromUser($_SESSION['Login']['User']);
		?>
		<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div><a href='index.php' class='green headerlg'>Autonomous</a></div>
				</div>
				<div id='hright'>
					<div class='login area'>
						<p class='area'>
							<span class='um'>
								Welcome <a href='cp.php' class='loggedinuser'><?= $_SESSION['Login']['User']/*." ".$_SESSION['Login']['Email']*/ ?></a>
								<span class='roundbutton'>
									<span class='tl'></span>
									<span class='tr'></span>
									<span class='bl'></span>
									<span class='br'></span>
									<a href='login.php?action=logout' class='button'>logout</a>
								</span>
							</span>
						</p>
						<p class='area'>
							<span class='um'>
								<?php if(IsUserAdmin($_SESSION['Login']['User'])) { ?>
								<span class='roundbutton'>
									<span class='tl'></span>
									<span class='tr'></span>
									<span class='bl'></span>
									<span class='br'></span>
									<a href='um.php' class='button' >Manage Users</a>	
								</span>
								<?php } 
								
								if(isset($_SESSION['Login']['Email']) && $_SESSION['Login']['Email'] != null)
								{
								?>
								<span class='roundbutton'>
									<span class='tl'></span>
									<span class='tr'></span>
									<span class='bl'></span>
									<span class='br'></span>
									<a href='er.php' class='button' >Email rules</a>	
								</span>
								<?php
								}
								?>
							</span>
						</p>
					</div>
				</div>
			</div>
			<div class='area'>
				<div id='hleft'>
					<div class='ltgrey headermed'>Self-Governing Routing</div>
				</div>
				<div id='hright'>
					<div class='dkgrey nodisplay'>
						Search for Term:
						<span class='roundinput'>
							<span class='tl'></span>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<input type='text' />
						</span>
						<span class='roundbutton'>
							<span class='tl'></span>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<input type='submit' value='GO' />
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class='divider'><!-- comment for IE --></div>
		<div id='main' class='area'>
			<div id='sidebar'>
				<div class='menuitem'>
					<div class='mititle'><a href='rules.php'>Port Forwarding</a></div>
					<div class='midesc'>Forward ports to LAN machines.</div>
					<br />
					<div class='mititle'><a href='dhcpleases.php'>DHCP Leases</a></div>
					<div class='midesc'>Shows computer name, mac address, ip address, etc.</div>
				</div>
			</div>
			<div id='content'>
				<div id='rules' class='area'>
					<form method="POST">
						<div class='center'>
							<span class='roundbutton'>
								<span class='tl'></span>
								<span class='tr'></span>
								<span class='bl'></span>
								<span class='br'></span>
								<input type="submit" name="submit" value="Update" title='Update all rules' />
							</span>
						</div>
						<?php
							foreach ( $_SESSION['Rules'] as $ruleid => $rule ) {
								if ( $rule->action != "DNAT" )
									continue; 
								//NEW							
								if(!IsHashInGivenRuleIDs($owned_rules,$rule->checkSum))
									continue;
								//
						?>
						<!-- rule <?= $ruleid ?> starts here -->
						<div class='ruleshadow'>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<div class='rule'>
								<span class='ruletl'></span>
								<span class='ruletr'></span>
								<span class='rulebl'></span>
								<span class='rulebr'></span>
								<div class='ruletitle'>
									<input type='text' value='<?= htmlentities ( $rule->GetComment() ) ?>' name='comment[<?= $ruleid ?>]' id='comment[<?= $ruleid ?>]' title='Rule description goes here.' />
									<input type='image' title='Delete this rule' alt='Delete' value='Delete' src='images/delete-15x15.png' name='delete[<?= $ruleid ?>]' id='delete[<?= $ruleid ?>]' />
								</div>
								<div class='rulebody'>
									<div class='area'>
										<p>
											<label for='destination_ip[<?= $ruleid ?>]'>LAN Computer IP:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='ip' value='<?= $rule->destination[1] ?>' name='destination_ip[<?= $ruleid ?>]' id='destination_ip[<?= $ruleid ?>]' title='*REQUIRED* LAN Computers IP Address.' />
											</span>
										</p>
										<p>
											<label for='protocol[<?= $ruleid ?>]'>Protocol:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<select name='protocol[<?= $ruleid ?>]' id='protocol[<?= $ruleid ?>]' title='Select the protocol.'>
													<option value='tcp'<?= ($rule->protocol=="tcp" ? " selected" : "") ?>>TCP</option>
													<option value='udp'<?= ($rule->protocol=="udp" ? " selected" : "") ?>>UDP</option>
												</select>
											</span>
										</p>
									</div>
									<div class='area'>
										<p>
											<label for='destination_port_start[<?= $ruleid ?>]'>LAN Port:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='<?= $rule->destination[2] ?>' name='destination_port_start[<?= $ruleid ?>]' id='destination_port_start[<?= $ruleid ?>]' title='*OPTIONAL* LAN Computers Destination Port. If there is a range, this is the starting port.' />
											</span>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='<?= $rule->destination[3] ?>' name='destination_port_end[<?= $ruleid ?>]' id='destination_port_end[<?= $ruleid ?>]' title='*OPTIONAL* If there is a range of ports for the LAN Computers Destination Port, this is the ending port.' />
											</span>
										</p>
										<p>
											<label for='wan_port_start[<?= $ruleid ?>]'>Internet Port:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='<?= $rule->destination_ports[0] ?>' name='wan_port_start[<?= $ruleid ?>]' id='wan_port_start[<?= $ruleid ?>]' title='*REQUIRED* Internet port. If there is a range, this is the starting port.' />
											</span>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='<?= $rule->destination_ports[1] ?>' name='wan_port_end[<?= $ruleid ?>]' id='wan_port_end[<?= $ruleid ?>]' title='*OPTIONAL* If there is a range for Internet ports, then this is the end port.' />
											</span>
										</p>
									</div>
								</div>
							</div>
						</div>
						<!-- rule ends here -->
						<?php
							}
						?>
						<!-- rule NEWRULEFORTEHLOVEOFALLTHATISPACKETY starts here -->
						<div class='ruleshadow'>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<div class='rule'>
								<span class='ruletl'></span>
								<span class='ruletr'></span>
								<span class='rulebl'></span>
								<span class='rulebr'></span>
								<div class='ruletitle'>
									<input type='text' value='New Rule Description' name='comment[new]' id='comment[new]' title='Rule description goes here.' />
								</div>
								<div class='rulebody'>
									<div class='area'>
										<p>
											<label for='destination_ip[new]'>LAN Computer IP:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='ip' value='' name='destination_ip[new]' id='destination_ip[new]' title='*REQUIRED* LAN Computers IP Address.' />
											</span>
										</p>
										<p>
											<label for='protocol[new]'>Protocol:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<select name='protocol[new]' id='protocol[new]' title='Select the protocol.'>
													<option value='tcp' selected>TCP</option>
													<option value='udp'>UDP</option>
												</select>
											</span>
										</p>
									</div>
									<div class='area'>
										<p>
											<label for='destination_port_start[new]'>LAN Port:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='' name='destination_port_start[new]' id='destination_port_start[new]' title='*OPTIONAL* LAN Computers Destination Port. If there is a range, this is the starting port.' />
											</span>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='' name='destination_port_end[new]' id='destination_port_end[new]' title='*OPTIONAL* If there is a range of ports for the LAN Computers Destination Port, this is the ending port.' />
											</span>
										</p>
										<p>
											<label for='wan_port_start[new]'>Internet Port:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='' name='wan_port_start[new]' id='wan_port_start[new]' title='*REQUIRED* Internet port. If there is a range, this is the starting port.' />
											</span>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='text' class='port' value='' name='wan_port_end[new]' id='wan_port_end[new]' title='*OPTIONAL* If there is a range for Internet ports, then this is the end port.' />
											</span>
										</p>
									</div>
								</div>
							</div>
						</div>
						<!-- rule ends here -->
						<div class='rulespacer'></div>
						<div class='center'>
							<span class='roundbutton'>
								<span class='tl'></span>
								<span class='tr'></span>
								<span class='bl'></span>
								<span class='br'></span>
								<input type="submit" name="submit" value="Update" title='Update all rules' />
							</span>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
		</div>
	</body>
</html>
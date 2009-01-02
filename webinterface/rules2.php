<?php require ('autonomous.inc.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous</title>
		<meta content="">
		<link href='css/style2.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
	<?php
	if ( $_SERVER['REQUEST_METHOD'] == "POST" )
		UpdateRules();
	?>
	<div id='header' class='area'>
		<div id='hleft'>
			<div class='green headerlg'>Autonomous</div>
			<div class='ltgrey headermed'>Self-Governing Routing</div>
		</div>
		<div id='hright'>
			<div class='bgcolor headerlg'>&nbsp;</div>
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
	<div class='divider'><!-- comment for IE --></div>
	<div id='main' class='area'>
		<div id='sidebar'>
			<div class='menuitem'>
				<div class='mititle'><a href='#'>Port Forwarding</a></div>
				<div class='midesc'>Forward ports to LAN machines.</div>
			</div>
		</div>
		<div id='content'>
			<div id='rules' class='area'>
				<form method="POST">
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="submit" name="submit" value="SUBMIT IT BIZOTCH" title='Update all rules' />
					</span>
					<?php
						foreach ( $_SESSION['Rules'] as $ruleid => $rule ) {
							if ( $rule->action != "DNAT" )
								continue; 
					?>
					<div class='rulespacer'></div>
					<!-- rule <?= $ruleid ?> starts here -->
					<div class='rulecontainer'>
						<div class='ruleshadow'>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
						</div>
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
					<div class='rulespacer'></div>
					<?php
						}
					?>
					<div class='rulespacer'></div>
					<!-- rule NEWRULEFORTEHLOVEOFALLTHATISPACKETY starts here -->
					<div class='rulecontainer'>
						<div class='ruleshadow'>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
						</div>
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
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="submit" name="submit" value="Submit" title='Update all rules' />
					</span>
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
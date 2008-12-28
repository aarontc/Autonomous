<?php
	require ('autonomous.inc.php'); 
	if ( $_SERVER['REQUEST_METHOD'] == "POST" )
		UpdateRules();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous</title>
		<meta content="">
		<link href='style2.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
	<div id='header' class='area'>
		<div id='hleft'>
			<div class='green headerlg'>Autonomous</div>
			<div class='ltgrey headermed'>Self-Governing Routing</div>
		</div>
		<div id='hright'>
			<div class='bgcolor headerlg'>&nbsp;</div>
			<div class='dkgrey'>Search for Term:<input type='text' class='text' /><input type='submit' class='button' value='GO' /></div>
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
				<?php
					foreach ( $_SESSION['Rules'] as $ruleid => $rule ) {
						if ( $rule->action != "DNAT" )
							continue; 
				?>
				<div class='rulespacer'></div>
				<!-- rule <?= $ruleid ?> starts here -->
				<div class='ruleshadow'>
				<div class='rule'>
					<div class='ruletitle'>
						<input type='text' class='text' value='Rule <?= $ruleid ?>: <?= htmlentities ( $rule->GetComment() ) ?>' />
						<input type='image' class='image' title='Delete' alt='Delete' value='Delete' src='delete-15x15.png' name='delete[<?= $ruleid ?>]' id='delete[<?= $ruleid ?>]' />
					</div>
					<div class='rulebody'>
						<div class='area'>
							<p>
								<label for='destination_ip[<?= $ruleid ?>]'>LAN Computer IP</label>
								<input type='text' class='text' style='width:120px;' value='<?= $rule->destination[1] ?>' name='destination_ip[<?= $ruleid ?>]' id='destination_ip[<?= $ruleid ?>]' />
							</p>
							<p>
								<label for='protocol[<?= $ruleid ?>]'>Protocol</label>
								<select name='protocol[<?= $ruleid ?>]' id='protocol[<?= $ruleid ?>]'>
									<option value='tcp'<?= ($rule->protocol=="tcp" ? " selected" : "") ?>>TCP</option>
									<option value='udp'<?= ($rule->protocol=="udp" ? " selected" : "") ?>>UDP</option>
								</select>
							</p>
						</div>
						<div class='area'>
							<p>
								<label for='destination_port_start[<?= $ruleid ?>]'>LAN Port</label>
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination[2] ?>' name='destination_port_start[<?= $ruleid ?>]' id='destination_port_start[<?= $ruleid ?>]' />
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination[3] ?>' name='destination_port_end[<?= $ruleid ?>]' id='destination_port_end[<?= $ruleid ?>]' />
							</p>
							<p>
								<label for='wan_port_start[<?= $ruleid ?>]'>Internet Port</label>
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination_ports[0] ?>' name='wan_port_start[<?= $ruleid ?>]' id='wan_port_start[<?= $ruleid ?>]' />
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination_ports[1] ?>' name='wan_port_end[<?= $ruleid ?>]' id='wan_port_end[<?= $ruleid ?>]' />
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
				</form>
			</div>
		</div>
	</div>
	<div class='divider'><!-- comment for IE --></div>
	<div id='footer' class='dkgrey'>
		Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
	</div>
	</body>
</html>
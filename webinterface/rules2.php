<?php
	require ('autonomous.inc.php'); 
	
	// Get file mtime and cache rules
	clearstatcache ();
	$_SESSION["RulesFile"]["path"] = SHOREWALL_RULES_FILE;
	$_SESSION["RulesFile"]["mtime"] = filemtime ( SHOREWALL_RULES_FILE );
	$_SESSION["Rules"] = ShorewallGetRules ( SHOREWALL_RULES_FILE );

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
				<?php
					foreach ( $_SESSION['Rules'] as $rule ) {
				?>
				<!-- rule <?= $rule->id ?> starts here -->
				<div class='ruleshadow'>
				<div class='rule'>
					<div class='ruletitle'>
						<input type='text' class='text' value='Rule <?= $rule->id ?>: <?= htmlentities ( $rule->comment ) ?>' />
						<input type='image' class='image' title='Delete' alt='Delete' value='Delete' src='delete-15x15.png' />
					</div>
					<div class='rulebody'>
						<div class='area'>
							<p>
								<label>LAN Computer IP</label>
								<input type='text' class='text' style='width:120px;' value='<?= $rule->destination[1] ?>' />
							</p>
							<p>
								<label>Protocol</label>
								<select>
									<option value='tcp'<?= ($rule->protocol=="tcp" ? " selected" : "") ?>>TCP</option>
									<option value='udp'<?= ($rule->protocol=="udp" ? " selected" : "") ?>>UDP</option>
								</select>
							</p>
						</div>
						<div class='area'>
							<p>
								<label>LAN Port</label>
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination[2] ?>' />
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination[3] ?>' />
							</p>
							<p>
								<label>Internet Port</label>
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination_ports[0] ?>' />
								<input type='text' class='text' style='width:40px;' value='<?= $rule->destination_ports[1] ?>' />
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
			</div>
		</div>
	</div>
	<div class='divider'><!-- comment for IE --></div>
	<div id='footer' class='dkgrey'>
		Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
	</div>
	</body>
</html>
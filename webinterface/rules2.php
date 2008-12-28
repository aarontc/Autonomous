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
	<div id='divider'><!-- comment for IE --></div>
	<div id='main' class='area'>
		<div id='sidebar'>
			<div id='menuitem'>
				<div id='mititle'><a href='#'>Port Forwarding</a></div>
				<div id='midesc'>Forward ports to LAN machines.</div>
			</div>
		</div>
		<div id='content'>
			<div id='rules' class='area'>
				<!-- rule starts here -->
				<div id='rule'>
					<div id='ruletitle'>
						<input type='text' class='text' />
						<input type='image' class='image' title='Delete' alt='Delete' value='Delete' src='delete-15x15.png' />
					</div>
					<div id='rulebody'>
						<div class='area'>
							<p><label>LAN Computer IP</label><input type='text' class='text' style='width:120px;' /></p>
							<p><label>Protocal</label><select><option value='tcp'>TCP</option><option value='udp'>UDP</option></select></p>
						</div>
						<div class='area'>
							<p><label>LAN Port</label><input type='text' class='text' style='width:40px;' /><input type='text' class='text' style='width:40px;' /></p>
							<p><label>Internet Port</label><input type='text' class='text' style='width:40px;' /><input type='text' class='text' style='width:40px;' /></p>
						</div>
					</div>
				</div>
				<!-- rule ends here -->
				<div id='rulespacer'></div>
				<!-- rule starts here -->
				<div id='rule'>
					<div id='ruletitle'>
						<input type='text' class='text' />
						<input type='image' class='image' title='Delete' alt='Delete' value='Delete' src='delete-15x15.png' />
					</div>
					<div id='rulebody'>
						<div class='area'>
							<p><label>LAN Computer IP</label><input type='text' class='text' style='width:120px;' /></p>
							<p><label>Protocal</label><select><option value='tcp'>TCP</option><option value='udp'>UDP</option></select></p>
						</div>
						<div class='area'>
							<p><label>LAN Port</label><input type='text' class='text' style='width:40px;' /><input type='text' class='text' style='width:40px;' /></p>
							<p><label>Internet Port</label><input type='text' class='text' style='width:40px;' /><input type='text' class='text' style='width:40px;' /></p>
						</div>
					</div>
				</div>
				<!-- rule ends here -->
			</div>
		</div>
	</div>
	<div id='divider'><!-- comment for IE --></div>
	<div id='footer' class='dkgrey'>
		Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
	</div>
	</body>
</html>
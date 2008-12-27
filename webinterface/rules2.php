<html>
	<head>
		<title>Autonomous</title>
		<meta content="">
		<link href='style2.css' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
	<div id='header' class='area'>
		<div id='hleft'>
			<div class='green headerlg'>Autonomous</div>
			<div class='ltgrey headermed'>Self-Governing Routing</div>
		</div>
		<div id='hright'>
			<div class='bgcolor headerlg'>&nbsp;</div>
			<div class='dkgrey'>Search for Term:<input type='text' /><input type='submit' value='GO' /></div>
		</div>
	</div>
	<div id='spacer'></div>
	<div id='main' class='area'>
		<div id='sidebar'>
			<div id='menuitem'>
				<div id='mititle'><a href='#'>Port Forwarding</a></div>
				<div id='midesc'>Forward ports to LAN machines.</div>
			</div>
		</div>
		<div id='content'>
			<div id='rules'>
				<!-- rule starts here -->
				<div id='rule'> 
					<div id='ruletitle' class='area'>
						<input type='text' />
						<input type='image' title='Delete' alt='Delete' value='Delete' src='delete-15x15.png' />
					</div>
					<div id='rulebody'>
						<div class='area'>
							<p><label>LAN Computer IP</label><input type='text' style='width:120px;' /></p>
							<p><label>Protocal</label><select><option value='tcp'>TCP</option><option value='udp'>UDP</option></select></p>
						</div>
						<div class='area'>
							<p><label>LAN Port</label><input type='text' style='width:40px;' /><input type='text' style='width:40px;' /></p>
							<p><label>Internet Port</label><input type='text' style='width:40px;' /><input type='text' style='width:40px;' /></p>
						</div>
					</div>
				</div>
				<!-- rule ends here -->
			</div>
		</div>
	</div>
	<div id='spacer'></div>
	<div id='footer' class='dkgrey'>
		Copyright &copy; 2008, <a href='http://www.principleofdesign.com' class='dkgrey'>Scott Deutsch, Ben Mann, Aaron Ten Clay</a>. All Rights Reserved.
	</div>
	</body>
</html>
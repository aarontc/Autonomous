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
			
			<?php
			  if(!ReadLeaseFile())
			  {
				echo "File not found!";
			  }
			?>
			
			</div>
		</div>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
		</div>
	</body>
</html>
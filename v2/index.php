<?php include("include/rules.php"); ?>
<?php if(strcasecmp($_SERVER["REQUEST_METHOD"], "POST") == 0) putRules(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous</title>
		<meta content="">
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<!--<script language='javascript' type='text/javascript' src='js/js.js'></script>-->
	</head>
	<body><!-- onload='init()'>-->
		<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div><a href='index.php' class='green headerlg'>Autonomous</a></div>
				</div>
				<div id='hright'>
					<div class='login'>
						<p>
							Welcome <a href='cp.php' class='loggedinuser'>testuser</a>
							<a href='login.php?action=logout' class='button'>logout</a>
						</p>
						<p>
							<a href='um.php' class='button'>Manage Users</a>
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
						<input type='text' />
						<input type='submit' value='GO' />
					</div>
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
					<div id='messagearea'><?php while(count($messages) > 0) echo array_pop($messages),"<br />\n"; ?></div>
					<form method="POST">
						<div class='center'>
							<input type="submit" name="submit" value="Submit" title='Update all rules' />
						</div>
						<div class='rulespacer'></div>
						<!-- small rule starts here -->
<?php /*echo getSmallHtmlRule(7);*/ ?>
						<!-- small rule ends here -->
						<!-- small rule starts here -->
<?php $rl = count($rules); for($i = 0; $i < $rl; $i++) { echo getSmallHtmlRule($i); } ?>
						<!-- small rule ends here -->
						<!-- rule starts here -->
<?php /*$rl = count($rules); for($i = 0; $i < $rl; $i++) { echo getHtmlRule($i); }*/ ?>
						<!-- rule ends here -->
						<!-- rule NEWRULEFORTEHLOVEOFALLTHATISPACKETY starts here -->
<?php echo getSmallHtmlRule(); ?>
						<!-- rule ends here -->
						<div class='rulespacer'></div>
						<div class='center'>
							<input type="submit" name="submit" value="Submit" title='Update all rules' />
						</div>
					</form>
					<pre><div id='debug' style='display:none'></div></pre>
				</div>
			</div>
		</div>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann. All Rights Reserved.
		</div>
	</body>
</html>
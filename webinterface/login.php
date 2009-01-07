<?php
include("utility.php");

session_start();

if(IsDBEmpty())
{
	header("Location: ius.php");
	exit;
}
else
{
	//logout if they are trying to logout
	if(strcmp($_GET['action'],"logout")==0 && isset($_SESSION['Login']['User']))
	{
		unset($_SESSION['Login']['User']);
		unset($_SESSION['Login']['Pass']);
		unset($_SESSION['Login']['Email']);
	}

	if(!IsGoodSession())
	{

		if(isset($_POST['pass']) && isset($_POST['user']))
		{

			//encrypt password
			$pass = hash('sha512',$_POST['pass']);
	
			$login = GoodUserPass($_POST['user'],$pass);
	
			if($login)
			{
			
				//add Session data
				$_SESSION['Login']['User'] = $_POST['user'];
				$_SESSION['Login']['Pass'] = $pass;
				$_SESSION['Login']['Email'] = GetEmail($_POST['user']);
				
				//change where it goes
				header("Location: rules.php");
			}
			else
			{
				$msg['error'] = "Incorrect login/pass";
			}
		}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous Router Login</title>
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
		<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div class='green headerlg'>Autonomous</div>
				</div>
				<div id='hright'>&nbsp;</div>
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
		<form action="login.php" method="POST">
			<div id="loginform">
				<p class='loginformhead'>Please Log In</p>
				<div class='loginformdiv'><!-- comment for IE --></div>
				<p class='area'>
					<label for="user">User Name:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="text" name="user" id="user" value='<?= $_POST['user'] ?>' />
					</span>
				</p>
				<p class='area'>
					<label for="pass">Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="password" name="pass" id="pass" value='<?= $_POST['pass'] ?>' />
					</span>
					<span class='error'><?= $msg['error'] ?></span>
				</p>
				<p>
					<a href="forgot.php">Forgot user name or password?</a>
				</p>
				<div class='loginformspacer'></div>
				<p>
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="submit" value="submit" />
					</span>
				</p>
			</div>
		</form>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
		</div>
	</body>
</html>
<?php
	}
	else
	{
		//echo "session"."<br />";
		//change where it goes
		header("Location: rules.php");
	}
}
?>
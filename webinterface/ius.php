<?php include('utility.php');

//ob_start();
session_start();


if(!IsDBEmpty())
{
	header('Location: login.php');
	exit;
}
else
{
	$counter = 0;
	$stop = false;

	if(isset($_POST['nuser']))
	{
		if (!validate_variable("user",$_POST['nuser'],$validation_struct))
		{
			$error['user'] = 'INVALID USER NAME'; 
		}
		else
		{
			$counter++;
			if(!CheckUserString($_POST['nuser']))
			{
				$error['user'] = 'INVALID USER NAME- Bad Character'; 
				$counter--;
			}
			else if(DoesUserExist($_POST['nuser']))
			{
				$error['user'] = 'USER NAME Already Exists'; 
				$counter--;
			}
		}
	}

	if(isset($_POST['npass']))
	{
		if (!validate_variable("password",$_POST['npass'],$validation_struct)) 
		{
			$error['pass'] = 'INVALID password'; 
		}
		else
		{
			$counter++;
		}
	}

	if(isset($_POST['cp']))
	{
		if (!validate_variable("password",$_POST['cp'],$validation_struct))
		{
			$error['cpass'] = 'INVALID password'; 
		}
		else
		{
			$counter++;
		}
	}

	if($counter == 3)
	{
		if(strcmp($_POST['cp'],$_POST['npass'])!=0)
		{
			$error['mismatch'] = 'PASSWORD MISMATCH'; 
		}
		else
		{
			$privliages['port forwarding'] = true;
			$privliages['user managment'] = true;

			//create the database
			CreateUser($_POST['nuser'],$_POST['npass'], $privliages);

			//add Session data
			$_SESSION['Login']['User'] = $_POST['nuser'];
			$_SESSION['Login']['Pass'] = hash('sha512',$_POST['npass']);
			
			

			header('LOCATION: login.php');
			$stop = true;
		}
	}
	
	if(!$stop)
	{

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous - Initial User Setup</title>
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
		<form action="ius.php" method="POST">
			<div id="loginform">
				<p class='loginformhead'>Initial User Setup For Autonomous Router </p>
				<div class='loginformdiv'><!-- comment for IE --></div>
				<p class='area'>
					<label for='nuser'>New User Name:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input maxlength="100" name="nuser" id="nuser" type="text" value='<?= $_POST['nuser'] ?>' />
					</span>
					<span class='error'><?= $error['user'] ?></span>
				</p>
				<p class='area'>
					<label for='npass'>New Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input maxlength="100" name="npass" id="npass" type="password" value='<?= $_POST['npass'] ?>' />
					</span>
					<span class='error'><?= $error['pass'] ?></span>
				</p>
				<p class='area'>
					<label for='cp'>Confirm Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input maxlength="100" name="cp" id="cp" type="password" value='<?= $_POST['cp'] ?>' />
					</span>
					<span class='error'>
						<?= $error['cpass'] ?>
						<?= $error['mismatch'] ?>
					</span>
				</p>
				<div class='loginformspacer'></div>
				<p>
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="submit" value="Create User" />
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
 }
?>
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
			$error['user'] = 'User Name---2 chars min/100 chars max'; 
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
			$error['pass'] = 'Password minimum length is 5 characters'; 
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
			$error['cpass'] = 'Password minimum length is 5 characters'; 
		}
		else
		{
			$counter++;
		}
	}

	if(isset($_POST['em']) && $_POST['em'] != null)
	{
		if (!IsValidEmail($_POST['em']))
		{
			$counter--;
			$error['email'] = 'Invalid Email Address'; 
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
			$privileges['port forwarding'] = true;
			$privileges['user management'] = true;
			$privileges['users data only'] = true;

			$email = null;

			if(isset($_POST['em']))
				$email = $_POST['em'];

			//create the database
			CreateUser($_POST['nuser'],$_POST['npass'], $privileges, $email);

			//add Session data
			$_SESSION['Login']['User'] = $_POST['nuser'];
			$_SESSION['Login']['Pass'] = hash('sha512',$_POST['npass']);

			$_SESSION['Login']['Email'] = $email;
			
			

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
				<p class='loginformhead'>Initial User Setup for Autonomous Router </p>
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
				<p class='area'>
					<label for='em'>Email (optional):</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input maxlength="100" name="em" id="em" type="text" value='<?= $_POST['em'] ?>' />
					</span>
					<span class='error'>
						<?= $error['email'] ?>
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
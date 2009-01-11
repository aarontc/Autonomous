<?php include('utility.php');

session_start();

if(IsDBEmpty())
{
	header('Location: ius.php');
	exit;
}

if(IsGoodSession())
{
	header('Location: rules.php');
	exit;
}

//echo "<pre>";
//print_r($_POST);
//print_r($_SESSION);
//echo "</pre>";

if(!isset($_POST['done']))
{
	if(!isset($_SESSION['verify']))
	{	
		if(isset($_GET['hash']) && strlen($_GET['hash'])==128)
		{
			//is there even a forgot ticket that is alive in the database
			$tickets = GetAliveForgets();
	
			if($tickets != null)
			{
				foreach($tickets as $ticket)
				{
					$info = GetInfoFromUID($ticket['UID']);
					$tempString = $ticket['Created'].$ticket['Stamp'].$ticket['UID'].$info['Password'];
					$hash = hash('sha512',$tempString);
					if(strcmp($hash,$_GET['hash'])==0)
					{						
						$_SESSION['verify'] = true;
						$_SESSION['UID'] = $ticket['UID'];
						$_SESSION['Stamp'] = $ticket['Stamp'];
						$_SESSION['Created'] = $ticket['Created'];
						break;
					}
				}
			}
		}
	
		if(!isset($_SESSION['verify']))
		{
			echo "There is no ticket with those settings or it has already been claimed";
		}
	}
}

if(isset($_SESSION['verify']) && $_SESSION['verify'])
{
	$counter = 0;

	if(isset($_POST['password']))
	{
		if(!validate_variable("password",$_POST['password'],$validation_struct))
		{
			$error['newerpass'] = 'Password minimum length is 5 characters'; 
		}
		else
		{
			$counter++;
		}
	}
		
	if(isset($_POST['confirmpass']))
	{
		if (!validate_variable("password",$_POST['confirmpass'],$validation_struct)) 
		{
			$error['confpass'] = 'Password minimum length is 5 characters'; 
		}
		else
		{
			$counter++;
		}
	}

	if($counter==2)
	{
		if(strcmp($_POST['confirmpass'],$_POST['password'])!=0)
		{
			$error['mismatch'] = 'Password MISTMATCH'; 
		}
		else
		{
			$info = GetInfoFromUID($_SESSION['UID']);

			//print_r($info);
			//print_r($_SESSION);

			if($info!=null)
			{
				//change password
				ChangePassword($info['User'],$_POST['password']);

				ChangeForgotStatusToClaimed($_SESSION['Created'],$_SESSION['Stamp']);

				//add session data
				$_SESSION['Login']['User'] = $info['User'];
				$_SESSION['Login']['Pass'] = hash('sha512',$_POST['password']);
				$_SESSION['Login']['Email'] = GetEmail($info['User']);

				$counter=3;
			}
		}
	}

	if($counter == 3) {
		unset($_SESSION['verify']);
		unset($_SESSION['UID']);
		unset($_SESSION['Stamp']);
		unset($_SESSION['Created']);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Change password</title>
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
		<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div><a href='index.php' class='green headerlg'>Autonomous</a></div>
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
		<?php if($counter != 3) { ?>
		<form action="verify.php" method="post">
			<div id="loginform">
				<p class='loginformhead'>Change Password</p>
				<div class='loginformdiv'><!-- comment for IE --></div>
				<p class='area'>
					<label for="password">New Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="password" name="password" id="password" />
					</span>
					<?php if(isset($error['newerpass'])) ?>
					<span class='error'><?= $error['newerpass'] ?></span>
				</p>
				<p class='area'>
					<label for="confirmpass">Confirm Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="password" name="confirmpass" id="confirmpass" />
					</span>
					<?php if(isset($error['confpass']) || isset($error['mismatch'])) ?>
					<span class='error'><?= $error['newerpass'] ?><?=$error['mismatch']?></span>
				</p>
				<?php if(!isset($success)) { ?>
				<div class='loginformspacer'></div>
				<p>
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="submit" name="done" value="Change" />
					</span>
				</p>
				<?php } ?>
			</div>
		</form>
		<?php } else { ?>
		<form action="rules.php" method="post">
			<div id="loginform">
				<p class='loginformhead'>Change Password</p>
				<div class='loginformdiv'><!-- comment for IE --></div>
				<p>
					<span class='success'>Password was successfully reset!</span>
				</p>
				<div class='loginformspacer'></div>
				<p>
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="submit" name="finished" value="Done" />
					</span>
				</p>
			</div>
		</form>
		<?php } ?>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
		</div>
	</body>
</html>
<?php } ?>

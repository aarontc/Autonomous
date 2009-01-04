<?php include('utility.php');

	session_start();
	//ob_start();

	if(IsDBEmpty())
	{
		header('Location: ius.php');
		exit;
	}
	else
	{
		if(!IsGoodSession())
		{
			header('Location: login.php');
			exit;
		}
		else
		{
		
			$counter = 0;
		
			if(isset($_SESSION['CP']))
			{
				$counter = $_SESSION['CP'];
			}

			if($counter != 4)
			{
				$stop = false;
				$user = $_SESSION['Login']['User'];
				if(isset($_POST['ppass']))
				{
					if (!QuickFindUserFromPass($user,$_POST['ppass'],true))
					{
						$error['curpass'] = 'Incorrect Password'; 
					}
					else
					{
						$counter++;
					}
				}

				if(isset($_POST['newpass']))
				{
					if (!validate_variable("password",$_POST['newpass'],$validation_struct)) 
					{
						$error['newerpass'] = 'INVALID password'; 
					}
					else
					{
						$counter++;
					}
				}
				
				if(isset($_POST['conpass']))
				{
					if (!validate_variable("password",$_POST['conpass'],$validation_struct)) 
					{
						$error['confpass'] = 'INVALID password'; 
					}
					else
					{
						$counter++;
					}
				}
			
				if($counter==3)
				{
					if(strcmp($_POST['conpass'],$_POST['newpass'])!=0)
					{
						$error['mismatch'] = 'Password MISTMATCH'; 
						$counter=0;
					}
					else
					{
						//change password
						ChangePassword($_SESSION['Login']['User'],$_POST['newpass']);
		
						//add session data
						$_SESSION['Login']['Pass'] = hash('sha512',$_POST['newpass']);
		
						//send counter to the session with 4
						$counter = 4;
						$_SESSION['CP'] = $counter;
						header('Location: cp.php');
						$stop = true;
						//kill it
						$counter=0;
					}
				}

				if(!$stop)
				{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Change Password</title>
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
		<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div class='green headerlg'>Autonomous</div>
				</div>
				<div id='hright'>
					<div class='login'>
						Welcome <a href='cp.php' class='loggedinuser'><?= $_SESSION['Login']['User'] ?></a>
						<span class='roundbutton'>
							<span class='tl'></span>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<a href='login.php?action=logout' class='button'>logout</a>
						</span>
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
		<form action="cp.php" method="POST">
			<div id="loginform">
				<p class='loginformhead'>Change Password for <?= $_SESSION['Login']['User'] ?></p>
				<div class='loginformdiv'><!-- comment for IE --></div>
				<p class='area'>
					<label for='ppass'>Previous Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input maxlength="100" name="ppass" id="ppass" type="password" value='<?= $_POST['ppass'] ?>' />
					</span>
					<span class='error'><?= $error['curpass'] ?></span>
				</p>
				<p class='area'>
					<label for='ppass'>New Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input maxlength="100" name="newpass" type="password" value='<?= $_POST['newpass'] ?>' />
					</span>
					<span class='error'><?= $error['newerpass']; ?></span>
				</p>
				<p class='area'>
					<label for='ppass'>Confirm Password:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input maxlength="100" name="conpass" type="password" value='<?= $_POST['conpass'] ?>' />
					</span>
					<span class='error'>
						<?= $error['confpass'] ?>
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
						<input type="submit" value="Update">
					</span>
				</p>
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
			else
			{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Password Updated Successfully</title>
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
		<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div class='green headerlg'>Autonomous</div>
				</div>
				<div id='hright'>
					<div class='login'>
						Welcome <a href='cp.php' class='loggedinuser'><?= $_SESSION['Login']['User'] ?></a>
						<span class='roundbutton'>
							<span class='tl'></span>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<a href='login.php?action=logout' class='button'>logout</a>
						</span>
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
		<form action='rules.php'>
			<div id="loginform">
				<p class='loginformhead'>Password was successfully changed for <?= $_SESSION['Login']['User'] ?></p>
				<div class='loginformdiv'><!-- comment for IE --></div>
				<p>
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="SUBMIT" value="OK" />
					</span>
				</p>
			</form>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
		</div>
		<?php unset($_SESSION['CP']); ?>
	</body>
</html>
<?php
			}
		}
	}
?>
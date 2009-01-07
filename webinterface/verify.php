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
		if(isset($_GET['created']) && isset($_GET['stamp']) && isset($_GET['uid']) && isset($_GET['hash']))
		{
			if(strlen($_GET['hash'])==128 && strlen($_GET['stamp'])==32)
			{
				//is there even a forgot ticket that is alive in the database
				$tickets = GetAliveForgets();
		
				if($tickets != null)
				{
					foreach($tickets as $ticket)
					{
						if($ticket['UID']==$_GET['uid'])
						{
							if(strcmp($ticket['Created'],$_GET['created'])==0)
							{
								if(strcmp($ticket['Stamp'],$_GET['stamp'])==0)
								{
									if(IsPasswordInDB($ticket['UID'],$_GET['hash']))
									{
										//check to see if they are using a legit browser
										$_SESSION['verify'] = true;
										$_SESSION['UID'] = $ticket['UID'];
										$_SESSION['Stamp'] = $ticket['Stamp'];
										$_SESSION['Created'] = $ticket['Created'];
										break;
									}
								}
							}
						}
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
	//check password here
	//if password is correct..change the ticket to claim

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

	if($counter!=3)
	{
?>

	<html>
	<head>
	<title>
	Change password
	</title>
	</head>
	<body>
	<div align="center">
		<form action="verify.php" method="post">
			New password:
			<INPUT type="password" name="password"> <?=$error['newerpass']?>
			<br>
			<br>
			Confirm password:
			<INPUT type="password" name="confirmpass"> <?=$error['confpass']?><?=$error['mismatch']?>
			<br>
			<br>
			<INPUT type="submit" name="done" value="Change">
		</form>
	</div>
	</body>
	</html>
<?php
	}
	else
	{
	unset($_SESSION['verify']);
	unset($_SESSION['UID']);
	unset($_SESSION['Stamp']);
	unset($_SESSION['Created']);
?>

	<html>
	<head>
	<title>
	Change password
	</title>
	</head>
	<body>
	<div align="center">
		<form action="rules.php" method="post">
			Password was successfully set!<br />
			<INPUT type="submit" name="finished" value="done">
		</form>
	</div>
	</body>
	</html>

<?php
	}
}
?>
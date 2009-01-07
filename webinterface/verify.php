<?php include('utility.php');

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

if(!isset($_SESSION['verify']))
{
	//echo "<pre>";
	//print_r($_GET);
	//echo "</pre>";
	
	if(isset($_GET['created']) && isset($_GET['stamp']) && isset($_GET['uid']) && isset($_GET['hash']))
	{
		if(strlen($_GET['hash'])==128 && strlen($_GET['stamp'])==32)
		{
			//is there even a forgot ticket that is alive in the database
			$tickets = GetAliveForgets();
	
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

if(isset($_SESSION['verify']))
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
			$info = GetUserFromUID($_GET['uid']);

			//change password
			ChangePassword($info['User'],$_POST['password']);

			//add session data
			$_SESSION['login']['User'] = $info['User'];
			$_SESSION['Login']['Pass'] = hash('sha512',$_POST['newpass']);
			$_SESSION['login']['Email'] = GetEmail($info['User']);

			$counter=3;
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
			<INPUT type="password" name="password">
			<br>
			<br>
			Confirm password:
			<INPUT type="password" name="confirmpass">
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
			<INPUT type="submit" name="done" value="Change">
		</form>
	</div>
	</body>
	</html>

<?php
	}
}
?>
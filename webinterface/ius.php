<?php include('utility.php');

//ob_start();
session_start();


if(!IsDBEmpty())
{
	header('Location: login.php');
}
else
{
	$counter = 0;
	$stop = false;

	if(isset($_POST['nuser']))
	{
		if (!validate_variable("user",$_POST['nuser'],$validation_struct))
		{
			$error['user'] = '<span style="color:red">INVALID USER NAME</span>'; 
		}
		else
		{
			$counter++;
			if(!CheckUserString($_POST['nuser']))
			{
				$error['user'] = '<span style="color:red">INVALID USER NAME- Bad Character</span>'; 
				$counter--;
			}
			else if(DoesUserExist($_POST['nuser']))
			{
				$error['user'] = '<span style="color:red">USER NAME Already Exists</span>'; 
				$counter--;
			}
		}
	}

	if(isset($_POST['npass']))
	{
		if (!validate_variable("password",$_POST['npass'],$validation_struct)) 
		{
			$error['pass'] = '<span style="color:red">INVALID password</span>'; 
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
			$error['cpass'] = '<span style="color:red">INVALID password</span>'; 
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
			$error['mismatch'] = '<span style="color:red">PASSWORD MISMATCH</span>'; 
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
	
		<html>
		<head>
			<title>
			IUS
			</title>
		</head>
		<body>
			<div align="center">
			<form action="ius.php" method="POST">
				<div align="center">
				Initial User Setup For Autonomous Router 
				<br>
				<br>
				<?php 
				echo 'New User Name: <INPUT maxlength="100" name="nuser" type="text" value='.$_POST['nuser'].'>';
				echo $error['user'];
				?>
				<br>
				<br>
				<?php
				echo 'New Password: <INPUT maxlength="100" name="npass" type="text" value='.$_POST['npass'].'>';
				echo $error['pass'];
				?>
				<br>
				<br>
				<?php
				echo 'Confirm Password: <INPUT maxlength="100" name="cp" type="text" value='.$_POST['cp'].'>';
				echo $error['cpass'];
				echo $error['mismatch'];
				?>
				<br>
				<br>
				<INPUT type="submit" value="submit">
				<br>
			</form>
			</div>
		</body>
		</html>
	<?php
	}
 }
?>
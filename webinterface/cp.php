<?php include('utility.php');

	session_start();
	//ob_start();

	if(IsDBEmpty())
	{
		header('Location: ius.php');
	}
	else
	{
		if(!IsGoodSession())
		{
			header('Location: login.php');
		}
		else
		{
		
			$counter = 0;
		
			if(isset($_SESSION['CP']))
			{
				//echo 'yay';
				$counter = $_SESSION['CP'];
				//echo $counter;
			}

			if($counter != 4)
			{
				$stop = false;
				$user = $_SESSION['Login']['User'];
				if(isset($_POST['ppass']))
				{
					if (!QuickFindUserFromPass($user,$_POST['ppass'],true))
					{
						$error['curpass'] = '<span style="color:red">Incorrect Password</span>'; 
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
						$error['newerpass'] = '<span style="color:red">INVALID password</span>'; 
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
						$error['confpass'] = '<span style="color:red">INVALID password</span>'; 
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
						$error['mismatch'] = '<span style="color:red">Password MISTMATCH</span>'; 
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
					<html>
					<head>
						<title>
						Change Password
						</title>
					</head>
					<body>
						<div align="center">
						<form action="cp.php" method="POST">
							<div align="center">
							Change Password
							<br>
							<br>
							<?php 
							echo "User: $user <br><br>";
							echo 'Previous Password: <INPUT maxlength="100" name="ppass" type="text" value='.$_POST['ppass'].'>';
							echo $error['curpass'];
							?>
							<br>
							<br>
							<?php 
							echo 'New Password: <INPUT maxlength="100" name="newpass" type="text" value='.$_POST['newpass'].'>';		
							echo $error['newerpass'];
							?>
							<br>
							<br>
							<?php 
							echo 'Confirm Password: <INPUT maxlength="100" name="conpass" type="text" value='.$_POST['conpass'].'>';
							echo $error['confpass'];
							echo $error['mismatch'];
							?>
							<br>
							<br>
							<INPUT type="submit" value="submit">
							</form>
						</div>
					</body>
					</html>
				<?php
				}
			}
			else
			{
				?>
				<html>
					<head>
						<title>
						Successfully Change Password
						</title>
					</head>
					<body>
						<div align="center">
						Password was successfuly changed<br>
						<FORM action=rules.php>
						<INPUT type="SUBMIT" value="Done">
						</FORM>
						<?php
						unset($_SESSION['CP']);
						?>
						</div>
					</body>
					</html>
				
				<?php
			}
		}
	}
?>
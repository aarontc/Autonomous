<?php include("utility.php");

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
		}

		//$good = isgoodsession();
		//if($good == 0 || $good == -1)

		if(!IsGoodSession())
		{

			if(isset($_POST['pass']) && isset($_POST['user']))
			{

				//echo "no session"."<br />";
	
				//encrypt password
				$pass = hash('sha512',$_POST['pass']);
		
				$login = GoodUserPass($_POST['user'],$pass);
				
				//echo $login;
	
				//$error = false;
		
				if($login)
				{
				
					//add Session data
					$_SESSION['Login']['User'] = $_POST['user'];
					$_SESSION['Login']['Pass'] = $pass;
					
					//change where it goes
					header("Location: rules.php");
				}
				else
				{
					$msg['error'] = "Incorrect login/pass";
					//$error = true;
				}
			}
			?>

			<html>
			<head>
				<title>
					Router Login
				</title>
			</head>
			<BODY>
				<div align="center">
				<form action="login.php" method="POST">
					<span style="font-weight: bold">Autonomous Router Login Page</span><br />
					<br />
					<?php
					echo '<span style="font-weight: bold">User Name: </span><INPUT type="text" name="user" value='.$_POST['user'].'><br />';
					?>
					<br />
					<?php
					echo '<span style="font-weight: bold">Password: </span><INPUT type="password" name="pass"value='.$_POST['pass'].'><br />';
					?>
					<br />
					<?php
					echo $msg['error'];
					?>
					<br />
					<input type="submit" value="submit">
				</form>
				</div>
			</BODY>
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
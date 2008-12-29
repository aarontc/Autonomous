<?php include("checkuser.php"); include("utility.php");

	session_start();

	if(IsDBEmpty())
	{
		header("Location: ius.php");
	}

	if((!isset($_SESSION['Login']['User']) || !isset($_SESSION['Login']['Pass'])) && ($_POST['user'] == null || $_POST['pass'] == null))
	{	
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
				<span style="font-weight: bold">User Name: </span><INPUT type="text" name="user"><br />
				<br />
				<span style="font-weight: bold">Password: </span><INPUT type="password" name="pass"><br />
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
		//$good = isgoodsession();
		//if($good == 0 || $good == -1)

		if(!isgoodsession())
		{

			//echo "no session"."<br />";

			//encrypt password
			$pass = hash('sha512',$_POST['pass']);
	
			$login = gooduserpass($_POST['user'],$pass);
	
			if($login == 1)
			{
			
				//add Session data
				$_SESSION['Login']['User'] = $_POST['user'];
				$_SESSION['Login']['Pass'] = $pass;
				
				//change where it goes
				header("Location: rules.php");
			}
			elseif($login == 0)
			{
				echo "Incorrect login/pass";
			}
			else
			{
				echo "Incorrect Password";
			}
		}
		else
		{
			//echo "session"."<br />";
			//change where it goes
			header("Location: rules.php");
		}
	}
?>
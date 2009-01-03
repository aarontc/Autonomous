<?php include("utility.php");

	session_start();

	if(IsDBEmpty())
	{
		header("Location: ius.php");
	}
	else
	{
		//logout if they are trying to logout
		if(strcmp($_GET['action'],"logout") && isset($_SESSION['Login']['User']))
		{
			unset($_SESSION['Login']['User']);
			unset($_SESSION['Login']['Pass']);
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
	
			if(!IsGoodSession())
			{
	
				//echo "no session"."<br />";
	
				//encrypt password
				$pass = hash('sha512',$_POST['pass']);
		
				$login = GoodUserPass($_POST['user'],$pass);

				$error = false;
		
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
					$msg['error'] = "Incorrect login/pass";
					$error = true;
				}
				else
				{
					$msg['error'] = "Incorrect Password";
					$error = true;
				}

				if($error)
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
			}
			else
			{
				//echo "session"."<br />";
				//change where it goes
				header("Location: rules.php");
			}
		}
	}
?>
<?php include('utility.php');

	session_start();
	ob_start();

	if(IsDBEmpty())
	{
		header('Location: ius.php');
	}

	if(!isset($_SESSION['Login']['User']))
	{
		header('Location: login.php');
	}

	$counter = 0;

	$validation_struct = array (
		"password" => array(
			"minimum_length" => 5,
			"maximum_length" => -1
		)
	);
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
		$user = $_SESSION['Login']['User'];
		echo "User: $user <br><br>";
		echo "Previous Password: <INPUT maxlength=\"100\" name=\"ppass\" type=\"text\" value=\""; echo $_POST['ppass']; echo "\">";
		?>
		<br>
		<br>
		<?php 
		echo "New Password: <INPUT maxlength=\"100\" name=\"newpass\" type=\"text\" value=\""; echo $_POST['newpass']; echo "\">";
		?>
		<br>
		<br>
		<?php 
		echo "Confirm Password: <INPUT maxlength=\"100\" name=\"conpass\" type=\"text\" value=\""; echo $_POST['conpass']; echo "\">";
		?>
		<br>
		<br>
		<INPUT type="submit" value="submit">
	</form>
	</div>
</body>
</html>
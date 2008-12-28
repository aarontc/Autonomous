<?php require('utility.php');

ob_start();
session_start();


if(!IsDBEmpty())
{
	header('Location: login.php');
}

	
$counter = 0;

$validation_struct = array (
	"user" => array(
		"minimum_length" => 5,
		"maximum_length" => 100
		),
	"password" => array(
		"minimum_length" => 5,
		"maximum_length" => -1
	)
);

function validate_variable ( $variable, $value, $validation_struct ) {
	global $counter;
	
	if (array_key_exists ($variable, $validation_struct  ) ) {
		foreach ( $validation_struct[$variable] as $validate => $requirement ) {
			switch ( $validate ) {
				case "minimum_length":
					if ( strlen ( $value ) < $requirement )
						return false;
					//$counter++;
					break;
				case "maximum_length":
					if ($requirement != -1 && strlen ( $value ) > $requirement)
						return false;
					//$counter++;
					break;
			}
		}
	}
	else{
		return false;
	}

	$counter++;
	return true;
				
}
	//if ( !validate_variable("user",$user,$validation_struct) ) {
	//	echo "YOU DINT ENTER A RIGHT USERNAME DUMBASAS";
	//}
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
		echo "New User Name: <INPUT maxlength=\"100\" name=\"nuser\" type=\"text\" value=\""; echo $_POST['nuser']; echo "\">";

		if(isset($_POST['nuser']))
		{
			if (!validate_variable("user",$_POST['nuser'],$validation_struct))
			{
				echo '<span style="color:red">INVALID USER NAME</span>'; 
			}
			else
			{
				if(!CheckUserString($_POST['nuser']))
				{
					echo '<span style="color:red">INVALID USER NAME- Bad Character</span>'; 
					$counter--;
				}
			}
		}
		?>
		<br>
		<br>
		<?php
		echo "New Password: <INPUT maxlength=\"100\" name=\"npass\" type=\"text\" value=\""; echo $_POST['npass']; echo "\">";
		
		 
		if(isset($_POST['npass']))
		{
			if (!validate_variable("password",$_POST['npass'],$validation_struct)) echo '<span style="color:red">INVALID password</span>'; 
		}
		?>
		<br>
		<br>
		<?php
		echo "Confirm Password: <INPUT name=\"cp\" type=\"text\" value=\""; echo $_POST['cp']; echo "\">";
		

		if(isset($_POST['cp']))
		{
			if (!validate_variable("password",$_POST['cp'],$validation_struct)) echo '<span style="color:red">INVALID password</span>'; 
		}

		if($counter == 3)
		{
			if($_POST['cp'] != $_POST['npass'])
			{
				echo '<span style="color:red">PASSWORD MISMATCH</span>'; 
			}
			else
			{
				$privliages['port forwarding'] = true;
				$privliages['user managment'] = true;

				//create the database
				CreateUser($_POST['nuser'],$_POST['npass'], $privliages);

				//add Session data
				//$_SESSION['Login']['User'] = $_POST['nuser'];
				//$_SESSION['Login']['Pass'] = hash('sha512',$_POST['npass']);
				
				

				//header('LOCATION: login.php');
			}
		}

		?>
		<br>
		<br>
		<INPUT type="submit" value="submit">
		<br>
	</form>
	</div>
</body>
</html>
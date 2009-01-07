<?php include('utility.php');

if(IsDBEmpty())
{
	header("Location: ius.php");
	exit;
}


if(isset($_POST['email']) && $_POST['email'] != null)
{
	if(IsValidEmail($_POST['email']))
	{
		$user_info = GetInfoFromEmail($_POST['email']);
	
		if(isset($user_info) && $user_info != null)
		{
			//echo "Found user info";

			$linkToSend = 'http://'.Website.'/autonomous/webinterface/verify.php?check="'.date("m/d/Y").'|'.$user_info['UID'].'|'.$user_info['User'].'|'.$user_info['Password'].'|'.$user_info['Email'].'"';

//			echo $linkToSend;

			//add to forgot table

			$message = "Hello ".$users_info['User']."\r\n";
			$message .= "Click this link to change your password (once this link is clicked...it will expire)\r\n";
			$message .= $linkToSend."\r\n";

			if(mail($users_info['Email'],"Forgot User/Pass",$message))
				echo "Message Sent";
			else
				echo "Delivery failed";

		}
		else
		{
			echo "There is no user with this email address";
		}
	}
	else
	{
		echo "That is not a email address";
	}
}

?>

<html>
<head>
<title>
Forgot user/pass
</title>
</head>
<body>
<div align="center">
	<form action="forgot.php" method="post">
		Enter Email Address:
		<INPUT type="text" name="email">
		<br>
		<br>
		<INPUT type="submit" name="done" value="Submit">
	</form>
</div>
</body>
</html>
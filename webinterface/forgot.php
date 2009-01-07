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
			//add to forgot table
			AddToForgot($user_info['UID']);

			$linkToSend = 'http://'.Website.'/autonomous/webinterface/verify.php?check='.date("m/d/Y").'|'.$user_info['UID'].'|'.$user_info['User'].'|'.$user_info['Password'].'|'.$user_info['Email'];


			$message = "Hello ".$users_info['User']."\n";
			$message .= "Click this link to change your password (once this link is clicked...it will expire)\n";
			$message .= $linkToSend."\n";
			$message .= "If you dont click this within 7 days, it will expire\n";

			$message = wordwrap($message,70);

			if(mail($_POST['email'],"Forgot User/Pass",$message))
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
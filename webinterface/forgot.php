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

			$stamp = md5(time());
			//add to forgot table
			AddToForgot($user_info['UID'],$stamp);

			//$linkToSend = 'http://'.Website.'/autonomous/webinterface/verify.php?created='.date("m/d/Y").'&stamp='.$stamp.'&uid='.$user_info['UID'].'&hash='.$user_info['Password'];/*.'&email='.$user_info['Email'];*/
			$tempString = date("m/d/Y").$stamp.$user_info['UID'].$user_info['Password'];
			//echo $tempString.'<br>';
			$hash = hash('sha512',$tempString);
			//echo $hash;
			$linkToSend = 'http://'.Website.'/autonomous/webinterface/verify.php?hash='.$hash;
			//echo $linkToSend;;

			$message = "Hello ".$user_info['User']."\n";
			$message .= "Click this link to change your password (once this link is clicked...it will expire)\n";
			$message .= $linkToSend."\n";
			$message .= "If you dont click this within 7 days, it will expire\n";
			$message .= "if this link has expired, you must click on 'forgot username/pass' again\n";

			$message = wordwrap($message,70);

			if(mail($_POST['email'],"Retrieve username/pass for Autonomous router",$message))
				$success = "Message Sent. Please check your email.";
			else
				$error = "Delivery failed";

		}
		else
		{
			$error = "There is no user with this email address";
		}
	}
	else
	{
		$error = "That is not a email address";
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous - Retrieve Username and/or Password</title>
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
		<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div><a href='index.php' class='green headerlg'>Autonomous</a></div>
				</div>
				<div id='hright'>&nbsp;</div>
			</div>
			<div class='area'>
				<div id='hleft'>
					<div class='ltgrey headermed'>Self-Governing Routing</div>
				</div>
				<div id='hright'>
					<div class='dkgrey nodisplay'>
						Search for Term:
						<span class='roundinput'>
							<span class='tl'></span>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<input type='text' />
						</span>
						<span class='roundbutton'>
							<span class='tl'></span>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<input type='submit' value='GO' />
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class='divider'><!-- comment for IE --></div>
		<form action="forgot.php" method="post">
			<div id="loginform">
				<p class='loginformhead'>Retrieve Username and/or Password</p>
				<div class='loginformdiv'><!-- comment for IE --></div>
				<p class='area'>
					<?php if(isset($success)) { ?>
					<span class='success'><?= $success ?></span>
					<?php } else { ?>
					<label for="email">Enter Email Address:</label>
					<span class='roundinput'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="text" name="email" id="email" />
					</span>
					<?php if(isset($error)) ?>
					<span class='error'><?= $error ?></span>
					<?php } ?>
				</p>
				<?php if(!isset($success)) { ?>
				<div class='loginformspacer'></div>
				<p>
					<span class='roundbutton'>
						<span class='tl'></span>
						<span class='tr'></span>
						<span class='bl'></span>
						<span class='br'></span>
						<input type="submit" name="done" value="Submit" />
					</span>
				</p>
				<?php } ?>
			</div>
		</form>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
		</div>
	</body>
</html>
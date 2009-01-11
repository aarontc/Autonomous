<?php require ('autonomous.inc.php');

session_start();

if(IsDBEmpty())
{
	header('Location: ius.php');
	exit;
}

if(!IsGoodSession())
{
	header('Location: login.php');
	exit;
}

$sent = false;

if(isset($_SESSION['Login']['Email']) && $_SESSION['Login']['Email'] != null)
{
	$owned_rules = GetOwnedRulesFromUser($_SESSION['Login']['User']);
	
	$messages = "A backup of your rules\n";

	
	foreach($_SESSION['Rules'] as $rule)
	{
		if(IsHashInGivenRuleIDs($owned_rules,$rule->checkSum))
		{
			$messages .= "Rule: \n";
			$messages .= $rule->ToText();
			$messages .= "\n\n\n";
		}
	}
	
	$messages = wordwrap($messages,70);
	
	echo $messages;
	
	if(mail($_SESSION['Login']['Email'],"Your rules",$messages))
		$sent = true;
}
else
{
	header('Location: rules.php');
	exit;
}

if($sent)
{
?>

<html>
<head>
<title>
Email rules
</title>
</head>
<body>
Email Sent
<br />
<a href="rules.php">go back to rules</a>
</body>
</html>

<?php
}

else
{
?>

<html>
<head>
<title>
Email rules
</title>
</head>
<body>
Email could not deliver
<br />
<a href="rules.php">go back to rules</a>
</body>
</html>

<?php
}
?>
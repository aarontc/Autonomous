<?php include('utility.php');

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

if(!IsUserAdmin($_SESSION['Login']['User']))
{
	//header('Location: rules.php');
	echo "ACCESS DENIED---You Do Not Have The Privilege To Have Access To This Page!<br>";
	exit;
}


//print_r(array_keys($_POST));

$keys = array_keys($_POST);

//$sel = 0;
$sid = 0;
$remove = false;
$errorpass = "";
$errotUser = "";

//get the removes and update keyword from the post
foreach($keys as $key)
{
	if(isset($_POST[$key]))
	{
		$pos = rev_strstr($key,"REMOVE");
		if($pos != -1)
		{
			$user = substr($key,0,$pos);
			if(strlen($user)>0)
				RemoveUser($user);  //for a single remove
			else
				$remove = true;
		}
		else
		{
			$pos = rev_strstr($key,"UPDATE");
			if($pos != -1)
			{
				$user = substr($key,0,$pos);
				//echo $user;
				if(strlen($user)>0)  //for a single update
				{
					if(isset($_POST[$user."PASS"]) && $_POST[$user."PASS"] != "")
					{
						if(!validate_variable("password",$_POST[$user."PASS"],$val))
						{
							$errorpass = "<span style=\"color:red\">Password minimum length is 5 characters</span>";
							$error_user = $user;
						}
						else
						{
							ChangePassword($user,$_POST[$user."PASS"]);
						}
					}

					if($errorpass=="")
					{
						$priv['port forwarding'] = true;
	
						if(strcmp($_POST[$user."CHECK"],"on")==0)
							$priv['user managment'] = true;
						else
							$priv['user managment'] = false;

						ChangePriv($user,$priv);
					}
				}
			}
			else
			{
				$pos = rev_strstr($key,"SEL"); //get everything that was selected
				if($pos != -1)
				{
					$user = substr($key,0,$pos);
					if(strlen($user)>0)
						$sel[$sid++] = $user;
				}
			}
		}
	}
}


//for mass update/deleting users
if($sid > 0)
{
	foreach($sel as $item)
	{
		//either remove or update

		if($remove)
		{
			RemoveUser($item);
		}
		else
		{
			if(isset($_POST[$item."PASS"]) && $_POST[$item."PASS"] != "")
			{
				//echo $_POST[$item."PASS"];
				ChangePassword($item,$_POST[$item."PASS"]);
			}

			$priv['port forwarding'] = true;

			if(strcmp($_POST[$item."CHECK"],"on")==0)
				$priv['user managment'] = true;
			else
				$priv['user managment'] = false;

			ChangePriv($item,$priv);
		}
	}
}


if(isset($_POST['add']))
{
	$counter = 0;
	
	if(isset($_POST['user']))
	{
		if (!validate_variable("user",$_POST['user'],$validation_struct))
		{
			$error['user'] = '<span style="color:red">User Name---2 chars min/100 chars max</span>'; 
		}
		else
		{
			$counter++;
			if(!CheckUserString($_POST['user']))
			{
				$error['user'] = '<span style="color:red">INVALID USER NAME- Bad Character</span>'; 
				$counter--;
			}
			else if(DoesUserExist($_POST['user']))
			{
				$error['user'] = '<span style="color:red">USER NAME Already Exists</span>'; 
				$counter--;
			}
		}
	}
	
	if(isset($_POST['pass']))
	{
		if (!validate_variable("password",$_POST['pass'],$validation_struct)) 
		{
			$error['pass'] = '<span style="color:red">Password minimum length is 5 characters</span>'; 
		}
		else
		{
			$counter++;
		}
	}
	
	if(isset($_POST['cpass']))
	{
		if (!validate_variable("password",$_POST['cpass'],$validation_struct))
		{
			$error['cpass'] = '<span style="color:red">Password minimum length is 5 characters</span>'; 
		}
		else
		{
			$counter++;
		}
	}
	
	if($counter == 3)
	{
		if(strcmp($_POST['cpass'],$_POST['pass'])!=0)
		{
			$error['mismatch'] = '<span style="color:red">PASSWORD MISMATCH</span>'; 
		}
		else
		{
			
			$privileges['port forwarding'] = true;
	
			if(strcmp($_POST['admin'],"on")==0)
				$privileges['user managment'] = true;
			else
				$privileges['user managment'] = false;
	
			//create the database
			CreateUser($_POST['user'],$_POST['pass'], $privileges);

			//unset($_POST['user']);
			//unset($_POST['pass']);
			//unset($_POST['cpass']);
			//unset($_POST['admin']);
			unset($_POST);
		}
	}
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
  <title>
  Autonomous User Management
  </title>
  </head>
  <body>
  <div align="center">
	Autonomous User Management <br><br>
	New User:
	<form action="um.php" method="post">
	<table border=1>
  	<tbody>
    	<tr>
      	<td>Add User: <INPUT type="text" name="user" value='<?=$_POST['user']?>'> <?=$error['user']?></td>
      	<td>Password:<INPUT type="text" name="pass" value='<?=$_POST['pass']?>'><?=$error['pass']?><br> Confirm Password:<INPUT type="text" name="cpass" value='<?=$_POST['cpass']?>'><?=$error['cpass']." ".$error['mismatch']?></td>
      	<td>
	<?php 
	if(strcmp($_POST['admin'],"on")==0)
		echo 'Make Admin <INPUT type="checkbox" name="admin" checked="on">';
	else
		echo 'Make Admin <INPUT type="checkbox" name="admin">';
	?></td>
      	<td><INPUT type="submit" value="Add" name="add"></td>
    	</tr>
  	</tbody>
	</table>
	<br><br>
	Edit User:
	<table border=1>
  	<tbody>
	<?php
	$numRows = HowManyUsers();
	$users_info = GetAllUsersInfo();
	
	for($i=0; $i < $numRows; $i++)
	{	
		echo "<tr>";
		$curUser = $_SESSION['Login']['User'];

		if(strcmp($curUser,$users_info[$i]['User'])==0)
		{
			echo "<td>".$users_info[$i]['User']."</td>";
		}
		else
		{
			$cbsel = $users_info[$i]['User']."SEL";
			echo "<td><INPUT type=\"checkbox\" name=".$cbsel.">".$users_info[$i]['User']."</td>";
		}

		$newpass = $users_info[$i]['User']."PASS";

		if($error_user == $users_info[$i]['User'])
			echo "<td>Set Password <INPUT type=\"text\" name=".$newpass.">".$errorpass."</td>";
		else
			echo "<td>Set Password <INPUT type=\"text\" name=".$newpass."></td>";

		$cbName = $users_info[$i]['User']."CHECK";

		if(strcmp($curUser,$users_info[$i]['User'])!=0)
		{
			$ad = ($users_info[$i]['RID'] & UserMan)>>1;
			if($ad)
				echo "<td><INPUT type=\"checkbox\" name=".$cbName." checked=\"on\">Admin</td>";
			else
				echo "<td><INPUT type=\"checkbox\" name=".$cbName.">Admin</td>";
		}
		else
		{
			echo "<td><INPUT type=\"checkbox\" name=".$cbName." checked=\"on\" disabled=\"true\">Admin</td>";
		}

		$upName = $users_info[$i]['User']."UPDATE";
		echo "<td><INPUT type=\"submit\" value=\"UPDATE\" name=".$upName.">";
		
		if(strcmp($curUser,$users_info[$i]['User'])!=0)
		{
			$rmName = $users_info[$i]['User']."REMOVE";
			echo "<INPUT type=\"submit\" value=\"REMOVE\" name =".$rmName."></td>";
		}
		echo "</tr>";
	}
	?>
	</tbody>
	</table>
	<INPUT type="submit" value="Update Selected" name="UPDATE">
	<INPUT type="submit" value="Remove Selected" name="REMOVE">
	</form>
  </div>
  </body>
</html>
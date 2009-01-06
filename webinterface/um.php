<?php include("utility.php");

session_start();

// $keys = array_keys($_POST);
// 
// foreach($keys as $key)
// {
// 	if(isset($_POST[$key]['new']))
// 		echo "yay";
// }

$users_info = $_SESSION['Users'];//GetAllUsersInfo();

if(isset($_POST['username']['new']))
{
	$name = trim($_POST['username']['new']);

	if(strcmp($name,"New Users Name")!=0)
	{
		$counter = 0;

		if(CheckUserString($name))
		{
			if(!DoesUserExist($name))
			{
				$counter++;
				
				if(isset($_POST['password']['new']))
				{
					if (!validate_variable("password",$_POST['password']['new'],$validation_struct)) 
					{
						$error['new']['pass'] = 'Password minimum length is 5 characters'; 
					}
					else
					{
						$counter++;
					}
				}
				
				if(isset($_POST['confirmpass']['new']))
				{
					if (!validate_variable("password",$_POST['confirmpass']['new'],$validation_struct))
					{
						$error['new']['cpass'] = 'Password minimum length is 5 characters'; 
					}
					else
					{
						$counter++;
					}
				}
				
				if($counter == 3)
				{
					if(strcmp($_POST['confirmpass']['new'],$_POST['password']['new'])!=0)
					{
						$error['new']['mismatch'] = 'PASSWORD MISMATCH'; 
					}
					else
					{
						
						$privileges['port forwarding'] = true;
				
						if(strcmp($_POST['admin']['new'],"on")==0)
							$privileges['user managment'] = true;
						else
							$privileges['user managment'] = false;
			
						if(strcmp($_POST['nullrules']['new'],"on")==0)
							$privileges['users data only'] = true;
						else
							$privileges['users data only'] = false;
				
						//create the database
						CreateUser($name,$_POST['password']['new'], $privileges);
					}
				}
	
			}
			else
			{
				//error here
				$error['new']['exists'] = "User already exists";
			}
		}
		else
		{
			//error here
			$error['new']['invalid'] = "Only allowed to use letters, numbers, and spaces";
		}
	}
}



if(isset($_POST['delete']))
{
	//echo "being deleted";
	$k = array_keys($_POST['delete']);
	RemoveUser($users_info[$k[0]]['User']);
}

for($i=0; $i < count($users_info); $i++)
{
	//change privs
	$change = 0;

	if(isset($_POST['admin'][$i]))
	{
		$change |= UserMan;
	}
	
	if(isset($_POST['nullrules'][$i]))
	{
		$change |= UserDataOnly;
	}

	$applyChange = false;

	if(((($users_info[$i]['RID'] & UserDataOnly )>>2) != (($change & UserDataOnly) >> 2)) || ((($users_info[$i]['RID'] & UserMan )>>1) != (($change & UserMan) >> 1)))
		$applyChange = true;

	if($applyChange)
	{

		$privileges['port forwarding'] = true;
					
		if((($change & UserMan) >> 1))
			$privileges['user managment'] = true;
		else
			$privileges['user managment'] = false;
	
		if((($change & UserDataOnly) >> 2))
			$privileges['users data only'] = true;
		else
			$privileges['users data only'] = false;


		ChangePriv($users_info[$i]['User'],$privileges);
	}

	$applyChange = false;


	//change password
	//print_r($_POST['password'][$i]);
	$pass1 = $_POST['password'][$i];
	//print_r($_POST['confirmpass'][$i]);
	$pass2 = $_POST['confirmpass'][$i];

	$pl1 = strlen($pass1);
	$pl2 = strlen($pass2);
	
	//echo $pl1." ".$pl2."<br>";

	if($pl1>0 || $pl2>0)
	{
		if($pl1==0 && $pl2>0)
		{
			echo "here1";
			//error goes here
			$error[$i]['cpass'] = 'Missing password field'; 
		}
		else if($pl1>0 && $pl2==0)
		{
			echo "here2";
			//error goes here
			$error[$i]['pass'] = 'Missing confirm password field'; 
		}
		else
		{
			//good
			//echo $pl1." ".$pl2;

			$counter = 1;

			if (!validate_variable("password",$pass1,$validation_struct)) 
			{
				$error[$i]['pass'] = 'Password minimum length is 5 characters'; 
			}
			else
			{
				$counter++;
			}
		
		
		
			if (!validate_variable("password",$pass2,$validation_struct))
			{
				$error[$i]['cpass'] = 'Password minimum length is 5 characters'; 
			}
			else
			{
				$counter++;
			}
			
			if($counter == 3)
			{
				if(strcmp($pass2,$pass1)!=0)
				{
					$error[$i]['mismatch'] = 'PASSWORD MISMATCH'; 
				}
				else
				{
					ChangePassword($users_info[$i]['User'],$pass1);
				}
			}
		}
	}


	//change username
	if(isset($_POST['username'][$i]))
	{
		if(strcmp($_POST['username'][$i],$users_info[$i]['User'])!=0)
		{
			$change = ChangeUserName($users_info[$i]['User'],$_POST['username'][$i]);
			if(strcmp($change,"")!=0)
				$error[$i]['un'] = $change;
		}
	}
}


unset($_SESSION['Users']);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Autonomous</title>
		<meta content="">
		<link href='css/style.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
		<link href='css/colors.css?<?= md5 ( time () ); ?>' type='text/css' media='screen,projection' rel='stylesheet' />
	</head>
	<body>
				<div id='header'>
			<div class='area'>
				<div id='hleft'>
					<div><a href='index.php' class='green headerlg'>Autonomous</a></div>
				</div>
				<div id='hright'>
					<div class='login'>
						Welcome <a href='cp.php' class='loggedinuser'><?=$_SESSION['Login']['User']?></a>
						<span class='roundbutton'>
							<span class='tl'></span>
							<span class='tr'></span>
							<span class='bl'></span>
							<span class='br'></span>
							<a href='login.php?action=logout' class='button'>logout</a>
						</span>
					</div>
				</div>
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
			$index				<span class='tr'></span>
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
		<div id='main' class='area'>
		<pre>
		<!-- <?php print_r ($_POST);?> -->
		</pre>
			<div id='sidebar'>
				<div class='menuitem'>
					<div class='mititle'><a href='#'>Port Forwarding</a></div>
					<div class='midesc'>Forward ports to LAN machines.</div>
				</div>
			</div>
			<div id='content'>
				<div id='users' class='area'>
					<form method="POST">
						<div class='center'>
							<span class='roundbutton'>
								<span class='tl'></span>
								<span class='tr'></span>
								<span class='bl'></span>
								<span class='br'></span>
								<input type="submit" name="submit" value="Submit" title='Update all Users' />
							</span>
						</div>
						<!-- user starts here -->

						<?php
						$users_info = GetAllUsersInfo();
						$_SESSION['Users'] = $users_info;
						$numRows = HowManyUsers();
						$curUser = $_SESSION['Login']['User'];
						
						for($i=0; $i < $numRows; $i++)
						{	

						?>

						<?php
						if(isset($error[$i]))
							$errclass = "error center";
						else
							$errclass = "nodisplay";
						?>
						<div class='<?=$errclass?>'><?=array_pop($error[$i])?></div>
						<div class='rulespacer'></div>
						<div class='usercontainer'>
							<div class='usershadow'>
								<span class='tr'></span>
								<span class='bl'></span>
								<span class='br'></span>
							</div>
							<div class='user'>
								<span class='usertl'></span>
								<span class='usertr'></span>
								<span class='userbl'></span>
								<span class='userbr'></span>
								<div class='usertitle'>
									<input type='text' value='<?=$users_info[$i]['User']?>' name='username[<?=$i?>]' id='username[<?=$i?>]' title='Username goes here.' />

									<?php
									if(strcmp($curUser,$users_info[$i]['User'])!=0)
									{
									?>
									<input type='image' title='Delete this user' alt='Delete' value='Delete' src='images/delete-15x15.png' name='delete[<?=$i?>]' id='delete[<?=$i?>]' />
									<?php
									}
									else
									{
									?>
									<img src='images/delete-gray-15x15.png' title='Cannot remove current user' />
									<?php
									}
									?>
								</div>
								<div class='userbody'>
									<div class='area'>
										<p>
											<label class='userlabel' for='password[<?=$i?>]'>New Password:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='password' class='password' value='' name='password[<?=$i?>]' id='password[<?=$i?>]' title='Type new password here.' />
											</span>
										</p>
										<p>
											<input type='checkbox' name='admin[<?=$i?>]' id='admin[<?=$i?>]' title='Is this user an administrator?' <? if ((($users_info[$i]['RID'] & UserMan)>>1)==true) { echo "checked='true'"; } if(strcmp($curUser,$users_info[$i]['User'])==0) echo "disabled='true'"; ?>/>
											<label class='checklabel' for='admin[<?=$i?>]'>Admin</label>
										</p>
									</div>
									<div class='area'>
										<p>
											<label class='userlabel' for='confirmpass[<?=$i?>]'>Confirm Password:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='password' class='password' value='' name='confirmpass[<?=$i?>]' id='confirmpass[<?=$i?>]' title='Confirm new password here.'>
											</span>
										</p>
										<p>
											<input type='checkbox' name='nullrules[<?=$i?>]' id='nullrules[<?=$i?>]' title='Can this user see rules owned by no one?' <? if ((($users_info[$i]['RID'] & UserDataOnly)>>2)==true) { echo "checked='true'"; } if(strcmp($curUser,$users_info[$i]['User'])==0) echo "disabled='true'"; ?> />
											<label class='checklabel' for='nullrules[<?=$i?>]'>Can see unowned rules.</label>
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class='rulespacer'></div>
						<!-- user ends here -->
						<?php
						}
						?>
						<!-- user NEWUSERFORTEHLOVEOFALLTHATISPACKETY starts here -->

						<?php
						if(isset($error['new']))
							$errclass = "error center";
						else
							$errclass = "nodisplay";
						?>
						<div class='<?=$errclass?>'><?=array_pop($error['new'])?></div>
						<div class='rulespacer'></div>
						<div class='usercontainer'>
							<div class='usershadow'>
								<span class='tr'></span>
								<span class='bl'></span>
								<span class='br'></span>
							</div>
							<div class='user'>
								<span class='usertl'></span>
								<span class='usertr'></span>
								<span class='userbl'></span>
								<span class='userbr'></span>
								<div class='usertitle'>
									<input type='text' value='New Users Name' name='username[new]' id='username[new]' title='New users name goes here.' />
								</div>
								<div class='userbody'>
									<div class='area'>
										<p>
											<label class='userlabel' for='password[new]'>Password:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='password' class='password' value='' name='password[new]' id='password[new]' title='Type password here.' />
											</span>
										</p>
										<p>
											<input type='checkbox' name='admin[new]' id='admin[new]' title='Is this user an administrator?' />
											<label class='checklabel' for='admin[new]'>Admin</label>
										</p>
									</div>
									<div class='area'>
										<p>
											<label class='userlabel' for='confirmpass[new]'>Confirm Password:</label>
											<span class='roundinput'>
												<span class='tl'></span>
												<span class='tr'></span>
												<span class='bl'></span>
												<span class='br'></span>
												<input type='password' class='password' value='' name='confirmpass[new]' id='confirmpass[new]' title='Confirm password here.'>
											</span>
										</p>
										<p>
											<input type='checkbox' name='nullrules[new]' id='nullrules[new]' title='Can this user see rules owned by no one?' />
											<label class='checklabel' for='nullrules[new]'>Can see unowned rules.</label>
										</p>
									</div>
								</div>
							</div>
						</div>
						<!-- new user ends here -->
						<div class='rulespacer'></div>
						<div class='center'>
							<span class='roundbutton'>
								<span class='tl'></span>
								<span class='tr'></span>
								<span class='bl'></span>
								<span class='br'></span>
								<input type="submit" name="submit" value="Submit" title='Update all users' />
							</span>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class='divider'><!-- comment for IE --></div>
		<div id='footer'>
			Copyright &copy; 2008, Scott Deutsch, Ben Mann, Aaron Ten Clay. All Rights Reserved.
		</div>
	</body>
</html>
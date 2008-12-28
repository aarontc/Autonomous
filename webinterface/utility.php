<?php

function IsDBEmpty()
{
	//connect to database
	$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//checks how many rows there are in the databasae
	$q = sqlite_query($dbhandle, 'SELECT * FROM logins') or die ("Cannout use login table");
	$num = sqlite_num_rows($q);

	//close it
	sqlite_close($dbhandle);
	
	return ($num > 0 ? false : true);
}


function CreateUser($user, $pass, $privliages)
{
	//echo $user." ".$pass;
	//connect to database
	$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//sha512 the password
	$np = hash('sha512',$pass);	

	$q =  "INSERT INTO logins VALUES(null, '$user','$np');";
	//create user
	$query = sqlite_exec($dbhandle, $q, $error);

	if (!$query) {
    		exit("Error in query: '$error'");
	} else {
		
		//add privliages to the user
		$q = "SELECT RID FROM roles;";
		$query = sqlite_exec($dbhandle, $q, $error);
		
		if (!$query) {
    			exit("Error in query: '$error'");
		} else {
			//$_SESSION['flash'] = $query;
		`	print_r($query);
			//add the user to the role, if in privlages array says so
			foreach($query as $rid)
			{
				switch($rid)
				{
				case '1':
					if($privlages['port'])
					{
						//add to the database
						
						//get user ID
						$q = "SELECT UID FROM logins WHERE User='$user';";
						$sql = sqlite_exec($dbhandle, $q, $error);
						
						if (!$query) {
    							exit("Error in query: '$error'");
						} else {
							//store privliage
							$q = "INSERT INTO logins_roles VALUES($sql,$rid);";
							$sql = sqlite_exec($dbhandle, $q, $error);

							if (!$sql) {
    								exit("Error in query: '$error'");
							}
						}
					}
					break;
				case '2':
					if($privlages['user_man'])
					{
						//add to the database
						//get user ID
						$q = "SELECT UID FROM logins WHERE User='$user';";
						$sql = sqlite_exec($dbhandle, $q, $error);
						
						if (!$query) {
    							exit("Error in query: '$error'");
						} else {
							//store privliage
							$q = "INSERT INTO logins_roles VALUES($sql,$rid);";
							$sql = sqlite_exec($dbhandle, $q, $error);

							if (!$sql) {
    								exit("Error in query: '$error'");
							}
						}
					}
					break;
				}
			}
		}
	}

	//close it
	sqlite_close($dbhandle);
}

function CheckUserString($user)
{
	for($i = 0; $i < strlen($user); $i++)
	{
		if($user[$i] == "'")
			return false;
	}

	return true;
}

?>

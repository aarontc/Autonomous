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
	
	$query = true;
	if (!$query) {
    		exit("Error in query: '$error'");
	} else {
		
		//add privliages to the user

		//first gather priv. info
		$q = "SELECT * FROM roles;";
		$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
		
		if (!$query) {
    			exit("Error in query: '$error'");
		} else {
			//add the user to the role, if in privlages array says so
			foreach($query as $entry)
			{
				if($privliages[$entry['Description']])
				{
					//add to the database		
					//get user ID
					$q = "SELECT UID FROM logins WHERE User='$user';";
					//secho $q;
					$sql = sqlite_exec($dbhandle, $q, $error);
					
					if (!$query) {
						exit("Error in query: '$error'");
					} else {
						//store privliage
						$rid = $entry['RID'];
						$q = "INSERT INTO logins_roles VALUES($sql,$rid);";
						$sql = sqlite_exec($dbhandle, $q, $error);

						if (!$sql) {
							exit("Error in query: '$error'");
						}
					}
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

function ChangePassword($user,$newPass)
{
	//connect to the database
	$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//hash the password
	$np = hash('sha512',$newPass);

	$q =  "UPDATE logins SET Password='$np' WHERE User='$user';";

	//change password
	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
		return false;
	}

	//close the datapase
	sqlite_close($dbhandle);

	return true;
}

function QuickFindUserFromPass($user,$pass,$hashit=false)
{
	//test me..................
	$good = false;
	//connect to the database
	$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//hash the password
	if($hashit)
	{
		$np = hash('sha512',$pass);
		$q =  "SELECT User FROM logins WHERE Password='$np'";
	}
	else
	{
		$q =  "SELECT User FROM logins WHERE Password='$pass'";
	}

	//change password
	$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);

	if($query){
		//exit("Error in Query: '$error'");
		foreach($query as $entry)
		{
			if(strcmp($entry['User'],$user)==0)
			{
				$good = true;
				break;
			}
		}
	}

	//close the datapase
	sqlite_close($dbhandle);

	return $good;
}

function GoodUserPass($user,$pass)
{
	$good=0;

	//connect to database
	$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");
	//grab login table
	$q = sqlite_query($dbhandle, 'SELECT * FROM logins') or die ("Cannout use login table");
	$result = sqlite_fetch_all($q,SQLITE_ASSOC);
	foreach ($result as $entry)
	{
		//is it the right user and pass
		if($user === $entry['User'] && $pass === $entry['Password'])
		{
			$good = 1;
			break;
		}
		//is it the right user and wrong pass
		elseif($user === $$entry['User'] && $pass != $$entry['Password'])
		{
			$good = -1;
			break;
		}
	}

	//close the sql database
	sqlite_close($dbhandle);	
	
	return $good;
}

function IsGoodSession()
{
	//return GoodUserPass($_SESSION['Login']['User'],$_SESSION['Login']['Pass']);
	return QuickFindUserFromPass($_SESSION['Login']['User'],$_SESSION['Login']['Pass']);
}

function DoesUserExist($user)
{
	$de = false;

	//connect to database
	$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");
	//grab login table
	$q = sqlite_query($dbhandle, "SELECT User FROM logins WHERE User='$user';",$error) or die ("Cannout use login table");

	if(!$q){
		exit("Error in Query: '$error'");
		$de = false;
	}

	if(strcmp($q,$user)==0)
		$de = true;

	//close the sql database
	sqlite_close($dbhandle);

	return $de;
}

function GetPrivFromUser($user)
{

}

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
	
	if (array_key_exists ($variable, $validation_struct  ) ) {
		foreach ( $validation_struct[$variable] as $validate => $requirement ) {
			switch ( $validate ) {
				case "minimum_length":
					if ( strlen ( $value ) < $requirement )
						return false;
					break;
				case "maximum_length":
					if ($requirement != -1 && strlen ( $value ) > $requirement)
						return false;
					break;
			}
		}
	}
	else{
		return false;
	}

	return true;
				
}
?>
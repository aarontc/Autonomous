<?php

function IsDBEmpty()
{
	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//checks how many rows there are in the databasae
	@$q = sqlite_query($dbhandle, 'SELECT * FROM logins');

	if(!$q)
	{
		CreateSqliteFile();	
		$num = 0;
	}
	else
	{
		@$num = sqlite_num_rows($q);
	}

	//close it
	sqlite_close($dbhandle);
	
	return ($num > 0 ? false : true);
}


function CreateUser($user, $pass, $privileges)
{
	//echo $user." ".$pass;
	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//sha512 the password
	$np = hash('sha512',$pass);	

	$q =  "INSERT INTO logins VALUES(null, '$user','$np');";

	//create user
	$query = sqlite_exec($dbhandle, $q, $error);
	
	$query = true;
	if (!$query) {
    		exit("Error in query: '$error'");
	} else {

		//get user ID
		//echo $user;
		$q = "SELECT UID FROM logins WHERE User='$user' LIMIT 1;";
		$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
		if (!$query) {
			exit("Error in query: Cannot select UID from logins table");
		} else {
			$UID = $query[0]['UID'];
			//echo $UID;
			//add privliages to the user

			//first gather priv. info
			$q = "SELECT * FROM roles;";
			$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
			
			if (!$query) {
				exit("Error in query: '$error'");
			} else {
				//add the user to the role, if in privlages array says so
				//echo "yay";
				foreach($query as $entry)
				{
					if($privileges[$entry['Description']])
					{
						//add to the database		
						//store privliage
						$rid = $entry['RID'];
						//echo $rid;
						$q = "INSERT INTO logins_roles VALUES($UID,$rid);";
						//echo $q;
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
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

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
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

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
	/*$good=0;

	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");
	//grab login table
	@$q = sqlite_query($dbhandle, 'SELECT * FROM logins') or die ("Cannout use login table");
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
	
	return $good;*/

	return QuickFindUserFromPass($user,$pass);
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
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");
	//grab login table
	@$q = sqlite_query($dbhandle, "SELECT User FROM logins WHERE User='$user';",$error) or die ("Cannout use login table");

	if(!$q){
		exit("Error in Query: '$error'");
		$de = false;
	}

	//if(strcmp($q,$user)==0)
	//	$de = true;

	$num = sqlite_num_rows($q);
	if($num > 0)
		$de = true;

	//close the sql database
	sqlite_close($dbhandle);

	return $de;
}

function RemoveUser($user)
{
	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	$q = "SELECT UID FROM logins WHERE User='$user' LIMIT 1;";
	$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
	if (!$query) {
		exit("Error in query: Cannot find UID from logins table");
	} 

	$UID = $query[0]['UID'];

	$q = "DELETE from logins Where UID=".$UID.";";
	$exec = sqlite_exec($dbhandle,$q,$error);
	if(!$exec)
	{
		exit("Could not delete from logins table");
	}

	$q = "DELETE from logins_roles Where UID=".$UID.";";
	$exec = sqlite_exec($dbhandle,$q,$error);
	if(!$exec)
	{
		exit("Could not delete from logins_roles table");
	}

	//close the sql database
	sqlite_close($dbhandle);
}

function GetPrivFromUser($user)
{
	$privs = 0;

	//add this function later
	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	$q = "SELECT UID FROM logins WHERE User='$user' LIMIT 1;";
	$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
	if (!$query) {
		exit("Error in query: Cannot Select UID from logins table");
	}

	$UID = $query[0]['UID'];
	$q = "SELECT RID FROM logins_roles WHERE UID=$UID;";

	$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
	if (!$query) {
		exit("Error in query: Cannot Select RID from logins_roles table");
	} 

	foreach($query as $entry)
	{
		$privs |= $entry['RID'];
	}

	//close the sql database
	sqlite_close($dbhandle);


	return $privs;
}

function HowManyUsers()
{
	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");
	//grab login table
	@$q = sqlite_query($dbhandle, "SELECT User FROM logins;",$error) or die ("Cannout use login table");

	if(!$q){
		exit("Error in Query: '$error'");
		$de = false;
	}

	$num = sqlite_num_rows($q);

	//close the sql database
	sqlite_close($dbhandle);

	return $num;
}

function ChangePriv($user, $priv)
{
	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	$q = "SELECT UID FROM logins WHERE User='$user' LIMIT 1;";
	$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
	if (!$query) {
		exit("Error in query: Cannot Select UID from logins table");
	} 

	$UID = $query[0]['UID'];

	//delete the users roles
	$q = "DELETE FROM logins_roles WHERE UID=$UID;";
	$sql = sqlite_exec($dbhandle, $q, $error);
	if (!$sql) {
		exit("Error in query: '$error'");
	}

	//first gather priv. info
	$q = "SELECT * FROM roles;";
	$query = sqlite_array_query($dbhandle, $q, SQLITE_ASSOC);
	
	if (!$query) {
		exit("Error in query: Cannot Select * from roles table");
	} else {
		//add the user to the role, if in privlages array says so
		//echo "yay";
		foreach($query as $entry)
		{
			$rid = $entry['RID'];

			if($priv[$entry['Description']])
			{
				//add to the database		
				$q = "INSERT INTO logins_roles VALUES($UID,$rid);";
				$sql = sqlite_exec($dbhandle, $q, $error);
				if (!$sql) {
					exit("Error in query: '$error'");
				}
			}
		}
	}

	//close the sql database
	sqlite_close($dbhandle);
}

function GetAllUsersInfo()
{
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//grab login table
	$q = sqlite_array_query($dbhandle, "SELECT * FROM logins;",SQLITE_ASSOC);

	if(!$q){
		exit("Error in Query: cannot use logins");
	}
	
	$id = 0;

	foreach($q as $entry)
	{
		$ui[$id]['User'] = $entry['User'];
		$ui[$id]['UID'] = $entry['UID'];
		$id++;
	}

	//get roles
	$q = sqlite_array_query($dbhandle, "SELECT * FROM logins_roles;",SQLITE_ASSOC);

	if(!$q){
		exit("Error in Query: cannot use logins");
	}

	foreach($q as $entry)
	{
		for($i=0; $i<$id; $i++)
		{
			if($ui[$i]['UID'] == $entry['UID'])
			{
			    $ui[$i]['RID'] |= $entry['RID'];
			     break;
			}
		}
	}

	//print_r($ui);

	//close the sql database
	sqlite_close($dbhandle);

	return $ui;
}

function CreateSqliteFile()
{

	//connect to database
	@$dbhandle = sqlite_open('/tmp/router.sqlite') or die("Connection Failure to Database");

	//create file
	$q = "CREATE table logins (UID integer primary key, User varchar(100), Password varchar(128));";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not create logins table");
	}

	$q = "create table logins_roles (UID integer references logins(uid) on delete cascade, RID integer references roles(rid) on delete cascade, constraint pk primary key (UID, RID));";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not create logins_roles table");
	}

	$q = "CREATE table roles (RID integer primary key, Description text);";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not create roles table");
	}

	$q = "INSERT INTO roles VALUES(1,'port forwarding');";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not insert into roles table");
	}

	$q = "INSERT INTO roles VALUES(2,'user managment');";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not insert into roles table");
	}

	//close the sql database
	sqlite_close($dbhandle);
}

$validation_struct = array (
	"user" => array(
		"minimum_length" => 2,
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

function rev_strstr($string, $search)
{
	$len = strlen($search);

	$j=0;

	for($i=strlen($string); $i>0; $i--)
	{
		if($search[$len] === $string[$i])
		{
			$count = 0;
			$id = $len;
			
			for($j=$i; $string[$j] == $search[$id] && $j>0 && $id>0; $count++,$id--,$j--);

			if($count==$len)
			{
				//we got a hit
				return $j;
			}
		}
	}

	return -1;
}

?>
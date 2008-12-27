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


function CreateUser($user,$pass)
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
		
	}

	//close it
	sqlite_close($dbhandle);
}

?>

<?php require_once("config.php");



function IsDBEmpty()
{
	//connect to database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//checks how many rows there are in the databasae
	$q = @sqlite_query($dbhandle, 'SELECT * FROM logins;');

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


function CreateUser($user, $pass, $privileges, $email=null)
{
	//echo $user." ".$pass;
	//connect to database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//sha512 the password
	$np = hash('sha512',$pass);

	$q =  "INSERT INTO logins VALUES(null, '$user','$np','$email');";

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

function AddToForgot($uid,$stamp)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$created = date("m/d/Y");
	$expires = date("m/d/Y",strtotime('+1 week'));
	$status = 1;

	$q = "INSERT INTO forgot VALUES('$uid','$status','$created','$expires','$stamp');";
	//echo $q;
	$sql = sqlite_exec($dbhandle, $q, $error);

	if (!$sql) {
		exit("Error in query: '$error'");
	}

	//close it
	sqlite_close($dbhandle);
}

function GetAliveForgets()
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = "SELECT * FROM forgot WHERE Status=1;";

	$query = sqlite_array_query($dbhandle,$q,SQLITE_ASSOC);

	if(!$query)
	{
		return null;
	}

	//close it
	sqlite_close($dbhandle);

	return $query;
}

function ChangeForgotStatusToClaimed($created,$stamp)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = "UPDATE forgot SET Status=0 WHERE Created='$created' AND Stamp='$stamp' AND Status=1;";

	//echo $q;
	$sql = sqlite_exec($dbhandle, $q, $error);

	if (!$sql) {
		exit("Error in query: '$error'");
	}

	//close it
	sqlite_close($dbhandle);
}

function ChangeForgotStatusToExpired($created,$expires)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = "UPDATE forgot SET Status=2 WHERE Created='$created' AND Expires='$expires' AND Status=1;";

	//echo $q;
	$sql = sqlite_exec($dbhandle, $q, $error);

	if (!$sql) {
		exit("Error in query: '$error'");
	}

	//close it
	sqlite_close($dbhandle);
}


function IsPasswordInDB($uid, $hash)
{
	$ret = false;

	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = "SELECT * FROM logins WHERE UID='$uid' AND Password='$hash';";

	$query = sqlite_query($dbhandle,$q,$error);

	if($query)
	{
		if(sqlite_num_rows($query) > 0)
			$ret = true;
	}

	//close it
	sqlite_close($dbhandle);

	return $ret;
}

function IsRulesTableEmpty()
{
	$ret = true;

	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//checks how many rows there are in the databasae
	$q = @sqlite_query($dbhandle, 'SELECT * FROM rules;');

	if($q)
	{
		if(sqlite_num_rows($q) > 0)
			$ret = false;
	}

	//close the datapase
	sqlite_close($dbhandle);

	return $ret;
}

function AddRuleToDB($rule_hash)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q =  "INSERT into rules VALUES(NULL,'$rule_hash');";

	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
	}

	//close the datapase
	sqlite_close($dbhandle);
}

function ChangeRuleInDB($prev_hash, $new_hash)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q =  "UPDATE rules SET Hash='$new_hash' WHERE Hash='$prev_hash';";

	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
	}

	//close the database
	sqlite_close($dbhandle);
}

function RemoveRuleInDB($hash)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q =  "DELETE from rules WHERE Hash='$hash';";

	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
	}

	//close the database
	sqlite_close($dbhandle);
}

function AttachOwnerToRule($user,$rule_hash)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");


	if($user != "-1")
		$q =  "insert into rules_owner VALUES((SELECT UID FROM logins WHERE User='$user' LIMIT 1),(SELECT RUID FROM rules WHERE Hash='$rule_hash' LIMIT 1));";
	else
		$q =  "insert into rules_owner VALUES(-1 ,(SELECT RUID FROM rules WHERE Hash='$rule_hash' LIMIT 1));";

	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
	}

	//close the database
	sqlite_close($dbhandle);
}

function RemoveOwnerFromRule($user,$rule_hash)
{
	//connect to the database
	@$dbhandle = sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q =  "DELETE FROM rules_owner WHERE UID=(SELECT UID FROM logins WHERE User='$user' LIMIT 1) AND RUID=(SELECT RUID FROM rules WHERE Hash='$rule_hash' LIMIT 1);";

	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
	}

	//close the database
	sqlite_close($dbhandle);
}

function GetOwnedRulesFromUser($user)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//$ret = array();
	$retID = 0;
	$q = "";

	if(IsUserAdmin($user))
	{
		//return everything
		if(!CanUserSeeOwnDataOnly($user)) //all owned files viewable?
		{
			$q = "SELECT RUID FROM rules_owner;";
		}
		else
		{
			$q = "SELECT RUID FROM rules_owner WHERE UID>0;";
		}
	}
	else
	{
		//$q = "SELECT UID from logins WHERE User='$user' LIMIT 1;";

//		$query = sqlite_array_query($dbhandle,$q,SQLITE_ASSOC);

//		$id = $query[0]['UID'];

		if(!CanUserSeeOwnDataOnly($user))
		{
		//return only things with the users ID and NULL ID's
			$q = "SELECT RUID FROM rules_owner WHERE UID=(SELECT UID from logins WHERE User='$user' LIMIT 1) OR UID=-1;";
//			$q = "SELECT RUID FROM rules_owner WHERE UID='$id' OR UID=-1;";
		}
		else
		{
			$q = "SELECT RUID FROM rules_owner WHERE UID=(SELECT UID from logins WHERE User='$user' LIMIT 1);";
		//	$q = "SELECT RUID FROM rules_owner WHERE UID='$id';";

		}
	}

	$query = sqlite_array_query($dbhandle,$q,SQLITE_ASSOC);

//	if(!$query)
//	{
		//exit('Cannot retrive RUID from rules_owner table');
//		return null;
//	}

	//if($query)
	//{
	foreach($query as $entry)
	{
		$ret[$retID++] = $entry['RUID'];
	}
	//}


	//close the database
	sqlite_close($dbhandle);

	return $ret;
}

function IsHashInGivenRuleIDs($rules,$hash)
{
	if(!isset($hash))
		return false;

	if(!isset($rules))
		return false;

	//$ret = -1;
	$ret = false;
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = "SELECT RUID FROM rules WHERE Hash='$hash' LIMIT 1;";
	//echo $q."<br>";
	$query = sqlite_array_query($dbhandle,$q,SQLITE_ASSOC);

	if(!$query)
	{
		//exit('Cannot retrive RUID from rules table');
		return false;
	}

	//$stop = false;

/*
	foreach($query as $item)
	{
		$counter = 0;
		foreach($rules as $r)
		{
			//echo $rules[$i]."<br>";
			echo "r: ".$r." item: ".$item['RUID']."<br>";
			if($r == $item['RUID'])
			{
				//echo "yay";
				$ret = $counter;
				$stop = true;
				//echo $counter;
				break;
			}

			$counter++;
		}

		if($stop)
			break;
	}
*/

	//print_r($rules);
	//if(array_search($query[0]['RUID'],$rules))
	if(in_array($query[0]['RUID'],$rules))
	{
		//echo "Found key ".$query[0]['RUID'];
		$ret = true;
	}
	//else
	//{
		//echo "Failed";
	//}


	//close the database
	sqlite_close($dbhandle);

	return $ret;
}

function CheckUserString($user)
{
	//user name must be at lease 2+ characters and only contain numbers or letters.
	preg_match("/^([:alpha:]|[:num:]){2,}$/",$user,$matches);
	if(count($matches) > 0)
	{
	  return true;
	}

	return false;

	//if(eregi("^[a-z0-9 ]+$",$user))
	//	return true;

	//return false;
}

function ChangePassword($user,$newPass)
{
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//hash the password
	$np = hash('sha512',$newPass);

	$q =  "UPDATE logins SET Password='$np' WHERE User='$user';";

	//change password
	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
		return false;
	}

	//close the database
	sqlite_close($dbhandle);

	return true;
}

function ChangeUserName($prev_user, $new_user)
{
	//does the username already exist?
	if(DoesUserExist($new_user))
		return "User name already exists";

	if(strcmp($prev_user,$new_user)==0)
		return "No point of setting it the same user name";

	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//hash the password
	$np = hash('sha512',$newPass);

	$q =  "UPDATE logins SET User='$new_user' WHERE User='$prev_user';";

	//change password
	$query = sqlite_exec($dbhandle, $q, $error);

	if(!$query){
		exit("Error in Query: '$error'");
		return "";
	}

	//close the database
	sqlite_close($dbhandle);

	return "";
}

function QuickFindUserFromPass($user,$pass,$hashit=false)
{
	//test me..................
	$good = false;
	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

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

	//close the database
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
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");
	//grab login table
	$q = @sqlite_query($dbhandle, "SELECT User FROM logins WHERE User='$user';",$error) or die ("Cannout use login table");
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
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

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

	//$q = "DELETE from rules_owner Where UID=".$UID.";";
	$q = "UPDATE rules_owner SET UID=-1 WHERE UID=".$UID.";";
	$exec = sqlite_exec($dbhandle,$q,$error);
	if(!$exec)
	{
		exit("Could not delete from rules_owner table");
	}

	//close the sql database
	sqlite_close($dbhandle);
}

function GetPrivFromUser($user)
{
	$privs = 0;

	//add this function later
	//connect to database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

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
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");
	//grab login table
	$q = @sqlite_query($dbhandle, "SELECT User FROM logins;",$error) or die ("Cannout use login table");

	if(!$q){
		exit("Error in Query: '$error'");
		$de = false;
	}

	$num = sqlite_num_rows($q);

	//close the sql database
	sqlite_close($dbhandle);

	return $num;
}

function GetInfoFromUID($uid)
{
	$ret = null;
	//connect to database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");
	//grab login table
	$q = sqlite_array_query($dbhandle, "SELECT * FROM logins WHERE UID='$uid' LIMIT 1;",SQLITE_ASSOC);

	if($q){
		return $q[0];
	}

	//close the sql database
	sqlite_close($dbhandle);

	return $ret;
}

function ChangePriv($user, $priv)
{
	//print_r($priv);
	//connect to database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

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


function IsValidEmail($email)
{
	//fix this
	//$exp = "([A-Za-z0-9._-]+)@([A-Za-z0-9._-]+)[.]([a-z]{2,4})$";

	//if(eregi($exp,$email))
	//	return true;

	//return false;
	if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$/i',$email)) {
		return TRUE;
    }

	return false;
}

function GetEmail($user)
{
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = sqlite_array_query($dbhandle, "SELECT Email FROM logins WHERE User='$user' LIMIT 1;",SQLITE_ASSOC);

	if(!$q){
		exit("Error in Query: cannot use logins");
		return NULL;
	}

	$email = $q[0]['Email'];

	//close the sql database
	sqlite_close($dbhandle);

	if(isset($email) && $email != null)
		return $email;

	return NULL;
}

function DoesEmailAlreadyExist($email)
{
	$ret = false;

	//connect to the database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//checks how many rows there are in the databasae
	$q = @sqlite_query($dbhandle, "SELECT * FROM logins WHERE Email='$email' LIMIT 1;");

	if($q)
	{
		if(sqlite_num_rows($q) > 0)
			$ret = true;
	}

	//close the datapase
	sqlite_close($dbhandle);

	return $ret;
}

function ChangeEmail($new_email, $user)
{
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = sqlite_exec($dbhandle, "UPDATE logins SET Email='$new_email' WHERE User='$user';",$error);

	if(!$q){
		exit("Error in Query: '$error'");
	}

	//close the sql database
	sqlite_close($dbhandle);
}

function GetInfoFromEmail($email)
{
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	$q = sqlite_array_query($dbhandle, "SELECT * FROM logins WHERE Email='$email' LIMIT 1;",SQLITE_ASSOC);

	if(!$q){
		//exit("Error in Query: cannot use logins");
		return null;
	}

	$email = $q[0]['Email'];

	//close the sql database
	sqlite_close($dbhandle);

	if(isset($email) && $email != null)
		return $q[0];

	return null;
}

function GetAllUsersInfo()
{
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

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
		$ui[$id]['Email'] = $entry['Email'];
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

function IsUserAdmin($user)
{
	return ((GetPrivFromUser($user) & UserMan) >> 1);
}

function CanUserSeeOwnDataOnly($user)
{
	return !((GetPrivFromUser($user) & UserDataOnly) >> 2);
}

function CreateSqliteFile()
{

	//connect to database
	$dbhandle = @sqlite_open(ROUTER_DB_FILE) or die("Connection Failure to Database");

	//create file
	$q = "CREATE table logins (UID integer primary key, User varchar(100), Password varchar(128), Email text);";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not create logins table");
	}

	$q = "create table logins_roles (UID integer references logins(UID) on delete cascade, RID integer references roles(RID) on delete cascade, constraint pk primary key (UID, RID));";
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

	$q = "INSERT INTO roles VALUES(2,'user management');";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not insert into roles table");
	}

	$q = "INSERT INTO roles VALUES(4,'users data only');";
	$exec = sqlite_exec($dbhandle,$q,$error);
	if(!$exec)
	{
		exit("Could not insert into roles table");
	}

	$q = "create table rules (RUID integer primary key, Hash varchar(128));";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not create rules table");
	}

	$q = "create table rules_owner (UID integer references logins(UID) on delete cascade, RUID integer references rules(RUID) on delete cascade, constraint pk primary key(UID,RUID));";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not create rules_owner table");
	}

	$q = "CREATE table forgot (UID integer references logins(UID), Status integer, Created text, Expires text, Stamp varchar(32));";
	$exec = sqlite_exec($dbhandle,$q,$error);

	if(!$exec)
	{
		exit("Could not create forgot table");
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

function ReadLeaseFile()
{

	//format of dhcp lease file
	//timestamp macaddress ip name client-id

	//read file
	if(file_exists(ROUTER_DHCP_LEASE_FILE))
	{
	  $content = file_get_contents(ROUTER_DHCP_LEASE_FILE);
	  $file_line = explode("\n",$content);


	  if(count($file_line) > 0)
	  {

		echo "<table border=1>";
		echo "<tr><td>Timestamp</td><td>Mac Address</td><td>IP</td><td>Name</td><td>Client-ID</td></tr>";

		foreach($file_line as $line)
		{

		  if($line != null)
		  {
			$segments = explode(" ", $line);
			echo "<tr>";
			echo "<td>";
			echo strftime(TIMESTAMP_LEASE_SHOWN,(int)$segments[0])."<br />";
			echo "</td>";
			echo "<td>";
			echo $segments[1];
			echo "</td>";
			echo "<td>";
			echo $segments[2];
			echo "</td>";
			echo "<td>";
			echo $segments[3];
			echo "</td>";
			echo "<td>";
			echo $segments[4];
			echo "</td>";
			echo "</tr>";
		  }
		}

		echo "</table>";
	  }

	  return true;
	}

	return false;
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

function DiffDates($date1,$date2)
{
	//$d1 = explode("/",$date1);
	//$d2 = explode("/",$date2);

	//$start_date = gregoriantojd($d1[0], $d1[1], $d1[2]);
	//$end_date = gregoriantojd($d2[0], $d2[1], $d2[2]);

	//return $end_date - $start_date;

	return ((strtotime($date2) - strtotime($date1) ) / (60 * 60 * 24));
}

?>

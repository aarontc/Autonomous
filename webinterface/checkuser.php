<?php
	function gooduserpass($user,$pass)
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

	function isgoodsession()
	{
		return gooduserpass($_SESSION['Login']['User'],$_SESSION['Login']['Pass']);
	}
?>
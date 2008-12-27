<?php
	function gooduserpass($user,$pass)
	{
		$good=0;

		//connect to mysql
		$handle = mysql_connect("localhost", 'root', 'root') or die("Connection Failure to Database");
		mysql_select_db('router', $handle) or die ('router' . " Database not found." . 'root');
	
		//grab login table
		$sql = "select * from logins";
		$result = mysql_query($sql) or die('Cannot use logins table');
	
		while($row = mysql_fetch_row($result))
		{
			if($user === $row[0] && $pass === $row[1])
			{
				$good = 1;
				break;
			}
			elseif($user === $row[0] && $pass != $row[1])
			{
				$good = -1;
			}
		}
		
		//free results and close the sql database
		mysql_free_result($result);
		mysql_close($handle);
		
		return $good;
	}

	function isgoodsession()
	{
		$good=0;

		$user = $_SESSION['Login']['User'];
		$pass = $_SESSION['Login']['Pass'];

		//connect to mysql
		$handle = mysql_connect("localhost", 'root', 'root') or die("Connection Failure to Database");
		mysql_select_db('router', $handle) or die ('router' . " Database not found." . 'root');
	
		//grab login table
		$sql = "select * from logins";
		$result = mysql_query($sql) or die('Cannot use logins table');
	
		while($row = mysql_fetch_row($result))
		{
			if($user === $row[0] && $pass === $row[1])
			{
				$good = 1;
				break;
			}
			elseif($user === $row[0] && $pass != $row[1])
			{
				//$good = -1;
				break;
			}
		}
		
		//free results and close the sql database
		mysql_free_result($result);
		mysql_close($handle);
		
		return $good;
	}
?>
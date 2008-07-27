<?php
	require ('autonomous.inc.php');
	session_start ();
	ob_start ();
	

?><pre>


<?php

	
	print_r ( $_POST );

	foreach ( $_POST["Action"] as $key => $action ) {
		switch ( $action ) {
			case "Delete":
				echo "DELETING ID $key\n";
				break;
			case "Update":
				echo "Updating ID $key\n";
				break;
			default:
				echo "Unknown action for ID $key\n";
		}
	}
	
?>
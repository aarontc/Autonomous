<?php
	require ('autonomous.inc.php');


	// Get file mtime and cache rules
	clearstatcache ();
	if ( SHOREWALL_RULES_FILE != $_SESSION["RulesFile"]["path"] )
		die ( "Error: cached shorewall rules file is not current shorewall rules file. (cached=" . $_SESSION["RulesFile"]["path"] . ", current=" . SHOREWALL_RULES_FILE . ")" );
	
	if ( filemtime ( SHOREWALL_RULES_FILE ) != $_SESSION["RulesFile"]["mtime"] )
		die ( "Error: file on disk was modified. changes not saved" );
	
	if ( ! isset ( $_SESSION["Rules"] ) )
		die ( "Error: session does not contain previous rules. Cannot continue" );	
	
?><pre>


<?php

	print_r ( $_SESSION["Rules"] ) ;
	
	print_r ( $_POST );

	if ( isset ( $_POST["Action"] ) )
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
	
	// Go through cached rules and detect updates
	for ( $id = 0; $id < count ( $_SESSION["Rules"] ); $id++ ) {
		if ( 
			$_SESSION["Rules"][$id]["Action"] == "DNAT" &&
			$_SESSION["Rules"][$id]["Params"]["Dest"][0] == "loc" &&
			isset ( $_POST["LAN_IP"][$id] )
		) {
			
			if ( $_SESSION["Rules"][$id]["Params"]["Dest"][1] != $_POST["LAN_IP"][$id] ) {
				$updates[$id] = $id;
									echo "update in lan ip for $id\n";

				$_SESSION["Rules"][$id]["Params"]["Dest"][1] = $_POST["LAN_IP"][$id];
				echo "update in lan ip for $id\n";
			}
			
			if ( $_SESSION["Rules"][$id]["Params"]["Dest"][2] != $_POST["LAN_Port_Start"][$id] ) {
				$updates[$id] = $id;
									echo "update in lan port start for $id\n";

				if ( $_POST["LAN_Port_Start"][$id] == "" )
					unset ( $_SESSION["Rules"][$id]["Params"]["Dest"][2] );
				else
					$_SESSION["Rules"][$id]["Params"]["Dest"][2] = $_POST["LAN_Port_Start"][$id];
			}
			
			if ( $_SESSION["Rules"][$id]["Params"]["Dest"][3] != $_POST["LAN_Port_End"][$id] ) {
				$updates[$id] = $id;
									echo "update in lan port end for $id\n";

				if ( $_POST["LAN_Port_End"][$id] == "" )
					unset ( $_SESSION["Rules"][$id]["Params"]["Dest"][3] );
				else
					$_SESSION["Rules"][$id]["Params"]["Dest"][3] = $_POST["LAN_Port_End"][$id];
			}

			if ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][0] != $_POST["WAN_Port_Start"][$id] ) {
				$updates[$id] = $id;
									echo "update in wan port start for $id\n";

				if ( $_POST["WAN_Port_Start"][$id] == "" )
					unset ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][0] );
				else
					$_SESSION["Rules"][$id]["Params"]["DestPorts"][0] = $_POST["WAN_Port_Start"][$id];
			}
			
			if ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][1] != $_POST["WAN_Port_End"][$id] ) {
				$updates[$id] = $id;
									echo "update in wan port end for $id\n";

				if ( $_POST["WAN_Port_End"][$id] == "" )
					unset ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][1] );
				else
					$_SESSION["Rules"][$id]["Params"]["DestPorts"][1] = $_POST["WAN_Port_End"][$id];
			}

			if ( $_SESSION["Rules"][$id]["Params"]["Proto"] != $_POST["Protocol"][$id] ) {
				$updates[$id] = $id;
									echo "update in protocol for $id\n";

				$_SESSION["Rules"][$id]["Params"]["Proto"] = $_POST["Protocol"][$id];
			}

			if ( $_SESSION["Rules"][$id - 1]["Params"]["Comment"] != $_POST["Description"][$id] ) {
				$updates[$id] = $id;
									echo "update in description for $id\n";

				$_SESSION["Rules"][$id - 1]["Params"]["Comment"] = $_POST["Description"][$id];
			}
		}
	}
	
	print_r ( $updates );
	print_r ( $_SESSION["Rules"] );
	
	$fp = fopen ( SHOREWALL_RULES_FILE, "w" );
	for ( $id = 0; $id < count ( $_SESSION["Rules"] ); $id++ ) {
		$rule = $_SESSION["Rules"][$id];
		switch ( $rule["Action"] ) {
			case "#":
				$line = "#" . $rule["Params"]["Comment"] . "\n";
				break;
			case "DNAT":
				$line = "DNAT\t";
				$line .= implode ( $rule["Params"]["Source"], ":" ) . "\t";
				$line .= implode ( $rule["Params"]["Dest"], ":" ) . "\t";
				$line .= $rule["Params"]["Proto"] . "\t";
				$line .= implode ( $rule["Params"]["DestPorts"], ":" ) . "\t";
				$line .= implode ( $rule["Params"]["SourcePorts"], ":" ) . "\t";
				$line .= implode ( $rule["Params"]["OriginalDest"], "," ) . "\t";
				$line .= $rule["Params"]["RateLimit"] . "\t";
				$line .= $rule["Params"]["UserGroup"] . "\t";
				$line .= $rule["Params"]["Mark"] . "\n";
				break;
			default:
				$line = $rule["Action"] . "\t";
				$line .= implode ( $rule["Params"], "\t" ) . "\n";
		}
		if ( $_POST["Action"][$id] != "Delete" )
		fputs ( $fp, $line );
	}
	
	
	unset ( $_SESSION["Rules"] );
?>
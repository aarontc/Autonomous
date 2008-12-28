<?php

	define ( "MAX_STRING_LENGTH", 32768 );
	define ( "SHOREWALL_RULES_FILE", "/tmp/rules" );
	
	session_start ();
	ob_start ();
	
	
		
	function stringTokenize ( $sBuffer, $sSplit ) {
			$iCount = 0;
		
			if ( strlen ( $sBuffer ) == 0)
				return;
		
			$sToken = strtok ( $sBuffer, $sSplit );
			$aTokens[$iCount] = $sToken;
	
			while ( $sToken !== false ) {
				$sToken = strtok ( $sSplit );
				if ( strlen ( $sToken ) > 0 ) {
					$iCount++;
					$aTokens[$iCount] = $sToken;
				}
			}    // end while
		
			return $aTokens;
		}	
	
	function ShorewallGetRules ( $file = "/etc/shorewall/rules" ) {
		if ( ! $fp = fopen ( $file, "r" ) )
			die ( "Unable to access rules file." );
		
		while ( $line = fgets ( $fp, MAX_STRING_LENGTH ) ) {
			$dumpnow = false;
			$line = trim ( $line );
			
			if ( substr ( $line, 0, 1 ) == "#" ) {
				$entry["Action"] = "#";
				$entry["Params"]["Comment"] = substr ( $line, 1 );
// 				if(strlen($entry["Params"]["Comment"]) != 0)
				$entry["Params"]["Comment"] = ereg_replace ( "^ {2,}", "\t", $entry["Params"]["Comment"] );
				$entry["Params"]["Comment"] = ereg_replace ( "^\t{2,}", "\t", $entry["Params"]["Comment"] );
			} else {
				
				$tokens = stringTokenize ( $line, " \t" );
				
				$entry["Action"] = $tokens[0];
				
				switch ( $entry["Action"] ) {
					case "DNAT":
						$entry["Params"]["Source"] = stringTokenize ( $tokens[1], ":" );
						$entry["Params"]["Dest"] = stringTokenize ( $tokens[2], ":" );
						$entry["Params"]["Proto"] = $tokens[3];
						$entry["Params"]["DestPorts"] = stringTokenize ( $tokens[4], ":" );
						$entry["Params"]["SourcePorts"] = stringTokenize ( $tokens[5], ":" );
						$entry["Params"]["OriginalDest"] = stringTokenize ( $tokens[6], "," );
						$entry["Params"]["RateLimit"] = $tokens[7];
						$entry["Params"]["UserGroup"] = $tokens[8];
						$entry["Params"]["Mark"] = $tokens[9];
						break;
					default:
						array_shift ( $tokens );
						$entry["Params"] = $tokens;
				}
			}
			if ( isset ( $prev_entry ) ) {
				if($prev_entry["Action"] == "#" && $entry["Action"] != "#" && substr($prev_entry["Params"]["Comment"], 0, 1) == "\t") { // this is a comment that belongs to an entry
					if(strlen($prev_entry["Params"]["Comment"]) == 0)
						$prev_entry["Params"]["Comment"] = "\tUnnamed rule";
						
					$entry["Params"]["Comment"] = $prev_entry["Params"]["Comment"];
				} else {
					$rules[] = $prev_entry;
// 						unset ( $prev_entry );
				}
			}
			$prev_entry = $entry;
			unset ( $entry );
		} // end while
		$rules[] = $prev_entry;
		return $rules;
	}

	function GenerateDisplayArray ( $rules ) {
		foreach ( $rules as $id => $rule ) {
			if ( $rule["Action"] == "DNAT" && $rule["Params"]["Dest"][0] == "loc" ) {
				$tmp["ID"] = $id;
				$tmp["LAN IP"] = $rule["Params"]["Dest"][1];
				$tmp["LAN Port Start"] = $rule["Params"]["Dest"][2];
				$tmp["LAN Port End"] = $rule["Params"]["Dest"][3];
				$tmp["WAN Port Start"] = $rule["Params"]["DestPorts"][0];
				$tmp["WAN Port End"] = $rule["Params"]["DestPorts"][1];
				$tmp["Protocol"] = $rule["Params"]["Proto"];
				$tmp["Description"] = trim ( $rule["Params"]["Comment"] );
// 				if ( $prevrule["Action"] == "#" )
// 					$tmp["Description"] = implode ( " ", $prevrule["Params"] );
				
				$display[] = $tmp;
				unset ( $tmp );
			}
// 			$prevrule = $rule;
		}
		return $display;
	}
	
	function WriteNewRules()
	{
		// TODO: Make comments NOT pile up!
		
		// Get file mtime and cache rules
		clearstatcache ();
		if ( SHOREWALL_RULES_FILE != $_SESSION["RulesFile"]["path"] ) {
			echo ( "Error: cached shorewall rules file is not current shorewall rules file. (cached=" . $_SESSION["RulesFile"]["path"] . ", current=" . SHOREWALL_RULES_FILE . ")" );
			return;
		}
		
		if ( filemtime ( SHOREWALL_RULES_FILE ) != $_SESSION["RulesFile"]["mtime"] ) {
			echo ( "Error: file on disk was modified. changes not saved" );
			return;
		}
		
		if ( ! isset ( $_SESSION["Rules"] ) ) {
			echo ( "Error: session does not contain previous rules. Cannot continue" );
			return;
		}
		
		// Go through cached rules and detect updates
		$updates = false; // tells us if any updates are to be made to the file
		for ( $id = 0; $id < count ( $_SESSION["Rules"] ); $id++ ) {
			if ( 
				$_SESSION["Rules"][$id]["Action"] == "DNAT" &&
				$_SESSION["Rules"][$id]["Params"]["Dest"][0] == "loc" &&
				isset ( $_POST["LAN_IP"][$id] )
			) {
				if ( $_SESSION["Rules"][$id]["Params"]["Dest"][1] != $_POST["LAN_IP"][$id] ) {
					$updates = true;
					$_SESSION["Rules"][$id]["Params"]["Dest"][1] = $_POST["LAN_IP"][$id];
				}
			
				if ( $_SESSION["Rules"][$id]["Params"]["Dest"][2] != $_POST["LAN_Port_Start"][$id] ) {
					$updates = true;
					if ( $_POST["LAN_Port_Start"][$id] == "" )
						unset ( $_SESSION["Rules"][$id]["Params"]["Dest"][2] );
					else
						$_SESSION["Rules"][$id]["Params"]["Dest"][2] = $_POST["LAN_Port_Start"][$id];
				}
				
				if ( $_SESSION["Rules"][$id]["Params"]["Dest"][3] != $_POST["LAN_Port_End"][$id] ) {
					$updates = true;
					if ( $_POST["LAN_Port_End"][$id] == "" )
						unset ( $_SESSION["Rules"][$id]["Params"]["Dest"][3] );
					else
						$_SESSION["Rules"][$id]["Params"]["Dest"][3] = $_POST["LAN_Port_End"][$id];
				}
	
				if ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][0] != $_POST["WAN_Port_Start"][$id] ) {
					$updates = true;
					if ( $_POST["WAN_Port_Start"][$id] == "" )
						unset ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][0] );
					else
						$_SESSION["Rules"][$id]["Params"]["DestPorts"][0] = $_POST["WAN_Port_Start"][$id];
				}
				
				if ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][1] != $_POST["WAN_Port_End"][$id] ) {
					$updates = true;
					if ( $_POST["WAN_Port_End"][$id] == "" )
						unset ( $_SESSION["Rules"][$id]["Params"]["DestPorts"][1] );
					else
						$_SESSION["Rules"][$id]["Params"]["DestPorts"][1] = $_POST["WAN_Port_End"][$id];
				}
	
				if ( $_SESSION["Rules"][$id]["Params"]["Proto"] != $_POST["Protocol"][$id] ) {
					$updates = true;
					$_SESSION["Rules"][$id]["Params"]["Proto"] = $_POST["Protocol"][$id];
				}
/*
##################################################################################
## The following are two different ways of dealing with blank rule descriptions ##
##                                                                              ##
## 1) Fill the empty rule descriptions with a generic name like "Unnamed rule"  ##
## 2) Leave the empty rule descriptions empty                                   ##
##################################################################################
*/
				$usefirst = false; // decide which one to use
				if($usefirst){
// ######### Posibility number 1 #########
					if($_POST["Description"][$id] == "") // if the rule has no name
						$_POST["Description"][$id] = "Unnamed rule"; // add a generic name
					if ( $_SESSION["Rules"][$id]["Params"]["Comment"] != "\t".$_POST["Description"][$id] ) { // if the original rule description does not equal the new rule description
						$updates = true;
						$_SESSION["Rules"][$id]["Params"]["Comment"] = "\t".$_POST["Description"][$id]; // Change description to new description
					}
// ######### End posibility number 1 #########
				}else{
// ######### Posibility number 2 #########
					if(!(trim($_POST["Description"][$id]) == "" && trim($_SESSION["Rules"][$id]["Params"]["Comment"]) == "") && // are nether the original nor the new rule descriptions empty?
					($_SESSION["Rules"][$id]["Params"]["Comment"] != "\t".trim($_POST["Description"][$id])) // and are they NOT the same?
					){
						$updates = true;
						$_SESSION["Rules"][$id]["Params"]["Comment"] = "\t".$_POST["Description"][$id]; // then set the new rule description
					}
// ######### End posibility number 2 #########
				}
			}
		}
	
// ######### Add new rule if it has a LAN IP #########
// %%% This uses the "$usefirst" bool %%%
		$newruleid = "-1";
		if ( isset ( $_POST["LAN_IP"][$newruleid] ) && strlen ( trim ( $_POST["LAN_IP"][$newruleid] ) ) > 0 ) { // Does the new rule have a LAN IP? (making it potentially valid)
			for ( $id = count ( $_SESSION["Rules"] ) - 1; $id > 0; $id-- ) {
	//		echo "strcasecmp ( \"".$_SESSION["Rules"][$id]["Params"]["Comment"]."\", \"LAST LINE -- ADD YOUR ENTRIES BEFORE THIS ONE -- DO NOT REMOVE\" ) = ".strcasecmp ( $_SESSION["Rules"][$id]["Params"]["Comment"], "LAST LINE -- ADD YOUR ENTRIES BEFORE THIS ONE -- DO NOT REMOVE" );
				if ( $_SESSION["Rules"][$id]["Action"] == "#" && strcasecmp ( $_SESSION["Rules"][$id]["Params"]["Comment"], "LAST LINE -- ADD YOUR ENTRIES BEFORE THIS ONE -- DO NOT REMOVE" ) == 0 ) { // find the last line
					$_SESSION["Rules"][$id+1] = $_SESSION["Rules"][$id];
// 				$id -= 1; // we want the line just before the last line
					if($usefirst && trim($_POST["Description"][$newruleid]) == "")
						$_POST["Description"][$newruleid] = "Unnamed Rule";
					$rule["Action"] = "DNAT";
					$rule["Params"]["Comment"] = "\t".trim($_POST["Description"][$newruleid]);
					$rule["Params"]["Source"][0] = "net";
					$rule["Params"]["Dest"][0] = "loc";
					$rule["Params"]["Dest"][1] = $_POST["LAN_IP"][$newruleid];
					if($_POST["LAN_Port_Start"][$newruleid] != "")
						$rule["Params"]["Dest"][2] = $_POST["LAN_Port_Start"][$newruleid];
					if($_POST["LAN_Port_End"][$newruleid] != "")
						$rule["Params"]["Dest"][3] = $_POST["LAN_Port_End"][$newruleid];
					$rule["Params"]["Proto"] = $_POST["Protocol"][$newruleid];
					$rule["Params"]["DestPorts"][0] = $_POST["WAN_Port_Start"][$newruleid];
					if($_POST["WAN_Port_End"][$newruleid] != "")
						$rule["Params"]["DestPorts"][1] = $_POST["WAN_Port_End"][$newruleid];
// 				$rule["Params"]["SourcePorts"] = null;
// 				$rule["Params"]["OriginalDest"] = null;
// 				$rule["Params"]["RateLimit"] = null;
// 				$rule["Params"]["UserGroup"] = null;
// 				$rule["Params"]["Mark"] = null;

					$_SESSION["Rules"][$id] = $rule;
// 				echo "rule[\"Action\"] = ".$_SESSION["Rules"][$id]["Action"]."\n";
// 				echo "rule[\"Action\"] = ".$_SESSION["Rules"][$id]["Params"]["Comment"]."\n";
					$updates = true;
					break;
				} else {
					$_SESSION["Rules"][$id+1] = $_SESSION["Rules"][$id];
				}
			}
		}
// ######### End Add new rule #########
		if ( isset ( $_POST["Action"] ) ) {
			foreach ( $_POST["Action"] as $action ) {
				if ( $action == "Delete" )
					$updates = true;
			}
		}
		if($updates == false) // there are no updates to be made
			return;
		$fp = fopen ( SHOREWALL_RULES_FILE, "w" );
// 		$linenum = 1;
		for ( $id = 0; $id < count ( $_SESSION["Rules"] ); $id++ ) {
			$rule = $_SESSION["Rules"][$id];
			switch ( $rule["Action"] ) {
				case "#":
					$line = "#" . $rule["Params"]["Comment"] . "\n";
// 					$debuginfo = sprintf($format, $linenum++, $id, $line);
					break;
				case "DNAT":
					$line = "";
	// 					$line = "[".sprintf("%04u", $linenum++)."]".$id."#" . $rule["Params"]["Comment"] . "\n";
	// 				$line .= "[".sprintf("%04u", $linenum++)."]"."DNAT\t";
					if(isset($rule["Params"]["Comment"]))
						$line = "#" . $rule["Params"]["Comment"] . "\n";
					$line .= "DNAT\t";
					@$line .= implode ( $rule["Params"]["Source"], ":" ) . "\t";
					@$line .= implode ( $rule["Params"]["Dest"], ":" ) . "\t";
					$line .= $rule["Params"]["Proto"] . "\t";
					@$line .= implode ( $rule["Params"]["DestPorts"], ":" ) . "\t";
					@$line .= implode ( $rule["Params"]["SourcePorts"], ":" ) . "\t";
					@$line .= implode ( $rule["Params"]["OriginalDest"], "," ) . "\t";
					$line .= $rule["Params"]["RateLimit"] . "\t";
					$line .= $rule["Params"]["UserGroup"] . "\t";
					$line .= $rule["Params"]["Mark"] . "\n";
// 					$npos = strpos($line, "\n") + 1;
// 					if($npos < strlen($line))
// 						$debuginfo = sprintf($format, $linenum++, $id, substr($line, 0, $npos)).sprintf($format, $linenum++, $id, substr($line, $npos));
// 					else
// 						$debuginfo = sprintf($format, $linenum++, $id, $line);
					break;
				case "SECTION":
					$line = $rule["Action"] . " " . implode ( $rule["Params"], " " ) . "\n";
// 					$debuginfo = sprintf($format, $linenum++, $id, $line);
					break;
				default:
					$line = "";
					if(isset($rule["Params"]["Comment"])){
						$line = "#" . $rule["Params"]["Comment"] . "\n";
						unset ( $rule["Params"]["Comment"] );
					}
					$line .= $rule["Action"] . "\t";
					@$line .= implode ( $rule["Params"], "\t" ) . "\n";
// 					$npos = strpos($line, "\n") + 1;
// 					if($npos < strlen($line))
// 						$debuginfo = sprintf($format, $linenum++, $id, substr($line, 0, $npos)).sprintf($format, $linenum++, $id, substr($line, $npos));
// 					else
// 						$debuginfo = sprintf($format, $linenum++, $id, $line);
					break;
			}
// 			if ( $_POST["Action"][$id] != "Delete" )
// 				echo $debuginfo;
			if ( $_POST["Action"][$id] != "Delete" )
				fputs ( $fp, $line );
		}
		fclose ( $fp );
		
		
		unset ( $_SESSION["Rules"] );
	}
?>
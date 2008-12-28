<?php

	define ( "MAX_STRING_LENGTH", 32768 );
	define ( "SHOREWALL_RULES_FILE", "/tmp/rules" );
	
	session_start ();
	ob_start ();
	
	class Rule {
		var $id;
		var $type;
		var $source;
		var $destination;
		var $protocol;
		var $destination_ports;
		var $source_ports;
		var $original_destination;
		var $rate_limit;
		var $user_group;
		var $mark;
		var $comment;
	}
		
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
	
	function ShorewallGetRules_old ( $file = "/etc/shorewall/rules" ) {
		if ( ! $fp = fopen ( $file, "r" ) )
			die ( "Unable to access rules file." );
		
		$need_comment = true;
		while ( $line = fgets ( $fp, MAX_STRING_LENGTH ) ) {
			$line = trim ( $line );
			
			if ( substr ( $line, 0, 1 ) == "#" ) {
				$entry["Action"] = "#";
				$entry["Params"]["Comment"] = substr ( $line, 1 );
				$need_comment = false;
			} else {
				if ( $need_comment ) {
					$rules[] = array ( "Action" => "#", "Params" => array ( "Comment" => "Unnamed rule" ) );
				}
				
				$need_comment = true;
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
			$rules[] = $entry;
			unset ( $entry );
		}
		return $rules;
	}

	function ShorewallGetRules ( $file = "/etc/shorewall/rules" ) {
		$rules = array();
		if ( ! $fp = fopen ( $file, "r" ) )
			die ( "Unable to access rules file." );
		
		// Read all the lines from the file
		$lines = array ();
		while ( $line = fgets ( $fp ) )
			$lines[] = trim ( $line );
		
		for ( $i = 0; $i < count ( $lines ); ++$i ) {
			// If this is a comment line, skip it
			if ( substr ( $line, 0, 1 ) == "#" )
				continue;
			
			$tokens = stringTokenize ( $lines[$i], " \t" );
			$rule = new Rule;
			$rule->id = $i;
			$rule->type = $tokens[0];

			switch ( $rule->type ) {
				case "DNAT":
					$rule->source = stringTokenize ( $tokens[1], ":" );
					$rule->destination = stringTokenize ( $tokens[2], ":" );
					$rule->protocol = $tokens[3];
					$rule->destination_ports = stringTokenize ( $tokens[4], ":" );
					$rule->source_ports = stringTokenize ( $tokens[5], ":" );
					$rule->original_destination = stringTokenize ( $tokens[6], "," );
					$rule->rate_limit = $tokens[7];
					$rule->user_group = $tokens[8];
					$rule->mark = $tokens[9];
					$rule->comment = "Unnamed rule";
					
					if ( $i > 0 )
						if ( substr ( $lines[$i-1], 0, 1 ) == "#" )
							$rule->comment = $lines[$i-1];
					
					$rules[] = $rule;
					break;
				default:

			}	
		}
		return $rules;
	}

?>
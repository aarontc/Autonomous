<?php

	define ( MAX_STRING_LENGTH, 32768 );

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
				if ( $prevrule["Action"] == "#" )
					$tmp["Description"] = implode ( " ", $prevrule["Params"] );
				
				$display[] = $tmp;
				unset ( $tmp );
			}
			$prevrule = $rule;
		}
		return $display;
	}

?>
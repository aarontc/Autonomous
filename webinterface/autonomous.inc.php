<?php

	define ( "SHOREWALL_VERSION", '4.2' );

	define ( "MAX_STRING_LENGTH", 32768 );
	define ( "SHOREWALL_RULES_FILE", "/tmp/rules" );
	
	session_start ();
	ob_start ();
	
		
	// Get file mtime and cache rules
			clearstatcache ();
	$_SESSION["RulesFile"]["path"] = SHOREWALL_RULES_FILE;
	$_SESSION["RulesFile"]["mtime"] = filemtime ( SHOREWALL_RULES_FILE );
	$_SESSION["Rules"] = ShorewallGetRules ( SHOREWALL_RULES_FILE );
	
	ShorewallPutRules ( $_SESSION["Rules"] );
	
	class Rule {
		var $id;
		var $action;
		var $source;
		var $destination;
		var $protocol;
		var $destination_ports;
		var $source_ports;
		var $original_destination;
		var $rate_limit;
		var $user_group;
		var $mark;
		var $connection_limit;
		var $time;
		protected $comment;
		
		function ParseLine ( $line ) {
			echo "PARSING $line";
			$tokens = stringTokenize ( $line, " \t" );
			
			$this->action = $tokens[0];
			$this->source = stringTokenize($tokens[1], ":");
			$this->destination = stringTokenize($tokens[2],":");
			$this->protocol = $tokens[3];
			$this->destination_ports = stringTokenize($tokens[4],":");
			$this->source_ports = stringTokenize($tokens[5],":");
			$this->original_destination = stringTokenize($tokens[6],":");
			$this->rate_limit = $tokens[7];
			$this->user_group = $tokens[8];
			$this->mark = $tokens[9];
			$this->connection_limit = $tokens[10];
			$this->time = $tokens[11];
			
		}
		
		function SetComment( $text ) {
			$text = str_replace ( "\n", "", $text );
			$this->comment = str_replace ( "\r", "", $text );
		}
		
		function ToText() {
			$text = "# " . $comment . "\n";
			$text .= $this->action . "\t";
			$text .= implode(":", $this->source) . "\t";
			$text .= implode(":", $this->destination) . "\t";
			$text .= $this->protocol . "\t";
			$text .= implode(":", $this->destination_ports) . "\t";
			$text .= implode(":", $this->source_ports) . "\t";
			$text .= implode(":", $this->original_destination) . "\t";
			$text .= $this->rate_limit . "\t";
			$text .= $this->user_group . "\t";
			$text .= $this->mark . "\t";
			$text .= $this->connection_limit . "\t";
			$text .= $this->time;
			return $text;
		}
	}
	
	$ACTIONS = array ( 
					 "ACCEPT",
					 "ACCEPT+",
					 "ACCEPT!",
					 "NONAT",
					 "DROP",
					 "DROP!",
					 "REJECT",
					 "REJECT!",
					 "DNAT",
					 "DNAT-",
					 "SAME",
					 "SAME-",
					 "REDIRECT",
					 "REDIRECT-",
					 "CONTINUE",
					 "CONTINUE!",
					 "LOG",
					 "QUEUE");
		
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
	
	/** Creates an array of Rule objects containing all the rules from the
		Shorewall rules file
	*/
	function ShorewallGetRules ( $file = SHOREWALL_RULES_FILE ) {
		$rules = array();
		if ( ! $fp = fopen ( $file, "r" ) )
			die ( "Unable to access rules file." );
		
		// Read all the lines from the file
		$lines = array ();
		while ( $line = fgets ( $fp ) )
			$lines[] = trim ( $line );
		
		print_r ( $lines );
		
		for ( $i = 0; $i < count ( $lines ); ++$i ) {
			// If this is a comment line, skip it
			if ( substr ( $lines[$i], 0, 1 ) == "#" )
				continue;
			
			$rule = new Rule;
			$rule->id = $i;
			echo $lines[$i];
			$rule->ParseLine ( $lines[$i] );
			$rule->SetComment ( "Unnamed Rule" );
			echo $lines[$i];
			
			if ( $i > 0 )
				if ( substr ( $lines[$i-1], 0, 1 ) == "#" )
					$rule->SetComment ( substr($lines[$i-1], 1) );

			$rules[] = $rule;
		}
		print_r ( $rules );
		return $rules;
	}

	function ShorewallPutRules ( $rules, $file = SHOREWALL_RULES_FILE ) {
		if ( ! $fp = fopen ( $file, "w" ) )
			die ( "Unable to access rules file." );
		
		foreach ( $rules as $rule ) {
			fputs ( $fp, $rule->ToText() . "\n" );
		}

		fclose ( $fp );
	}

?>
<?php include('utility.php');

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
	
//	ShorewallPutRules ( $_SESSION["Rules"] );
	
	class Rule {
		var $action;
		var $source = array();
		var $destination = array();
		var $protocol;
		var $destination_ports = array();
		var $source_ports = array ();
		var $original_destination = array ();
		var $rate_limit;
		var $user_group;
		var $mark;
		var $connection_limit;
		var $time;
		protected $comment;

		//NEW
		var $checkSum;
		//
		
		function ParseLine ( $line ) {
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
// 			echo "updating comment from ''$this->comment'' to ''$text''";
			$text = str_replace ( "\n", "", $text );
			$this->comment = trim ( str_replace ( "\r", "", $text ) );
// 			echo "comment now ''$text''";
		}
		function GetComment () {
			return $this->comment;
		}
		
		function ToText() {
			$text = "# " . $this->comment . "\n";
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
			$aTokens = array ();
		
			if ( strlen ( $sBuffer ) == 0)
				return $aTokens;
		
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
		$rulecount = 0;
		if ( ! $fp = fopen ( $file, "r" ) )
			die ( "Unable to access rules file." );
		
		// Read all the lines from the file
		$lines = array ();	

		//NEW
		//$hashs = array();
		//$hashCount = 0;
		//
		
		while ( $line = fgets ( $fp ) )
		{
			$lines[] = trim ( $line );
			
			//NEW
			//$hashs[$hashCount++] = hash('sha512',trim($line));
			//
		}

		for ( $i = 0; $i < count ( $lines ); ++$i ) {
			// If this is a comment line, skip it
			if ( substr ( $lines[$i], 0, 1 ) == "#" )
				continue;
			if ( strlen ( $lines[$i] ) < 2 )
				continue;
			
			$rule = new Rule;
			$rule->ParseLine ( $lines[$i] );
			$rule->SetComment ( "Unnamed Rule" );

			//NEW
			$rule->checkSum = $hashs[$i];
			//

			if ( $i > 0 )
				if ( substr ( $lines[$i-1], 0, 1 ) == "#" )
					$rule->SetComment ( substr($lines[$i-1], 1) );

			$rules[$rulecount++] = $rule;
		}

		//NEW
		for( $i = 0; $i < count ( $rules ); $i++)
		{
			
			$rules[$i]->checkSum = hash('sha512',$rules[$i]->ToText());
		}
		//
		//print_r ( $rules );
		return $rules;
	}

	function ShorewallPutRules ( $rules, $file = SHOREWALL_RULES_FILE ) {
		if ( ! $fp = fopen ( $file, "w" ) )
			die ( "Unable to access rules file." );
		
		//NEW
		$rules_counter = 0;
		//

		foreach ( $rules as $rule ) {
			fputs ( $fp, $rule->ToText() . "\n" );
			
			//NEW
			$tmpHash = $rule->checkSum;
			//echo $tmpHash;

			$rules[$rules_counter]->checkSum = hash('sha512',$rule->ToText());
			//echo $rules[$rules_counter++]->checkSum;

			ChangeRuleInDB($tmpHash,$rule->checkSum);
			//
		}

		fclose ( $fp );
	}
	
	function ShorewallDeleteRule ( $ruleid ) {
		array_splice ( $_SESSION["Rules"], $ruleid, 1 );
	}
	
	function UpdateRules () {
		print_r ( $_POST );
		
		if ( isset ( $_POST['delete'] ) ) {
			foreach ( $_POST['delete'] as $delete => $dummy ) {
				//NEW
				RemoveRuleInDB($delete->checkSum);
				RemoveOwnerFromRule($_SESSION['Login']['User'],$delete->checkSum);
				//
				ShorewallDeleteRule ( $delete );
			}
		} else {

			foreach ( $_SESSION["Rules"] as $ruleid => $rule ) {
				$field = "comment";
				$description = "Name";
				if ( isset ( $_POST[$field] ) ) {
					if ( array_key_exists ( $ruleid, $_POST[$field] ) ) {
						if ( strcmp ( $rule->GetComment(), $_POST[$field][$ruleid] ) != 0 ) {
							$changes[$ruleid][$description]["Old"] = $rule->GetComment();
							$changes[$ruleid][$description]["New"] = $_POST[$field][$ruleid];
							$rule->SetComment($_POST[$field][$ruleid]);
						}
					}
				}
	
				$field = "destination_ip";
				$description = "LAN Computer IP";
				if ( isset ( $_POST[$field] ) ) {
					if ( array_key_exists ( $ruleid, $_POST[$field] ) ) {
						if ( $rule->destination[1] != $_POST[$field][$ruleid] ) {
							$changes[$ruleid][$description]["Old"] = $rule->destination[1];
							$changes[$ruleid][$description]["New"] = $_POST[$field][$ruleid];
							$rule->destination[1] = $_POST[$field][$ruleid];
						}
					}
				}
	
				$field = "protocol";
				$description = "Protocol";
				if ( isset ( $_POST[$field] ) ) {
					if ( array_key_exists ( $ruleid, $_POST[$field] ) ) {
						if ( $rule->protocol != $_POST[$field][$ruleid] ) {
							$changes[$ruleid][$description]["Old"] = $rule->protocol;
							$changes[$ruleid][$description]["New"] = $_POST[$field][$ruleid];
							$rule->protocol = $_POST[$field][$ruleid];
						}
					}
				}
	
				$field = "destination_port_start";
				$description = "LAN Port Start";
				if ( isset ( $_POST[$field] ) ) {
					if ( array_key_exists ( $ruleid, $_POST[$field] ) ) {
						if ( $rule->destination[2] != $_POST[$field][$ruleid] ) {
							$changes[$ruleid][$description]["Old"] = $rule->destination[2];
							$changes[$ruleid][$description]["New"] = $_POST[$field][$ruleid];
							$rule->destination[2] = $_POST[$field][$ruleid];
						}
					}
				}
	
				$field = "destination_port_end";
				$description = "LAN Port End";
				if ( isset ( $_POST[$field] ) ) {
					if ( array_key_exists ( $ruleid, $_POST[$field] ) ) {
						if ( $rule->destination[3] != $_POST[$field][$ruleid] ) {
							$changes[$ruleid][$description]["Old"] = $rule->destination[3];
							$changes[$ruleid][$description]["New"] = $_POST[$field][$ruleid];
							$rule->destination[3] = $_POST[$field][$ruleid];
						}
					}
				}
	
				$field = "wan_port_start";
				$description = "Internet Port Start";
				if ( isset ( $_POST[$field] ) ) {
					if ( array_key_exists ( $ruleid, $_POST[$field] ) ) {
						if ( $rule->destination_ports[0] != $_POST[$field][$ruleid] ) {
							$changes[$ruleid][$description]["Old"] = $rule->destination_ports[0];
							$changes[$ruleid][$description]["New"] = $_POST[$field][$ruleid];
							$rule->destination_ports[0] = $_POST[$field][$ruleid];
						}
					}
				}
	
				$field = "wan_port_end";
				$description = "Internet Port End";
				if ( isset ( $_POST[$field] ) ) {
					if ( array_key_exists ( $ruleid, $_POST[$field] ) ) {
						if ( $rule->destination_ports[1] != $_POST[$field][$ruleid] ) {
							$changes[$ruleid][$description]["Old"] = $rule->destination_ports[1];
							$changes[$ruleid][$description]["New"] = $_POST[$field][$ruleid];
							$rule->destination_ports[1] = $_POST[$field][$ruleid];
						}
					}
				}
	
			}
		}
		
		$nr = new Rule;
		$nr->action = "DNAT";
		$nr->source[0] = "net";
		$nr->protocol = "tcp";
		$nr->destination[0] = "loc";
		$newrule = false;
		// CHECK FOR A NEW RULE
		if ( isset ( $_POST['comment'] ) ) {
			if ( isset ( $_POST['comment']['new'] ) ) {
				if ( strcmp ( $_POST['comment']['new'], "New Rule Description" ) != 0 ) {
					$newrule = true;
					$nr->SetComment($_POST['comment']['new']);
					echo "newcomment";
				}
			}
		}
			
		if ( isset ( $_POST['destination_ip'] ) ) {
			if ( isset ( $_POST['destination_ip']['new'] ) ) {
				if ( strcmp ( $_POST['destination_ip']['new'], "" ) != 0 ) {
					$newrule = true;
					$nr->destination[1] = $_POST['destination_ip']['new'];
					echo "newdestination";
				}
			}
		}
		if ( isset ( $_POST['protocol'] ) ) {
			if ( isset ( $_POST['protocol']['new'] ) ) {
				if ( strcmp ( $_POST['protocol']['new'], "tcp" ) != 0 ) {
					$newrule = true;
					$nr->protocol = $_POST['protocol']['new'];
					echo "newprotocol";
				}
			}
		}
		if ( isset ( $_POST['destination_port_start'] ) ) {
			if ( isset ( $_POST['destination_port_start']['new'] ) ) {
				if ( strcmp ( $_POST['destination_port_start']['new'], "" ) != 0 ) {
					$newrule = true;
					$nr->destination[2] = $_POST['destination_port_start']['new'];
					echo "newdestportstart";
				}
			}
		}
		if ( isset ( $_POST['destination_port_end'] ) ) {
			if ( isset ( $_POST['destination_port_end']['new'] ) ) {
				if ( strcmp ( $_POST['destination_port_end']['new'], "" ) != 0 ) {
					$newrule = true;
					$nr->destination[3] = $_POST['destination_port_end']['new'];
					echo "newdestportend";
				}
			}
		}
		if ( isset ( $_POST['wan_port_start'] ) ) {
			if ( isset ( $_POST['wan_port_start']['new'] ) ) {
				if ( strcmp ( $_POST['wan_port_start']['new'], "" ) != 0 ) {
					$newrule = true;
					$nr->destination_ports[0] = $_POST['wan_port_start']['new'];
					echo "newwanportstart";
				}
			}
		}
		if ( isset ( $_POST['wan_port_end'] ) ) {
			if ( isset ( $_POST['wan_port_end']['new'] ) ) {
				if ( strcmp ( $_POST['wan_port_end']['new'], "" ) != 0 ) {
					$newrule = true;
					$nr->destination_ports[1] = $_POST['wan_port_end']['new'];
					echo "newwanportend";
				}
			}
		}

		if ( $newrule === true ) {
			//if ( strcmp ( $nr->GetComment(), "New Rule Description" ) == 0 )
			//	$nr->SetComment( "UNTITLED RULE PLEASE GIVE ME A NAME" );
			$_SESSION["Rules"][] = $nr;

			//NEW
			$nr->checkSum = hash('sha512',$nr->ToText());
			AddRuleToDB($nr->checkSum);
			
			//get current user
			$user = $_SESSION['Login']['User'];
			AttachOwnerToRule($user,$nr->checkSum);
			//
		}

		echo "<pre>";
		print_r ( $changes );
		echo "</pre>";
		//exit;
		
		ShorewallPutRules ( $_SESSION["Rules"] );
	}

?>
<?php
session_start();
$rules; // global rules container
$messages = Array(); // message container
require("config.php");
getRules();
function getRules()
{
	global $rules;
	$rule_regexp = genRegExp("DNAT");
	@ $rf = file_get_contents(SHOREWALL_RULES_FILE) or die("Failed to open ".SHOREWALL_RULES_FILE."\n");
	if(strlen($rf) == 0) die(SHOREWALL_RULES_FILE." is empty!");
	@ $_SESSION["RULES_FILE"] = $rf = explode("\n", $rf);
	$rflines = count($rf); // assign count() to a variable so it doesn't have to keep calling it...makes this faster?
	for($linenum = 0; $linenum < $rflines; $linenum++) {
		$l = trim($rf[$linenum]);
		if(strlen($l) == 0) continue; // skip blank lines
		if(preg_match("/^SECTION ESTABLISHED|^SECTION ESTABLISHED|^SECTION NEW/i", $l)) {
			$start_start_line = $linenum; // take note of the section start header line number
		}
		if(preg_match("/".$rule_regexp."/", $l, $match)) { // is this a rule?
			$match = array_clean($match); // remove blank array elements (resulting from the regular expression)
			if($linenum - 1 > $start_start_line && preg_match("/^\#[ -\~]+/", $rf[$linenum - 1], $rulename))
				$rulename = trim(substr($rulename, 1));
			else $rulename = "";
			array_unshift($match, $rulename);
			array_unshift($match, $linenum); // prepend line number of where this rule came from
			$rules[] = $match;
			if(IGNORE_WHITESPACE_CHANGE) { // if IGNORE_WHITESPACE_CHANGE == true then recombind the rule so it won't look like white space changed later
				$i = count($rules) - 1;
				$rules[$i][2] = $rules[$i][3]."\t".$rules[$i][4]."\t".$rules[$i][5]."\t".$rules[$i][6]."\t".$rules[$i][7];
				if($rules[$i][8]) $rules[$i][2] .= "\t".$rules[$i][8];
			}
		}
	}
	// [0] => rule line number (56)
	// [1] => rule name (a comment one line above the rule)
	// [2] => entire rule (DNAT net loc:10.0.0.10:1234 tcp 8080)
	// [3] => rule action (DNAT)
	// [4] => source zone (net)
	// [5] => dest zone (loc:10.0.0.10:1234)
	// [6] => protocal (tcp)
	// [7] => dest port (8080)
	// [8] => source port
}
function putRules()
{
	global $messages;
	// TODO: Add support for rule descriptions as comments
	$haschanged = false;
	if(strcasecmp($_SERVER["REQUEST_METHOD"], "POST") != 0) return false; // if this is not a post request, then ignore it
	$n = count($_POST["linenum"]);
	for($r = 0; $r < $n; $r++) {
		$tmp = $_POST["action"][$r]."\t".$_POST["src_zone"][$r]."\t".$_POST["dst_zone"][$r].":".$_POST["destination_ip"][$r]."\t".$_POST["protocol"][$r]."\t".$_POST["wan_port"][$r];
		if($_POST["destination_port"][$r]) $tmp .= "\t".$_POST["destination_port"][$r];
		$tmphash = hash(HASH_METHOD, $tmp);
		if(strcmp($tmphash, $_POST[HASH_METHOD][$r]) != 0) {
			$_SESSION["RULES_FILE"][intval($_POST['linenum'][$r])] = $tmp;
			$haschanged = true;
		}
	}
	if($haschanged) {
		file_put_contents(SHOREWALL_RULES_FILE, implode("\n", $_SESSION["RULES_FILE"]));
		array_push($messages,"Shorewall rules file has been updated.");
	}
}
function getSmallHtmlRule($rulenum = "new")
{
	global $rules;

// [0] = everything
// [1] = zone
// [2] = everything else
	preg_match("/([a-zA-Z]{1,5})(?::([0-9a-zA-Z\:\.,\~\-]+))?/", $rules[$rulenum][4], $src);
	preg_match("/([a-zA-Z]{1,5})(?::([0-9a-zA-Z\:\.,\~\-]+))?/", $rules[$rulenum][5], $dest);
// 	$portIsRange = preg_match("/[^0-9]/", $rules[$rulenum][7]) || strcmp($rules[$rulenum][7],$rules[$rulenum][8]) != 0 ? true : false;
	ob_start();
?>
<div class='smallruleshadow'>
	<span class='tr'></span>
	<span class='bl'></span>
	<span class='br'></span>
	<div class='smallrule'>
		<span class='ruletl'></span>
		<span class='ruletr'></span>
		<span class='rulebl'></span>
		<span class='rulebr'></span>
		<p class='area'>
			<label for='comment[<?=$rulenum?>]'>Name:</label>
			<span class='roundinput'>
				<span class='tl'></span>
				<span class='tr'></span>
				<span class='bl'></span>
				<span class='br'></span>
				<input type='text' class='smallrulename' value='<?=(strlen($rules[$rulenum][1]) ? $rules[$rulenum][1] : (strcmp($rulenum,"new") == 0 ? "New Rule" : "Unnamed Rule"))?>' name='comment[<?=$rulenum?>]' id='comment[<?=$rulenum?>]' title='Rule description goes here.' />
			</span>
			<label for='destination_ip[<?=$rulenum?>]'>LAN IP:</label>
			<span class='roundinput'>
				<span class='tl'></span>
				<span class='tr'></span>
				<span class='bl'></span>
				<span class='br'></span>
				<input type='text' class='ip' value='<?=$dest[2]?>' name='destination_ip[<?=$rulenum?>]' id='destination_ip[<?=$rulenum?>]' title='LAN Computers IP Address.' />
			</span>
			<label for='protocol[<?=$rulenum?>]'>Protocol:</label>
			<span class='roundinput'>
				<span class='tl'></span>
				<span class='tr'></span>
				<span class='bl'></span>
				<span class='br'></span>
				<select name='protocol[<?=$rulenum?>]' id='protocol[<?=$rulenum?>]' title='Select the protocol.'>
					<option value='tcp'<?=(strcasecmp($rules[$rulenum][6], "tcp") == 0 ? " selected" : "")?>>TCP</option>
					<option value='udp'<?=(strcasecmp($rules[$rulenum][6], "udp") == 0 ? " selected" : "")?>>UDP</option>
				</select>
			</span>
			<label for='wan_port[<?=$rulenum?>]'>Public Port:</label>
			<!--<input type='text' class='port' value='<?=($portIsRange ? "range" : $rules[$rulenum][7])?>' name='wan_port[<?=$rulenum?>]' id='wan_port[<?=$rulenum?>]' title='Port.'<?=($portIsRange ? "disabled='true'" : "")?> />-->
			<span class='roundinput'>
				<span class='tl'></span>
				<span class='tr'></span>
				<span class='bl'></span>
				<span class='br'></span>
				<input type='text' class='smallport' value='<?=$rules[$rulenum][7]?>' name='wan_port[<?=$rulenum?>]' id='wan_port[<?=$rulenum?>]' title='Public Port.' />
			</span>
			<label for='destination_port[<?=$rulenum?>]'>LAN Port:</label>
			<span class='roundinput'>
				<span class='tl'></span>
				<span class='tr'></span>
				<span class='bl'></span>
				<span class='br'></span>
				<input type='text' class='smallport' value='<?=$rules[$rulenum][8]?>' name='destination_port[<?=$rulenum?>]' id='destination_port[<?=$rulenum?>]' title='LAN Port.' />
			</span>
			<input type='submit' class='delete' title='Delete this rule' alt='Delete' name='delete[<?=$rulenum?>]' id='delete[<?=$rulenum?>]' value='' />
			<!--<input type='button' class='restore' title='Click for more settings' alt='Minimize' name='minimize[<?=$rulenum?>]' id='minimize[<?=$rulenum?>]' />-->
		</p>
			<?php if(strcmp($rulenum,"new") != 0) { ?>
		<input type='hidden' name='action[<?=$rulenum?>]' value='<?=$rules[$rulenum][3]?>' />
		<input type='hidden' name='src_zone[<?=$rulenum?>]' value='<?=$src[1]?>' />
		<input type='hidden' name='dst_zone[<?=$rulenum?>]' value='<?=$dest[1]?>' />
		<input type='hidden' name='linenum[<?=$rulenum?>]' value='<?=$rules[$rulenum][0]?>' />
		<input type='hidden' name='<?=HASH_METHOD?>[<?=$rulenum?>]' value='<?=hash(HASH_METHOD, $rules[$rulenum][2])?>' />
			<?php } ?>
	</div>
</div>
<?php
	$ret = ob_get_contents();
	ob_end_clean();
	return $ret;
}
function getHtmlRule($rulenum)
{
	global $rules;
	
// [0] = everything
// [1] = zone
// [2] = everything else
	preg_match("/([a-zA-Z]{1,5})(?::([0-9a-zA-Z\:\.,\~\-]+))?/", $rules[$rulenum][4], $src);
	preg_match("/([a-zA-Z]{1,5})(?::([0-9a-zA-Z\:\.,\~\-]+))?/", $rules[$rulenum][5], $dest);
	
	ob_start();
?>
<div class='ruleshadow'>
	<div class='rule'>
		<div class='ruletitle'>
			<input type='text' value='<?=(strlen($rules[$rulenum][1]) ? $rules[$rulenum][1] : "Unnamed Rule")?>' name='comment[<?=$rulenum?>]' id='comment[<?=$rulenum?>]' title='Rule description goes here.' />
			<input type='submit' class='delete' title='Delete this rule' alt='Delete' name='delete[<?=$rulenum?>]' id='delete[<?=$rulenum?>]' value='' onclick='test()' />
			<input type='button' class='minimize' title='Click for small view' alt='Minimize' name='minimize[<?=$rulenum?>]' id='minimize[<?=$rulenum?>]' />
		</div>
		<div class='rulebody'>
			<div class='area'>
				<p>
					<label for='destination_ip[<?=$rulenum?>]'>LAN Computer IP:</label>
					<input type='text' class='ip' value='<?=$dest[2]?>' name='destination_ip[<?=$rulenum?>]' id='destination_ip[<?=$rulenum?>]' title='*REQUIRED* LAN Computers IP Address.' />
				</p>
				<p>
					<label for='protocol[<?=$rulenum?>]'>Protocol:</label>
					<select name='protocol[<?=$rulenum?>]' id='protocol[<?=$rulenum?>]' title='Select the protocol.'>
						<option value='tcp'<?=(strcasecmp($rules[$rulenum][6],"tcp") == 0 ? " selected" : "")?>>TCP</option>
						<option value='udp'<?=(strcasecmp($rules[$rulenum][6],"udp") == 0 ? " selected" : "")?>>UDP</option>
					</select>
				</p>
			</div>
			<div class='area'>
				<p>
					<label for='destination_port[<?=$rulenum?>]'>LAN Port:</label>
					<input type='text' class='port' value='<?=$rules[$rulenum][8]?>' name='destination_port[<?=$rulenum?>]' id='destination_port[<?=$rulenum?>]' title='*OPTIONAL* LAN Computers Destination Port or Ports.' />
					<!--<input type='text' class='port' value='' name='destination_port_end[<?=$rulenum?>]' id='destination_port_end[<?=$rulenum?>]' title='*OPTIONAL* If there is a range of ports for the LAN Computers Destination Port, this is the ending port.' />-->
				</p>
				<p>
					<label for='wan_port[<?=$rulenum?>]'>Public Port:</label>
					<input type='text' class='port' value='<?=$rules[$rulenum][7]?>' name='wan_port[<?=$rulenum?>]' id='wan_port[<?=$rulenum?>]' title='*REQUIRED* Internet Port or Ports.' />
					<!--<input type='text' class='port' value='' name='wan_port_end[<?=$rulenum?>]' id='wan_port_end[<?=$rulenum?>]' title='*OPTIONAL* If there is a range for Internet ports, then this is the end port.' />-->
				</p>
			</div>
			<input type='hidden' name='action[<?=$rulenum?>]' value='<?=$rules[$rulenum][3]?>' />
			<input type='hidden' name='src_zone[<?=$rulenum?>]' value='<?=$src[1]?>' />
			<input type='hidden' name='dst_zone[<?=$rulenum?>]' value='<?=$dest[1]?>' />
			<input type='hidden' name='linenum[<?=$rulenum?>]' value='<?=$rules[$rulenum][0]?>' />
			<input type='hidden' name='<?=HASH_METHOD?>[<?=$rulenum?>]' value='<?=hash(HASH_METHOD, $rules[$rulenum][2])?>' />
		</div>
	</div>
</div>
<?php
	$ret = ob_get_contents();
	ob_end_clean();
	return $ret;
}

function genRegExp($inc_act = "ACCEPT,NONAT,DROP,REJECT,DNAT,SAME,REDIRECT,CONTINUE,LOG,QUEUE,NFQUEUE,COMMENT")
{
	/* SECTIONS
	ESTABLISHED
	The only ACTIONs allowed in this section are ACCEPT, DROP, REJECT, LOG and QUEUE

	RELATED
	The only ACTIONs allowed in this section are ACCEPT, DROP, REJECT, LOG and QUEUE

	NEW
	Packets in the NEW and INVALID states are processed by rules in this section.
	*/
	/*
	ACTION - {ACCEPT[+|!]|NONAT|DROP[!]|REJECT[!]|DNAT[-]|SAME[-]|REDIRECT[-]|CONTINUE[!]|LOG|QUEUE[!]|NFQUEUE[(queuenumber)]|COMMENT|action|macro[(target)]}[:{log-level|none}[!][:tag]]
	SOURCE - {zone|all[+][-]}[:interface][:{address-or-range[,address-or-range]...[exclusion]|exclusion|+ipset}
	DEST - {zone|all[+][-]}[:{interface|address-or-range[,address-or-range]...[exclusion]|exclusion|+ipset}][:port[:random]]
	PROTO (Optional) - {-|tcp:syn|ipp2p|ipp2p:udp|ipp2p:all|protocol-number|protocol-name|all}
	DEST PORT(S) (Optional) - {-|port-name-number-or-range[,port-name-number-or-range]...}
	SOURCE PORT(S) (Optional) - {-|port-name-number-or-range[,port-name-number-or-range]...}
	ORIGINAL DEST (Optional) - [-|address[,address]...[exclusion]|exclusion]
	RATE LIMIT (Optional) - [-|rate/{sec|min}[:burst]
	USER/GROUP (Optional) - [!][user-name-or-number][:group-name-or-number][+program-name]
	MARK - [!]value[/mask][:C]
	CONNLIMIT - [!]limit[:mask]
	TIME - timeelement[,timelement...]
	*/
// 	echo "inc_act='$inc_act'";
	if(stripos($inc_act, "ACCEPT") !== false) $actions[] = "ACCEPT[!+]?";
	if(stripos($inc_act, "NONAT") !== false) $actions[] = "NONAT";
	if(stripos($inc_act, "DROP") !== false) $actions[] = "DROP[!]?";
	if(stripos($inc_act, "REJECT") !== false) $actions[] = "REJECT[!]?";
	if(stripos($inc_act, "DNAT") !== false) $actions[] = "DNAT[-]?";
	if(stripos($inc_act, "SAME") !== false) $actions[] = "SAME[-]?";
	if(stripos($inc_act, "REDIRECT") !== false) $actions[] = "REDIRECT[-]?";
	if(stripos($inc_act, "CONTINUE") !== false) $actions[] = "CONTINUE[!]?";
	if(stripos($inc_act, "LOG") !== false) $actions[] = "LOG";
	if(stripos($inc_act, "QUEUE") !== false) $actions[] = "QUEUE[!]?";
	if(stripos($inc_act, "NFQUEUE") !== false) $actions[] = "NFQUEUE[0-9]*";
	if(stripos($inc_act, "COMMENT") !== false) $actions[] = "COMMENT";

	$sep = "[ \\t]+"; // regexp for the rule component seperators

	$zones = getValidZones(); // get valid zones from zones file
	$add = "[\+\-0-9a-fA-F,:\!\/\.]*";
	for($i = 0; $i < count($zones); $i++) $zones[$i] .= $add;
	$zones[] = "\\\$FW";
	$zones[] = "all";
	$zones = implode($zones, "|");

	// $proto = "-|tcp(:syn)?|udp|ipp2p|ipp2p:udp|ipp2p:all|protocol-number|protocol-name|all";
	$proto = "[a-zA-Z0-9:]+|\-";

	$port = "[0-9]+(?::[0-9]+)?|\-";

	for($i = 0; $i < count($actions); $i++)
		$rule_regexp[] = "^(".$actions[$i].")".$sep."(".$zones.")".$sep."(".$zones.")(?:".$sep."(".$proto."))?(?:".$sep."(".$port."))?(?:".$sep."(".$port."))?";
	$rule_regexp = implode($rule_regexp, "|");

	return $rule_regexp;
}

function getValidZones()
{
	if(($zf = fopen(SHOREWALL_ZONES_FILE, 'r')) === false) return "";
	while($l = fgets($zf)) {
		$l = trim($l);
		if(preg_match("/^[a-zA-Z]+\:?[a-zA-Z]*,?[a-zA-Z]*/", $l, $m))
			$zones[] = $m[0];
	}
	fclose($zf);
	return $zones;
}
function array_clean($ary) // remove blank elements from array (this include elements filled with just white space)
{
	for($i = count($ary) - 1; $i >= 0; $i--)
		if(strlen(trim($ary[$i])) == 0)
			array_splice($ary, $i, 1);
	return $ary;
}
?>
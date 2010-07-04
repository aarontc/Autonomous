<pre><?php


$pattern_ipv4_octet="([0-9]|[0-9]{2}|(0|1)[0-9]{2}|2[0-4][0-9]|25[0-5])";
$pattern_ipv4_cidr_mask_bits="([0-9]|(0|1|2)[0-9]|3[0-2])";

$pattern_ipv4_address="((".$pattern_ipv4_octet."\.){3}".$pattern_ipv4_octet.")";	// Just 1.2.3.4 style IP
$pattern_ipv4_address_cidr="(".$pattern_ipv4_address."(\/".$pattern_ipv4_cidr_mask_bits."){0,1})";	// 1.2.3.4 or 1.2.3.4/8

$pattern_ipv4_address_or_range = "(".$pattern_ipv4_address_cidr."|".$pattern_ipv4_address."-".$pattern_ipv4_address.")";


$pattern_shorewall_exclusion="(!$pattern_ipv4_address_or_range(,$pattern_ipv4_address_or_range)*)";

// 0 to 65535
$pattern_port="([0-9]{1,4}|[0-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])";

$pattern_port_or_range_hyphen="(".$pattern_port."|".$pattern_port."-".$pattern_port.")";
$pattern_port_or_range_colon="(".$pattern_port."|".$pattern_port.":".$pattern_port.")";


	$test = $_GET['test'];

	preg_match_all("/^" . $pattern_port_or_range_colon . "$/", $test, $matches);
	print_r($matches);

?>
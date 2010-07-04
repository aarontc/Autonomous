<pre><?php


$pattern_ipv4_octet="([0-9]|[0-9]{2}|(0|1)[0-9]{2}|2[0-4][0-9]|25[0-5])";
$pattern_ipv4_cidr_mask_bits="([0-9]|(0|1|2)[0-9]|3[0-2])";

$pattern_ipv4_address="((".$pattern_ipv4_octet."\.){3}".$pattern_ipv4_octet.")";	// Just 1.2.3.4 style IP
$pattern_ipv4_address_cidr="(".$pattern_ipv4_address."(\/".$pattern_ipv4_cidr_mask_bits."){0,1})";	// 1.2.3.4 or 1.2.3.4/8

$pattern_ipv4_address_or_range = "(".$pattern_ipv4_address_cidr."|".$pattern_ipv4_address."-".$pattern_ipv4_address.")";

	$test = $_GET['test'];

	preg_match_all("/^" . $pattern_ipv4_address_or_range . "$/", $test, $matches);
	print_r($matches);

?>
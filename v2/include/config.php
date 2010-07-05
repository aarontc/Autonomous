<?php
define("SHOREWALL_RULES_FILE", "/tmp/rules");
// define("SHOREWALL_ACTIONS_FILE", "/etc/shorewall/actions"); // not used yet
// define("SHOREWALL_ZONES_FILE", "/etc/shorewall/zones");
define("SHOREWALL_ZONES_FILE", "/tmp/zones");
define("SHOREWALL_INTERFACES_FILE", "/tmp/interfaces");
define("SERVICES_FILE", "/etc/services");
define("PROTOCOLS_FILE", "/etc/protocols");
define("IGNORE_WHITESPACE_CHANGE", true);
define("HASH_METHOD", "md5");
define("NEW_RULE", "button"); // this can be ether "blank_rule" or "button"
?>
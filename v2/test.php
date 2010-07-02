#!/usr/bin/php
<?php
$foo = "1234,2345-3456";
$dest = preg_match("/[^0-9]/", $foo);
echo $dest, "\n";
?>
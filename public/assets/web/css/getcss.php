<?php
// print_r($_SERVER);
$css = preg_replace('/^[^a-z]+/','',$_SERVER["QUERY_STRING"]);
$css1 = explode(".",$css);
$file_less = $css1[0] . ".less";

if(file_exists($file_less)){
	require "less.inc.php";
	$less = new lessc;
	header("Content-type: text/css", true);
	echo $less->compileFile($file_less);
} else {
	header("Content-type: text/css", true);
	echo file_get_contents($css);
}
exit;
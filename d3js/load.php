<?php

// This can be done on linux servers to get the avg cpu load.
//$load = shell_exec("uptime | sed 's/.*load average: //' | awk -F\, '{print $2}'")

header('Content-type: application/json');

// On windows I am generating random numbers.

$randomVal = (float)rand()/(float)getrandmax();

$out = array(
	'value' => $randomVal * 1.9,
	'timestamp' => time() + 61,
    'success' => true
);
$out = json_encode($out);
echo $out;

?>
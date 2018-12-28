<?php
include_once('../../config.php');
include_once('../search.php');

$params = [];
parse_str($_SERVER['QUERY_STRING'], $params);
$a = HawkSearch::proxy($params);
echo $a;

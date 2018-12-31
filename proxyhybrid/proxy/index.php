<?php
include_once('../../config.php');
include_once('../model.php');
include_once('../search.php');
include_once('../render.php');
$params = [];
parse_str($_SERVER['QUERY_STRING'], $params);
$a = HawkSearch::search($params);
$render = new Render($a);
$r = $render->proxy_results(); // render proxy

echo $r;

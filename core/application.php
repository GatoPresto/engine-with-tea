<?php
session_start();
require_once(dirname(__FILE__).'/application_base.php');

$httpRequest = new HttpRequestCore;

$route = new RoutingCore;
$route->specifiesRequested();
?>
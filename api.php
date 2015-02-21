<?php
	header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");

    $args = $_REQUEST;
	$method = $_SERVER['REQUEST_METHOD'];

	include_once('class/MonumentAPI.class.php');
	$api = new MonumentAPI($args, $method);
	$json = $api->processAPI();

	echo $json;
?>
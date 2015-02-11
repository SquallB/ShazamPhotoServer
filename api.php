<?php
	header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");

	$json = '{}';
	$method = $_SERVER['REQUEST_METHOD'];

	if($method === 'POST') {
		$args = $_POST;
	}
	else if($method === 'GET') {
		$args = $_GET;
	}


	include_once('class/MonumentAPI.class.php');
	$api = new MonumentAPI($args, $method);
	$json = $api->processAPI();

	echo $json;

?>
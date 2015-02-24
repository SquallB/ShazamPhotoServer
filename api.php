<?php
	header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json; charset=utf-8");

    $method = $_SERVER['REQUEST_METHOD'];

    if($method === 'GET') {
    	$args = $_GET;
    }
    else if($method === 'POST') {
    	$args = $_POST;
    }

	include_once('class/MonumentAPI.class.php');
	$api = new MonumentAPI($args, $method);
	$json = $api->processAPI();

	echo $json;
?>
<?php
	header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Content-Type: application/json; charset=utf-8');

    $method = $_SERVER['REQUEST_METHOD'];

    //Depending on the http method used, we get the right arguments.
    if($method === 'GET') {
    	$args = $_GET;
    }
    else if($method === 'POST') {
    	$args = $_POST;
    }
    else if($method === 'PUT') {
        parse_str(file_get_contents("php://input"), $args);
    }
    else if($method === 'DELETE') {
        parse_str(file_get_contents("php://input"), $args);
    }

    //Processing the API with the method and the arguments.
	include_once('class/MonumentAPI.class.php');
	$api = new MonumentAPI($args, $method);
	$json = $api->processAPI();

	echo $json;
?>

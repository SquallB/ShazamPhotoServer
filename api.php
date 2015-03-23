<?php
	header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT");
    header("Content-Type: application/json; charset=utf-8");

    $method = $_SERVER['REQUEST_METHOD'];

    if($method === 'GET') {
    	$args = $_GET;
    }
    else if($method === 'POST') {
    	$args = $_POST;

        if(isset($_FILES['photo'])) {
            $args['photo'] = $_FILES['photo'];
        }
    }
    else if($method === 'PUT') {
        parse_str(file_get_contents("php://input"), $args);
    }

	include_once('class/MonumentAPI.class.php');
	$api = new MonumentAPI($args, $method);
	$json = $api->processAPI();

	echo $json;
?>

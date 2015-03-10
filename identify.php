<?php

header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json; charset=utf-8");

include_once('class/ListKeyPoints.class.php');
include_once('class/MonumentDAO.class.php');

$json = '{}';

if(isset($_POST['keypoints']) && isset($_POST['descriptor'])) {
	$listKeyPoints = new ListKeyPoints(array("keypoints" => json_decode($_POST['keypoints'], true)));
	$descriptor = new Descriptor(json_decode($_POST['descriptor'], true));
	$dao = new MonumentDAO();
	$monuments = $dao->findAll();

	foreach($monuments as $monument) {
		$arg1 = '{}';
		$arg2 = '{}';
		
		if(exec('../cpp/compare ' + $arg1 + ' ' + $arg2)) {
			$json = json_encode($monument);
		}
	}
}
else {
	$json = '{"error", "arguments not found"}';
}

echo $json

?>
<?php

header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json; charset=utf-8");

include_once('class/ListKeyPoints.class.php');
include_once('class/MonumentDAO.class.php');

$json = '{}';

if(isset($_POST['listskeypoints']) && isset($_POST['descriptors'])) {
	$file = fopen('../cpp/arg1.txt', 'w');
	fwrite($file, $_POST['listskeypoints']);
	fclose($file);

	$file = fopen('../cpp/arg2.txt', 'w');
	fwrite($file, substr($_POST['descriptors'], 1, strlen($_POST['descriptors']) - 2));
	fclose($file);

	$dao = new MonumentDAO();

	if(isset($_POST['localization'])) {
		$localization = json_decode($_POST['localization'], true);
		$monuments = $dao->searchByLocalization($localization['latitude'], $localization['longitude'], 0.01);
	}
	else {
		$monuments = $dao->findAll();
	}

	$ratio = 0.1;

	foreach($monuments as $monument) {
		if(count($monument->getListsKeypoints()) > 0 && count($monument->getDescriptors()) > 0) {
			$file = fopen('../cpp/arg3.txt', 'w');
			fwrite($file, json_encode(array('keypoints' => $monument->getListsKeypoints()[0]->getKeyPoints()), JSON_NUMERIC_CHECK));
			fclose($file);

			$file = fopen('../cpp/arg4.txt', 'w');
			fwrite($file, json_encode($monument->getDescriptors()[0], JSON_NUMERIC_CHECK));
			fclose($file);

			$result = exec('../cpp/compare');

			if($result > $ratio) {
				$ratio = $result;
				$foundMonument = $monument;
			}
		}
	}

	if($foundMonument !== null) {
		$json = json_encode($foundMonument);
	}
}
else {
	$json = '{"error", "arguments not found"}';
}

echo $json

?>
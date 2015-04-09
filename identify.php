<?php

header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: POST");
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
		$monuments = $dao->searchByLocalization($localization['latitude'], $localization['longitude'], 0.001);
	}
	else {
		$monuments = $dao->findAll();
	}

	$ratio = 0.002;

	$file2 = fopen('ratio.txt', 'a');
	fwrite($file2, date("j M G:i:s"));

	foreach($monuments as $monument) {
		$listsKeyPoints = $monument->getListsKeypoints();
		$descriptors = $monument->getDescriptors();
		$size = count($listsKeyPoints);
		if(count($descriptors) < $size) {
			$size = count($descriptors);
		}

		for($i = 0; $i < $size; $i++) {
			$file = fopen('../cpp/arg3.txt', 'w');
			fwrite($file, json_encode(array('keypoints' => $listsKeyPoints[$i]->getKeyPoints()), JSON_NUMERIC_CHECK));
			fclose($file);

			$file = fopen('../cpp/arg4.txt', 'w');
			fwrite($file, json_encode($descriptors[$i], JSON_NUMERIC_CHECK));
			fclose($file);

			unset($result);
			exec('../cpp/compare', $result);

			if(isset($result[0]) && isset($result[1])) {
				fwrite($file2, $result[0] . " " . $result[1] . " ");

				if($result[0] > $ratio || $result[1] > $ratio) {
					$ratio = $result;
					$foundMonument = $monument;
				}
			}
		}
	}

	fwrite($file2, PHP_EOL);
	fclose($file2);

	if(isset($foundMonument)) {
		$json = json_encode($foundMonument);
	}
}
else {
	$json = '{"error", "arguments not found"}';
}

echo $json

?>
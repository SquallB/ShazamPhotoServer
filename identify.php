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
		$monumentsIds = $dao->findIdsByLocalization($localization['latitude'], $localization['longitude'], 0.002);
	}
	else {
		$monumentsIds = $dao->findIds();
	}

	$ratio = 0.1;
	$maxRatio = 0;

	$file2 = fopen('ratio.txt', 'a');
	fwrite($file2, date("j M G:i:s"));

	foreach($monumentsIds as $monumentId) {
		$monument = $dao->find($monumentId);
		$listsKeyPoints = $monument->getListsKeypoints();
		$descriptors = $monument->getDescriptors();
		$size = count($listsKeyPoints);
		if(count($descriptors) < $size) {
			$size = count($descriptors);
		}
		fwrite($file2, ' ' . $monument->getCharacteristics()[0]->getName() . ' (');
		$tmpMaxRatio = 0;
		for($i = 0; $i < $size; $i++) {
			unset($result);

			$file = fopen('../cpp/arg3.txt', 'w');
			fwrite($file, json_encode(array('keypoints' => $listsKeyPoints[$i]->getKeyPoints()), JSON_NUMERIC_CHECK));
			fclose($file);

			$file = fopen('../cpp/arg4.txt', 'w');
			fwrite($file, json_encode($descriptors[$i], JSON_NUMERIC_CHECK));
			fclose($file);

			exec('../cpp/compare', $result);

			if(isset($result[0]) && $result[0] > $tmpMaxRatio){
				$tmpMaxRatio = $result[0];
				fwrite($file2, ' ' . $result[0]);
			}
			if(isset($result[1]) && $result[1] > $tmpMaxRatio){
				$tmpMaxRatio = $result[1];
				fwrite($file2, ' ' . $result[1]);
			}
		}

		fwrite($file2, ')');
		
		if($tmpMaxRatio >$maxRatio ) {
			$maxRatio = $tmpMaxRatio;
			$foundMonument = $monument;
		}
	}

	fwrite($file2, PHP_EOL);
	fclose($file2);

	if(isset($foundMonument) && ($maxRatio > $ratio)) {
		$json = json_encode($foundMonument);
	}
}
else {
	$json = '{"error", "arguments not found"}';
}

echo $json

?>
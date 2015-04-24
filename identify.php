<?php

header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=utf-8");

include_once('class/ListKeyPoints.class.php');
include_once('class/MonumentDAO.class.php');

$json = '{}';

//The identification script needs both descriptors and key points to work,
//we'll return an error if either of them isn't found.
if(isset($_POST['listskeypoints']) && isset($_POST['descriptors'])) {
	//Putting the key points into a file so that it can be used by the C program.
	$file = fopen('../cpp/arg1.txt', 'w');
	fwrite($file, $_POST['listskeypoints']);
	fclose($file);

	//Same thing for the descriptor.
	$file = fopen('../cpp/arg2.txt', 'w');
	fwrite($file, substr($_POST['descriptors'], 1, strlen($_POST['descriptors']) - 2));
	fclose($file);

	//If there is localisation data, we'll only get the monuments around the given localization,
	//otherwise we'll get all monuments (we get only the Ids so that we don't get all the data at once).
	$dao = new MonumentDAO();
	if(isset($_POST['localization'])) {
		$localization = json_decode($_POST['localization'], true);
		$monumentsIds = $dao->findIdsByLocalization($localization['latitude'], $localization['longitude'], 0.002);
	}
	else {
		$monumentsIds = $dao->findIds();
	}

	//$ratio corresponds to the minimum ratio required for a monument to be considered as a valid answer.
	$ratio = 0.13;
	$maxRatio = 0;

	//We can now go through all monuments and compare them with the given datas.
	foreach($monumentsIds as $monumentId) {
		$monument = $dao->find($monumentId);
		$listsKeyPoints = $monument->getListsKeypoints();
		$descriptors = $monument->getDescriptors();
		$size = count($listsKeyPoints);
		if(count($descriptors) < $size) {
			$size = count($descriptors);
		}
		$tmpMaxRatio = 0;
		for($i = 0; $i < $size; $i++) {
			unset($result);

			//As before, we're putting the key points into a file.
			$file = fopen('../cpp/arg3.txt', 'w');
			fwrite($file, json_encode(array('keypoints' => $listsKeyPoints[$i]->getKeyPoints()), JSON_NUMERIC_CHECK));
			fclose($file);

			//Same thing for the descriptors
			$file = fopen('../cpp/arg4.txt', 'w');
			fwrite($file, json_encode($descriptors[$i], JSON_NUMERIC_CHECK));
			fclose($file);

			//This command is where we actually execute the C program. The returned ratios are stored into $result.
			exec('../cpp/compare', $result);

			//Updating the current maximum ratio.
			if(isset($result[0]) && $result[0] > $tmpMaxRatio){
				$tmpMaxRatio = $result[0];
			}
			if(isset($result[1]) && $result[1] > $tmpMaxRatio){
				$tmpMaxRatio = $result[1];
			}
		}
		
		//Updating the maximum ratio, as well as the monument that will be returned.
		if($tmpMaxRatio >$maxRatio ) {
			$maxRatio = $tmpMaxRatio;
			$foundMonument = $monument;
		}
	}

	if(isset($foundMonument) && ($maxRatio > $ratio)) {
		$json = json_encode($foundMonument);
	}
}
else {
	$json = '{"error", "arguments not found"}';
}

echo $json

?>
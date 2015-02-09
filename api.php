<?php
	header("Content-Type: application/json");

	$json = array();
	$method = $_SERVER['REQUEST_METHOD'];

	if($method === 'POST') {
		$queries = $_POST;
	}
	else if($method === 'GET') {
		$queries = $_GET;
	}


	if(count($queries) > 0) {
		foreach($queries as $key => $value) {
			$key = htmlspecialchars($key);
			$value = htmlspecialchars($value);

			switch($key) {
				case "n":
				$column = $key;
				$argument = $value;
				break;
			}

			if(isset($column) && isset($argument)) {
				include_once('class/MonumentDAO.class.php');
				$dao = new MonumentDAO();
				$monuments = $dao->findAll();

				foreach($monuments as $monument) {
					$found = false;
					foreach($monument->getCharacteristics() as $characteristic) {
						if ($found === false && strpos($characteristic->getName(), $argument) !== false) {
						    $json[] = $monument;
						    $found = true;
						}
					}
				}
			}
		}
	}

	echo json_encode(array("Search" => $json));

?>
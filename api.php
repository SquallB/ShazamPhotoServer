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
				include('pgconf.php');
				$db = pg_connect($pgconf);
				$result = pg_prepare($db, "my_query", 'SELECT * FROM monument WHERE name LIKE $1;');
				$result = pg_execute($db, "my_query", array('%'.$argument.'%'));

				if($result) {
					while($row = pg_fetch_assoc($result)) {
						$json[] = $row;
					}
				}

				pg_close($db);
			}
		}
	}

	echo json_encode(array("Search" => $json));

?>
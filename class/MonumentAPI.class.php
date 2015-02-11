<?php

include_once('API.class.php');
include_once('MonumentDAO.class.php');

class MonumentAPI extends API {
	public function __construct($args, $method, $returnType = 0) {
		parent::__construct($args, $method, $returnType);
	}

	public function getAll() {
		$dao = new MonumentDAO();
		return $dao->findAll();
	}

	public function searchByName($name) {
		$dao = new MonumentDAO();
		$monuments = $dao->searchByName($name);

		return json_encode(array("Search" => $monuments));
	}

	public function searchByLocalization($latitude, $longitude, $offset) {
		$dao = new MonumentDAO();
		$monuments = $dao->findAll();
		$returnArray = array();

		foreach($monuments as $monument) {
			$localization = $monument->getLocalization();

			if($localization->getLatitude() < ($latitude + $offset) && $localization->getLatitude() > ($latitude - $offset) && $localization->getLongitude() < ($longitude + $offset) && $localization->getLongitude() > ($longitude - $offset)) {
				$returnArray[] = $monument;
			}
		}


		return json_encode(array("Search" => $returnArray));
	}

	public function processAPI() {
		$return = '{}';
		if(isset($this->getArgs()['n'])) {
			$return = $this->searchByName($this->getArgs()['n']);
		}
		else if(isset($this->getArgs()['la']) && isset($this->getArgs()['lo'])) {
			if(isset($this->getArgs()['o'])) {
				$offset = $this->getArgs()['o'];
			}
			else {
				$offset = 1;
			}
			$return = $this->searchByLocalization($this->getArgs()['la'], $this->getArgs()['lo'], $offset);
		}

		return $return;
	}
}

?>
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
		$monuments = $dao->findAll();
		$returnArray = array();

		foreach($monuments as $monument) {
			$inReturnArray = false;
			foreach($monument->getCharacteristics() as $characteristic) {
				if($inReturnArray === false && strpos($characteristic->getName(), $name) !== false) {
				    $returnArray[] = $monument;
				}
			}
		}

		return json_encode(array("Search" => $returnArray));
	}

	public function processAPI() {
		$return = '{}';
		if(count($this->getArgs()) > 0) {
			if(isset($this->getArgs()['n']))
			$return = $this->searchByName($this->getArgs()['n']);
		}

		return $return;
	}
}

?>
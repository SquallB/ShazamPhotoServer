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

		return array("Search" => $monuments);
	}

	public function searchByLocalization($latitude, $longitude, $offset) {
		$dao = new MonumentDAO();
		$monuments = $dao->searchByLocalization($latitude, $longitude, $offset);

		return array("Search" => $monuments);
	}

	public function processAPI() {
		$return = '{}';
		$args = $this->getArgs();

		if($this->getMethod() === 'GET') {
			if(isset($args['n'])) {
				$return = $this->searchByName($args['n']);
			}
			else if(isset($args['la']) && isset($args['lo'])) {
				if(isset($args['o'])) {
					$offset = $args['o'];
				}
				else {
					$offset = 1;
				}
				$return = $this->searchByLocalization($args['la'], $args['lo'], $offset);
			}
		}
		else if($this->getMethod() === 'POST') {
			if(isset($args['monument'])) {
				$args['monument'] = $args['monument'];
				$monument = new Monument(json_decode($args['monument'], true));
				$monumentDAO = new MonumentDAO();
				//$monumentDAO->save($monument);
				$return = $monument;
			}
		}
		else if($this->getMethod() === 'PUT') {
			if(isset($args['monument'])) {
				$monument = new Monument(json_decode($args['monument'], true));
				$monumentDAO = new MonumentDAO();
				$monumentDAO->save($monument);
				$return = $monument;
			}
		}
		else if($this->getMethod() === 'DELETE') {
			if(isset($args['id'])) {
				$monumentDAO = new MonumentDAO();
				$monement = $monumentDAO->find($args['id']);
				$monumentDAO->delete($monument);
			}
		}

		return json_encode($return);
	}
}

?>
<?php

include_once('API.class.php');
include_once('MonumentDAO.class.php');
include_once('Descriptor.class.php');
include_once('ListKeyPoints.class.php');

class MonumentAPI extends API {
	public function __construct($args, $method, $returnType = 0) {
		parent::__construct($args, $method, $returnType);
	}

	//performs a query in the dabatase to look for monuments with a name containing the string
	//return an array (called search) with monuments
	public function searchByName($name) {
		$dao = new MonumentDAO();
		$monuments = $dao->searchByName($name, false);

		return array("Search" => $monuments);
	}

	//return the monuments localized in a square (its size is determined by the offset) around
	//the specifed position
	public function searchByLocalization($latitude, $longitude, $offset) {
		$dao = new MonumentDAO();
		$monuments = $dao->searchByLocalization($latitude, $longitude, $offset, false);

		return array("Search" => $monuments);
	}

	public function getAll() {
		$dao = new MonumentDAO();
		$monuments = $dao->findAll(false);

		return array("Search" => $monuments);
	}

	//function used to process the api, using the given method and arguments
	//return something in json
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
			else {
				$return = $this->getAll();
			}
		}
		else if($this->getMethod() === 'POST') {
			if(isset($args['monument'])) {
				$monument = new Monument(json_decode($args['monument'], true));
				if(isset($args['photo'])) {
					$uploaddir = '/home/shazam/public_html/photos/';
					$uploadfile = $uploaddir . basename($args['photo']['name']);

					if(move_uploaded_file($args['photo']['tmp_name'], $uploadfile)) {
						$photoPath = 'http://37.187.216.159/shazam/photos/' . basename($uploadfile);
					    $monument->setPhotoPath($photoPath);
					}
				}
				$monumentDAO = new MonumentDAO();
				$monument->setId($monumentDAO->save($monument));
				$return = $monument;
			}
		}
		else if($this->getMethod() === 'PUT') {
			if(isset($args['id'])) {
				$monumentDAO = new MonumentDAO();
				
				if(isset($args['monument'])) {
					$monument = new Monument(json_decode($args['monument'], true));
					$monument->setId($args['id']);
				}
				else {
					$monument = $monumentDAO->find($args['id']);

					if(isset($args['nblikes'])) {
						$nbLikes = $monument->getNbLikes();
						if($args['nblikes']) {
							$nbLikes++;
						}
						else {
							$nbLikes--;
						}
						$monument->setNbLikes($nbLikes);
					}
					if(isset($args['nbvisitors'])) {
						$nbVisitors = $monument->getNbVisitors();
						if($args['nbvisitors']) {
							$nbVisitors++;
						}
						else {
							$nbVisitors--;
						}
						$monument->setNbVisitors($nbVisitors);
					}
					if(isset($args['descriptors'])) {
						$descriptors = $monument->getDescriptors();
						$json = json_decode($args['descriptors'], true);
						foreach($json as $newDescriptor) {
							$descriptors[] = new Descriptor($newDescriptor);
						}
						$monument->setDescriptors($descriptors);
					}
					if(isset($args['listskeypoints'])) {
						$listsKeyPoints = $monument->getListsKeyPoints();
						$json = json_decode($args['listskeypoints'], true);
						foreach($json as $list) {
							$listsKeyPoints[] = new ListKeyPoints($list);
						}
						$monument->setListsKeyPoints($listsKeyPoints);
					}
					if(isset($args['characteristics'])) {
						$characteristics = $monument->getCharacteristics();
						$characteristics[] = new MonumentCharacteristics(json_decode($args['characteristics'], true));
						$monument->setCharacteristics($characteristics);
					}
				}

				$monumentDAO->save($monument);
				$return = $monument;
			}
		}
		else if($this->getMethod() === 'DELETE') {
			if(isset($args['id'])) {
				$monumentDAO = new MonumentDAO();
				$monument = $monumentDAO->find($args['id']);
				if($monument->getId() !== null) {
					if($monument->getPhotoPath() !== null && $monument->getPhotoPath() !== '') {
						$filePath = '/home/shazam/public_html/photos/' . basename($monument->getPhotoPath());
						if(file_exists($filePath)) {
							unlink($filePath);
						}
					}
				}

				$monumentDAO->delete($monument);
			}
		}

		return json_encode($return);
	}
}

?>
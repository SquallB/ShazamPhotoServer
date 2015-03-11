<?php

include_once('Localization.class.php');
include_once('Address.class.php');
include_once('MonumentCharacteristics.class.php');
include_once('ListKeyPoints.class.php');
include_once('Descriptor.class.php');

class Monument implements JsonSerializable {
	private $id;
	private $characteristics;
	private $photoPath;
	private $year;
	private $nbVisitors;
	private $nbLikes;
	private $localization;
	private $address;
	private $listsKeyPoints;
	private $descriptors;

	public function __construct($data = null) {
		if (is_array($data)) {
			if (isset($data['id'])) {
				$this->setId($data['id']);
			}
			
			$characteristics = array();
			if(isset($data['characteristics']) && is_array($data['characteristics'])) {
				foreach($data['characteristics'] as $characteristic) {
					$characteristics[] = new MonumentCharacteristics($characteristic);
				}
			}
			$this->setCharacteristics($characteristics);

			$this->setPhotoPath($data['photopath']);
			$this->setYear($data['year']);
			$this->setNbVisitors($data['nbvisitors']);
			$this->setNbLikes($data['nblikes']);

			if(isset($data['localization'])) {
				if(is_array($data['localization'])) {
					$localization = new Localization($data['localization']);
				}
				else {
					$localization = $data['localization'];
				}

			}
			else {
				$localization = new Localization(array('id' => $data['l_id'], 'latitude' => $data['latitude'], 'longitude' => $data['longitude']));
			}
			$this->setLocalization($localization);

			if(isset($data['address'])) {
				if(is_array($data['address'])) {
					$address = new Address($data['address']);
				}
				else {
					$address = $data['address'];
				}
			}
			else if(isset($data['a_id'])) {
				$address = new Address(array('id' => $data['a_id'], 'number' => $data['number'], 'street' => $data['street'], 'ci_id' => $data['ci_id'], 'ci_name' =>  $data['ci_name'], 'co_id' => $data['co_id'], 'co_name' => $data['co_name']));
			}
			else {
				$address = null;
			}
			$this->setAddress($address);

			$listsKeyPoints = array();
			if(isset($data['listskeypoints']) && is_array($data['listskeypoints'])) {
				foreach($data['listskeypoints'] as $listKeyPoints) {
					$listsKeyPoints[] = new ListKeyPoints($listKeyPoints);
				}
			}
			$this->setListsKeyPoints($listsKeyPoints);

			$descriptors = array();
			if(isset($data['descriptors']) && is_array($data['descriptors'])) {
				foreach($data['descriptors'] as $descriptor) {
					$descriptors[] = new Descriptor($descriptor);
				}
			}
			$this->setDescriptors($descriptors);
		}
	}	

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		if(is_numeric($id)) {
			$this->id = $id;
		}
 	}

 	public function getCharacteristics() {
		return $this->characteristics;
	}

	public function setCharacteristics($characteristics) {
		if(is_array($characteristics)) {
			$this->characteristics = $characteristics;
		}
 	}

 	public function getPhotoPath() {
		return $this->photoPath;
	}

	public function setPhotoPath($photoPath) {
		if(is_string($photoPath)) {
			$this->photoPath = $photoPath;
		}
 	}

 	public function getYear() {
		return $this->year;
	}

	public function setYear($year) {
		if(is_numeric($year)) {
			$this->year = $year;
		}
 	}

 	public function getNbVisitors() {
		return $this->nbVisitors;
	}

	public function setNbVisitors($nbVisitors) {
		if(is_numeric($nbVisitors)) {
			$this->nbVisitors = $nbVisitors;
		}
 	}

 	public function getNbLikes() {
		return $this->nbLikes;
	}

	public function setNbLikes($nbLikes) {
		if(is_numeric($nbLikes)) {
			$this->nbLikes = $nbLikes;
		}
 	}

 	public function getLocalization() {
		return $this->localization;
	}

	public function setLocalization($localization) {
		if(is_a($localization, 'Localization')) {
			$this->localization = $localization;
		}
 	}

 	public function getAddress() {
		return $this->address;
	}

	public function setAddress($address) {
		if(is_a($address, 'Address')) {
			$this->address = $address;
		}
 	}

 	public function getListsKeyPoints() {
    	return $this->listsKeyPoints;
    }

    public function setListsKeyPoints($listsKeyPoints) {
    	if(is_array($listsKeyPoints)) {
    		$this->listsKeyPoints = $listsKeyPoints;
    	}
    }

    public function getDescriptors() {
    	return $this->descriptors;
    }

    public function setDescriptors($descriptors) {
    	if(is_array($descriptors)) {
    		$this->descriptors = $descriptors;
    	}
    }

 	public function jsonSerialize() {
        return [
            'characteristics' => $this->getCharacteristics(),
            'photopath' => $this->getPhotoPath(),
            'year' => $this->getYear(),
            'nbvisitors' => $this->getNbVisitors(),
            'nblikes' => $this->getNbLikes(),
            'localization' => $this->getLocalization(),
            'address' => $this->getAddress(),
            'listskeypoints' => $this->getListsKeyPoints(),
            'descriptors' => $this->getDescriptors()
        ];
    }
}

?>
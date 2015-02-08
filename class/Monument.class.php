<?php

include_once('Localization.class.php');
include_once('Address.class.php');

class Monument implements JsonSerializable {
	private $id;
	private $characteristics;
	private $photoPath;
	private $year;
	private $nbVisitors;
	private $nbLikes;
	private $localization;
	private $address;

	public function __construct($data = null) {
		if (is_array($data)) {
			if (isset($data['id'])) {
				$this->setId($data['id']);
			}
			
			if(isset($data['characteristics'])) {
				$this->setCharacteristics($data['characteristics']);
			}
			else {
				$this->setCharacteristics(array());
			}

			$this->setPhotoPath($data['photopath']);
			$this->setYear($data['year']);
			$this->setNbVisitors($data['nbvisitors']);
			$this->setNbLikes($data['nblikes']);

			if(isset($data['localization'])) {
				$this->setLocalization($data['localization']);
			}
			else {
				$this->setLocalization(new Localization(array('id' => $data['l_id'], 'latitude' => $data['latitude'], 'longitude' => $data['longitude'])));
			}

			if(isset($data['address'])) {
				$this->setAddress($data['address']);
			}
			else {
				$this->setAddress(new Address(array('id' => $data['a_id'], 'number' => $data['number'], 'street' => $data['street'], 'ci_id' => $data['ci_id'], 'ci_name' =>  $data['ci_name'], 'co_id' => $data['co_id'], 'co_name' => $data['co_name'])));
			}
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

 	public function jsonSerialize() {
        return [
            'characteristics' => $this->getCharacteristics(),
            'photoPath' => $this->getPhotoPath(),
            'year' => $this->getYear(),
            'nbVisitors' => $this->getNbVisitors(),
            'nbLikes' => $this->getNbLikes(),
            'localization' => $this->getLocalization(),
            'address' => $this->getAddress()
        ];
    }
}

?>
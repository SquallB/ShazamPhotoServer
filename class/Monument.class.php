<?php

class Monument implements JsonSerializable {
	private $id;
	private $characteristics;
	private $photoPath;
	private $year;
	private $nbVisitors;
	private $nbLikes;
	private $localization;
	private $address;

	public function __construct($id = 0, $characteristics = array(), $photoPath = "", $year = 0, $nbVisitors = 0, $nbLikes = 0, $localization = NULL, $address = NULL) {
		$this->setId($id);
		$this->setCharacteristics($characteristics);
		$this->setPhotoPath($photoPath);
		$this->setYear($year);
		$this->setNbVisitors($nbVisitors);
		$this->setNbLikes($nbLikes);
		$this->setLocalization($localization);
		$this->setAddress($address);
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
            'id' => $this->getId(),
            'characteristics' => $this->getCharacteristics,
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
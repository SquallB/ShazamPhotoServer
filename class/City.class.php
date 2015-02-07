<?php

class City implements JsonSerializable {
	private $id;
	private $name;
	private $country;

	public function __construct($id = 0, $name = "", $country = NULL) {
		$this->setId($id);
		$this->setName($name);
		$this->setCountry($country);
	}	

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		if(is_numeric($id)) {
			$this->id = $id;
		}
 	}

 	public function getName() {
 		return $this->name;
 	}

 	public function setName($name) {
 		if(is_string($name)) {
 			$this->name = $name;
 		}
 	}

 	public function getCountry() {
 		return $this->country;
 	}

 	public function setCountry($country) {
 		if(is_a($country, 'Country')) {
 			$this->country = $country;
 		}
 	}

 	public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'country' => $this->getCountry()
        ];
    }
}

?>
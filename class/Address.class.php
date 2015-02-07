<?php

class Address implements JsonSerializable {
	private $id;
	private $number;
	private $street;
	private $city;

	public function __construct($id = 0, $number = "", $street = "", $city = NULL) {
		$this->setId($id);
		$this->setNumber($number);
		$this->setStreet($street);
		$this->setCity($city);
	}	

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		if(is_numeric($id)) {
			$this->id = $id;
		}
 	}

 	public function getNumber() {
 		return $this->number;
 	}

 	public function setNumber($number) {
 		if(is_string($number)) {
 			$this->number = $number;
 		}
 	}

 	public function getStreet() {
 		return $this->number;
 	}

 	public function setStreet($street) {
 		if(is_string($street)) {
 			$this->street = $street;
 		}
 	}

 	public function getCity() {
 		return $this->city;
 	}

 	public function setCity($city) {
 		if(is_a($city, 'City')) {
 			$this->city = $city;
 		}
 	}

 	public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'number' => $this->getNumber(),
            'street' => $this->getStreet(),
            'city' => $this->getCity()
        ];
    }
}

?>
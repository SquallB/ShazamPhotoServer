<?php

include_once('City.class.php');

class Address implements JsonSerializable {
	private $id;
	private $number;
	private $street;
	private $city;

	public function __construct($data = null) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			$this->setNumber($data['number']);
			$this->setStreet($data['street']);
			
			if(isset($data['city'])) {
				if(is_array($data['city'])) {
					$city = new City($data['city']);
				}
				else {
					$city = $data['city'];
				}
			}
			else {
				if(isset($data['ci_id'])) {
					$city = new City(array('id' => $data['ci_id'], 'name' => $data['ci_name'], 'co_id' => $data['co_id'], 'co_name' => $data['co_name']));
				}
				else {
					$city = new City();
				}
			}
			$this->setCity($city);
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

 	public function getNumber() {
 		return $this->number;
 	}

 	public function setNumber($number) {
 		if(is_string($number)) {
 			$this->number = $number;
 		}
 	}

 	public function getStreet() {
 		return $this->street;
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
            'number' => $this->getNumber(),
            'street' => $this->getStreet(),
            'city' => $this->getCity()
        ];
    }
}

?>
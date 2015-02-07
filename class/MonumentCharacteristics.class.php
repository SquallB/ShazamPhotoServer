<?php

class MonumentCharacteristics implements JsonSerializable {
	private $id;
	private $name;
	private $description;
	private $language;

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

 	public function getDescription() {
 		return $this->description;
 	}

 	public function setDescription($description) {
 		if(is_string($description)) {
 			$this->description = $description;
 		}
 	}

 	public function getLanguage() {
 		return $this->language;
 	}

 	public function setLanguage($language) {
 		if(is_a($language, 'Language')) {
 			$this->language = $language;
 		}
 	}

 	public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'language' => $this->getLanguage()
        ];
    }
}

?>
<?php

include_once('Language.class.php');

class MonumentCharacteristics implements JsonSerializable {
	private $id;
	private $name;
	private $description;
	private $language;

	public function __construct($data = null) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			$this->setName($data['name']);
			$this->setDescription($data['description']);

			if(isset($data['language'])) {
				if(is_array($data['language'])) {
					$language = new Language($data['language']);
				}
				else {
					$language = $data['language'];
				}
			}
			else {
				$language = new Language(array('id' => $data['l_id'], 'name' => $data['l_name'], 'value' => $data['l_value']));
			}
			$this->setLanguage($language);
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
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'language' => $this->getLanguage()
        ];
    }
}

?>
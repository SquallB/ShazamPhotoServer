<?php

include('Descriptor.class.php');

class ListDescriptors implements JsonSerializable {
	private $id;
	private $descriptors;

	public function __construct($data = null) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

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
            'descriptors' => $this->getDescriptors()
        ];
    }
}

?>
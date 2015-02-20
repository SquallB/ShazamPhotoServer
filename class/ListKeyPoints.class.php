<?php

class ListKeyPoints implements JsonSerializable {
	private $id;
	private $keyPoints;

	public function __construct($data = null) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			if(isset($data['keyPoints'])) {
				$this->setKeyPoints($data['keyPoints']);
			}
			else {
				$this->setKeyPoints(array());
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

	public function getKeyPoints() {
		return $this->keyPoints;
	}

	public function setKeyPoints($keyPoints) {
		if(is_array($keyPoints)) {
			$this->keyPoints = $keyPoints;
		}
	}

	public function jsonSerialize() {
        return [
            'keyPoints' => $this->getKeyPoints()
        ];
    }
}

?>
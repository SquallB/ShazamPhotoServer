<?php

class ListKeyPoints implements JsonSerializable {
	private $id;
	private $keyPoints;

	public function __construct($data = null) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			$keyPoints = array();
			if(isset($data['keypoints']) && is_array($data['keypoints'])) {
				foreach($data['keypoints'] as $keyPoint) {
					$keyPoints[] = new KeyPoint($keyPoint);
				}
			}
			$this->setKeyPoints($keyPoints);
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
            'keypoints' => $this->getKeyPoints()
        ];
    }
}

?>
<?php

class Localization implements JsonSerializable {
	private $id;
	private $latitude;
	private $longitude;

	public function __construct($data = null) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			$this->setLatitude($data['latitude']);
			$this->setLongitude($data['longitude']);
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

	public function getLatitude() {
		return $this->latitude;
	}

	public function setLatitude($latitude) {
		if(is_numeric($latitude)) {
			$this->latitude = $latitude;
		}
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function setLongitude($longitude) {
		if(is_numeric($longitude)) {
			$this->longitude = $longitude;
		}
	}

	public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude()
        ];
    }
}

?>
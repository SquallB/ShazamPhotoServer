<?php

class KeyPoint implements JsonSerializable {
	private $id;
	private $x;
	private $y;
	private $size;
	private $angle;
	private $response;
	private $octave;
	private $classId;

	public function __construct($data = null) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			$this->setX($data['x']);
			$this->setY($data['y']);
			$this->setSize($data['size']);
			$this->setAngle($data['angle']);
			$this->setResponse($data['response']);
			$this->setOctave($data['octave']);
			$this->setClassId($data['class_id']);
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

	public function getX() {
		return $this->x;
	}

	public function setX($x) {
		if(is_numeric($x)) {
			$this->x = $x;
		}
	}

	public function getY() {
		return $this->y;
	}

	public function setY($y) {
		if(is_numeric($y)) {
			$this->y = $y;
		}
	}

	public function getSize() {
		return $this->size;
	}

	public function setSize($size) {
		if(is_numeric($size)) {
			$this->size = $size;
		}
	}

	public function getAngle() {
		return $this->angle;
	}

	public function setAngle($angle) {
		if(is_numeric($angle)) {
			$this->angle = $angle;
		}
	}

	public function getResponse() {
		return $this->response;
	}

	public function setResponse($response) {
		if(is_numeric($response)) {
			$this->response = $response;
		}
	}

	public function getOctave() {
		return $this->octave;
	}

	public function setOctave($octave) {
		if(is_numeric($octave)) {
			$this->octave = $octave;
		}
	}

	public function getClassId() {
		return $this->classId;
	}

	public function setClassId($classId) {
		if(is_numeric($classId)) {
			$this->classId = $classId;
		}
	}

	public function jsonSerialize() {
        return [
       		'x' => $this->getX(),
			'y' => $this->getY(),
			'size' => $this->getSize(),
			'angle' => $this->getAngle(),
			'response' => $this->getResponse(),
			'octave' => $this->getOctave(),
			'class_id' => $this->getClassId()
        ];
    }
}

?>
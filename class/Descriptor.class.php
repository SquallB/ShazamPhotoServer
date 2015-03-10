<?php

class Descriptor implements JsonSerializable {
	private $id;
	private $rows;
	private $cols;
	private $data;
	private $type;

	public function __construct($data = null) {
		if(is_array($data)) {
			if (isset($data['id'])) {
				$this->setId($data['id']);
			}

			$this->setRows($data['rows']);
			$this->setCols($data['cols']);
			$this->setData($data['data']);
			$this->setType($data['type']);
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

	public function getRows() {
		return $this->rows;
	}

	public function setRows($rows) {
		if(is_numeric($rows)) {
			$this->rows = $rows;
		}
	}

	public function getCols() {
		return $this->cols;
	}

	public function setCols($cols) {
		if(is_numeric($cols)) {
			$this->cols = $cols;
		}
	}

	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		if(is_string($data)) {
			$this->data = $data;
		}
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		if(is_numeric($type)) {
			$this->type = $type;
		}
	}

	public function jsonSerialize() {
        return [
            'rows' => $this->getRows(),
            'cols' => $this->getCols(),
            'data' => $this->getData(),
            'type' => $this->getType()
        ];
    }
}

?>
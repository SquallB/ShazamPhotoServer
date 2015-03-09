<?php

class Descriptor implements JsonSerializable {
	private $id;
	private $dims;
	private $rows;
	private $cols;
	private $data;

	public __construct($data = null) {
		if(is_array($data)) {
			if (isset($data['id'])) {
				$this->setId($data['id']);
			}

			$this->setDims($data['dims']);
			$this->setRows($data['rows']);
			$this->setCols($data['cols']);
			$this->setData($data['data']);
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

	public function getDims() {
		return $this->dims;
	}

	public function setDims($dims) {
		if(is_numeric($dims)) {
			$this->dims = $dims;
		}
	}

	public function getDims() {
		return $this->dims;
	}

	public function setDims($dims) {
		if(is_numeric($dims)) {
			$this->dims = $dims;
		}
	}

	public function getRows() {
		return $this->rows;
	}

	public function setCols($rows) {
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

	public function jsonSerialize() {
        return [
            'dims' => $this->getDims(),
            'rows' => $this->getRows(),
            'cols' => $this->getCols(),
            'data' => $this->getData(),
        ];
    }
}

?>
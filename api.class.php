<?php

abstract class API {
	private $args;
	private $method;
	private $returnType;

	public function __construct($args, $method, $returnType = 0) {
		$this->args = array();
		$this->method = $method;

		foreach($args as $key => $value) {
			$key = htmlspecialchars($key);
			$value = htmlspecialchars($value);

			$this->args[$key] = $value;
		}

		$this->returnType = 0;
	}

	public function getArgs() {
		return $this->args;
	}

	public function setArgs($args) {
		if(is_array($args)) {
			$this->args = $args;
		}
	}

	public function getMethod() {
		return $this->method;
	}

	public function setMethod($method) {
		$this->method = $method;
	}

	public function getReturnType() {
		return $this->returnType;
	}

	public function setReturnType($returnType) {
		$this->returnType = $returnType;
	}
}

?>
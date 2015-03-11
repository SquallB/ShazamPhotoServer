<?php

abstract class API {
	private $args;
	private $method;
	private $returnType;

	public function __construct($args, $method, $returnType = 0) {
		$this->setArgs($args);
		$this->setMethod($method);
		$this->setReturnType($returnType);
	}

	public function getArgs() {
		return $this->args;
	}

	public function setArgs($args) {
		if(is_array($args)) {
			$this->args = array();
			foreach($args as $key => $value) {
				$key = $key;
				$value = $value;
				$this->args[$key] = $value;
			}
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

	public abstract function processAPI();
}

?>
<?php

include_once('Monument.class.php');
include_once('DAO.class.php');

class MonumentDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	private function queryToMonument($result) {

	}

	public function find($id) {
		 $request = $this->getConnection()->prepare('
			SELECT *
			FROM monument
			WHERE id = :id
		');
		$request->bindParam(':id', $id);
		$request->execute();
		return $this->queryToMonument($request->fetch());
	}

	public function findAll() {
		$request = $this->getConnection()->prepare('
			SELECT *
			FROM monument
		');
		$request->execute();
		return $request->fetchAll();
	}

	public function save($data) {

	}

	public function update($data) {

	}
}

?>
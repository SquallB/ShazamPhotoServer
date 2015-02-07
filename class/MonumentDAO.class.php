<?php

include_once('Monument.class.php');
include_once('MonumentCharacteristics.class.php');
include_once('DAO.class.php');

class MonumentDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	public function find($id) {
		 $stmt = $this->getConnection()->prepare('
			SELECT m.id, m.photoPath, m.year, m.nbVisitors, m.nbLikes, l.id as l_id, l.latitude, l.longitude, a.id as a_id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM monument m
			INNER JOIN localization l ON m.localization_id = l.id
			INNER JOIN address a ON m.address_id = a.id
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
			WHERE m.id = :id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$monument = new Monument($stmt->fetch());
		$stmt = $this->getConnection()->prepare('
			SELECT mc.id, mc.name, mc.description, l.id as l_id, l.name as l_name, l.value as l_value
			FROM monument_characteristics mc INNER JOIN language l ON mc.language_id = l.id
			WHERE mc.monument_id = :id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$characteristics = array();
		foreach($stmt->fetchAll() as $row) {
			$characteristics[] = new MonumentCharacteristics($row);
		}
		$monument->setCharacteristics($characteristics);
		return $monument;
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT m.id, m.photoPath, m.year, m.nbVisitors, m.nbLikes, l.id as l_id, l.latitude, l.longitude, a.id as a_id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM monument m
			INNER JOIN localization l ON m.localization_id = l.id
			INNER JOIN address a ON m.address_id = a.id
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
		');
		$stmt->execute();
		$array = array();
		foreach($stmt->fetchAll() as $row) {
			$monument = new Monument($row);
			$stmt = $this->getConnection()->prepare('
				SELECT mc.id, mc.name, mc.description, l.id as l_id, l.name as l_name, l.value as l_value
				FROM monument_characteristics mc INNER JOIN language l ON mc.language_id = l.id
				WHERE mc.monument_id = :id
			');
			$stmt->bindParam(':id', $monument->getId());
			$stmt->execute();
			$characteristics = array();
			foreach($stmt->fetchAll() as $row) {
				$characteristics[] = new MonumentCharacteristics($row);
			}
			$monument->setCharacteristics($characteristics);
			$array[] = $monument;
		}
		return $array;
	}

	public function save($data) {

	}

	public function update($data) {

	}

	public function delete($data) {

	}
}

?>
<?php

include_once('Monument.class.php');
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
		return new Monument($stmt->fetch());
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT m.id as m_id, m.photoPath, m.year, m.nbVisitors, m.nbLikes, l.id as l_id, l.latitude, l.longitude, a.id as a_id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM monument m
			INNER JOIN localization l ON m.localization_id = l.id
			INNER JOIN address a ON m.address_id = a.id
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
		');
		$stmt->execute();
		$array = array();
		foreach($stmt->fetchAll() as $row) {
			$array[] = new Monument($row);
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
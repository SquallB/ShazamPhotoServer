<?php

include_once('City.class.php');
include_once('DAO.class.php');
include_once('CountryDAO.class.php');

class CityDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	public function find($id) {
		$stmt = $this->getConnection()->prepare('
			SELECT ci.id, ci.name, co.id as co_id, co.name as co_name
			FROM city ci
			INNER JOIN country co ON ci.country_id = co.id
			WHERE id = :id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		return new City($stmt->fetch());
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT ci.id, ci.name, co.id as co_id, co.name as co_name
			FROM city ci
			INNER JOIN country co ON ci.country_id = co.id
		');
		$stmt->execute();
		$array = array();
		foreach($stmt->fetchAll() as $row) {
			$array[] = new City($row);
		}
		return $array;
	}

	public function save($data) {
		if($data->getId() !== null) {
			return $this->update($data);
		}

		$countryDAO = new CountryDAO($this->getConnection());
		$countryId = $countryDAO->save($data->getCountry());
		$stmt = $this->getConnection()->prepare('
		INSERT INTO city
		(name, country_id)
		VALUES
		(:name, :country_id)
		RETURNING id
		');
		$stmt->bindParam(':name', $data->getName());
		$stmt->bindParam(':country_id', $countryId);
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function update($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot update city that does not yet exist in the database.'
			);
		}

		$countryDAO = new CountryDAO($this->getConnection());
		$countryId = $countryDAO->save($data->getCountry());

		$stmt = $this->getConnection()->prepare('
		UPDATE city
		SET name = :name, country_id = :country_id
		WHERE id = :id
		RETURNING id
		');
		$stmt->bindParam(':name', $data->getName());
		$stmt->bindParam(':country_id', $countryId);
		$stmt->bindParam(':id', $data->getId());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function delete ($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot delete city that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		DELETE FROM city
		WHERE id = :id
		');
		$stmt->bindParam(':id', $data->getId());
		return $stmt->execute(); 
	}
}

?>
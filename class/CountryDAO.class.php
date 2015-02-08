<?php

include_once('Country.class.php');
include_once('DAO.class.php');

class CountryDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	public function find($id) {
		$stmt = $this->getConnection()->prepare('
			SELECT *
			FROM country
			WHERE id = :id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		return new Country($stmt->fetch());
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT *
			FROM country
		');
		$stmt->execute();
		$array = array();
		foreach($stmt->fetchAll() as $row) {
			$array[] = new Country($row);
		}
		return $array;
	}

	public function save($data) {
		if($data->getId() !== null) {
			return $this->update($data);
		}

		$stmt = $this->getConnection()->prepare('
		INSERT INTO country
		(name)
		VALUES
		(:name)
		RETURNING id
		');
		$stmt->bindParam(':name', $data->getName());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function update($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot update country that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		UPDATE country
		SET name = :name
		WHERE id = :id
		RETURNING id
		');
		$stmt->bindParam(':name', $data->getName());
		$stmt->bindParam(':id', $data->getId());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function delete ($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot delete country that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		DELETE FROM country
		WHERE id = :id
		');
		$stmt->bindParam(':id', $data->getId());
		return $stmt->execute(); 
	}
}

?>
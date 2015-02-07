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
		// Set the fetchmode to populate an instance of 'Country'
		// This enables us to use the following:
		// $country = $repository->find(1234);
		// echo $country->getName();
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Country'); 
		return $stmt->fetch();
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT *
			FROM country
		');
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Country');
		// fetchAll() will do the same as above, but we'll have an array. ie:
		// $countries = $repository->findAll();
		// echo $country[0]->getName(); 
		return $stmt->fetchAll();
	}

	public function save($data) {
		 // If the ID is set, we're updating an existing record
		if (isset($data->id)) {
			return $this->update($data);
		}

		$stmt = $this->getConnection()->prepare('
		INSERT INTO country
		(name)
		VALUES
		(:name)
		');
		$stmt->bindParam(':name', $data->getName());
		return $stmt->execute();
	}

	public function update($data) {
		$id = $data->getId();
		if(!isset($id)) {
			// We can't update a record unless it exists...
			throw new \LogicException(
				'Cannot update country that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		UPDATE country
		SET name = :name
		WHERE id = :id
		');
		$stmt->bindParam(':name', $data->getName());
		$stmt->bindParam(':id', $id);
		return $stmt->execute(); 
	}

	public function delete ($data) {
		$id = $data->getId();
		if(!isset($id)) {
			// We can't delete a record unless it exists...
			throw new \LogicException(
				'Cannot delete country that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		DELETE FROM country
		WHERE id = :id
		');
		$stmt->bindParam(':id', $id);
		return $stmt->execute(); 
	}
}

?>
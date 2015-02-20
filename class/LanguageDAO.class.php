<?php

include_once('Language.class.php');
include_once('DAO.class.php');

class LanguageDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	public function find($id) {
		$stmt = $this->getConnection()->prepare('
			SELECT *
			FROM language
			WHERE id = :id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		return new Language($stmt->fetch());
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT *
			FROM language
		');
		$stmt->execute();
		$array = array();
		foreach($stmt->fetchAll() as $row) {
			$array[] = new Language($row);
		}
		return $array;
	}

	public function save($data) {
		if($data->getId() !== null) {
			return $this->update($data);
		}

		$stmt = $this->getConnection()->prepare('
		INSERT INTO language
		(name, value)
		VALUES
		(:name, :value)
		RETURNING id
		');
		$stmt->bindParam(':name', $data->getName());
		$stmt->bindParam(':value', $data->getValue());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function update($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot update language that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		UPDATE language
		SET name = :name, value = :value
		WHERE id = :id
		RETURNING id
		');
		$stmt->bindParam(':name', $data->getName());
		$stmt->bindParam(':value', $data->getValue());
		$stmt->bindParam(':id', $data->getId());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function delete ($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot delete language that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		DELETE FROM language
		WHERE id = :id
		');
		$stmt->bindParam(':id', $data->getId());
		return $stmt->execute(); 
	}
}

?>
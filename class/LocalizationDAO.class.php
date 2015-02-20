<?php

include_once('Localization.class.php');
include_once('DAO.class.php');

class LocalizationDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	public function find($id) {
		$stmt = $this->getConnection()->prepare('
			SELECT *
			FROM localization
			WHERE id = :id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		return new Localization($stmt->fetch());
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT *
			FROM localization
		');
		$stmt->execute();
		$array = array();
		foreach($stmt->fetchAll() as $row) {
			$array[] = new Localization($row);
		}
		return $array;
	}

	public function save($data) {
		if($data->getId() !== null) {
			return $this->update($data);
		}

		$stmt = $this->getConnection()->prepare('
		INSERT INTO localization
		(latitude, longitude)
		VALUES
		(:latitude, :longitude)
		RETURNING id
		');
		$stmt->bindParam(':latitude', $data->getLatitude());
		$stmt->bindParam(':longitude', $data->getLongitude());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function update($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot update localization that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		UPDATE localization
		SET latitude = :latitude, longitude = :longitude
		WHERE id = :id
		RETURNING id
		');
		$stmt->bindParam(':latitude', $data->getLatitude());
		$stmt->bindParam(':longitude', $data->getLongitude());
		$stmt->bindParam(':id', $data->getId());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function delete ($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot delete localization that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		DELETE FROM localization
		WHERE id = :id
		RETURNING id
		');
		$stmt->bindParam(':id', $data->getId());
		return $stmt->execute(); 
	}
}

?>
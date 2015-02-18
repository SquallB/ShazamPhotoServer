<?php

include_once('Address.class.php');
include_once('DAO.class.php');
include_once('CityDAO.class.php');

class AddressDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	public function find($id) {
		$stmt = $this->getConnection()->prepare('
			SELECT a.id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM address a
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
			WHERE id = :id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		return new Address($stmt->fetch());
	}

	public function findAll() {
		$stmt = $this->getConnection()->prepare('
			SELECT a.id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM address a
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
		');
		$stmt->execute();
		$array = array();
		foreach($stmt->fetchAll() as $row) {
			$array[] = new Address($row);
		}
		return $array;
	}

	public function save($data) {
		if($data->getId() !== null) {
			return $this->update($data);
		}

		$cityDAO = new CityDAO($this->getConnection());
		$cityId = $cityDAO->save($data->getCity());

		$stmt = $this->getConnection()->prepare('
		INSERT INTO address
		(number, street, city_id)
		VALUES
		(:number, :street, :city_id)
		RETURNING id
		');
		$stmt->bindParam(':number', $data->getNumber());
		$stmt->bindParam(':street', $data->getStreet());
		$stmt->bindParam(':city_id', $cityId);
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function update($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot update address that does not yet exist in the database.'
			);
		}

		$cityDAO = new CityDAO($this->getConnection());
		$cityId = $cityDAO->save($data->getCity());

		$stmt = $this->getConnection()->prepare('
		UPDATE address
		SET number = :number, street = :street, city_id = :city_id
		WHERE id = :id
		RETURNING id
		');
		$stmt->bindParam(':number', $data->getNumber());
		$stmt->bindParam(':street', $data->getStreet());
		$stmt->bindParam(':city_id', $cityId);
		$stmt->bindParam(':id', $data->getId());
		$stmt->execute();
		return $stmt->fetch()['id'];
	}

	public function delete ($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot delete address that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
		DELETE FROM address
		WHERE id = :id
		');
		$stmt->bindParam(':id', $data->getId());
		return $stmt->execute(); 
	}
}

?>
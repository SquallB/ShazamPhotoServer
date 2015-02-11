<?php

include_once('Monument.class.php');
include_once('MonumentCharacteristics.class.php');
include_once('DAO.class.php');
include_once('LocalizationDAO.class.php');
include_once('AddressDAO.class.php');
include_once('LanguageDAO.class.php');

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
			SELECT m.id, m.photopath, m.year, m.nbvisitors, m.nblikes, l.id as l_id, l.latitude, l.longitude, a.id as a_id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM monument m
			INNER JOIN localization l ON m.localization_id = l.id
			INNER JOIN address a ON m.address_id = a.id
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
		');
		$stmt->execute();
		$array = array();
		$stmt2 = $this->getConnection()->prepare('
			SELECT mc.id, mc.name, mc.description, l.id as l_id, l.name as l_name, l.value as l_value
			FROM monument_characteristics mc INNER JOIN language l ON mc.language_id = l.id
			WHERE mc.monument_id = :id
		');
		foreach($stmt->fetchAll() as $row) {
			$monument = new Monument($row);
			$stmt2->bindParam(':id', $monument->getId());
			$stmt2->execute();
			$characteristics = array();
			foreach($stmt2->fetchAll() as $row) {
				$characteristics[] = new MonumentCharacteristics($row);
			}
			$monument->setCharacteristics($characteristics);
			$array[] = $monument;
		}
		return $array;
	}

	public function searchByName($name) {
		$name = '%'.$name.'%';
		$array = array();
		$stmt = $this->getConnection()->prepare('
			SELECT DISTINCT monument_id
			FROM monument_characteristics
			WHERE name LIKE :name
		');
		$stmt->bindParam(':name', $name);
		$stmt->execute();
		foreach($stmt->fetchAll() as $row) {
			$array[] = $this->find($row['monument_id']);
		}
		return $array;
	}

	public function save($data) {
		if($data->getId() !== null) {
			return $this->update($data);
		}

		$localizationDAO = new LocalizationDAO($this->getConnection());
		$localizationId = $localizationDAO->save($data->getLocalization());

		$addressDAO = new AddressDAO($this->getConnection());
		$addressId = $addressDAO->save($data->getAddress());

		$stmt = $this->getConnection()->prepare('
			INSERT INTO monument
			(photopath, year, nbvisitors, nblikes, localization_id, address_id)
			VALUES
			(:photopath, :year, :nbvisitors, :nblikes, :localization_id, :address_id)
			RETURNING id
		');
		$stmt->bindParam(':photopath', $data->getPhotoPath());
		$stmt->bindParam(':year', $data->getYear());
		$stmt->bindParam(':nbvisitors', $data->getNbVisitors());
		$stmt->bindParam(':nblikes', $data->getNbLikes());
		$stmt->bindParam(':localization_id', $localizationId);
		$stmt->bindParam(':address_id', $addressId);
		$stmt->execute();
		$monumentId = $stmt->fetch()['id'];

		$stmt = $this->getConnection()->prepare('
			INSERT INTO monument_characteristics
			(name, description, language_id, monument_id)
			VALUES
			(:name, :description, :language_id, :monument_id)
		');
		foreach($data->getCharacteristics() as $characteristic) {
			$languageDAO = new LanguageDAO($this->getConnection());
			$languageId = $languageDAO->save($characteristic->getLanguage());
			$stmt->bindParam(':name', $characteristic->getName());
			$stmt->bindParam(':description', $characteristic->getDescription());
			$stmt->bindParam(':language_id', $languageId);
			$stmt->bindParam(':monument_id', $monumentId);
			$stmt->execute();
		}

		return $monumentId;
	}

	public function update($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot update monument that does not yet exist in the database.'
			);
		}

		$localizationDAO = new LocalizationDAO($this->getConnection());
		$localizationId = $localizationDAO->save($data->getLocalization());

		$addressDAO = new AddressDAO($this->getConnection());
		$addressId = $addressDAO->save($data->getAddress());

		$stmt = $this->getConnection()->prepare('
			UPDATE monument
			SET photopath = :photoPath, year = :year, nbvisitors = :nbvisitors, nblikes = :nblikes, localization_id = :localization_id, address_id = :address_id
			WHERE id = :id
			RETURNING id
		');
		$stmt->bindParam(':photopath', $data->getPhotoPath());
		$stmt->bindParam(':year', $data->getYear());
		$stmt->bindParam(':nbvisitors', $data->getNbVisitors());
		$stmt->bindParam(':nblikes', $data->getNbLikes());
		$stmt->bindParam(':localization_id', $localizationId);
		$stmt->bindParam(':address_id', $addressId);
		$stmt->bindParam(':id', $data->getId());
		$stmt->execute();
		$monumentId = $stmt->fetch()['id'];

		$stmt = $this->getConnection()->prepare('
			INSERT INTO monument_characteristics
			(name, description, language_id, monument_id)
			VALUES
			(:name, :year, :description, :language_id, :monument_id)
		');
		foreach($data->getCharacteristics() as $characteristic) {
			$languageDAO = new LanguageDAO($this->getConnection());
			$languageId = $languageDAO->save($characteristic->getLanguage());
			$stmt->bindParam(':name', $characteristic->getName());
			$stmt->bindParam(':description', $characteristic->getDescription());
			$stmt->bindParam(':language_id', $languageId);
			$stmt->bindParam(':monument_id', $monumentId);
			$stmt->execute();
		}

		return $monumentId;
	}

	public function delete($data) {
		if($data->getId() === null) {
			throw new \LogicException(
				'Cannot delete monument that does not yet exist in the database.'
			);
		}

		$stmt = $this->getConnection()->prepare('
			DELETE FROM monument_characteristics
			WHERE id = :id
		');
		foreach($data->getCharacteristics() as $characteristic) {
			$stmt->bindParam(':id', $characteristic->getId());
			$stmt->execute();
		}

		$stmt = $this->getConnection()->prepare('
			DELETE FROM monument
			WHERE id = :id
		');
		$stmt->bindParam(':id', $data->getId());
		return $stmt->execute(); 
	}
}

?>
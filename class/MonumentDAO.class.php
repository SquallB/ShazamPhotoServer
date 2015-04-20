<?php

include_once('Monument.class.php');
include_once('MonumentCharacteristics.class.php');
include_once('DAO.class.php');
include_once('LocalizationDAO.class.php');
include_once('AddressDAO.class.php');
include_once('LanguageDAO.class.php');
include_once('ListKeyPoints.class.php');
include_once('KeyPoint.class.php');

class MonumentDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	private function getCharacteristics($monumentId) {
		$stmt = $this->getConnection()->prepare('
			SELECT mc.id, mc.name, mc.description, l.id as l_id, l.name as l_name, l.value as l_value
			FROM monument_characteristics mc
			INNER JOIN language l ON mc.language_id = l.id
			WHERE mc.monument_id = :id
			ORDER BY mc.id
		');
		$stmt->bindParam(':id', $monumentId);
		$stmt->execute();
		$characteristics = array();
		foreach($stmt->fetchAll() as $row) {
			$characteristics[] = new MonumentCharacteristics($row);
		}
		return $characteristics;
	}

	private function getListKeyPoints($monumentId) {
		$lists = array();
		$stmt = $this->getConnection()->prepare('
			SELECT id
			FROM list_key_points
			WHERE monument_id = :id
			ORDER BY id
		');
		$stmt->bindParam(':id', $monumentId);
		$stmt->execute();
		$stmt2 = $this->getConnection()->prepare('
			SELECT id, x, y, size, angle, response, octave, class_id
			FROM key_points
			WHERE list_id = :id
			ORDER BY id
		');
		foreach($stmt->fetchAll() as $listId) {
			$stmt2->bindParam(':id', $listId['id']);
			$stmt2->execute();
			$keyPoints = $stmt2->fetchAll();
			$lists[] = new ListKeyPoints(array('id' => $listId['id'], 'keypoints' => $keyPoints));
		}
		return $lists;
	}

	private function getDescriptors($monumentId) {
		$stmt = $this->getConnection()->prepare('
			SELECT id, rows, cols, type, data
			FROM descriptor
			WHERE monument_id = :id
			ORDER BY id
		');
		$stmt->bindParam(':id', $monumentId);
		$stmt->execute();
		$descriptors = array();
		foreach($stmt->fetchAll() as $row) {
			$descriptors[] = new Descriptor($row);
		}
		return $descriptors;
	}

	public function find($id, $descriptors = true) {
		 $stmt = $this->getConnection()->prepare('
			SELECT m.id, m.photoPath, m.year, m.nbVisitors, m.nbLikes, l.id as l_id, l.latitude, l.longitude, a.id as a_id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM monument m
			INNER JOIN localization l ON m.localization_id = l.id
			INNER JOIN address a ON m.address_id = a.id
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
			WHERE m.id = :id
			ORDER BY m.id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$monument = new Monument($stmt->fetch());
		$monument->setCharacteristics($this->getCharacteristics($id));
		if($descriptors) {
			$monument->setListsKeyPoints($this->getListKeyPoints($id));
			$monument->setDescriptors($this->getDescriptors($id));
		}
		
		return $monument;
	}

	public function findAll($descriptors = true) {
		$stmt = $this->getConnection()->prepare('
			SELECT m.id, m.photopath, m.year, m.nbvisitors, m.nblikes, l.id as l_id, l.latitude, l.longitude, a.id as a_id, a.number, a.street, ci.id as ci_id, ci.name as ci_name, co.id as co_id, co.name as co_name
			FROM monument m
			INNER JOIN localization l ON m.localization_id = l.id
			INNER JOIN address a ON m.address_id = a.id
			INNER JOIN city ci ON a.city_id = ci.id
			INNER JOIN country co ON ci.country_id = co.id
			ORDER BY m.id
		');
		$stmt->execute();
		$array = array();

		foreach($stmt->fetchAll() as $row) {
			$monument = new Monument($row);
			$monument->setCharacteristics($this->getCharacteristics($monument->getId()));
			if($descriptors) {
				$monument->setListsKeyPoints($this->getListKeyPoints($monument->getId()));
				$monument->setDescriptors($this->getDescriptors($monument->getId()));
			}
			$array[] = $monument;
		}
		return $array;
	}

	public function searchByName($name, $descriptors = true) {
		$name = '%'.$name.'%';
		$array = array();
		$stmt = $this->getConnection()->prepare('
			SELECT DISTINCT monument_id
			FROM monument_characteristics
			WHERE LOWER(name) LIKE LOWER(:name)
			ORDER BY monument_id
		');
		$stmt->bindParam(':name', $name);
		$stmt->execute();
		foreach($stmt->fetchAll() as $row) {
			$array[] = $this->find($row['monument_id'], $descriptors);
		}
		return $array;
	}

	public function searchByLocalization($latitude, $longitude, $offset, $descriptors = true) {
		$ids = $this->findIdsByLocalization($latitude, $longitude, $offset);
		$array = array();
		foreach($ids as $id) {
			$array[] = $this->find($id, $descriptors);
		}
		return $array;
	}

	public function findIds() {
		$stmt = $this->getConnection()->prepare('
			SELECT id
			FROM monument
			ORDER BY id
		');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
	}

	public function findIdsByLocalization($latitude, $longitude, $offset) {
		$array = array();
		$minLatitude = $latitude - $offset;
		$maxLatitude = $latitude + $offset;
		$minLongitude = $longitude - $offset;
		$maxLongitude = $longitude + $offset;
		$stmt = $this->getConnection()->prepare('
			SELECT DISTINCT m.id
			FROM monument m
			INNER JOIN localization l ON m.localization_id = l.id
			WHERE l.latitude > (:minlatitude) AND l.latitude < (:maxlatitude) AND l.longitude > (:minlongitude) AND l.longitude < (:maxlongitude)
			ORDER BY m.id
		');
		$stmt->bindParam(':minlatitude', $minLatitude);
		$stmt->bindParam(':maxlatitude', $maxLatitude);
		$stmt->bindParam(':minlongitude', $minLongitude);
		$stmt->bindParam(':maxlongitude', $maxLongitude);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
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

		$stmt = $this->getConnection()->prepare('
			INSERT INTO list_key_points
			(monument_id)
			VALUES
			(:monument_id)
			RETURNING id
		');
		foreach($data->getListsKeyPoints() as $list) {
			$stmt->bindParam(':monument_id', $monumentId);
			$stmt->execute();
			$listId = $stmt->fetch()['id'];

			$stmt = $this->getConnection()->prepare('
				INSERT INTO key_points
				(x, y, size, angle, response, octave, class_id, list_id)
				VALUES
				(:x, :y, :size, :angle, :response, :octave, :class_id, :list_id)
			');
			foreach($list->getKeyPoints() as $keyPoint) {
				$stmt->bindParam(':x', $keyPoint->getX());
				$stmt->bindParam(':y', $keyPoint->getY());
				$stmt->bindParam(':size', $keyPoint->getSize());
				$stmt->bindParam(':angle', $keyPoint->getAngle());
				$stmt->bindParam(':response', $keyPoint->getResponse());
				$stmt->bindParam(':octave', $keyPoint->getOctave());
				$stmt->bindParam(':class_id', $keyPoint->getClassId());
				$stmt->bindParam(':list_id', $listId);
				$stmt->execute();
			}
		}

		$stmt = $this->getConnection()->prepare('
			INSERT INTO descriptor
			(rows, cols, data, type, monument_id)
			VALUES
			(:rows, :cols, :data, :type, :monument_id)
		');
		foreach($data->getDescriptors() as $descriptor) {
			$stmt->bindParam(':rows', $descriptor->getRows());
			$stmt->bindParam(':cols', $descriptor->getCols());
			$stmt->bindParam(':data', $descriptor->getData());
			$stmt->bindParam(':type', $descriptor->getType());
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
			SET photopath = :photopath, year = :year, nbvisitors = :nbvisitors, nblikes = :nblikes, localization_id = :localization_id, address_id = :address_id
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

		$stmt1 = $this->getConnection()->prepare('
			INSERT INTO monument_characteristics
			(name, description, language_id, monument_id)
			VALUES
			(:name, :description, :language_id, :monument_id)
		');
		$stmt2 = $this->getConnection()->prepare('
			UPDATE monument_characteristics
			SET name = :name, description = :description, language_id = :language_id, monument_id = :monument_id
			WHERE id = :id
		');
		foreach($data->getCharacteristics() as $characteristic) {
			if($characteristic->getId() !== null) {
				$stmt = $stmt2;
				$stmt->bindParam(':id', $characteristic->getId());
			}
			else {
				$stmt = $this->getConnection()->prepare('
					SELECT mc.id
					FROM monument_characteristics mc
					INNER JOIN language l ON mc.language_id = l.id
					WHERE mc.monument_id = :monument_id AND l.name = :l_name AND l.value = :l_value
				');
				$stmt->bindParam(':monument_id', $monumentId);
				$stmt->bindParam(':l_name', $characteristic->getLanguage()->getName());
				$stmt->bindParam(':l_value', $characteristic->getLanguage()->getValue());
				$stmt->execute();

				if($row = $stmt->fetch()) {
					$stmt = $stmt2;
					$stmt->bindParam(':id', $row['id']);
				}
				else {
					$stmt = $stmt1;
				}
			}
			$languageDAO = new LanguageDAO($this->getConnection());
			$languageId = $languageDAO->save($characteristic->getLanguage());
			$stmt->bindParam(':name', $characteristic->getName());
			$stmt->bindParam(':description', $characteristic->getDescription());
			$stmt->bindParam(':language_id', $languageId);
			$stmt->bindParam(':monument_id', $monumentId);
			$stmt->execute();
		}

		$stmt1 = $this->getConnection()->prepare('
			INSERT INTO list_key_points
			(monument_id)
			VALUES
			(:monument_id)
			RETURNING id
		');
		$stmt2 = $this->getConnection()->prepare('
			UPDATE list_key_points
			SET monument_id = :monument_id
			WHERE id = :id
			RETURNING id
		');
		foreach($data->getListsKeyPoints() as $list) {
			if($list->getId() !== null) {
				$stmt = $stmt2;
				$stmt->bindParam(':id', $list->getId());
			}
			else {
				$stmt = $stmt1;
			}
			$stmt->bindParam(':monument_id', $monumentId);
			$stmt->execute();
			$listId = $stmt->fetch()['id'];

			$stmt3 = $this->getConnection()->prepare('
				INSERT INTO key_points
				(x, y, size, angle, response, octave, class_id, list_id)
				VALUES
				(:x, :y, :size, :angle, :response, :octave, :class_id, :list_id)
			');
			$stmt4 = $this->getConnection()->prepare('
				UPDATE key_points
				SET x = :x, y = :y, size = :size, angle = :angle, response = :response, octave = :octave, class_id = :class_id, list_id = :list_id
				WHERE id = :id
			');
			foreach($list->getKeyPoints() as $keyPoint) {
				if($keyPoint->getId() !== null) {
					$stmt = $stmt4;
					$stmt->bindParam(':id', $keyPoint->getId());
				}
				else {
					$stmt = $stmt3;
				}
				$stmt->bindParam(':x', $keyPoint->getX());
				$stmt->bindParam(':y', $keyPoint->getY());
				$stmt->bindParam(':size', $keyPoint->getSize());
				$stmt->bindParam(':angle', $keyPoint->getAngle());
				$stmt->bindParam(':response', $keyPoint->getResponse());
				$stmt->bindParam(':octave', $keyPoint->getOctave());
				$stmt->bindParam(':class_id', $keyPoint->getClassId());
				$stmt->bindParam(':list_id', $listId);
				$stmt->execute();
			}
		}

		$stmt1 = $this->getConnection()->prepare('
			INSERT INTO descriptor
			(rows, cols, data, type, monument_id)
			VALUES
			(:rows, :cols, :data, :type, :monument_id)
		');
		$stmt2 = $this->getConnection()->prepare('
			UPDATE descriptor
			SET rows = :rows, cols = :cols, data = :data, type = :type, monument_id = :monument_id
			WHERE id = :id
		');
		foreach($data->getDescriptors() as $descriptor) {
			if($descriptor->getId() !== null) {
				$stmt = $stmt2;
				$stmt->bindParam(':id', $list->getId());
			}
			else {
				$stmt = $stmt1;
			}
			$stmt->bindParam(':rows', $descriptor->getRows());
			$stmt->bindParam(':cols', $descriptor->getCols());
			$stmt->bindParam(':data', $descriptor->getData());
			$stmt->bindParam(':type', $descriptor->getType());
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
			DELETE FROM list_key_points
			WHERE id = :id
		');
		foreach($data->getListsKeyPoints() as $list) {
			$stmt2 = $this->getConnection()->prepare('
				DELETE FROM key_points
				WHERE id = :id
			');
			foreach($list->getKeyPoints() as $keyPoint) {
				$stmt2->bindParam(':id', $keyPoint->getId());
				$stmt2->execute();
			}
			$stmt->bindParam(':id', $list->getId());
			$stmt->execute();
		}
		$stmt = $this->getConnection()->prepare('
			DELETE FROM descriptor
			WHERE id = :id
		');
		foreach($data->getDescriptors() as $descriptor) {
			$stmt->bindParam(':id', $descriptor->getId());
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
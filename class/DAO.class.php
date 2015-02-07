<?php

abstract class DAO {
	private $connection;

	public function __construct(PDO $connection = null)	{
		$this->connection = $connection;
		if ($this->connection === null) {
			$this->connection = new PDO(
				'pgsql:host=localhost;port=6543;dbname=shazam',
				'isen',
				''
			);
			$this->connection->setAttribute(
				PDO::ATTR_ERRMODE,
				PDO::ERRMODE_EXCEPTION
			);
		}
	}

	public function getConnection() {
		return $this->connection;
	}

	public function setConnection(PDO $connection) {
		$this->connection = $connection;
	}

	public abstract function find($id);
	public abstract function findAll();
	public abstract function save($data);
	public abstract function update($data);
	public abstract function delete($data);
}

?>
<?php

class Database {

	private $dbHost = "localhost:3306";
	private $dbUser = "paneluser";
	private $dbPass = "!!!ldhaoz126549dgzhju31!!.___856kjfd3gs15fds321!abh-nignog-79p12t6778036bjavb..d57904b78ghjkqdvb";
	private $dbName = "panel-edit";

	protected $statement;
	protected $error;

	protected function connect() {

		try {
			
			$dsn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
			$pdo = new PDO($dsn, $this->dbUser, $this->dbPass);
			$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			return $pdo;
			
		} catch(PDOException $e) {

			print "Error!: " . $e->getMessage() . "<br/>";
			die();

		}

	}

	protected function query($sql) {

		$this->statement = $this->connect()->query($sql);

	}

	protected function prepare($sql) {

		$this->statement = $this->connect()->prepare($sql);
		
	}
	

}

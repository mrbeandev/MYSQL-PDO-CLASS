<?php

/**
* This Was Forked From https://github.com/a1phanumeric/PHP-MySQL-Class
* and Modified to make it more usefull.
* i have also added Error handling for each query.
*/

class DBPDO {
	public $pdo;
	private $error;
	
	function __construct() {
		$this->connect();
	}
	
	function prep_query($query){
		return $this->pdo->prepare($query);
	}
	
	function connect(){
		if(!$this->pdo){
			$dsn      = 'mysql:dbname=' . DATABASE_NAME . ';host=' . DATABASE_HOST.';charset=utf8';
			$user     = DATABASE_USER;
			$password = DATABASE_PASS;
			try {
				$this->pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_PERSISTENT => true));
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return true;
			} catch (PDOException $e) {
				$this->error = $e->getMessage();
				return $this->error;
			}
		}else{
			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			return true;
		}
	}


	function table_exists($table_name){
		try{
			$stmt = $this->prep_query('SHOW TABLES LIKE ?');
			$stmt->execute(array($table_name));
			return $stmt->rowCount() > 0;
		} catch(PDOException $e){
			return $e->getMessage();
		}
	}

	function execute($query, $values = null){
		try{
			if($values == null){
				$values = array();
			}else if(!is_array($values)){
				$values = array($values);
			}
			$stmt = $this->prep_query($query);
			$stmt->execute($values);
			return $stmt;
		} catch(PDOException $e){
			return $e->getMessage();
		}
	}
	
	function count($query, $values = null){
		try{
			if($values == null){
				$values = array();
			}else if(!is_array($values)){
				$values = array($values);
			}
			$stmt = $this->prep_query($query);
			$stmt->execute($values);
			$number = $stmt->rowCount();
			return $number;
		} catch(PDOException $e){
			return $e->getMessage();
		}
	}

	// this returns a php object
	function fetch($query, $values = null){
		try{
			if($values == null){
				$values = array();
			}else if(!is_array($values)){
				$values = array($values);
			}
			$stmt = $this->execute($query, $values);
			return $stmt->fetchObject(PDO::FETCH_ASSOC);
		} catch(PDOException $e){
			return $e->getMessage();
		}
	}

	function fetchAll($query, $values = null, $key = null){
		try{
			if($values == null){
				$values = array();
			}else if(!is_array($values)){
				$values = array($values);
			}
			$stmt = $this->execute($query, $values);
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Allows the user to retrieve results using a
			// column from the results as a key for the array
			if($key != null && $results[0][$key]){
				$keyed_results = array();
				foreach($results as $result){
					$keyed_results[$result[$key]] = $result;
				}
				$results = $keyed_results;
			}
			return $results;
		} catch(PDOException $e){
			return $e->getMessage();
		}
	}

	function lastInsertId(){
		return $this->pdo->lastInsertId();
	}

}

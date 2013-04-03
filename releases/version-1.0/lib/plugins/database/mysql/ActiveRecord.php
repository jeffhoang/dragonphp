<?php

/*
 ======================================================================
 DragonPHP - ActiveRecord
 
 A web application framework based on the MVC Model 2 architecture.
 
 by Jeff Hoang
 
 Latest version, features, manual and examples:
        http://www.dragonphp.com/

 -----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ======================================================================
 
 @package    plugins/database
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

include_once(APPLICATION_CONFIG_DIR . 'db.php');
require_once(FRAMEWORK_PLUGINS_DIR . 'database/ActiveRecordIF.php');
require_once(FRAMEWORK_COMMON_DIR . 'CommonLogger.php');

class ActiveRecord implements ActiveRecordIF {
	
	private $_connection;
	private $_entityData;
	protected $_TABLE_NAME;
	protected $_COLUMNS;
	protected $_dbServer;
	protected $_dbName;
	protected $_username;
	protected $_password;
	
	public static $logger;
	
	protected function _init(){
		$this->_entityData[$this->_TABLE_NAME] = array();
	}
	
	public function set($key, $value){

		array_push($this->_entityData[$this->_TABLE_NAME] ,array($key => $value));

	}
	
	public function getConnection(){
		
		try{
			
			$this->_connection = mysql_connect($this->_dbServer, $this->_username, $this->_password);
			mysql_select_db($this->_dbName);
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
		
		return $this->_connection;
	}
	
	public function beginTransaction(){
		
		if($this->_connection){
			$this->executeQuery('SET AUTOCOMMIT = 0');
			$this->executeQuery('BEGIN');
		}
	}
	
	public function commit(){
		
		if($this->_connection){
			$this->executeQuery('COMMIT');
		}
	}
	
	public function close(){
		mysql_close($this->_connection);
	}
	
	public function rollBack(){
		
		if($this->_connection){
			$this->executeQuery('ROLLBACK');
		}
	}
	
	public function save($isUpdate = false, $criteria = false){
		
		$status = 1;
		
		if($this->_entityData[$this->_TABLE_NAME]){
			
			try {
				$this->getConnection();
				$this->beginTransaction();
				$columns = $this->_entityData[$this->_TABLE_NAME];
				
				self::$logger->debug($columns, false, true);
				$query = '';
				
				if($isUpdate == true){
					$query = 'UPDATE ' . $this->_TABLE_NAME;
				}else {
					$query = 'INSERT INTO '. $this->_TABLE_NAME;
				}
				
				$i = 0;
				$nameStmt = '';
				$valueStmt = '';
			
				foreach($columns as $index => $columnInfo){

					foreach($columnInfo as $columnName => $columnValue){
						$name = $columnName;
						$value = $columnValue;
						self::$logger->debug($name . '-> ' . $value);
						$actualValue = '';
						
						if($this->_COLUMNS{$name}){
							
							$type = $this->_COLUMNS{$name};
							self::$logger->debug('type = ' . $type);
							
							if($type == 'integer' || $type == 'function' || $type == 'decimal' || $type == 'long' || $type == 'double' || $type == 'float' || $type == 'number'){
								$actualValue = $value;
							}else{
								$actualValue = "'" . $value . "'";
							}
							
							self::$logger->debug($type . '->' . $actualValue);
							
						}else{
							// default is set to a string type
							$actualValue = "'". $value . "'";
						}
						
						if($i > 0){
							
							if($isUpdate == true){
								$query .= ', ' . $name . '=' . $actualValue . ' ';	
								self::$logger->debug($sql);
							}else{							
								$nameStmt .= ','. $name;
								$valueStmt .= ',' . $actualValue;
							}
							
						}else{
							
							if($isUpdate == true){
								$query .= ' SET ' . $name . '=' . $actualValue . ' ';	
							}else{
								$nameStmt .= $name;
								$valueStmt .= $actualValue;
							}
						}
							
						$i++;
					}
				}
				
				if($isUpdate == false){
					$query .= ' (' . $nameStmt . ') values ('. $valueStmt . ')';
				}else{
					$query .= ' WHERE ';
					
					$count = 0;
					foreach($criteria as $k => $v){
						
						if($count == 0){
							$query  .= $k;
						}else{
							$query .= $v . ' ' . $k . ' ';
						}
						
						$count++;
					}
				}
				
				$result = $this->executeQuery($query, $this->_connection);
				
				self::$logger->debug($query);
				
				$errorMessage = mysql_error();
				
				if($errorMessage){
					$this->rollBack();
					$status = $errorMessage;
					self::$logger->error($errorMessage);
				}else{
					$this->commit();	
				}
			
				$this->close();
				
			} catch (Exception $ex){
				$status = $ex->getMessage();
				$this->rollBack();
				self::$logger->error($status);
			}
		}
		
		return $status;
	}
	
	public function update(){
		
		$this->save(true);
		
	}
	
	public function findByCriteria($criteria = false, $returnModels = false){
		return $this->find(false, false, $criteria, false, false, $returnModels);
	}
	
	public function findAll($criteria = false, $orderBy = false, $sortType = false, $returnModels = false, $columnNames = false){
		return $this->find(false, false, $criteria, $orderBy, $sortType, $returnModels, $columnNames);		
	}
	
	public function find($firstResult = false, $maxResults = false, $criteria = false, $orderBy = false, $sortType = false, $returnModels = false, $columnNames = false){
		
		$resultSet = null;
		
		try{
			$connection = $this->getConnection();
			
			if($columnNames == false){
				$sql = 'SELECT * FROM ' . $this->_TABLE_NAME;
			}else{
				$sql = 'SELECT ';
				$count = 0;
				foreach($columnNames as $k){
					if($count > 0){
						$sql .= ', ' .$k;
					}else{
						$sql .= $k;
					}
					$count++;
				}
				$sql .= ' FROM ' . $this->_TABLE_NAME;
			}
			
			if($criteria){
				$sql .= ' WHERE ';
				$i = 0;
				foreach($criteria as $k => $v){
					
					if($i == 0){	
						$sql .= $k;
					}else{
						$sql .= ' ' . $v . ' ' . $k . ' ';
					}
					
					$i++;
				}
			}

			if(isset($orderBy) && is_array($orderBy)){
				$sql .= ' ORDER BY ';
			 	$i = 0;
				foreach($orderBy as $column){
					if($i == 0){
						$sql .= $column;
					}else{
						$sql .= ',' . $column;
					}
					$i++;
				}
			}

			if(isset($orderBy) && isset($sortType)){
				$sql .= ' ' . $sortType;
			}
			
			if(($firstResult == 0 && !empty($maxResults))){
				$sql .= ' LIMIT ' . $maxResults;
			}elseif ((!empty($firstResult) || $firstResult == 0) && $maxResults){
				$sql .= ' LIMIT ' . $firstResult . ',' . $maxResults;
			}
			
			self::$logger->debug($sql);
			
			$resultSet = $this->executeQuery($sql);
		
		}catch(Exception $ex){
			self::$logger->error('Error during execute query', $ex);
		}
		
		if($returnModels){
			return $this->getModels($resultSet);
		}else {
			return $resultSet;
		}
	}
	
	public function executeQuery($sql){
		return mysql_query($sql);
	}

	public function delete($criteria) {
		
		if($criteria){
			
			try {
				$sql = 'DELETE from ' . $this->_TABLE_NAME . ' WHERE ';
				
				foreach($criteria as $k => $v){
					$sql .= $k . '=' . $v;
				}
				
				$connection = $this->getConnection();
				
				self::$logger->debug($sql);
				
				$this->executeQuery($sql);
		
			}catch(Exception $ex){
				self::$logger->error('Could not delete record', $ex);
			}
		}
	}
	
	public function getModels($result){

		if($result){
			$total = mysql_num_rows($result);
			$models = array();
			
			self::$logger->debug($this->_COLUMNS, false, true);
			
			for ($i=0; $i < $total; $i++) { 
				
				$rowInfo = mysql_fetch_object($result);
				// store object
				$model = new Model();
				foreach($this->_COLUMNS as $columnName => $columnType){

					$columnValue = stripslashes($rowInfo->{$columnName});
					self::$logger->debug($columnName . '='. $columnValue);
					$model->$columnName = $columnValue;
				}
				$models[] = $model;
			}
		}
		return $models;
	}
	
	public function getTotal($model){
		if(isset($model)){
			$columnName = 'count';
			return $model[0]->$columnName;
		}else{
			return 0;
		}
	}
}
?>
<?php

/*
 ======================================================================
 DragonPHP - ActiveRecord (using PDO)

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

require_once(FRAMEWORK_PLUGINS_DIR . 'database/ActiveRecordIF.php');
require_once(FRAMEWORK_COMMON_DIR . 'CommonLogger.php');
require_once(FRAMEWORK_HELPERS_DIR . 'CacheHelper.php');
require_once(FRAMEWORK_COMMON_DIR . 'IniParser.php');
require_once(FRAMEWORK_COMMON_DIR . 'FrameworkConstants.php');
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');
require_once(FRAMEWORK_MODEL_DIR . 'Model.php');
require_once(FRAMEWORK_HELPERS_DIR . 'StringUtilHelper.php');

class ActiveRecord implements ActiveRecordIF {

	protected $_data = array();
	private static $_db;

	const DB_TYPE = 'mysql';
	const ENTITY_CACHE_AGE = '36000';
	const PRIMARY_KEY_DIRECTIVE = 'PRI';

	protected static $_logger;
	protected static $_batch;
	protected static $_dbSessions = array();
	protected static $_currentBatches = array();

	private static $_datasourceRegistry = array();

	private $_currentDataSourceName = null;

	private $_returnErrors = false;
	
	public function __get($record) {
        if (isset($this->_data[$record])) {
            return $this->_data[$record];
        }
    }

    public function __set($key, $value) {
    	$this->_data[$key] = $value;
    }

    public function setData($data){
    	$this->_data = $data;
    }

   	public function init(){

   		// create logger
		self::$_logger = LoggerFactory::getInstance(ucfirst($this->{FrameworkConstants::ENTITY_NAME}) . 'ActiveRecord');

   		// load database configs
   		$file = $this->{FrameworkConstants::DATABASE_CONFIG_FILE};
   		$configSection = $this->{FrameworkConstants::DATABASE_CONFIG_SECTION};

   		if(!is_file($file)){
   			throw new Exception("Can't find database config file (" . $file . ")...");
   		}

   		self::$_logger->debug('Looking for db section -> ' . $configSection);

   		$configData = IniParser::parse($file, $configSection);

   		$data = $configData[$configSection];

		$this->{FrameworkConstants::DATABASE_TYPE} = $data[FrameworkConstants::DATABASE_TYPE];
		$this->{FrameworkConstants::DATABASE_HOST} = $data[FrameworkConstants::DATABASE_HOST];
		$this->{FrameworkConstants::DATABASE_NAME} = $data[FrameworkConstants::DATABASE_NAME];
		$this->{FrameworkConstants::DATABASE_USERNAME} = $data[FrameworkConstants::DATABASE_USERNAME];
		$this->{FrameworkConstants::DATABASE_PASSWORD} = $data[FrameworkConstants::DATABASE_PASSWORD];
		$this->{FrameworkConstants::MYSQL_COMMAND} = $data[FrameworkConstants::MYSQL_COMMAND];
		$this->{FrameworkConstants::MYSQL_COMMAND_CONTINUE_ON_ERROR} = $data[FrameworkConstants::MYSQL_COMMAND_CONTINUE_ON_ERROR];

		$this->_currentDataSourceName = $this->{FrameworkConstants::DATABASE_CONFIG_SECTION};

		$this->_returnErrors = $this->{FrameworkConstants::RETURN_ERRORS};
		
		// Load table info
		$this->getTableInfo();
	}

	public function getConnection(){

		try {

			$dbConnection = self::$_datasourceRegistry[$this->_currentDataSourceName];

			if(!isset($dbConnection) || $dbConnection == null){

				$dbType = $this->{FrameworkConstants::DATABASE_TYPE};
				$host = $this->{FrameworkConstants::DATABASE_HOST};
				$dbName = $this->{FrameworkConstants::DATABASE_NAME};
				$userName = $this->{FrameworkConstants::DATABASE_USERNAME};
				$password = $this->{FrameworkConstants::DATABASE_PASSWORD};

				self::$_datasourceRegistry[$this->_currentDataSourceName] = new PDO($dbType . ':host='. $host . ';dbname='. $dbName, $userName, $password);
			}

		} catch( PDOException $e ){


			$traceMessage = '<p>Message: '.  $e->getMessage();

			if(strcasecmp(LOGGER_LEVEL, '0') == 0){
				$traceMessage .= '<p>StackTrace: <pre>' . $e->getTraceAsString() . '</pre></p>';
			}

			throw new PDOException($traceMessage);
		}
	}

	public function beginTransaction(){

		if(!self::$_datasourceRegistry[$this->_currentDataSourceName]){
			$this->getConnection();
		}

		self::$_datasourceRegistry[$this->_currentDataSourceName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 		self::$_datasourceRegistry[$this->_currentDataSourceName]->beginTransaction();

	}

	public function commit(){
		self::$_datasourceRegistry[$this->_currentDataSourceName]->commit();
	}

	public function close(){

		if(!$this->{FrameworkConstants::DATABASE_PERSIST}){
			self::$_datasourceRegistry[$this->_currentDataSourceName] = null;
		}
	}

	public function endTransaction(){
		self::$_datasourceRegistry[$this->_currentDataSourceName]->commit();
		$this->close();
	}

	public function rollBack(){
		self::$_datasourceRegistry[$this->_currentDataSourceName]->rollback();
	}

	public function rollBackTransaction(){
		$this->rollBack();
		self::$_datasourceRegistry[$this->_currentDataSourceName] = null;
	}

	public function getUpdateString($criteria = false){
		return $this->update($criteria, true);
	}

	public function update($criteria = false, $returnQueryString = false){

		try{

			$sql = $this->update_statement;

			// get input data
			$data = $this->data;

			$count = 0;

			$inputParams = array();

			// set the input params
			foreach($this->columns as $field){
				$fieldName = $field->Field;

				$primaryKey = $field->Key;

				if(!strcmp($primaryKey, 'PRI') == 0){

					if(isset($data[$fieldName])){

						if($count > 0){
							$sql .= ', ' . $fieldName . ' = ?';
						}else{
							
							$sql .= ' set ' . $fieldName . ' = ?';
						}

						$inputParams[] = $data[$fieldName];

						$count++;
					}

				}
			}

			// check for criteria
			$criteria = $this->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS};
			$criteriaData = $this->{FrameworkConstants::CRITERIA_DATA};

			if(isset($criteria) && is_array($criteria)){

				$count = 0;
				foreach($criteria as $fieldInfo){

					$fieldName = $fieldInfo['field_name'];
					$expression = $fieldInfo['expression'];
					$junction = $fieldInfo['junction'];

					if($count == 0){
						$sql .= ' where ' . $fieldName . ' ' . $expression . ' ? ';

					}else{
						$sql .= ' ' . $junction . ' ' . $fieldName . ' ' . $expression . ' ? ';
					}

					$inputParams[] = $criteriaData[$count];
					$count++;
				}
			}

			self::$_logger->debug($sql);

			if(!$returnQueryString){
				$this->beginTransaction();

				$this->executePreparedStatement($sql, false, false, $inputParams);

				$this->endTransaction();
			}else{
				return $sql;
			}

		}catch(Exception $ex){

			if(!$returnQueryString){
				$this->rollBackTransaction();
			}

			throw new Exception($ex->getMessage());
		}
	}

	public function getDeleteString($criteria = false){
		
		try {
			return $this->delete($criteria, true);
		}catch(Exception $ex){
			
			throw new Exception($ex->getMessage());
		}
	}

	public function delete($criteria = false, $returnQueryString = false){

		try{

			if(!$returnQueryString){
				$this->beginTransaction();
			}

			$sql = $this->delete_statement;

			// check for criteria
			$criteriaCondition = $this->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS};
			$criteriaData = $this->{FrameworkConstants::CRITERIA_DATA};

			$inputParams = null;

			if(isset($criteriaCondition) && isset($criteriaData) && is_string($criteriaCondition)){
				$sql .= ' where '. $criteriaCondition;

				$inputParams = array();

				foreach($criteriaData as $data){
					$inputParams[] = $data;
				}
			}elseif(is_array($criteriaCondition)){

				$count = 0;

				if(!isset($inputParams)){
					$inputParams = array();
				}

				foreach($criteriaCondition as $fieldInfo){

					$fieldName = $fieldInfo['field_name'];
					$expression = $fieldInfo['expression'];
					$junction = $fieldInfo['junction'];

					if($count == 0){

						if(empty($joins)){

							$sql .= ' where ' . $fieldName . ' ' . $expression . ' ? ';
						}else {
							$sql .= ' ' . $junction . ' ' . $fieldName . ' ' . $expression . ' ? ';
						}
					}else{
						$sql .= ' ' . $junction . ' ' . $fieldName . ' ' . $expression . ' ? ';
					}

					$inputParams[] = $criteriaData[$count];
					$count++;
				}
			}

			self::$_logger->debug($sql);
			self::$_logger->debug($inputParams, true, true);

			if(!$returnQueryString){

				$this->executePreparedStatement($sql, false, false, $inputParams);

				$this->endTransaction();

			}else{

				return $sql;
			}

		}catch(Exception $ex){

			if(!$returnQueryString){
				$this->rollBackTransaction();
			}

			throw new Exception($ex->getMessage());
		}
	}

	public function getCreateString($criteria = false, $updateOnDuplicate = false){
		return $this->create($criteria, $updateOnDuplicate, true);
	}

	public function addToBatch($criteria = false, $updateOnDuplicate = false){
		return $this->create($criteria, $updateOnDuplicate, true, true);
	}

	public function create($criteria = false, $updateOnDuplicate = false, $returnQueryString = false, $addToBatch = false){

		$entityObject = new Model();

		// default id name
		$idName = 'id';

		try{

			if(!$returnQueryString){
				$this->beginTransaction();
			}

			$inputParams = array();

			// get input data
			$data = $this->data;

			$primaryKeyName = null;

			if($returnQueryString){
				$fieldValuesString = '';
				$totalColumns = sizeof($this->columns);
			}

			// set the input params
			$cnt = 1;

			$fieldTypes = array();

			foreach($this->columns as $field){
				$fieldName = $field->Field;
				$primaryKey = $field->Key;
				$extra = $field->Extra;
				$allowNull = $field->Null;
				$fieldType = $field->Type;

				$fieldTypes[$fieldName] = $fieldType;

				if($cnt == 1){
					$fieldValuesString = '(';
				}

				if(isset($primaryKey) && strcmp($extra, 'auto_increment') == 0){
					// set id name
					$idName = $fieldName;

					$fieldName = null;

					$totalColumns = $totalColumns - 1;

				}

				if(isset($primaryKey) && strcmp($primaryKey, self::PRIMARY_KEY_DIRECTIVE) == 0){
					$primaryKeyName = $fieldName;
				}

				if($fieldName){
					$fieldValue = $data[$fieldName];

					self::$_logger->debug($fieldName . ' = ' . $fieldValue);

					if(strcmp($allowNull, 'NO') == 0 && !isset($fieldValue)){
						throw new Exception ('Error! ' . $fieldName . ' can not be NULL');
					}elseif(isset($fieldValue)){
						$inputParams[] = $fieldValue;
						$entityObject->{$fieldName} = $fieldValue;

						if($returnQueryString){

							if(preg_match('/long|real|double|float|decimal|numeric/', $fieldType)){

								if($cnt < $totalColumns){
									$fieldValuesString .= $fieldValue . ',';
								}elseif($cnt == $totalColumns){
									$fieldValuesString .= $fieldValue . ')';
								}

							}else{

								if($cnt < $totalColumns){
									$fieldValuesString .= "'". $fieldValue . "',";
								}elseif($cnt == $totalColumns){
									$fieldValuesString .= "'" .$fieldValue . "')";
								}
							}
						}

					}else{

						$inputParams[] = null;

							if($returnQueryString){

							if(preg_match('/long|real|double|float|decimal|numeric/', $fieldType)){

								if($cnt < $totalColumns){
									$fieldValuesString .=  ',';
								}elseif($cnt == $totalColumns){
									$fieldValuesString .= ')';
								}

							}else{

								if($cnt < $totalColumns){
									$fieldValuesString .= "'',";
								}elseif($cnt == $totalColumns){
									$fieldValuesString .= "'')";
								}
							}
						}

						$entityObject->{$fieldName} = null;
					}

					$cnt++;
				}
			}

			$sql = $this->create_statement;

			$baseSqlString = $sql;

			$finalString = '';

			if($returnQueryString){
				$baseSqlString = preg_replace('/(.*)(\(.*\))/', '$1', $baseSqlString);

				$finalString .= $baseSqlString . ' ' .$fieldValuesString;

				self::$_logger->debug('SQL Statement String: ' . $finalString);
			}

			if($updateOnDuplicate){
				$sql .= ' ON duplicate KEY UPDATE ';

				if($returnQueryString){
					$finalString .= ' ON duplicate KEY UPDATE ';
				}

				$cnt = 0;
				foreach($this->columns as $field){
				foreach($data as $fieldName=>$fieldValue){
					if (strcmp($fieldName, $field->Field) == 0) {
					if(strcmp($fieldName, $primaryKeyName) == 0){

						self::$_logger->debug('Primary Key = ' . $fieldName);

					}else{

						if($cnt == 0 && isset($fieldValue)){

							$sql .= $fieldName . '= ?';

							$inputParams[] = $fieldValue;

							$fieldType = $fieldTypes[$fieldName];

							if($returnQueryString){
								if(preg_match('/long|real|double|float|decimal|numeric/', $fieldType)){
									$finalString .= $fieldName . ' = ' . $fieldValue;
								
								}else{
									$finalString .= $fieldName . " = '" . $fieldValue . "' ";
								}
							}

							$cnt++;
						}elseif($cnt > 0 && isset($fieldValue)){

							$sql .= ', ' . $fieldName . '= ?';

							$inputParams[] = $fieldValue;

							$fieldType = $fieldTypes[$fieldName];
							
							if($returnQueryString){
								
								if(preg_match('/int|long|real|double|float|decimal|numeric/', $fieldType)){
									$finalString .=  ' , ' . $fieldName . ' = ' . $fieldValue;
								
								}else{
										
								
									$finalString .= ' , ' . $fieldName . " = '" . $fieldValue . "' ";
								}
							}

							$cnt++;
						}

						//echo $fieldName . '=' . $fieldValue . "\n";
					}
				}
			}
				}
			}
			if($returnQueryString){

				// check if execute batch insert from command line has been requested
				if($addToBatch){

					if(empty(self::$_batch)){
						self::$_batch = array();
					}

					// check if db session id has been set

					if(empty(self::$_dbSessions[$this->entity_name])){
						self::$_dbSessions[$this->entity_name] = uniqid(rand(), true) . '_' . $this->entity_name;
					}

					// write string temporily to a file for processing;
					if (empty(self::$_currentBatches[self::$_dbSessions[$this->entity_name]])) {

						$tempDir = APPLICATION_ACTIVE_RECORD_CACHE_DIR . date('Y.m.d.H', time()) . '/';
						$fileName = 'ar_' . self::$_dbSessions[$this->entity_name] . '.sql';
						self::$_currentBatches[self::$_dbSessions[$this->entity_name]] = $tempDir . $fileName;

					} else {

						$tempDir = dirname(self::$_currentBatches[self::$_dbSessions[$this->entity_name]]) . '/';
						$fileName = basename(self::$_currentBatches[self::$_dbSessions[$this->entity_name]]);
					}

					if(!is_dir($tempDir)){
						mkdir($tempDir, 0777, true);
					}

					$cachedFile = $tempDir . $fileName;

					self::$_logger->debug('SQL Statement: ' . $finalString);

					$finalString .= ';';

					if(!is_file($cachedFile)){
						// echo "NEW insert : Current Session " . self::$_datasourceRegistry[$this->_currentDataSourceName]Sessions[$this->entity_name] . "\n";
						//When creating new file, set autocommit = 0;
						CacheHelper::saveCache($tempDir, $fileName, "SET AUTOCOMMIT=0;", false, true);
					}

					// echo "APPEND : Current Session " . self::$_datasourceRegistry[$this->_currentDataSourceName]Sessions[$this->entity_name] . "\n";
					// append to batch file
					CacheHelper::addToCache($tempDir, $fileName, $finalString);
				}

				return $finalString;
			}

			// execute the query
			self::$_logger->debug($inputParams, true, true);

			if($updateOnDuplicate){
					self::$_logger->debug('Update on duplicate sql: ' . $sql);
			}

			$id = $this->executePreparedStatement($sql, false, true, $inputParams);

			$entityObject->{$idName} = $id;

			$this->endTransaction();

		}catch(Exception $ex){

			$this->rollBackTransaction();
			throw new Exception($ex->getMessage());
		}

		return $entityObject;
	}

	public function commitBatch($cleanup = false, $continueOnError = false){

		$count = 0;

		if(sizeof(self::$_currentBatches) > 0){

			foreach(self::$_currentBatches as $id=>$batchFile){

				if(preg_match('/' . $this->entity_name . '/' , $id)){
					self::$_logger->debug('Looking for batch file ' . $batchFile);

					if(is_file($batchFile)){

						//before executing batch, use command 'COMMIT'
						$tempDir = dirname($batchFile) . '/';
						$fileName = basename($batchFile);
						CacheHelper::addToCache($tempDir, $fileName, "COMMIT;");

						self::$_logger->debug('Processing batch file ' . $batchFile);

						$dbUsername = $this->{FrameworkConstants::DATABASE_USERNAME};
						$dbPassword = $this->{FrameworkConstants::DATABASE_PASSWORD};
						$dbHost = $this->{FrameworkConstants::DATABASE_HOST};
						$dbName = $this->{FrameworkConstants::DATABASE_NAME};

						$mysqlCommand = $this->{FrameworkConstants::MYSQL_COMMAND};

						if(empty($mysqlCommand)){
							throw new Exception('MYSQL command line parameter was not set in your env_db.ini for datasource ' . $this->{FrameworkConstants::DATABASE_CONFIG_SECTION});
						}

						if(!$continueOnError){
							$continueOnError = $this->{FrameworkConstants::MYSQL_COMMAND_CONTINUE_ON_ERROR};
						}

						if(!empty($continueOnError) || $continueOnError){
							$continueOnError = '--force';
						}

						$command = $mysqlCommand . ' ' . $continueOnError . ' --user=' . $dbUsername . ' --password=' . $dbPassword . ' --host=' . $dbHost . ' --database= ' . $dbName;

						$command .= ' < ' . $batchFile;

						exec($command, $output, $count);

						self::$_logger->debug($output);

						if($cleanup){
							unlink($batchFile);
						}
					}
				}
			}
		}
	}

	public function read($criteria = false, $insert_select = false){

		$data = null;

		try{

			$this->getConnection();

			$sql = $this->read_statement;

			$joins = $this->joins;

			if(isset($joins)){
				$sql = $this->joined_query;
			}

			$resultSet = $this->{FrameworkConstants::RESULT_SET};

			if(!empty($resultSet)){
				//$resulSet = preg_replace("/\*/", "\\*", $resulSet);
				$sql = preg_replace("/\*/", "$resultSet", $sql);
			}

			$preparedCriteria = $this->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS};
			$criteriaData = $this->{FrameworkConstants::CRITERIA_DATA};
			$conditions = $this->{FrameworkConstants::CONDITIONS};
			$entities = $this->{FrameworkConstants::ENTITIES};
			$inputParams = null;

			if(!empty($entities)){
				$sql .= ',' . $entities;
			}
			
			if(!empty($preparedCriteria) && !empty($criteriaData) && is_string($preparedCriteria)){
				$inputParams = array();

				if(!$this->joins){
					$sql .= ' where ' . $preparedCriteria;
				}else{
					$sql .= ' and ' . $preparedCriteria;
				}

				foreach($criteriaData as $data){
					$inputParams[] = $data;
				}
			} elseif (!empty($conditions) && empty($preparedCriteria)){
				$sql .= ' where ' . $conditions;
			}

			if(isset($preparedCriteria) && is_array($preparedCriteria)){

				$count = 0;

				if(!isset($inputParams)){
					$inputParams = array();
				}

				foreach($preparedCriteria as $fieldInfo){

					$fieldName = $fieldInfo['field_name'];
					$expression = $fieldInfo['expression'];
					$junction = $fieldInfo['junction'];
					$leftBound = $fieldInfo['left_bound'];
					$rightBound = $fieldInfo['right_bound'];
					$leftParenthesis = $fieldInfo['left_parenthesis'];
					$rightParenthesis = $fieldInfo['right_parenthesis'];

					if($count == 0){

						if(empty($joins)){

							if(!$leftBound || !$rightBound){
								
								if($leftParenthesis){
									$sql .= ' where ' . $junction . ' (' . $fieldName . ' ' . $expression . ' ? ';
								}else{
									$sql .= ' where ' . $fieldName . ' ' . $expression . ' ? ';
								}
								
							}else{
								
								if($leftParenthesis){
									$sql .= ' (' . $fieldName . ' ' . $expression . ' ' . $leftBound . '?' . $rightBound;
								}else{
									$sql .= ' where ' . $fieldName . ' ' . $expression . ' ' . $leftBound . '?' . $rightBound;
								}
							}
						}else {


							if(!$leftBound || !$rightBound){

								if($leftParenthesis){
									$sql .= ' ' . $junction . ' (' . $fieldName . ' ' . $expression . ' ? ';
								}else{
									$sql .= ' ' . $junction . ' ' . $fieldName . ' ' . $expression . ' ? ';
								}
							}else{

								if($leftParenthesis){
									$sql .= ' (' . $fieldName . ' ' . $expression . ' ' . $leftBound . '?' . $rightBound;
								}else{
									$sql .= ' ' . $junction . ' ' . $fieldName . ' ' . $expression . ' ' . $leftBound . '?' . $rightBound;
								}
							}

						}
					}else{

						if(!$leftBound || !$rightBound){

							if($rightParenthesis){
								$sql .= $junction . ' ' . $fieldName . ' ' . $expression . ' ? ' . ') ';
							}else{
								$sql .= $junction . ' ' . $fieldName . ' ' . $expression . ' ? ';
							}

						}else{

							if($rightParenthesis){
								$sql .= ' ' . $junction . ' ' . $fieldName . ' ' . $expression . ' ' . $leftBound . '?' . $rightBound . ') ';
							}else{
								$sql .= ' ' . $junction . ' ' . $fieldName . ' ' . $expression . ' ' . $leftBound . '?' . $rightBound;
							}
						}

					}

					$inputParams[] = $criteriaData[$count];
					$count++;
				}
			}

			$groupBy = $this->{FrameworkConstants::GROUP_BY};
			if(!empty($groupBy)){
				$sql .= ' group by ' . $groupBy;
			}

			$orderBy = $this->{FrameworkConstants::ORDER_BY};

			if(!empty($orderBy)){
				$sql .= ' order by ' . $orderBy;
			}

			$first = $this->{FrameworkConstants::FIRST_RESULT};
			$last = $this->{FrameworkConstants::LAST_RESULT};

			if(!empty($first) && !empty($last)){
                $sql .= ' limit ' . $first . ',' . $last;
			}elseif(empty($first) && !empty($last)){
                $sql .= ' limit ' . $last;
			}

			if ($insert_select){
				$insert_select_statement = 'INSERT ' . $this->{FrameworkConstants::INSERT_SELECT_TABLE} . ' ';
				if ($this->{FrameworkConstants::INSERT_SELECT_COLUMNS}){
					$insert_select_statement .= $this->{FrameworkConstants::INSERT_SELECT_COLUMNS} . ' ';
				}
				$sql = $insert_select_statement . $sql;
			}
			
			$rowKey = $this->{FrameworkConstants::ROW_KEY};

			$data = $this->executePreparedStatement($sql, true, false, $inputParams, $rowKey);

			$this->close();

		}catch(Exception $ex){
			$this->close();
			throw new Exception($ex->getMessage());
		}
		if ($insert_select){
			return ;
		}
		
		return $data;
	}

	public function copy($criteria = false){
		$this->read($criteria, true);
	}
	

	public function insertBatch($criteria){

		try{

			$mainData = $this->{FrameworkConstants::BATCH_DATA};

			if(is_array($mainData)){

				$sql = $this->create_statement;

				$this->beginTransaction();

				foreach($mainData as $dataRow){

					$inputParams = array();

					foreach($dataRow as $data){
						$inputParams[] = $data;
					}

					$this->executePreparedStatement($sql, false, false, $inputParams);
				}

				$this->endTransaction();


			}

		}catch(Exception $ex){
			$this->rollBackTransaction();
			throw new Exception($ex->getMessage());
		}
	}

	public function call($procedureName, $parameters){

		try {

			$this->getConnection();

			$sql = 'call ' . $procedureName . '(';

			$inputParams = array();
			$count = 0;
			foreach($parameters as $parameter){

				if($count == 0){
					$sql .= '?';
				}else{
					$sql .= ',?';
				}

				$inputParams[] = $parameter;
				$count++;
			}

			$sql .= ')';

			$rowKey = $this->{FrameworkConstants::ROW_KEY};

			$data = $this->executePreparedStatement($sql, true, false, $inputParams, $rowKey);

		}catch(Exception $ex){
			$this->close();
			throw new Exception($ex->getMessage());
		}
	}

	public function executePreparedStatement($sql, $returnResults = false, $returnKey = false, $inputParams = false, $rowKey = false){

		try {

			self::$_logger->debug('executing query: ' . $sql);
			self::$_logger->debug($inputParams, true, true);

			$stmt = self::$_datasourceRegistry[$this->_currentDataSourceName]->prepare($sql);

			$data = array();

			if($returnResults){

				if($inputParams){
					if( $stmt->execute($inputParams)) {
						while( $row = $stmt->fetch(PDO::FETCH_OBJ)) {

							if($rowKey){
								$data[$row->{$rowKey}] = $row;
							}else{
			    				$data[] = $row;
							}

							if(!row){
								break;
							}
		    			}
		    		}
				}else{
					if( $stmt->execute()) {
						while( $row = $stmt->fetch(PDO::FETCH_OBJ)) {
							if($rowKey){
								$data[$row->{$rowKey}] = $row;
							}else{
			    				$data[] = $row;
							}

							if(!row){
								break;
							}
		    			}
		    		}
				}

	    		if($returnKey){
	    			$id = $this->_getLastInsertedKey();
	    			$data['primary_key'] = $id;
	    		}

	    		self::$_logger->debug($data, true, true);

	    		return $data;
			}else{
				$stmt->execute($inputParams);

				if($returnKey){
					return $this->_getLastInsertedKey();
				}
			}
		}catch(Exception $ex){
			
			self::$_logger->error($ex->getMessage());
			if($this->_returnErrors == true){
			
				throw new Exception($ex->getMessage());
			}
		}
	}

	protected function _getLastInsertedKey(){

		$id = null;

		$stmt = self::$_datasourceRegistry[$this->_currentDataSourceName]->prepare("SELECT LAST_INSERT_ID()");
		if ($stmt->execute()) {
  			while ($row = $stmt->fetch()) {
   				$id = $row[0];
  			}
		}
		return $id;
	}

	public function getTableInfo(){

		try {

			$entityName = $this->{FrameworkConstants::ENTITY_NAME};

			$entityName = strtolower(StringUtilHelper::underscore($entityName));

			// pluralize it
			$lastCharacter = substr($entityName, strlen($entityName) - 1, 1);

			if(strcmp($lastCharacter, 's') != 0 && $this->{FrameworkConstants::PLURALIZE}){
				$entityName .= 's';
			}else{
				// check last 2 characters and pluralize accordingly
				$lastTwoCharacters = substr($entityName, strlen($entityName) - 2, 2);

				if(strcasecmp($lastTwoCharacters, 'ss') == 0){
					$entityName .= 'es';
				}
			}

			self::$_logger->debug('Entity name -> ' . $entityName);

			self::$_logger->debug('Caching within: ' . CACHE_DIR . APPLICATION_NAME . '/database_tables/');

			$entityFile = $this->{FrameworkConstants::DATABASE_NAME } . '_' . $entityName;

			// check from cache first
			$fields = CacheHelper::getCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/', $entityFile, self::ENTITY_CACHE_AGE, true);

			if(empty($fields)){

				$fields = array();
				$this->getConnection();


				$sql = 'SHOW COLUMNS FROM ' . $entityName;

				$stmt = self::$_datasourceRegistry[$this->_currentDataSourceName]->prepare($sql);

				if( $stmt->execute()) {
	    			while( $row = $stmt->fetch(PDO::FETCH_OBJ)) {
	    				$fields[$row->Field] = $row;
	    			}
				}

				// cache the fields
				CacheHelper::saveCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/' , $entityFile, $fields, true);

				$this->close();
			}

			$this->columns = $fields;

			$createSql = CacheHelper::getCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/', $entityFile  . '_create_statement', self::ENTITY_CACHE_AGE);

			if(empty($createSql)){

				// create crud prepared statements
				$createSql = 'insert into ' . $entityName;

				$sqlFields = '';
				$sqlValues = '';
				$count = 0;

				foreach($fields as $field){
					$primaryKey = $field->Key;
					$extra = $field->Extra;
					$fieldName = $field->Field;

					if(isset($primaryKey) && strcmp($extra, 'auto_increment') == 0){
						$fieldName = null;
					}

					if($fieldName){
						if($count < 1){
							$sqlFields = $fieldName;
							$sqlValues = '?' ;
						}else{
							$sqlFields .= ', ' . $fieldName;
							$sqlValues .= ', ?';
						}

						$count++;
					}
				}

				$createSql = $createSql . '(' . $sqlFields . ') values (' . $sqlValues . ')';

				// cache this prepared statement
				CacheHelper::saveCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/' , $entityFile . '_create_statement', $createSql);
			}


			$this->create_statement = $createSql;
			self::$_logger->debug('Caching: ' . $createSql);

			// create read/select prepared statement
			$readSql = CacheHelper::getCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/', $entityFile  . '_read_statement', self::ENTITY_CACHE_AGE);

			if(empty($readSql)){

				$readSql = 'select * from ' . $entityName;

				CacheHelper::saveCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/' , $entityFile . '_read_statement', $readSql);
			}

			$this->read_statement = $readSql;
			self::$_logger->debug('Caching: ' . $readSql);

			// create update prepared statement
			$updateSql = CacheHelper::getCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/', $entityFile  . '_update_statement', self::ENTITY_CACHE_AGE);

			if(empty($updateSql)){

				$updateSql = 'update ' . $entityName;

				CacheHelper::saveCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/' , $entityFile . '_update_statement', $updateSql);
			}

			$this->update_statement = $updateSql;
			self::$_logger->debug('Caching: ' . $updateSql);

			// create delete prepared statement
			$deleteSql = CacheHelper::getCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/', $entityFile  . '_delete_statement', self::ENTITY_CACHE_AGE);

			if(empty($deleteSql)){

				$deleteSql = 'delete from ' . $entityName;

				CacheHelper::saveCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/' , $entityFile . '_delete_statement', $deleteSql);
			}

			$this->delete_statement = $deleteSql;
			self::$_logger->debug($deleteSql);

			if($this->joins){

				$from = $this->joins['from'];
				$where = $this->joins['where'];

				//$sql = CacheHelper::getCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/', $from . '_' . $where . '_statement', self::ENTITY_CACHE_AGE);

				//if(empty($sql)){
				$sql = 'select * from ' . $from . ' where ' . $where;

					//CacheHelper::saveCache(CACHE_DIR . APPLICATION_NAME . '/database_tables/' , $from . '_' . $where . '_statement', $sql);
				//}

				$this->joined_query = $sql;

				self::$_logger->debug('Caching: ' . $sql);
			}

		}catch(Exception $ex){
			$this->close();
			self::$_logger->error('Could not delete record', $ex);
		}
	}
}
?>
<?php
 
 /*
 ======================================================================
 DragonPHP - ServiceManager

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

 @package    services
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang

 */

require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');
require_once(FRAMEWORK_COMMON_DIR . 'FrameworkConstants.php');

class ServiceManager {

	static $_instance;
	private $_data;
	private $_currentService;

	const DEFAULT_SERVICE_CLASS = 'ActiveService';

	/**
	 * getInstance
	 *
	 * Get an instance of a service manager
	 *
	 * @param unknown_type $serviceModule - name of service module
	 * @param unknown_type $serviceName - service name
	 * @param unknown_type $serviceRootDir - optional service root directory; the default the APPLICATION's /lib/services dir
	 * @return unknown
	 */
	public static function getInstance($serviceModule = false, $serviceName = false, $serviceRootDir = false, $databaseDir = false, $datasource = false, $enablePersistancy = false) {

		$enablePersistancy = false;

		if( !isset($_instance) ) {
			self::$_instance = new self();
		}

		self::$_instance->{FrameworkConstants::SERVICE_MODULE} = $serviceModule;
		self::$_instance->{FrameworkConstants::SERVICE_NAME} = $serviceName;

		if($serviceRootDir){
			self::$_instance->{FrameworkConstants::SERVICE_DIR} = $serviceRootDir;
		}else{
			self::$_instance->{FrameworkConstants::SERVICE_DIR} = APPLICATION_LIB_DIR . 'services/';
		}

		if($databaseDir){
			self::$_instance->{FrameworkConstants::DAO_DIR} = $databaseDir;
		}else{
			self::$_instance->{FrameworkConstants::DAO_DIR} = APPLICATION_LIB_DIR . 'database/';
		}

		if($datasource){
			self::$_instance->{FrameworkConstants::DATABASE_CONFIG_SECTION} = $datasource;
		}

		if($enablePersistancy){
			self::$_instance->{FrameworkConstants::DATABASE_PERSIST} = true;
		}

		self::$_instance->{FrameworkConstants::PLURALIZE} = 'true';

		return self::$_instance;
	}
	
	public static function getConnection($datasource, $serviceName = false){
	
		return self::getInstance(false, $serviceName, false, false, $datasource);
	
	}

	private function __construct(){
		if(!$this->_data){
			$this->_data = array();
		}
	}

	public function __set($key, $value){
		$this->_data{$key} = $value;
	}

	public function __get($key){
		if($this->_data){
			return $this->_data{$key};
		}else{
			return null;
		}
	}

	public function getAllData(){
		return $this->_data;
	}

	public function execute($method, $args = false){

		try{

			$service = $this->getService();

			return call_user_func_array(array($service, $method), $args);

		}catch(Exception $ex){

			$traceMessage = $ex->getMessage();

			throw new Exception($traceMessage);
		}
	}

	public function getService(){

		if($this->_currentService == null){
			$serviceClassFile = self::$_instance->{FrameworkConstants::SERVICE_DIR} .
			self::$_instance->{FrameworkConstants::SERVICE_MODULE} . '/' .
			self::$_instance->{FrameworkConstants::SERVICE_NAME}. 'Service.php';

			$serviceClassName = self::DEFAULT_SERVICE_CLASS;
			if(is_file($serviceClassFile)){
				require_once($serviceClassFile);
				$serviceClassName = self::$_instance->{FrameworkConstants::SERVICE_NAME} . 'Service';
			}else{
				// use the default active service
				require_once(FRAMEWORK_SERVICES_DIR . 'ActiveService.php');
			}

			$service = new $serviceClassName;

			// pass through the input data
			$data = $this->data;

			// unset some controller parameters
			unset($data['controller']);
			unset($data['module']);
			unset($data['command']);
			unset($data['submit']);
			$service->data = $data;

			// extra's
			$service->joins = $this->joins;
			$service->result_set = $this->result_set;

			// set criteria
			$service->{FrameworkConstants::READ_CRITERIA} = $this->{FrameworkConstants::READ_CRITERIA};
			$service->{FrameworkConstants::CREATE_CRITERIA} = $this->{FrameworkConstants::CREATE_CRITERIA};
			$service->{FrameworkConstants::UPDATE_CRITERIA} = $this->{FrameworkConstants::UPDATE_CRITERIA};
			$service->{FrameworkConstants::DELETE_CRITERIA} = $this->{FrameworkConstants::DELETE_CRITERIA};
			$service->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS} = $this->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS};
			$service->{FrameworkConstants::CRITERIA_DATA} = $this->{FrameworkConstants::CRITERIA_DATA};
			$service->{FrameworkConstants::ORDER_BY} = $this->{FrameworkConstants::ORDER_BY};
			$service->{FrameworkConstants::GROUP_BY} = $this->{FrameworkConstants::GROUP_BY};
			$service->{FrameworkConstants::ROW_KEY} = $this->{FrameworkConstants::ROW_KEY};
			$service->{FrameworkConstants::BATCH_DATA} = $this->{FrameworkConstants::BATCH_DATA};
			$service->{FrameworkConstants::AR_PARAMETERS} = $this->{FrameworkConstants::AR_PARAMETERS};
			$service->{FrameworkConstants::AR_PROCEDURE_NAME} = $this->{FrameworkConstants::AR_PROCEDURE_NAME};
			$service->{FrameworkConstants::PLURALIZE} = $this->{FrameworkConstants::PLURALIZE };
			$service->{FrameworkConstants::FIRST_RESULT} = $this->{FrameworkConstants::FIRST_RESULT};
            $service->{FrameworkConstants::LAST_RESULT} = $this->{FrameworkConstants::LAST_RESULT};
            $service->{FrameworkConstants::DATABASE_PERSIST} = $this->{FrameworkConstants::DATABASE_PERSIST};
			$service->{FrameworkConstants::CONDITIONS} = $this->{FrameworkConstants::CONDITIONS};
			$service->{FrameworkConstants::ENTITIES} = $this->{FrameworkConstants::ENTITIES};
			$service->{FrameworkConstants::INSERT_SELECT_TABLE} = $this->{FrameworkConstants::INSERT_SELECT_TABLE};
			$service->{FrameworkConstants::INSERT_SELECT_COLUMNS} = $this->{FrameworkConstants::INSERT_SELECT_COLUMNS};
			$service->{FrameworkConstants::RETURN_ERRORS} = $this->{FrameworkConstants::RETURN_ERRORS};

			$dbTable = self::$_instance->{FrameworkConstants::SERVICE_NAME};

			$service->{FrameworkConstants::ENTITY_NAME} = $dbTable;

			$datasource = self::$_instance->{FrameworkConstants::DATABASE_CONFIG_SECTION};

			$service->init(self::$_instance->{FrameworkConstants::SERVICE_NAME} . 'ActiveRecord', self::$_instance->{FrameworkConstants::DAO_DIR}, $datasource);

			$service->serviceName = self::$_instance->{FrameworkConstants::SERVICE_NAME};

			if(!is_dir($daoDirectory)){
				// if it's not set, then use the application's datatbase dir
				$daoDirectory = APPLICATION_LIB_DIR . 'database/';
			}

			$service->{FrameworkConstants::DAO_DIR} = $daoDirectory;

			$this->_currentService = $service;

			return $service;

		}else{
			return $this->_currentService;
		}
	}

	//public function setCriteria($data){
		//self::$_instance->{FrameworkConstants::CRITERIA_DATA} = $data;
	//}

	public function setJoins($data){
		self::$_instance->{FrameworkConstants::JOINS} = $data;
	}

	public function setResultSet($data){
		self::$_instance->{FrameworkConstants::RESULT_SET} = $data;
	}

	public function setRowKey($data){
		self::$_instance->{FrameworkConstants::ROW_KEY} = $data;
	}

	public function setCriteria($criteria, $data){
		self::$_instance->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS} = $criteria;

		self::$_instance->{FrameworkConstants::CRITERIA_DATA} = $data;
	}

	public function setInsertSelectTarget($table, $columns = false){
		self::$_instance->{FrameworkConstants::INSERT_SELECT_TABLE} = $table;
		if($columns){
			self::$_instance->{FrameworkConstants::INSERT_SELECT_COLUMNS} = '(`.' . implode('`,`',$columns) . '`)';	
		}
	}
	
	public function setData($data, $reset = false){
		
		if($reset){
			$this->_currentService = '';
		}
			
		self::$_instance->{FrameworkConstants::DATA} = $data;
	}

	public function setOrderBy($data){
		self::$_instance->{FrameworkConstants::ORDER_BY} = $data;
	}

	public function setGroupBy($data){
		self::$_instance->{FrameworkConstants::GROUP_BY} = $data;
	}

	public function create($updateOnDuplicate = false){
		return self::$_instance->execute(FrameworkConstants::AR_CREATE, $updateOnDuplicate);
	}

	public function read(){
		return self::$_instance->execute(FrameworkConstants::AR_READ);
	}

	public function copy(){
		return self::$_instance->execute(FrameworkConstants::AR_COPY);
	}

	public function update(){
		return self::$_instance->execute(FrameworkConstants::AR_UPDATE);
	}

	public function delete(){
		return self::$_instance->execute(FrameworkConstants::AR_DELETE);
	}

	public function call(){
		return self::$_instance->execute(FrameworkConstants::AR_CALL);
	}

	public function insertBatch(){
		return self::$_instance->execute(FrameworkConstants::AR_INSERT_BATCH);
	}

	public function setBatchData($data){
		self::$_instance->{FrameworkConstants::BATCH_DATA} = $data;
	}

	public function close(){
		self::$_instance->_data = null;
	}

	public function setDataSource($handle){
		self::$_instance->{FrameworkConstants::DATABASE_CONFIG_SECTION} = $handle;
	}

	public function setParameters($parameters){
		self::$_instance->{FrameworkConstants::AR_PARAMETERS} = $parameters;
	}

	public function setStoredProcedureName($name){
		self::$_instance->{FrameworkConstants::AR_PROCEDURE_NAME} = $name;
	}

	public function setPluralize($switch){
		self::$_instance->{FrameworkConstants::PLURALIZE} = $switch;
	}

	public function setFirst($data){
        self::$_instance->{FrameworkConstants::FIRST_RESULT} = $data;
	}

	public function setLast($data){
	    self::$_instance->{FrameworkConstants::LAST_RESULT} = $data;
	}

	public function setMaxResults($data){
	    self::$_instance->{FrameworkConstants::LAST_RESULT} = $data;
	}

	public function getCreateString($updateOnDuplicate = false){
		return self::$_instance->execute(FrameworkConstants::AR_GET_CREATE_STRING);
	}

	public function getDeleteString(){
		return self::$_instance->execute(FrameworkConstants::AR_GET_DELETE_STRING);
	}

	public function getUpdateString(){
		return self::$_instance->execute(FrameworkConstants::AR_GET_UPDATE_STRING);
	}

	public function addToBatch($updateOnDuplicate = false){
		return self::$_instance->execute(FrameworkConstants::AR_ADD_TO_BATCH, $updateOnDuplicate);
	}

	public function commitBatch($cleanup = false, $continueOnError = false){
		return self::$_instance->execute(FrameworkConstants::AR_COMMIT_BATCH, array($cleanup, $continueOnError));
	}
	
	public function setConditions($data){
		self::$_instance->{FrameworkConstants::CONDITIONS} = $data;
	}
	
	public function setEntities($data){
		self::$_instance->{FrameworkConstants::ENTITIES} = $data;
	}
	
	public function returnErrors($data){
		self::$_instance->{FrameworkConstants::RETURN_ERRORS} = $data;
	}
	
	public function reset(){
		self::$_instance->{FrameworkConstants::RESET};
	}
}
?>
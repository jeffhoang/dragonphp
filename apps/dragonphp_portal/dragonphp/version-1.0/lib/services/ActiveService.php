<?php

/*
 ======================================================================
 DragonPHP - ActiveService

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

require_once('ActiveServiceIF.php');
require_once(FRAMEWORK_HELPERS_DIR . 'ActiveRecordHelper.php');
require_once(FRAMEWORK_COMMON_DIR . 'FrameworkConstants.php');
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');

class ActiveService implements ActiveServiceIF{

	private $_data = array();
	const DAO_SUFFIX = 'Dao';
	const JUNCTION = 'junction';

	public static $logger;
	public static $service;
	
	public function init($handle = false, $daoDir = false, $dataAccessSection = false){

		$daoName = $handle;

		if(!$handle){
			$className = get_class();
			$daoName = preg_replace('/Service/','', $className);
		}

		try {

			$classFile = $daoDir . $daoName . '.php';
			if(is_file($classFile)){
				require_once($classFile);
			}elseif(is_file(APPLICATION_LIB_DIR . 'database/'. $daoName . '.php')){
				// use application database directory
				require_once(APPLICATION_LIB_DIR . 'database/'. $daoName . '.php');
			}else{
				require_once(ACTIVE_RECORD);
				$daoName = 'ActiveRecord';
			}

		}catch (Exception $ex){
			throw new Exception($ex->getMessage());
		}

		// dynamically instantiate the Active Record
		$this->{FrameworkConstants::ACTIVE_RECORD} = new $daoName;

		// set entity name
		$entityName = $this->{FrameworkConstants::ENTITY_NAME };

		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::ENTITY_NAME} = $entityName;

		// pass input data to dao
		$this->{FrameworkConstants::ACTIVE_RECORD}->data = $this->data;

		// initialize the dao
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::DATABASE_CONFIG_FILE} = APPLICATION_CONFIG_DIR . FrameworkConstants::DATABASE_CONFIG_FILE_NAME;

		if(!is_file($this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::DATABASE_CONFIG_FILE})){
			$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::DATABASE_CONFIG_FILE} = APPLICATION_DB_DIR .  FrameworkConstants::DATABASE_CONFIG_FILE_NAME;	
		}
		
		if($dataAccessSection){
	   		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::DATABASE_CONFIG_SECTION} = $dataAccessSection;
		}else{
			$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::DATABASE_CONFIG_SECTION} = FrameworkConstants::DEFAULT_SECTION;
		}

		// extra's
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::JOINS} = $this->{FrameworkConstants::JOINS};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::RESULT_SET} = $this->{FrameworkConstants::RESULT_SET};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS} = $this->{FrameworkConstants::CRITERIA_PREPARED_CONDITIONS};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::CRITERIA_DATA} = $this->{FrameworkConstants::CRITERIA_DATA};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::ORDER_BY} = $this->{FrameworkConstants::ORDER_BY};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::GROUP_BY} = $this->{FrameworkConstants::GROUP_BY};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::ROW_KEY} = $this->{FrameworkConstants::ROW_KEY};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::BATCH_DATA} = $this->{FrameworkConstants::BATCH_DATA};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::PLURALIZE} = $this->{FrameworkConstants::PLURALIZE};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::FIRST_RESULT} = $this->{FrameworkConstants::FIRST_RESULT};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::LAST_RESULT} = $this->{FrameworkConstants::LAST_RESULT};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::DATABASE_PERSIST} = $this->{FrameworkConstants::DATABASE_PERSIST};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::MYSQL_COMMAND_CONTINUE_ON_ERROR}= $this->{FrameworkConstants::MYSQL_COMMAND_CONTINUE_ON_ERROR};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::CONDITIONS} = $this->{FrameworkConstants::CONDITIONS};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::ENTITIES} = $this->{FrameworkConstants::ENTITIES};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::INSERT_SELECT_TABLE} = $this->{FrameworkConstants::INSERT_SELECT_TABLE};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::INSERT_SELECT_COLUMNS } = $this->{FrameworkConstants::INSERT_SELECT_COLUMNS};
		$this->{FrameworkConstants::ACTIVE_RECORD}->{FrameworkConstants::RETURN_ERRORS} = $this->{FrameworkConstants::RETURN_ERRORS};
		$this->{FrameworkConstants::ACTIVE_RECORD}->init();

	}

	public function __get($record) {
        if (isset($this->_data[$record])) {
            return $this->_data[$record];
        }
    }

    public function __set($key, $value) {
    	$this->_data[$key] = $value;
    }

	public function create($updateOnDuplicate = false){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->create($this->{FrameworkConstants::CREATE_CRITERIA}, $updateOnDuplicate);
	}

	public function read(){		
		return $this->{FrameworkConstants::ACTIVE_RECORD}->read($this->{FrameworkConstants::READ_CRITERIA});
	}

	public function copy(){		
		return $this->{FrameworkConstants::ACTIVE_RECORD}->copy($this->{FrameworkConstants::READ_CRITERIA});
	}

	public function update(){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->update($this->{FrameworkConstants::UPDATE_CRITERIA});
	}

	public function delete(){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->delete($this->{FrameworkConstants::DELETE_CRITERIA});
	}

	public function insertBatch(){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->insertBatch(FrameworkConstants::INSERT_BATCH_CRITERIA);
	}

	public function call(){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->call($this->{FrameworkConstants::AR_PROCEDURE_NAME}, $this->{FrameworkConstants::AR_PARAMETERS});
	}

	public function getData(){
		return $_data;
	}

		public function getParameter($key) {

		if(self::$_data) {

			$requestValue = self::$_data[$key];
			return $requestValue;

		} else {

			return null;

		}
	}

	public function setParameter($key, $value) {

		if(!self::$_data) {
			self::$_data = array();
		}

		//echo 'setting ' . $key . ' -> ' . $value;

		self::$_data[$key] = $value;

	}

	public function remove($key){
		unset(self::$_data[$key]);
	}

	public function reset(){
		self::$_data = array();
	}
	
	public function getCreateString($updateOnDuplicate = false){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->getCreateString($this->{FrameworkConstants::CREATE_CRITERIA}, $updateOnDuplicate);
	}
	
	public function getDeleteString(){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->getDeleteString($this->{FrameworkConstants::CREATE_CRITERIA});
	}
	
	public function getUpdateString(){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->getUpdateString($this->{FrameworkConstants::CREATE_CRITERIA});
	}
	
	public function addToBatch($updateOnDuplicate = false){
		return $this->{FrameworkConstants::ACTIVE_RECORD}->addToBatch($this->{FrameworkConstants::CREATE_CRITERIA}, $updateOnDuplicate);
	}
	
	public function commitBatch($cleanup = false, $continueOnError = false){
		
		return $this->{FrameworkConstants::ACTIVE_RECORD}->commitBatch($cleanup, $continueOnError);
	}
	
	public function initLogger($currentClass = false){

		if(empty($currentClass)){
			$currentClass = get_class();
		}
		
		self::$logger = LoggerFactory::getInstance($currentClass);
		
	}
	
	public function defineCriteria($service, $criteria, $junctions, $commonJunction = false){
		
		try {
			if(!empty($service)){		
		
				$cnt = 0;
				
				$criteriaData = array();
				
				foreach($criteria as $key=>$criteriaArray){
					
					if($cnt > 0){
						
						if(is_array($junctions)){
							$criteriaArray[self::JUNCTION] = $junctions[$key];
						}elseif(!empty($commonJunction)){
							$criteriaArray[self::JUNCTION] = $commonJunction;
						}
						
						$criteria[$key] = $criteriaArray;
					}
					
					$value = $criteriaArray['value'];
					array_push($criteriaData, $value);
					
					$cnt++;					
				}
				
				$service->setCriteria($criteria, $criteriaData);
			}
		} catch (Exception $ex){
			throw new Exception ($ex->getMessage());
		}
	}
}
?>
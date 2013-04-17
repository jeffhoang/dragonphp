<?php

require_once(ACTIVE_RECORD_MYSQL);
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');

class RegistrationInfo extends ActiveRecord {
	
	protected $_TABLE_NAME = 'user_registration_info';
	protected $_COLUMNS = array('id' => STRING, 
	'user_id' => STRING, 
	'email_address' => STRING,
	'date_created' => NATIVE_FUNCTION);
	
	function __construct(){
		
		$this->_init($this->_TABLE_NAME);
		
		$this->_dbServer = DB_SERVER;
		$this->_dbName = DB_NAME;
		$this->_username = DB_USERNAME;
		$this->_password = DB_PASSWORD;
		
		self::$logger = LoggerFactory::getInstance(get_class());
	}
}
?>
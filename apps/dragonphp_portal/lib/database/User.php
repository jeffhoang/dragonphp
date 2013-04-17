<?php

require_once(ACTIVE_RECORD_MYSQL);
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');

class User extends ActiveRecord {
	
	protected $_TABLE_NAME = 'user';
	protected $_COLUMNS = array('id' => NUMBER, 
	'user_id' => STRING, 
	'password' => STRING,
	'email' => STRING,
	'date_created' => NATIVE_FUNCTION,
	'date_login' => NATIVE_FUNCTION,
	'last_activity' => NATIVE_FUNCTION);
	
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
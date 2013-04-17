<?php

require_once(ACTIVE_RECORD_MYSQL);
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');

class Role extends ActiveRecord {
	
	protected $_TABLE_NAME = 'role';
	protected $_COLUMNS = array('id' => NUMBER, 
	'parent_id' => STRING, 
	'role' => STRING);
	
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
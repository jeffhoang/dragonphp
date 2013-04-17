<?php

/*
 ======================================================================
 DragonPHP - CommonLogger
 
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
 
 @package    common
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

class CommonLogger {
	
	private $_className = null;
	
	/* Log level id's:
		0 = DEBUG
		1 = INFO
		2 = WARN
		3 = ERROR
	*/
	const LOG_0 = 0;
	const LOG_1 = 1;
	const LOG_2 = 2;
	const LOG_3 = 3;
	
	private $_logLevels = null;
	
	const DEFAULT_FRAMEWORK_LOG_NAME = 'dragonphp.log';
	
	private $_logFileName;
	
	// Logger level - default is 0 (ERROR)
	private $_currentLoggerLevel = 3;
	
	public function __construct($className, $logFileName = false){
		
		$this->_className = $className;
		
		if($logFileName){
			$this->_logFileName = $logFileName;
		}else{
			$this->_logFileName = self::DEFAULT_FRAMEWORK_LOG_NAME;
		}
		
		if($this->_logLevels == null){
			$this->_logLevels = array('DEBUG', 'INFO', 'WARN', 'ERROR');
		}
	
		$this->_currentLoggerLevel = LOGGER_LEVEL;
		
		//$this->_log(1, 'Current Logger Level: ' . $this->_logLevels[$this->_currentLoggerLevel]);
	}
	
	
	public function debug($message, $exception = false, $varExport = false, $printR = false){
		if($this->_currentLoggerLevel == 0)
			$this->_log(self::LOG_0, $message, $exception = false,$varExport, $printR);
	}
	
	public function info($message, $exception = false, $varExport = false, $printR = false){
		if($this->_currentLoggerLevel == 1 || $this->_currentLoggerLevel == 0)
			$this->_log(self::LOG_1, $message, $exception,$varExport, $printR);
	}
	
	public function warn($message, $exception = false, $varExport = false, $printR = false){
		if(($this->_currentLoggerLevel == 2 && $this->_currentLoggerLevel <= 3) || $this->_currentLoggerLevel == 0)
			$this->_log(self::LOG_2, $message, $exception, $varExport, $printR);
	}
	
	public function error($message, $exception = false, $varExport = false, $printR = false){
		if($this->_currentLoggerLevel <= 3)
		$this->_log(self::LOG_3, $message, $exception, $varExport, $printR);
	}

	protected function _log($logLevel, $message, $exception = false, $varExport = false, $printR = false){
		
		if(!is_string($message)){
			ob_start();
			if($varExport)
				var_export($message);
		
			if($printR)
				print_r($message);
				
			$message = ob_get_contents();
			ob_end_clean();
		}
		
		if(!defined('LOGGER_DATE_FORMAT')){
			define('LOGGER_DATE_FORMAT', 'm/d/Y H:i:s');
		}
		
		$message = date(LOGGER_DATE_FORMAT) . ' - ' . $this->_logLevels[$logLevel] . ': ' . ' [ ' . $this->_className . ' ] ' . $message;
		
		if($exception){
			$message .= "\nException Message: " . $exception->getMessage();
		}

		if(!is_dir(LOG_DIR)){
			mkdir(LOG_DIR, 0777);
		}
		
		file_put_contents(LOG_DIR . $this->_logFileName , $message . "\n", FILE_APPEND);
	}
}
?>
<?php

/*
 ======================================================================
 DragonPHP - LoggerFactory
 
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

require_once('CommonLogger.php');

class LoggerFactory{
	
	private static $_registry = array();
	
	public static function getInstance($className, $logFileName = false){
					
		if(empty($logFileName)){
			$logFileName = 'dragonphp.log';
		}
			
		if(defined('LOG_ROTATION_TYPE')){
	
			switch(LOG_ROTATION_TYPE){
				case 1:
					$logFileName = date('Y_m_d_H', time()) . '_' . $logFileName;
					break;
				default:
					// default rotation is daily
					$logFileName = date('Y_m_d', time()) . '_' . $logFileName;
			}
			
		}else{
			// default rotation is daily
			$logFileName = date('Y_m_d', time()) . '_' . $logFileName; 
		}
		
		if(!$logFileName){
			$logger = self::$_registry[$className];
		}else{
			$key = $className . '-' . $logFileName;
			
			if(isset(self::$_registry[$key]))
				$logger = self::$_registry[$key];
		}
		
		if(!isset($logger) || !$logger){
			self::$_registry[$className] = new CommonLogger($className, $logFileName);
			
			if(!isset($loggerFileName)){
				$logger = self::$_registry[$className];
			}else{
				$key = $className . '-' . $logFileName;
				$logger = self::$_registry[$key];
			}
		}
		
		return $logger;
	
	}
}
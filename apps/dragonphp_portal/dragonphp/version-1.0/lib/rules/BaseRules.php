<?php

/*
 ======================================================================
 DragonPHP - Rules
 
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
 
 @package    rules
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */
require_once(FRAMEWORK_MODEL_DIR . 'Error.php');
require_once(RESOURCE_BUNDLE_HELPER);

class BaseRules {
	
	protected static $_regularExpressions = array();
	protected static $_errors = array();
	protected static $_commonRegularExpressions = array(
					'email' => '^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$',
					'alphaNumeric' => '[a-zA-Z0-9]'		
	);
	
	public static function addError($key, $errorMessage) {

		if(!self::$_errors[$key]) {
			self::$_errors[$key] = array($errorMessage);
		} else {
			$errorList = self::$_errors[$key];
			array_push($errorList, $errorMessage);
			self::$_errors[$key] = $errorList;
		}
		
	}
	
	public static function getErrors(){
		
		return self::$_errors;
	}
	
	protected static function getMessage($key, $inputData) {

		$moduleName = $inputData{MODULE_PARAM};
		$controllerName = $inputData{CONTROLLER_PARAM};
		$locale = $inputData{LOCALE_PARAM};
		
		// get resource file name, the form_name takes precedence
		$resourceFileName = $inputData{FORM_NAME};
		
		if(!$resourceFileName) {
			$resourceFileName = $inputData{RESOURCE_FILE_NAME_PARAM};
		}
		
		$msg = ResourceBundleHelper::getMessage($key, $controllerName, $moduleName, $locale, $resourceFileName);
		
		return $msg;
		
	}
}
?>
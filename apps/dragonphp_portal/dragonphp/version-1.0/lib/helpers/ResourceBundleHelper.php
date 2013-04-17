<?php

/*
 ======================================================================
 DragonPHP - ResosurceBundleHelper
 
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
 
 @package    helpers
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */
class ResourceBundleHelper {
	
	private static $_data = array();
	private static $_resourceBundleKey;

	const RESOURCES_DIR_NAME = 'resources';
	
	public static function loadResourceBundle($controllerName, $moduleName, $locale = false, $resourceFileName = false) {

		if(!$locale) {
			$locale = DEFAULT_LOCALE;	
		}
		
		if(!$resourceFileName) {
			$resourceFileName = $controllerName;
		}
		
		$resourceBundleKey = $moduleName . '_' . $locale . '_' . $resourceFileName;
		
		self::$_resourceBundleKey = $resourceBundleKey;
		
		if(self::$_data{$resourceBundleKey}) {

			// getting resource bundle from static object using key

			return self::$_data{$resourceBundleKey};
			
		} else {
		
			// loading resource bundle from file
			
			$data = self::_loadDataFromFile($moduleName, $locale, $resourceFileName);
		}		

		return $data;
	}
	
	public static function loadResourceBundleForRenderer($renderer, $controllerName, $moduleName, $locale = false, $resourceFileName = false) {

		try {
			
			$data = self::loadResourceBundle($controllerName, $moduleName, $locale, $resourceFileName);

			if($data) {
				foreach($data as $k => $v) {
					$renderer->setAttribute($k, $v);	
				}
			}
			
		} catch (Exception $ex) {
		
			
		}
		
		return $data;
	}
	
	private static function _loadDataFromFile($moduleName, $locale, $resourceFileName) {
		
		$resourceFile = APPLICATION_MODULE_DIR . $moduleName . '/' . self::RESOURCES_DIR_NAME . '/' . $locale . '/' . $resourceFileName . '.ini';
	
		if(is_file($resourceFile)) {
		
			// load the resource bundle
			$data  = IniParser::parse($resourceFile);
		}
		
		if($data) {
			self::$_data{self::$_resourceBundleKey} = $data;	
		}
		
		return $data;
	}
	
	public static function getMessage($key, $controllerName, $moduleName, $locale = false, $resourceFileName = false) {

		$data = self::loadResourceBundle($controllerName, $moduleName, $locale, $resourceFileName);
		
		if($data{$key}) {
			return $data{$key};
		}
	}
}
?>
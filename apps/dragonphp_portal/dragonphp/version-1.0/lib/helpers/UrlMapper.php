<?php

/*
 ======================================================================
 DragonPHP - UrlMapper
 
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

require_once(FRAMEWORK_COMMON_DIR . 'IniParser.php');
require_once('ConfigCacheHelper.php');

class UrlMapper{
	
	public static function getInfo($data){

		$file = APPLICATION_CONFIG_DIR . 'url_mapper.ini';
		
		if(is_file($file) && is_array($data)){
			
			// check modification date of url_mapper file
			$mapperFileInfo = stat($file);
			$mapperLastModified = $mapperFileInfo['mtime'];
			
			$cacheLastModified = 0;
			
			if(is_file(APPLICATION_CACHE_DIR . 'url_mapper')){
				$cacheFileInfo = stat(APPLICATION_CACHE_DIR . 'url_mapper');
				$cacheLastModified = $cacheFileInfo['mtime'];
			}
			
			// let's check the cache first
			if($mapperLastModified < $cacheLastModified){
				$iniData = ConfigCacheHelper::getCache(APPLICATION_CACHE_DIR, 'url_mapper');
			}else{
				$iniData  = IniParser::parse($file, true);
				ConfigCacheHelper::saveCache(APPLICATION_CACHE_DIR, 'url_mapper', $iniData);
			}
			
			if($iniData){
				$shortcuts = $iniData['shortcuts'];
			}
			
			if(is_array($shortcuts)){
				foreach ($data as $k=>$v){
					if(isset($k) && empty($v)){
						if($shortcuts[$k]){
							
							// found a matching map, parse it and update the request
							parse_str($shortcuts[$k], $output);
							$request = Request::getInstance();

							foreach($output as $k => $v){
								$request->setParameter($k, $v);
							}
						}						
					}
				}
			}
		}
	}	
}
?>
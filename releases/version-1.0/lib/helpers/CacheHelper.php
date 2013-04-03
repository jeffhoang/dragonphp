<?php

/*
 ======================================================================
 DragonPHP - CacheHelper
 
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
 
 @package    helper
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */
require_once('StringUtilHelper.php');

class CacheHelper{
	
	public static function getCache($dir, $cacheName, $maxAge, $deserialize = false, $plainCache = false){

		if(is_dir($dir)){
			
			$cacheFile = $dir . $cacheName;
			
			if(is_file($cacheFile)){
				// get file stats
				$fileInfo = stat($cacheFile);
				
				$lastModified = $fileInfo['mtime'];
				
				$now = mktime();
				
				//echo '<p>time now = ' . $now;
				
				$diff = $now - $lastModified;
				
				//echo '<p>time elapsed = ' .$diff;
				
				if($diff >= $maxAge){
					
					//echo 'clearing cache';
					
					unlink($cacheFile);
					
					return false;
				}
				
				//echo '<p>returning cache';
				
				if($deserialize == false && $plainCache == false){
					include_once($cacheFile);
				}else if($deserialize == false && $plainCache){
					$data = file_get_contents($cacheFile);		
				}else{
					$data = unserialize((file_get_contents($cacheFile)));
				}
				return $data;
							
			}else{
				return false;
			}
		}
	}
	
	public static function saveCache($dir, $cacheName, $data, $serialize = false, $plainCache = false){
		
		if(!is_dir($dir)){
			mkdir($dir, 0777);	
		}
		
		$cacheFile = $dir . $cacheName;
		
		$f = fopen($cacheFile, 'w');
		$cacheString = '';
				
		if($serialize == false && $plainCache == false){
			$cacheString = StringUtilHelper::arrayToString($data);
			$cacheString = '<?php $data = ' . $cacheString . '; ?>';
		}else if($serialize == false && $plainCache){
			$cacheString = $data;
		}else{
			$cacheString = serialize($data);
		}
		
		fputs($f, $cacheString);
		fclose($f);
	}

	public static function removeCache($dir, $cacheName){
		
		$cacheFile = $dir . $cacheName;
		
		if(is_file($cacheFile)){
			unlink($cacheFile);
		}
	}
}
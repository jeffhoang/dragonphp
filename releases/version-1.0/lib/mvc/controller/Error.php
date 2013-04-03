<?php

/*
 ======================================================================
 DragonPHP - Error
 
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
 
 @package    mvc/controller
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

class Error {
	
	private static $_data;
	private static $_instance;
	
	public static function getInstance(){
		if(!self::$_instance){
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function getError($key) {

		if(self::$_data) {
		
			$requestValue = self::$_data[$key];
			
			if($requestValue) {
				return $requestValue;
			} else {
				return null;
			}
		
		} else {
			
			return null;
			
		}
	}
	
	public function setError($key, $value) {
		
		if(!self::$_data){
			self::$_data = array();
		}
		
		//echo 'setting ' . $key . ' -> ' . $value;
			
		self::$_data[$key] = $value;
		
	}
	
	public function getErrors() {
		if(self::$_data)
		{	
			return self::$_data;
		}else{
			return null;
		}
		
	}
	
	public function setErrors($data){
		if($data){
			self::$_data = $data;
		}
		
	}
	
	public function remove($key){
		unset(self::$_data[$key]);
	}
}

?>
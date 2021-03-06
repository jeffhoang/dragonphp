<?php

/*
 ======================================================================
 DragonPHP - Model
 
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
 
 @package    mvc/model
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

class Model{
	
	protected $_data = null;
	
	function __construct(){
		if(!$this->_data){
			$this->_data = array();
		}
	}

	public function __set($key, $value){
		$this->_data{$key} = $value;
	}
	
	public function __get($key){
		if($this->_data){
			return $this->_data{$key};
		}else{
			return null;
		}
	}
	
	public function getAllData(){
		return $this->_data;
	}
}
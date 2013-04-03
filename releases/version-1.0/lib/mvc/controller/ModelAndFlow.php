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
 
 @package    mvc/controller
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

require_once(FRAMEWORK_MODEL_DIR . 'Model.php');

class ModelAndFlow{
	
	private $_model;
	private $_flow;
	private $_phpFile;
	private $_redirectUrl;
	
	private static $_instance;
	
	public static function getInstance() {
		
		if(!self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	function __construct($model = false, $flow = false, $phpFile = false, $redirectUrl = false){
		$this->_model = $model;
		$this->_flow = $flow;
		$this->_phpFile = $phpFile;
		$this->_redirectUrl = $redirectUrl;
	}
}
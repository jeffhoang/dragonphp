<?php

/*
 ======================================================================
 DragonPHP - RequestValidator
 
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

require_once(VALIDATOR_INTERFACE);
require_once(FRAMEWORK_ERROR_CLASS);

class RequestValidator implements RequestValidatorIF {
	
	private static $_errors;
	private static $_request;

	protected $_renderer;	
	protected $_errorTemplateSetId;
	
	protected $_currentModule;
	protected $_currentController;
	
	function __construct(){
		self::$_errors = Error::getInstance();
	}
	
	public function validate(Request $request, Session $session, $renderer = false){}
	
	protected function _setError($key, $value){
		self::$_errors->setError($key, $value);
	}
	
	protected function _removeError($key){
		self::$_errors->remove($key);
	}
	
	public function getErrors(){
		if(self::$_errors->getErrors()){
			return self::$_errors->getErrors();
		}else {
			return null;
		}
	}
	
	public function addErrors($errors){
		if(sizeof(self::$_errors->getErrors()) > 0 && is_array(self::$_errors->getErrors())){
			array_merge(self::$_errors->getErrors(), $errors);
		}else{
			self::$_errors->setErrors($errors);
		}
	}
	
	public function setRequest($request){
		self::$_request = $request;
	}
	
	public static function isRequired($parameterName, $errorMessage = false){
		if($parameterName){
			$parameterValue = trim(self::$_request->getParameter($parameterName));
			
			if(empty($parameterValue)){
				if(!$errorMessage){
					$errorMessage = $parameterName . ' is required';	
				}
				
				self::_setError($parameterName, $errorMessage);
			}
		}
	}
	
	public function getErrorTemplateSetId(){
		return $this->_errorTemplateSetId;
	}
	
	public function setRenderer($renderer){
		$this->_renderer = $renderer;
	}
	
	public function setModel($key, $value){
		if($this->_renderer){
			$this->_renderer->setAttribute($key, $value);
		}
	}
	
	public function setModule($module){
		$this->_currentModule = $module;
	}
	
	public function setController($controller){
		$this->_currentController = $controller;
	}
	
	public function getModule(){
		return $this->_currentModule;
	}
	
	public function getController(){
		return $this->_currentController;
	}
	
	public function isValidSubmission(){
		
		$session = Session::getInstance();
		$token = $session->get(UNIQUE_FORM_TOKEN);
		
		if(isset($token)){
			return true;
		}else{
			return false;
		}
	}
	
	public function setAttribute($key, $value){
		$this->_renderer->setAttribute($key, $value);
	}
	
	public function addHeadLink($href, $rel, $type){		
		$this->_renderer->addHeadLink($href, $rel, $type);
	}
	
	public function setTitle($title){
		$this->_renderer->setTitle($title);
	}
	
	public function addJavascript($javascriptHandle, $directPath = false){
		$this->_renderer->addJavascript($javascriptHandle, $directPath);
	}
	
	public function addMeta($httpEquiv, $content){
		$this->_renderer->addMeta($httpEquiv, $content);
	}
	
	public function addCss($href){
		$this->addHeadLink($href, 'stylesheet', 'text/css');
	}
	
}
?>
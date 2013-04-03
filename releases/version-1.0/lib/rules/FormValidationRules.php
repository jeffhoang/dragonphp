<?php

/*
 ======================================================================
 DragonPHP - FormValidationRules
 
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

require_once('BaseRules.php');

class FormValidationRules extends BaseRules{
	
	public static function isRequired($fieldElements, $rule, $inputData){
		
		$fieldName = (string)$fieldElements->name;
		
		if(!$inputData{$fieldName}) {
			
			$msg_key = (string)$rule->error_message_id;
			
			$msg = self::_getErrorMessage($msg_key, $rule, $inputData);
			
			self::addError($fieldName, $msg);
			
		}
	}
		
	public static function isMatch($fieldElements, $rule, $inputData){
		
		// get input arguements
		$args = $rule->args->field;
		
		$rootFieldName = (string) $fieldElements->name;
		
		$msg_key = (string)$rule->error_message_id;
		
		$msg = self::_getErrorMessage($msg_key, $rule, $inputData);
		
		foreach($args as $fieldName) {
		
			$fieldName = (string) $fieldName;
			
			if($inputData{"$fieldName"} != $inputData{$rootFieldName}) {
			
				self::addError($rootFieldName, $msg);	
			}
			
		}
		
	}
	
	public static function evaluateRegularExpression($fieldElements, $rule, $inputData) {
		
		$types = $rule->types->type;
		
		$rootFieldName = (string) $fieldElements->name;
		
		foreach($types as $type) {
			
			$id = (string) $type->id;
			
			$pattern = self::$_commonRegularExpressions{$id};
			
			if(ereg($pattern, $inputData{$rootFieldName}) != 1) {

				$msg_key = (string)$type->error_message_id;
				$msg = self::_getErrorMessage($msg_key, $type, $inputData);
				
				self::addError($rootFieldName, $msg);
			}
			
		}
		
	}
	
	private static function _getErrorMessage($msg_key, $rule, $inputData) {

		if($msg_key){
			$msg = self::getMessage($msg_key, $inputData);
		} else {
			$msg = (string)$rule->error_message; 
		}
		
		return $msg;
	}
}
?>
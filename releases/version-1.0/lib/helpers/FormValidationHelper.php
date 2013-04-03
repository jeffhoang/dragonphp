<?php

/*
 ======================================================================
 DragonPHP - FormValidationHelper
 
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
require_once('FormHelper.php');

class FormValidationHelper {

	const GET_ERRORS_METHOD = 'getErrors';
	
	public function validate() {

		$inputData = Request::getRequest();
		
		$formName = $inputData{FORM_NAME};
		
		$form = FormHelper::getModel($inputData{MODULE_PARAM}, $formName);
		
		$validationRulesName = (string)$form->rulesClass;
		
		if(!$validationRulesName) {
			$validationRulesName = DEFAULT_FRAMEWORK_VALIDATION_RULES;		
		}
		
		$validValidationFiles = array(APPLICATION_LIB_RULES_DIR . $validationRulesName . '.php',
		BASE_FORM_VALIDATION_RULES);
		
		// scan for the target validation rules file
		foreach($validValidationFiles as $rulesFile) {

			if(is_file($rulesFile)){
				break;
			}
		}
		
		require_once($rulesFile);
		
		$formData = $form->elements;
		
		if($formData) {
		
			$formElements = $formData->element;
			
			$validation = new $validationRulesName;
			
			// iterator each attribute of a model and evaluation its validation rules
			foreach($formElements as $formElement => $fieldElements) {
				// get validation rules
				$rules = $fieldElements->validation_rules;
				
				//print_r($rules);
				
				foreach($rules as $ruleGroup) {
					
					foreach($ruleGroup as $rule) {

						// cast the simple xml object to a string
						$method = (string)$rule->method;
						
						// validate this form element
						$args = array($fieldElements, $rule, $inputData);
						
						call_user_func_array(array($validation, $method), $args);
					}
				
				}
				
			}
			
			$method = self::GET_ERRORS_METHOD;
			$errors = call_user_func_array(array($validationRulesName, $method), array());
			
			return $errors;
		}
	}
	
}
?>
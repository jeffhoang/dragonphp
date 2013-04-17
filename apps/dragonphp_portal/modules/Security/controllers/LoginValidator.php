<?php

require_once(VALIDATOR);
require_once(FRAMEWORK_ERROR_CLASS);
require_once(FRAMEWORK_SERVICES_DIR . 'security/SecurityService.php');

class LoginValidator extends RequestValidator{

	public function validate(Request $request, Session $session)	{

		$this->addMeta('Content-Type' , 'text/html; charset=UTF-8' );
		
		// include css stylesheets
		$this->addCss('styles/styles.css');
		$this->addCss('styles/menu.css');
		$this->addCss('styles/table_style.css');
		
		$username = trim($request->getParameter('username'));
		$password = trim($request->getParameter('password'));
		
		self::isRequired('username');
		self::isRequired('password');
		
		
	}
}
?>
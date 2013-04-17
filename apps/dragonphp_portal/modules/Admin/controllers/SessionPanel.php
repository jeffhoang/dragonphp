<?php

/**
 * Index
 *
 * This class is the Session Panel controller.
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package
 * @version
 */

require_once(APPLICATION_LIB_DIR . 'mvc/AbstractSecuredController.php');
require_once(FRAMEWORK_SERVICES_DIR . 'security/SecurityService.php');

class SessionPanel extends AbstractSecuredController {
	
	private $_service;
	private static $_debug = false;
	
	public function introspect(Request $request, Session $session, $view){
		
		$this->setAttribute('session', $session->getAttributes());
		
		return new Template('session_panel');
	}
	
}
?>
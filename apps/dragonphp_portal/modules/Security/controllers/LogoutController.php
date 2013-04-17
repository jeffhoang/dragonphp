<?php

/**
 * LogoutController
 *
 * This class is the logout controller
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package modules/Security/controllers
 * @version
 */

require_once(DRAGON_CONTROLLER);

class LogoutController extends Controller {
	
	public function execute(Request $request, Session $session, $view) {
		
		$this->addMeta('Content-Type' , 'text/html; charset=UTF-8' );
		
		// include css stylesheets
		$this->addCss('styles/styles.css');
		$this->addCss('styles/menu.css');
		$this->addCss('styles/table_style.css');
		
		$request->setParameter('controller', 'Login');
		$session->destroySession();
		
		return $view;
	}
}
?>
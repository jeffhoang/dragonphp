<?php

/**
 * AbstractSecuredController
 *
 * This class implements a filter to only allow signed in user through.
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package application/lib/mvc
 * @version
 */

require_once(DRAGON_CONTROLLER);

class AbstractSecuredController extends Controller{
	
	public function executeFilter(Request $request, Session $session, $renderer){

		$this->addHeadLink('images/favicon.ico', 'icon', 'image/x-icon');
		$this->addHeadLink('images/favicon.ico', 'shortcut icon', 'image/x-icon');

		// Check permission
		// add your permission checker code here
		
		// check if user has signed in?
		// get user
		$user = $session->get('user');

		if(empty($user)){
			$this->forward('Security', 'Login');
		}

		// make the current signed in user's info available to the view.
		$data = $user->getAllData();
	
		if(is_array($data)){
			foreach($data as $k=>$v){
				$this->setAttribute('session_user_' . $k, $v);	
			}
		}
			
		$this->setModel('current_date', date('m/d/Y H:i', time()));
		
		if(strcmp($this->_currentModule, 'Security') == 0){
			
			// change this to the module and controller for forwarding when 
			// the requested module is Security
			$this->forward('Admin', 'Index');
		}
		
		$this->_includeHeader();
		
	}

	protected function _includeHeader($enableFeatures = false, $title = false, $userJs = false, $roleJs = false, $orgJs = false){

		if(isset($title)){
			$this->setTitle($title);
		}else{
			$this->setTitle('Portal');
		}
			
		$this->addMeta('Content-Type' , 'text/html; charset=UTF-8' );
		
		// include css stylesheets
		$this->addCss('styles/styles.css');
		$this->addCss('styles/menu.css');
		$this->addCss('styles/table_style.css');

		if(is_array($enableFeatures)){
			
			if(in_array('jquery', $enableFeatures)){
				$this->addJavascript('/js/jquery/jquery-1.4.2.js', true);
			}
	
			if(in_array('fancybox', $enableFeatures)){
				// fancybox
				$this->addCss('styles/jquery.fancybox-1.3.1.css');
				$this->addJavascript('/js/fancybox/jquery.fancybox-1.3.1.js', true);
			}
		}
		
		if(!empty($userJs)){
			$this->addJavascript('/js/control_panel/users.js', true);
		}
		
		if(!empty($roleJs)){
			$this->addJavascript('/js/control_panel/roles.js', true);
		}
		
		if(!empty($orgJs)){
			$this->addJavascript('/js/control_panel/organizations.js', true);
		}
	}
}
?>
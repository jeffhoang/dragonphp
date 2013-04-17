<?php

/**
 * LoginController
 *
 * This class is the login controller
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package modules/Security/controllers
 * @version
 */

require_once(DRAGON_CONTROLLER);
require_once(FRAMEWORK_SERVICES_DIR . 'security/SecurityService.php');
require_once(FRAMEWORK_MODEL_DIR . 'Model.php');

class LoginController extends Controller {
	
	public function execute(Request $request, Session $session, $view) {
		
		$this->addMeta('Content-Type' , 'text/html; charset=UTF-8' );
		
		// include css stylesheets
		$this->addCss('styles/styles.css');
		$this->addCss('styles/menu.css');
		$this->addCss('styles/table_style.css');
		
		if(isset($user)){
			$view->setEntry('onSuccess');
			return $view;
		}
		
		if(!$request->getParameter(SUBMIT_PARAM)){
			$view->setEntry('execute');
		}else{
			
			$username = $request->getParameter('username');
			$password = $request->getParameter('password');
			
			// Authenticat user
			$service = new SecurityService();
			
			$userInfo = $service->auth($username, $password);
				
			if(!$userInfo){
				$this->_setError('invalid_login', 'Invalid login info!');
				$view->setEntry('onError');
			}else{
				
				$isActive = $userInfo->is_active;
				
				if(strcmp($isActive, '0') == 0){
					$this->_setError('invalid_login', 'This user has been de-activated!');
					$view->setEntry('onError');
					
					return $view;
				}
				
				// set user info into the session
				$user = new Model();
			
				$user->username = $username;
				$userInfoArray = get_object_vars($userInfo);
				
				foreach($userInfoArray as $k=>$v){
					$user->{$k} = $v;
				}
				
				// get access control info
				$userId = $userInfo->id;
				
				$acl = $service->getAcl($userId);
				
				if(!isset($acl)){
					self::$_logger->warn('This user has no roles...');
				}else{
					$user->roles = $acl;
				}
				
				self::$_logger->debug($user, false, false, true);
			
				$session->set('user', $user);
				
				$view->setEntry('onSuccess');
			}
		}
		
		return $view;
	}
}
?>
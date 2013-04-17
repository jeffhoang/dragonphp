<?php

/**
 * Index
 *
 * This class is the ControlPanel controller.
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package
 * @version
 */

require_once(APPLICATION_LIB_DIR . 'mvc/AbstractSecuredController.php');
require_once(FRAMEWORK_SERVICES_DIR . 'security/SecurityService.php');

class ControlPanel extends AbstractSecuredController {

	private $_service;
	private static $_debug = false;
	
	public function showUsers(Request $request, Session $session, $view){
		
		$this->_includeHeader(array('jquery', 'fancybox', 'jquery_tools'), '', true);
		
		// Get list of users from database
		$service = new SecurityService();
		$listOfUsers = $service->getUsers();
		
		$loggedInUser = $this->_getCurrentUser();
		$currenUserId = $loggedInUser->id;
				
		$this->setAttribute('current_id', $currenUserId);
		$this->setAttribute('list_of_users', $listOfUsers);
		
		return new Template('show_users');
	}
	
	public function updateUser(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
	
			$id = $request->getParameter('id');
			
			// get user info
			$service = new SecurityService();
			
			$userInfo = $service->getUser($id);
			
			// get role
			$selectedRole = $service->getRoleByUserId($id, 'r.id');
			$selectedRoleId = $selectedRole->id;
				
			self::dumpObject($userInfo, self::$_debug);

			$organizations = $service->getOrganizations(false, 'id, name');
			
			self::dumpObject($organizations, self::$_debug);
			
			$listOfOrganizations = array();
			
			foreach($organizations as $idx=>$organization){
				$name = $organization->name;
				$id = $organization->id;
				$listOfOrganizations[$id] = $name;
			}
			
			self::dumpObject($listOfOrganizations, self::$_debug);
			
			$roles = $service->getRoles('id');
			
			$listOfRoles = array();
			
			foreach($roles as $idx=>$role){
				$name = $role->name;
				$id = $role->id;
				$listOfRoles[$id] = $name;
			}
			
			$listOfRoles[0] = '=== Select One ===';
			ksort($listOfRoles);
			self::dumpObject($listOfRoles, self::$_debug);
			
			$status = array(1 => 'Yes',0 => 'No');
			
			$this->setAttribute('activestatus', $status); 
			$this->setAttribute('selected_status', $userInfo->is_active); 
			$this->setAttribute('list_of_organizations', $listOfOrganizations);
			$this->setAttribute('selected_organization', $userInfo->organization_id);
			$this->setAttribute('user', $userInfo);
			$this->setAttribute('list_of_roles', $listOfRoles);
			$this->setAttribute('selected_role', $selectedRoleId);
			
			return new Template('update_user');

		}else{
			
			$responseCode = 1;
			
			$id = $request->getParameter('id');
			$email = $request->getParameter('email');
			$originalEmail = $request->getParameter('original_email');
			
			// duplicate check
			$result = $this->_dupeCheck($originalEmail, $email);
			
			$responseCode = $result['response_code'];
			
			if($responseCode == -1){
				$message = $result['message'];	
			}else{
				
				$firstName = $request->getParameter('firstname');
				$lastName = $request->getParameter('lastname');
				$email = $request->getParameter('email');
				$organizationId = $request->getParameter('organizationid');
				$roleId = $request->getParameter('role_id');
				
				// update info
				$data = array('first_name' => $firstName,
				'last_name' => $lastName,
				'email' => $email,
				'organization_id' => $organizationId,
				'role_id' => $roleId);
				
				$loggedInUser = $this->_getCurrentUser();
				$currenUserId = $loggedInUser->id;
				
				if(strcmp($currenUserId, $id) != 0){
					$data['is_active'] = $request->getParameter('is_active');
				}elseif(strcmp($currenUserId, $id) == 0){
					$loggedInUser->email = $email;
					$loggedInUser->organization_id = $organizationId;
					$loggedInUser->first_name = $firstName;
					$loggedInUser->last_name = $lastName;
				}
				
				$result = $this->_updateUser($id, $data);
			}
			
			$data = array('response_code' => $responseCode, 'message' => $message);
			
			return $this->showJsonResponse($data);
			
		}
		
	}
	
	public function createUser(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
	
			$service = new SecurityService();
			$organizations = $service->getOrganizations(false, 'id, name');
			
			self::dumpObject($organizations, self::$_debug);
			
			$listOfOrganizations = array();
			
			foreach($organizations as $idx=>$organization){
				$name = trim($organization->name);
				$id = $organization->id;
				$listOfOrganizations[$id] = $name;
			}
			
			$listOfOrganizations[0] = '=== Select One ===';
			
			ksort($listOfOrganizations);
			
			self::dumpObject($listOfOrganizations, self::$_debug);
			
			$roles = $service->getRoles('id');
			
			$listOfRoles = array();
			foreach($roles as $idx=>$role){
				$name = trim($role->name);
				$id = $role->id;
				$listOfRoles[$id] = $name;
			}
			
			$listOfRoles[0] = '=== Select One ===';
			ksort($listOfRoles);
			self::dumpObject($listOfRoles, self::$_debug);
			
			$status = array(1 => 'Yes',0 => 'No');
			
			$this->setAttribute('list_of_roles', $listOfRoles); 
			$this->setAttribute('activestatus', $status); 
			$this->setAttribute('selected_status', 0); 
			$this->setAttribute('list_of_organizations', $listOfOrganizations);
			$this->setAttribute('selected_organization', 0);
			
			return new Template('create_user');

		}else{
			
			$responseCode = 1;
			
			$id = $request->getParameter('id');
			$email = $request->getParameter('email');
			// duplicate check
			$result = $this->_dupeCheck(false, $email);
			
			$responseCode = $result['response_code'];
			
			if($responseCode == -1){
				$message = $result['message'];	
			}else{
				
				$firstName = trim($request->getParameter('firstname'));
				$lastName = trim($request->getParameter('lastname'));
				$email = trim($request->getParameter('email'));
				$organizationId = $request->getParameter('organizationid');
				$password = trim($request->getParameter('password'));
				$isActive = $request->getParameter('is_active');
				$roleId = $request->getParameter('role_id');
				
				// update info
				$data = array('first_name' => $firstName,
				'last_name' => $lastName,
				'email' => $email,
				'organization_id' => $organizationId,
				'password' => $password,
				'is_active' => $isActive);
				
				$result = $this->_createUser($data, $roleId);
				
				$responseCode = $result['response_code'];
			}
			
			$data = array('response_code' => $responseCode, 'message' => $message);
			
			return $this->showJsonResponse($data);
			
		}
		
	}

	public function changePassword(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
			
			$this->setAttribute('id', $request->getParameter('id'));
			
			return new Template('change_password');
			
		}else{
			
			$id = $request->getParameter('id');
			$password = $request->getParameter('password');
			
			$result = $this->_updatePassword($id, $password);
			
			$responseCode = $result['response_code'];
			
			$data = array('response_code' => $responseCode);
			
			if($responseCode == -1){
				$data['message'] = 'Unable to change password: ' . $result['error'];	
			}else{
				
				$loggedInUser = $this->_getCurrentUser();
				$currenUserId = $loggedInUser->id;
				
				if(strcmp($currenUserId, $id) == 0){
					$encryptedPassword = $result['encrypted_password'];
					$loggedInUser->password = $encryptedPassword;
				}
			}
			
			return $this->showJsonResponse($data);
		}
	}
	
	public function removeUser(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
			
			$this->setAttribute('id', $request->getParameter('id'));
			
			return new Template('remove_user');
			
		}else{
			
			$responseCode = 1;
			
			$id = $request->getParameter('id');
			
			if(!empty($id)){
				$result = $this->_removeUser($id);
				
				if($result['response_code'] == -1){
					$result['message'] = 'Error: can not remove user: ' . $result['error'];
				}
			}
			
			$data['response_code'] = $responseCode;
			
			return $this->showJsonResponse($data);
		}
	}
	
	private function _removeUser($id){
		
		if(!$this->_service){
			$this->_initSecurityService();
		}
		
		$responseCode = 1;
		$result = array();
		
		try {
			
			$this->_service->removeRoleByUserId($id);
			
			$this->_service->removeUser($id);
			
			
		}catch(Exception $ex){
			$responseCode = -1;
			$result['error'] = $ex->getMessage();
		}
		
		$result['response_code'] = $responseCode;
		
		return $result;
	}
	
	private function _updatePassword($id, $password){

		if(!$this->_service){
			$this->_initSecurityService();
		}
		
		$responseCode = 1;
		$result = array();
		
		try {
			$encryptedPassword = $this->_service->updatePassword($id, $password);
			
			if(!empty($encryptedPassword)){
				$result['encrypted_password'] = $encryptedPassword;
				
			}
			
		}catch(Exception $ex){
			$responseCode = -1;
			$result['error'] = $ex->getMessage();
		}
		
		$result['response_code'] = $responseCode;
		
		return $result;
	}
	
	private function _dupeCheck($originalEmail = false, $email){
		
		$this->_service = new SecurityService();
		
		$user = $this->_service->getUserByEmail($email);
		
		$result['response_code'] = 1;
		
		if(isset($user)){
			
			$userEmail = $user->email;
			
			if((!isset($originalEmail) && !empty($userEmail)) || (isset($originalEmail) && strcmp($originalEmail, $userEmail) != 0)){
				$message = 'Error: the email address ' . $email .' is already taken.';
				$result['message'] = $message;
				$result['response_code'] = -1;
			}
		}
		
		return $result;
		
	}
	
	private function _createUser($data, $roleId = false){
		
		try{
			
			$responseCode = 1;
			
			$this->_service = new SecurityService();
		
			$user = $this->_service->createUser($data);
			
			if(!empty($roleId)){
				$userId = $user->id;
				
				$this->_service->assignRole($userId, $roleId);
			}		
			
		}catch(Exception $ex){
			$result['error'] = $ex->getMessage();
			$responseCode = -1;
		}
		
			$result['response_code'] = $responseCode;
			
			return $result;
	}
	
	private function _updateUser($id, $data){
		
		if(!$this->_service){
			$this->_initSecurityService();
		}
		
		$this->_service->updateUser($id, $data);
		
		$roleId = $data['role_id'];
		
		$this->_service->updateUserRole($id, $roleId);
	}
	
	private function _initSecurityService(){
		$this->_service = new SecurityService();
	}
}

?>
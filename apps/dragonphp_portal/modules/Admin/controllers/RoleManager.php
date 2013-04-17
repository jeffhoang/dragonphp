<?php

/**
 * Index
 *
 * This class is the Role Manager controller.
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package
 * @version
 */

require_once(APPLICATION_LIB_DIR . 'mvc/AbstractSecuredController.php');
require_once(FRAMEWORK_SERVICES_DIR . 'security/SecurityService.php');

class RoleManager extends AbstractSecuredController {
	
	private $_service;
	private static $_debug = false;
	
	public function showRoles(Request $request, Session $session, $view){
		
		$this->_includeHeader(array('jquery', 'fancybox'), false, false, true);
		
		// Get list of roles from database
		$service = new SecurityService();
		$listOfRoles = $service->getRoles();
		
		$this->setAttribute('list_of_roles', $listOfRoles);
		
		return new Template('show_roles');
	}
	
	public function updateRole(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){

			$this->setAttribute('id', $request->getParameter('id'));
			$this->setAttribute('name', $request->getParameter('name'));
			
			return new Template('update_role');
			
		}else{

			$id = $request->getParameter('id');
			$name = $request->getParameter('name');
			
			$responseCode = 1;
			
			self::$_logger->debug('NAME = ' . $name);
			
			$data = array('name' => $name);
			
			$result = $this->_updateRole($id, $data);
			
			$responseCode = $result['response_code'];
			
			$message = $result['message'];
			
			$data = array('response_code' => $responseCode, 'message' => $message);
			
			return $this->showJsonResponse($data);
		}

	}
	
	private function _updateRole($id, $data){
		
		try {
			
			$service = new SecurityService();
			
			$service->updateRole($id, $data);
		
			$result['response_code'] = 1;
			
		}catch(Exception $ex){
			
			$result['response_code'] = -1;
			$result['message'] = $ex->getMessage();
			return $result;
		}
		
		return $result;
		
	}
	
	public function createRole(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
		
			return new Template('create_role');
			
		}else{
			
			$name = $request->getParameter('name');
		
			$name = preg_replace('/^\W+|\W+|\+s/', '', $name);
			
			$service = new SecurityService();
		
			self::$_logger->debug('role name: ' . $name);
			
			$roleId = $service->getRoleByName($name);
			
			if(!empty($roleId)){
			
				$result['response_code'] = -1;
				$result['message'] = 'This role name is taken. Please enter a different one.';
					
			}else{
				
				$dataIn = array('name' => $name);
			
				$service->createRole($dataIn);
				
				$result['response_code'] = 1;
				
			}
			
			return $this->showJsonResponse($result);
		}
	}
	
	public function removeRole(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
		
			return new Template('remove_role');
			
		}else{
			
			$id = $request->getParameter('id');
			
			$service = new SecurityService();
		
			try {
				
				$result['response_code'] = 1;
				
				$service->removeRole($id);
			}catch(Exception $ex){
				
				$result['response_code'] = -1;
				
				$message = $ex->getMessage();
				
				if(preg_match('/user_roles/', $message)){
					$result['message'] = 'This role can not be removed because it has already been assigned.';
				}elseif(preg_match('/role_permissions/', $message)){
					$result['message'] = 'This role can not be removed because it has associations to role permissions.';
				}else{
					$result['message'] = 'This role can not be removed. Error message: ' . $message;
				}
					
			}
			
			return $this->showJsonResponse($result);
		}
	}
	
	public function showRolePermissions(Request $request, Session $session, $view){
		
		$this->_includeHeader(array('jquery', 'fancybox'), false, false, true);
		
		$id = $request->getParameter('id');
		
		// Get list of roles from database
		$service = new SecurityService();
		
		$listOfRolePermissions = $service->getRolePermissions($id);
		
		$info = $service->getRoleById($id);
		
		$this->setAttribute('name', $info->name);
		$this->setAttribute('role_id', $info->id);
		$this->setAttribute('list_of_role_permissions', $listOfRolePermissions);
		
		return new Template('show_role_permissions');
	}

		
	public function createRolePermission(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
		
			$this->setAttribute('role_id', $request->getParameter('role_id'));
			
			return new Template('create_role_permission');
			
		}else{
			
			$roleId = $request->getParameter('role_id');
			$permission = $request->getParameter('permission');
		
			$name = preg_replace('/^\W+|\W+|\+s/', '', $permission);
			
			$service = new SecurityService();
		
			self::$_logger->debug('permission: ' . $permission);
			
			$permissionInfo = $service->getRolePermissionByHandle($roleId, $permission);
			
			if(!empty($permissionInfo)){
			
				$result['response_code'] = -1;
				$result['message'] = 'This role permission already exists. Please enter a different one.';
					
			}else{
				
				$dataIn = $request->getParameters(true);
				
				$service->createRolePermission($dataIn);
				
				$result['response_code'] = 1;
				
			}
			
			return $this->showJsonResponse($result);
		}
	}
	
	public function removeRolePermission(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
		
			return new Template('remove_role_permission');
			
		}else{
			
			$id = $request->getParameter('id');
			
			$service = new SecurityService();
		
			try {
				
				$result['response_code'] = 1;
				
				$service->removeRolePermission($id);
			
			}catch(Exception $ex){
				
				$result['response_code'] = -1;
				
				$message = $ex->getMessage();
				
				$result['message'] = 'This role can not be removed. Error message: ' . $message;
					
			}
			
			return $this->showJsonResponse($result);
		}
	}
	
	public function updateRolePermission(Request $request, Session $session, $view){
		
		$service = new SecurityService();
		
		if(!$request->getParameter(SUBMIT_PARAM)){

			$id = $request->getParameter('id');
			
			$info = $service->getRolePermissionById($id);
			
			$this->setAttribute('id', $id);
			$this->setAttribute('role_id', $request->getParameter('role_id'));
			
			$this->setAttribute('original_permission_handle', $info->permission);
			
			$this->setAttributes(get_object_vars($info));
			
			return new Template('update_role_permission');
			
		}else{

			$id = $request->getParameter('id');
			$roleId = $request->getParameter('role_id');
			$orginalPermissionHandle = $request->getParameter('original_permission_handle');
			$permission = $request->getParameter('permission');
			
			$info = $service->getRolePermissionByHandle($roleId, $permission);
			
			$permissionName = $info->permission;
			
			if(!empty($info) && strcmp($permissionName, $orginalPermissionHandle) != 0){
			
				$result['response_code'] = -1;
				$result['message'] = 'This permission handle is taken. Please enter a different one.';
					
			}else{
				
				$responseCode = 1;
				
				self::$_logger->debug('pattern = ' . $request->getParameter('pattern'));
				
				$data = $request->getParameters(true);
				
				$service->updateRolePermission($id, $data);
			
				$result['response_code'] = $responseCode;
			}
			
			return $this->showJsonResponse($result);
		}

	}
}
?>
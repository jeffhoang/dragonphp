<?php

/**
 * SecurityService
 *
 * This class contains functions for accessing authentication services.
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 DragonPHP
 * @author Jeff Hoang
 * @package lib/services/security
 * @version
 */

require_once(FRAMEWORK_CONSTANTS_DIR . 'DatabaseConstants.php');
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');
require_once(FRAMEWORK_SERVICES_DIR . 'ActiveService.php');
require_once(FRAMEWORK_SERVICES_DIR . 'ServiceManager.php');
require_once(FRAMEWORK_HELPERS_DIR . 'AuthenticationHelper.php');

class SecurityService extends ActiveService {
	
	const DB_USER_HANDLE = 'User';
	const DB_USER_ORGANIZATION_HANDLE = 'Organization';
	
	public function auth($username, $password){

		try{
			
			// encrypt password
			$encryptedPassword = AuthenticationHelper::encryptPasswordMd5($password);
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY);
			
			$joins = array('from' => 'users as u, organizations as o',
						   'where' => 'u.organization_id = o.id');
	
			$resultSet = 'u.*, o.name as org_name, o.city as org_city, o.state as org_state, o.zipcode as org_zipcode, o.province as org_province, o.country as country';
	
			$criteria = array();
			$criteriaData = array();
	
			array_push($criteria, array('field_name' => 'u.email',
				'junction' => 'and',
				'expression' => '='
				));
			array_push($criteriaData, $username);
	
			array_push($criteria, array('field_name' => 'u.encrypted_password',
				'junction' => 'and',
				'expression' => '='
				));
			array_push($criteriaData, $encryptedPassword);
	
			$service->setJoins($joins);
			$service->setResultSet($resultSet);
			$service->setCriteria($criteria, $criteriaData);
	
			$userInfo = $service->read();

			if(isset($userInfo)){
				return $userInfo[0];
			}else{
				return null;
			}
				
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getAcl($userId){

		try{
		
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY);

			$joins = array('from' => 'users as u, user_roles as ur, roles as r, role_permissions as rp',
						   'where' => 'u.id = ur.user_id and r.id = ur.role_id and ur.role_id = rp.role_id');
	
			$resultSet = 'rp.*, r.name';
	
			$criteria = array();
			$criteriaData = array();
	
			array_push($criteria, array('field_name' => 'u.id',
				'junction' => 'and',
				'expression' => '='
				));
			array_push($criteriaData, $userId);
	
			$service->setJoins($joins);
			$service->setResultSet($resultSet);
			$service->setCriteria($criteria, $criteriaData);
	
			$acl = $service->read();
			
			$roles = array();
			
			foreach($acl as $record){
				
				$role = $record->name;
				$permission = $record->permission;
				$type = $record->permission_type;
				$pattern = $record->pattern;
				
				$roles[$role][$permission] = array('type' => $type, 'pattern' => $pattern);	
			}
			
			return $roles;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	public function getUsers($sortBy = false){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY);
			
			$joins = array('from' => 'users as u, organizations as o',
						   'where' => 'u.organization_id = o.id');
	
			$resultSet = 'u.*, o.name as org_name, o.city as org_city, o.state as org_state, o.zipcode as org_zipcode, o.province as org_province, o.country as country';
	
			$service->setJoins($joins);
			$service->setResultSet($resultSet);
			$service->setRowKey('id');
			
			if(!empty($sortBy)){
				$service->sortBy($sortBy);	
			}
			
			$users = $service->read();
			
			return $users;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getUser($id){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY);
			
			$joins = array('from' => 'users as u, organizations as o',
						   'where' => 'u.organization_id = o.id');
	
			$resultSet = 'u.*, o.name as org_name, o.city as org_city, o.state as org_state, o.zipcode as org_zipcode, o.province as org_province, o.country as country';
	
			$criteria = array();
			$criteriaData = array();
	
			array_push($criteria, array('field_name' => 'u.id',
				'junction' => 'and',
				'expression' => '='
				));
			array_push($criteriaData, $id);
	
			$service->setJoins($joins);
			$service->setResultSet($resultSet);
			$service->setCriteria($criteria, $criteriaData);
	
			$userInfo = $service->read();

			if(isset($userInfo)){
				return $userInfo[0];
			}else{
				return null;
			}
				
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getOrganizations($rowKey = false, $resultSet = false){
		
		try {
		
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ORGANIZATIONS_TBL);
			
			if(isset($rowKey)){
				$service->setRowKey($rowKey);	
			}else{
				$service->setRowKey('id');
			}
			
			if(isset($resultSet)){
				$service->setResultSet($resultSet);
			}
			
			$organizations = $service->read();
			
			return $organizations;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getRoles($rowKey = false, $resultSet = false){
		
		try {
		
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ROLES_TBL);
			
			if(isset($rowKey)){
				$service->setRowKey($rowKey);	
			}else{
				$service->setRowKey('id');
			}
			
			if(isset($resultSet)){
				$service->setResultSet($resultSet);
			}
			
			$roles = $service->read();
			
			return $roles;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getRolePermissions($id, $rowKey = false, $resultSet = false){
		
		try {
		
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ROLE_PERMISSIONS_TBL);
			
			if(isset($rowKey)){
				$service->setRowKey($rowKey);	
			}else{
				$service->setRowKey('id');
			}
			
			if(isset($resultSet)){
				$service->setResultSet($resultSet);
			}
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'role_id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$rolePermissions = $service->read();
			
			return $rolePermissions;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getRolePermissionByHandle($roleId, $permission){
		
		try {
		
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ROLE_PERMISSIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'role_id',
				'expression' => '='
				));
			array_push($criteriaData, $roleId);
			
			array_push($criteria, array('field_name' => 'permission',
				'junction' => 'and',
				'expression' => '='
				));
			array_push($criteriaData, $permission);
		
			$service->setCriteria($criteria, $criteriaData);
			
			$info = $service->read();
			
			return $info[0];
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getRolePermissionById($id){
		
		try {
		
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ROLE_PERMISSIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$info = $service->read();
			
			return $info[0];
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getUserByEmail($email){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::USERS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'email',
				'expression' => '='
				));
			array_push($criteriaData, $email);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$userInfo = $service->read();
			
			if(isset($userInfo)){
				return $userInfo[0];	
			}else{
				return null;
			}
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getRoleByUserId($id, $resultSet = false){
	
		try {
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY);
			
			$joins = array('from' => 'users as u, user_roles as ur, roles as r',
						   'where' => 'u.id = ur.user_id and r.id = ur.role_id');
	
			$criteria = array();
			$criteriaData = array();
				
			array_push($criteria, array('field_name' => 'u.id',
					'junction' => 'and',
					'expression' => '='
					));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
				
			$service->setJoins($joins);
			
			if(empty($resultSet)){
				$resultSet = 'r.name';
			}
	
			$service->setResultSet($resultSet);
				
			$role = $service->read();
			
			return $role[0];
		
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getRoleByName($name){
	
		try {
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ROLES_TBL);
			
			$criteria = array();
			$criteriaData = array();
				
			array_push($criteria, array('field_name' => 'name',
					'expression' => '='
					));
			array_push($criteriaData, $name);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$resultSet = 'id';
			
			$service->setResultSet($resultSet);
				
			$roleId = $service->read();
			
			return $roleId;
		
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	public function getRoleById($id){
	
		try {
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ROLES_TBL);
			
			$criteria = array();
			$criteriaData = array();
				
			array_push($criteria, array('field_name' => 'id',
					'expression' => '='
					));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
				
			$roleName = $service->read();
			
			if(!empty($roleName)){
				return $roleName[0];
			}else{
				return false;
			}
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	public function updateUser($id, $data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::USERS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$data['updated_date'] = strftime('%F %T');
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->setData($data);
			
			$service->update();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function createUser($data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::USERS_TBL);
			
			// encrypt password
			$encryptedPassword = AuthenticationHelper::encryptPasswordMd5($password);
			$data['encrypted_password'] = $encryptedPassword;
			
			$service->returnErrors(true);
			
			$data['created_date'] = strftime('%F %T');
			$service->setData($data);
			
			$id = $service->create();
			
			return $id;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function createRole($data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ROLES_TBL);
			
			$service->returnErrors(true);
			
			$service->setData($data);
			
			$id = $service->create();
			
			return $id;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	public function createRolePermission($data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ROLE_PERMISSIONS_TBL);
			
			$service->returnErrors(true);
			
			$service->setData($data);
			
			$id = $service->create();
			
			return $id;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function assignRole($userId, $roleId){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::USER_ROLE_TBL);
			
			$data['role_id'] = $roleId;
			$data['user_id'] = $userId;
			
			$service->setData($data);
			
			$service->create();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
		
	public function updatePassword($id, $password){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::USERS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			// encrypt password
			$encryptedPassword = AuthenticationHelper::encryptPasswordMd5($password);
			$data = array('encrypted_password' => $encryptedPassword);
			
			$service->setData($data);
			
			$service->update();
			
			return $encryptedPassword;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function removeUser($id){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::USERS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->delete();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function removeOrganization($id){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ORGANIZATIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			$service->returnErrors(true);
			$service->setCriteria($criteria, $criteriaData);
			
			$service->delete();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function removeRole($id){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ROLES_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->returnErrors(true);
			
			$service->delete();
			
		}catch(Exception $ex){
			
			throw new Exception($ex->getMessage());
		}
	}
	
	public function removeRolePermission($id){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ROLE_PERMISSIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->returnErrors(true);
			
			$service->delete();
			
		}catch(Exception $ex){
			
			throw new Exception($ex->getMessage());
		}
	}
		
	public function removeRoleByUserId($id){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::USER_ROLE_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'user_id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->delete();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function updateRole($id, $data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ROLES_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
				
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->setData($data);
			
			$service->update();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	

	public function updateRolePermission($id, $data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ROLE_PERMISSIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
				
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->setData($data);
			
			$service->update();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function updateUserRole($userId, $roleId){
		
		try{
			
			$this->removeRoleByUserId($userId);
			
			$this->assignRole($userId, $roleId);
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function getOrganizationByName($name){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ORGANIZATIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'name',
				'expression' => '='
				));
			array_push($criteriaData, $name);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$info = $service->read();
			
			if(isset($info)){
				return $info[0];	
			}else{
				return null;
			}
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	public function getOrganizationById($id){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_READONLY, DatabaseConstants::ORGANIZATIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$info = $service->read();
			
			if(isset($info)){
				return $info[0];	
			}else{
				return null;
			}
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	public function createOrganization($data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ORGANIZATIONS_TBL);
			
			$service->returnErrors(true);
			
			$service->setData($data);
			
			$id = $service->create();
			
			return $id;
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	public function updateOrganization($id, $data){
		
		try{
			
			$service = ServiceManager::getConnection(DatabaseConstants::DRAGONPHP_DS, DatabaseConstants::ORGANIZATIONS_TBL);
			
			$criteria = array();
			$criteriaData = array();
			
			array_push($criteria, array('field_name' => 'id',
				'expression' => '='
				));
			array_push($criteriaData, $id);
			
			$service->setCriteria($criteria, $criteriaData);
			
			$service->setData($data);
			
			$service->update();
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

}
?>
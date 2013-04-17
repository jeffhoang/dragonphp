<?php

/**
 * Index
 *
 * This class is the Organization Manager controller.
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package
 * @version
 */

require_once(APPLICATION_LIB_DIR . 'mvc/AbstractSecuredController.php');
require_once(FRAMEWORK_SERVICES_DIR . 'security/SecurityService.php');

class OrganizationManager extends AbstractSecuredController {
	
	private $_service;
	private static $_debug = false;
	
	public function showOrganizations(Request $request, Session $session, $view){
		
		$this->_includeHeader(array('jquery', 'fancybox'), false, false, false, true);
		
		// Get list of organizations from database
		$service = new SecurityService();
		$listOfOrganizations = $service->getOrganizations();
		
		$this->setAttribute('list_of_organizations', $listOfOrganizations);
		
		return new Template('show_organizations');
	}
	
	
	public function createOrganization(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
		
			return new Template('create_organization');
			
		}else{
			
			$service = new SecurityService();
		
			$name = trim($request->getParameter('name'));
			
			$orgName = $service->getOrganizationByName($name);
			
			if(!empty($orgName)){
			
				$result['response_code'] = -1;
				$result['message'] = 'This organization name is taken. Please enter a different one.';
					
			}else{
			
				$data = $request->getParameters(true);
				
				$service->createOrganization($data);
				
				$result['response_code'] = 1;
				
			}
			
			return $this->showJsonResponse($result);
		}
	}
	
	public function updateOrganization(Request $request, Session $session, $view){
		
		$service = new SecurityService();
		$id = $request->getParameter('id');
			
		if(!$request->getParameter(SUBMIT_PARAM)){
			
			$info = $service->getOrganizationById($id);
			
			$this->setAttribute('original_org_name', $info->name);
			
			if(isset($info)){
				$this->setAttributes(get_object_vars($info));
			}
		
			self::dumpObject($info, self::$_debug);
			
			return new Template('update_organization');
			
		}else{
		
			$name = trim($request->getParameter('name'));
			
			$org = $service->getOrganizationByName($name);
			
			$orginalOrgName = $request->getParameter('original_org_name');
			
			$orgName = $org->name;
			
			if(!empty($org) && strcmp($orgName, $orginalOrgName) != 0){
			
				$result['response_code'] = -1;
				$result['message'] = 'This organization name is taken. Please enter a different one.';
					
			}else{
			
				$data = $request->getParameters(true);
				
				$service->updateOrganization($id, $data);
				
				$result['response_code'] = 1;
				
			}
			
			return $this->showJsonResponse($result);
		}
	}
	
	public function removeOrganization(Request $request, Session $session, $view){
		
		if(!$request->getParameter(SUBMIT_PARAM)){
		
			return new Template('remove_organization');
			
		}else{
			
			$id = $request->getParameter('id');
			
			$service = new SecurityService();
		
			try {
				
				$result['response_code'] = 1;
				
				$service->removeOrganization($id);
			}catch(Exception $ex){
				
				$result['response_code'] = -1;
				
				$message = $ex->getMessage();
				
				if(preg_match('/user_roles/', $message)){
					$result['message'] = 'This organization can not be removed because it already has at least 1 user.';
				}else{
					$result['message'] = 'This organization can not be removed. Error message: ' . $message;
				}
					
			}
			
			return $this->showJsonResponse($result);
		}
	}
}
?>		
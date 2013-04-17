<?php
/*
 ======================================================================
 DragonPHP - Controller

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

 @package    mvc/controller
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

require_once('ControllerIF.php');
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');
require_once(FRAMEWORK_COMMON_DIR . 'FrameworkConstants.php');
require_once(FRAMEWORK_VIEW_DIR . 'Template.php');
require_once(FRAMEWORK_CONTROLLER_DIR . 'Error.php');
require_once(FRAMEWORK_HELPERS_DIR . 'CacheHelper.php');
require_once(FRAMEWORK_EXTERNAL_DIR . 'json/JSON.php');

class Controller implements ControllerIF {

	private $_viewClass;
	protected $_model;
	protected $_securedModule;
	protected $_securedController;
	protected $_currentController;
	protected $_currentModule;

	private static $_errors;
	protected static $_logger;
	protected $_renderer;
	protected $_modelName;

	const USER = 'user';
	const SUBMIT_X = 'submit_x';
	const SUBMIT_Y = 'submit_y';

    const SECURE_PORT = '443';
    const NON_SECURE_PORT = '80';
    
    const CONTENT_TYPE_TEXT = 'text/plain';

    protected $_sslEnabled = false;

	function __construct($module = false, $controllerName = false, $renderer = false){

		$this->setView($module, $controllerName, $renderer);
		self::$_logger = LoggerFactory::getInstance(get_class());

		if(!$this->_model){
			$this->_model = new Model();
		}

		$this->_currentController = $controllerName;
		$this->_currentModule = $module;
		$this->_renderer = $renderer;

		$this->_modelName = StringUtilHelper::underscore($controllerName) . 's';
	}
	
	public function setLogger($logFileName){
		self::$_logger = CommonLogger::getInstance(get_class(), $logFileName);
	}

	public function getView(){
		return $this->_viewClass;
	}

	public function setView($module, $controllerName, $renderer = false){

		$className = $controllerName . VIEW_SUFFIX;
		$viewClassFileName = APPLICATION_MODULE_DIR . $module . '/views/'. $className . '.php';

		if(is_file($viewClassFileName)) {
			require_once($viewClassFileName);

			// instantiate a default view object
			if($renderer){
				$this->_viewClass = new $className($renderer, $module, $controllerName);
			}
		}
	}

	public function executeFilter(Request $request, Session $session, $renderer) {

	}

	public function execute(Request $request, Session $session, $view) {

	}

	public function sendRedirect($url) {

		header("Location: $url");

		exit;

	}

	public function setModel($key, $value){
		if(!$this->_model){
			$this->_model = new Model();
		}

		$this->_model->{$key} = $value;
	}

	protected function _setError($key, $value){
		if(!self::$_errors){
			self::$_errors = Error::getInstance();
		}

		self::$_errors->setError($key, $value);
	}

	protected function _removeError($key){
		self::$_errors->remove($key);
	}

	public function getErrors(){
		if(self::$_errors && self::$_errors->getErrors()){
			return self::$_errors->getErrors();
		}else {
			return null;
		}
	}

	public function getModel(){
		return $this->_model;
	}

	public function setModels($model){

		if(!$this->_model){
			$this->_model = new Model();
		}

		foreach($model as $k=>$v){
			$this->_model->{$k} = $v;
		}

	}

	public function isSecure(){
		return false;
	}

	public function setFormToken(){

		$token = uniqid();
		CacheHelper::saveCache(CACHE_DIR . APPLICATION_NAME . '/form_tokens/', $token, $token);
		$this->_renderer->setAttribute('form_token', $token);
	}

	public function resetFormToken(){

		$request = Request::getInstance();
		$token = $request->getParameter('form_token');
		CacheHelper::removeCache(CACHE_DIR . APPLICATION_NAME . '/form_tokens/', $token);
	}

	public function isValidSubmission(){

		$request = Request::getInstance();
		$tokenIn = $request->getParameter('form_token');

		self::$_logger->debug('request token -> ' . $tokenIn);

		$cachedToken = CacheHelper::getCache(CACHE_DIR . APPLICATION_NAME . '/form_tokens/', $tokenIn, 360000);

		self::$_logger->debug('cached token -> ' . $cachedToken);

		if(strcmp($tokenIn, $cachedToken) == 0){
			return true;
		}else{
			return false;
		}
	}

	public function setSecuredModule($module){
		$this->_securedModule = $module;
	}

	public function setSecuredController($controller){
		$this->_securedController = $controller;
	}

	public function getSecuredModule(){
		return $this->_securedModule;
	}

	public function getSecuredController(){
		return $this->_securedController;
	}

	public function redirect($module, $controller, $protocolScheme = false) {
		$this->forward($module, $controller, $protocolScheme, true);
	}

	public function forward($module, $controller, $protocolScheme = false, $isRedirect = false){

		if($protocolScheme == false){
			$protocolScheme = 'http';
		}

		if($isRedirect == true){
			$url = $protocolScheme . '://' . $_SERVER['HTTP_HOST'] . '/?module='. $module . '&controller='. $controller;
			header("Location: $url");
			exit;
		}else{

			$dispatcher = Dispatcher::getInstance();

			$dispatcher->forward($module, $controller, $this->_renderer);
			exit;
		}
	}

	public function redirectToUrl($url){
		header("Location: $url");
		exit;
	}

	public function setAttribute($key, $value){
		$this->_renderer->setAttribute($key, $value);
	}

	public function getAttribute($key){
		return $this->_renderer->getAttribute($key);
	}

	public function addHeadLink($href, $rel, $type){
		$this->_renderer->addHeadLink($href, $rel, $type);
	}

	public function setTitle($title){
		$this->_renderer->setTitle($title);
	}

	public function addJavascript($javascriptHandle, $directPath = false){
		$this->_renderer->addJavascript($javascriptHandle, $directPath);
	}

	public function addMeta($httpEquiv, $content){
		$this->_renderer->addMeta($httpEquiv, $content);
	}

	public function addCss($href){
		$this->addHeadLink($href, 'stylesheet', 'text/css');
	}

	public function getCurrentController(){
		return $this->_currentController;
	}

	public function getCurrentModule(){
		return $this->_currentModule;
	}

	public function isFormSubmission(Request $request){

		$submitX = $request->getParameter(self::SUBMIT_X);
		$submitY = $request->getParameter(self::SUBMIT_Y);
		$submit = $request->getParameter(SUBMIT_PARAM);

		self::$_logger->debug("submit image check (x) -> " . $submitX);
        self::$_logger->debug("submit iamge check (y) -> " . $submitY);
        self::$_logger->debug("submit button check -> " . $submit);

		if(isset($submit) || (isset($submitX) && isset($submitY))){
			self::$_logger->debug("FORM SUBMISSION DETECTED");
			return true;
		}else{
			return false;
		}
	}

	public function getRenderer(){
		return $this->_renderer;
	}

	public function getModelName(){
		return $this->_modelName;
	}

	public function setAttributes($object){
		foreach($object as $k=>$v){
			$this->setAttribute($k, $v);
		}
	}

	public function hasAccess($realmHandle){

		$realms = $this->getRealms();
		$realmHandle = FrameworkConstants::ACL_PREFIX . $realmHandle;

		if(!empty($realms[$realmHandle])){
			return true;
		}else{
			return false;
		}
	}

	public function getRealms(){

		$currentUser = $this->_getCurrentUser();

		return $currentUser->realms;
	}

	protected function _getCurrentUser(){
		$session = Session::getInstance();
		return $session->get(self::USER);
	}

	public function reservedPostexecute(Request $request){

		$realms = $this->getRealms();

		if(sizeof($realms) > 0){
			foreach($realms as $k=>$realmInfo){
				$this->setAttribute($k, $realmInfo);
			}
		}
	}

	/**
	 * Is SSL enabled
	 * 
	 * This function redirects to an ssl page if SSL_ENABLED is defined and the page 
	 *
	 */
	public function isSslEnabled(){

		$requestUrl = $_SERVER['REQUEST_URI'];
        $serverPort = $_SERVER['SERVER_PORT'];
        $serverName = $_SERVER['SERVER_NAME'];

		if($this->_sslEnabled || (defined('SSL_ENABLED')) && strcmp($serverPort, self::NON_SECURE_PORT) == 0){

		    if(strcmp($serverPort, self::SECURE_PORT) != 0){
            	$redirectUrl = 'https://'. $serverName . $requestUrl;
                $this->redirectToUrl($redirectUrl);
            }
		}

		// switch to non-ssl if _sslEnabled is turned off but the current requested URL is ssl'ed
		if(!$this->_sslEnabled && strcmp($serverPort, self::SECURE_PORT) == 0){
			$redirectUrl = 'http://'. $serverName . $requestUrl;
            $this->redirectToUrl($redirectUrl);
		}
	}
	
	/**
	 * Check if a handle has access
	 *
	 * @param unknown_type $handle
	 * @param unknown_type $action
	 * @return unknown $hasAccess
	 */
	protected function _hasAccess($handle, $action = false){
		
		$currentUser = $this->_getCurrentUser();
		$realmPermissions = $currentUser->realm_permissions;
		
		$realmActions = $realmPermissions['ACL_' . $handle];
		
		if(!empty($realmActions) && !$action){
			return true;
		}elseif(empty($realmActions) && !$action){
			return false;
		}
		
		// check action permission
		if($action && !empty($realmActions)){
			
			if(in_array($action, $realmActions)){
				return true;
			}else{
				return false;
			}
	
		}else{
			return false;
		}
	}

	/**
	 * Get login ID
	 *
	 * @return $loginId
	 */
	protected function _getLoginId(){
		$user = $this->_getCurrentUser();
		$loginId = $user->info->login_id;
		return $user->info->login_id;
	}

	/**
	 * Get organization ID
	 *
	 * @return $organizationId
	 */
	protected function _getOrganizationId(){
		$user = $this->_getCurrentUser();
		$organizationId = $user->info->organization_id;
		return $organizationId;
	}

	/**
	 * Set permissions so they are available within the view
	 *
	 * @param Request $request
	 */
	protected function _setPermissions(Request $request){
		$currentUser = $this->_getCurrentUser();
		$realmPermissions = $currentUser->realm_permissions;
		
		if($realmPermissions){
			foreach($realmPermissions as $realm=>$actions){
				$request->setParameter($realm, '1');
			}
		}
	}

	/**
	 * Show AJAX response header type of text/plain
	 *
	 * @param unknown_type $values
	 */
	protected function _showAjaxTextResponse($values){

		$json = new Services_JSON();
		$output = $json->encode($values);

		self::$_logger->debug('json ajax response ' . $output);

		header('Content-type: ' . self::CONTENT_TYPE_TEXT);

		echo $output;

		exit;
	}
	
	/**
	 * Marshal html string for embedding within a xml envelope (i.e. soap envelope)
	 *
	 * @param unknown_type $creative
	 * @return unknown $newMarshalledString
	 */
	protected function _marshalHtml($creative){
		return  '<![CDATA['.  $creative . ']]>';
	}
	
	public static function dumpObject($value, $debug = false){
		
		if($debug == true){
			echo '<pre>';
				
			print_r($value);
			
			echo '</pre>';
		}
	}
	
	public function showJsonResponse($data = false){
		
		$responseType = 'text/plain';	
		
		$json = new Services_JSON();
		
 		$output = $json->encode($data);
 		
 		self::$_logger->debug('json: ' . $output);
 		
 		$this->setAttribute('message', $output);
 		
 		return new Template('json_response', $responseType, true);
	}
}
?>

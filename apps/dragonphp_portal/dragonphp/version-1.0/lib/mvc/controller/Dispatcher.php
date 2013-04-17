<?php

/*
 ======================================================================
 DragonPHP - Dispatcher

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

include_once(FRAMEWORK_CONFIG_DIR . 'application_definitions.php');

require_once(DRAGON_CONTROLLER);
require_once(DRAGON_FLOW_CONTROLLER);
require_once(DRAGON_VIEW);
require_once(FRAMEWORK_HELPERS_DIR . FORM_VALIDATION_HELPER);
require_once(RESOURCE_BUNDLE_HELPER);
require_once(FRAMEWORK_HELPERS_DIR . 'UrlMapper.php');
require_once('DispatcherIF.php');
require_once('Session.php');
require_once('ModelAndFlow.php');
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');

class Dispatcher implements DispatcherIF {

	private static $_instance;
	private static $_errors;
	private static $_request;
	private static $_session;
	private static $_currentLocale;
	private static $_method;
	private static $_modelAndFlow;
	private static $_timer;
	protected $_currentController;
	protected $_currentRenderer;

	private static $_logger;

	const LOG_FILE_NAME = 'dragon_dispatcher.log';
	const SUBMIT_X = 'submit_x';
	const SUBMIT_Y = 'submit_y';
	const RESERVED_POST_EXECUTE_COMMAND = 'reservedPostexecute';

	private static $_reservedKeywords = array('controller', 'Controller', 'UrlMapper', 'urlmapper');
	
	public function __construct() {
		// create logger
		self::$_logger = LoggerFactory::getInstance(get_class(), self::LOG_FILE_NAME);
	
		// initialize
		self::_init();

		// resolve url mapper
		self::_resolve();
	}

	private static function _init() {

		try {
			self::_parseRequest();

			$controller = self::$_request->getParameter(CONTROLLER_PARAM);
			
			if(in_array($controller, self::$_reservedKeywords)){
				throw new Exception('Error! Controller name can not be ' . $controller . ' because it is a reserved framework word.');
			}
		} catch (Exception $ex) {
			if(self::$_logger){
				self::$_logger->error($ex->getMessage());		
			}
			
			throw $ex;
		}
	}

	public static function getInstance() {

		try {
			if(defined('BENCHMARK_TIMER') && BENCHMARK_TIMER == 'on') {
				self::startTimer();
			}

			if(!self::$_instance) {
				self::$_instance = new self();
				self::$_modelAndFlow = ModelAndFlow::getInstance();
			}
			
			if(!self::$_session){
				self::$_session = Session::getInstance();
			}
			
		} catch (Exception $ex) {
			
			if(self::$_logger){
				self::$_logger->error('Can not get a instance of the dispatcher', $ex);
			}
			
			// also throw it to the container
			throw new Exception($ex->getMessage());
		}

		return self::$_instance;
	}

	public function execute() {

		try {

			self::$_logger->debug(self::$_request->getRequest());

			// get standard parameters
			$module = self::$_request->getParameter(MODULE_PARAM);

			$controller = self::$_request->getParameter(CONTROLLER_PARAM);
			$command = self::$_request->getParameter(COMMAND_PARAM);
			
			if(empty($module)){
				$module = DEFAULT_MODULE;
				self::$_request->setParameter(MODULE_PARAM, $module);
			}

			if(!$controller){
				$controller = DEFAULT_CONTROLLER;
				self::$_request->setParameter(CONTROLLER_PARAM, $controller);
			}

			// execute custom filter if it's implemented
			$this->_executeFilter($module, $controller);

			// execute security filter
			if(($module != 'Security' && $controller != 'Login') ||
				($module != $this->_currentController->getSecuredModule && $controller !=
				$this->_currentController->getSecuredController)){

				$this->_executeSecurityFilter($module, $controller);
				

			}

			// execute form validation if it's a form submission
			if($this->isFormSubmission(self::$_request))
			{
				$this->_executeValidator($module, $controller);
			}

			// execute controller
			$this->_executeController($module, $controller, $command);

			
		}catch (Exception $ex){
			self::$_logger->error('Error while executing controller.', $ex);

			$this->_displayErrorMessage($ex);
		}
	}

	private function _executeFilter($module, $controllerName){

		try {
			if($module && $controllerName){

				$controllerFile = APPLICATION_MODULE_DIR . $module . '/controllers/'. $controllerName . '.php';
				$controllerClass = $controllerName;

				if(!is_file($controllerFile)){
					$controllerFile = APPLICATION_MODULE_DIR . $module . '/controllers/'. $controllerName . CONTROLLER_SUFFIX . '.php';

					$controllerClass = $controllerName . CONTROLLER_SUFFIX;
				}

				
				if(is_file($controllerFile)){

					require_once($controllerFile);

					// get default view renderer
					$renderer = ViewHelper::getRenderer($module, DEFAULT_RENDERER);

					$this->_currentRenderer = $renderer;

					$controller = new $controllerClass($module, $controllerName, $renderer);

					// check ssl enabled flag
					$controller->isSslEnabled();

					if(method_exists($controller, FILTER_COMMAND)){

						$args = array(self::$_request, self::$_session, $renderer);

						call_user_func_array(array($controller, FILTER_COMMAND), $args);

						// check for errors
						$errors = $controller->getErrors();
						if($errors && sizeof($errors) > 0){
							self::$_errors = $errors;
							$a->errors = self::$_errors;
						}
					}

					$this->_currentController = $controller;
				}
			}
		}catch(Exception $ex){
			throw new Exception('Error while executing filter method: '. $ex->getMessage());
		}

	}

	private function _executeSecurityFilter($module, $controllerName){

		$controller = $this->_currentController;

		if(!$controller){
			$controllerFile = APPLICATION_MODULE_DIR . $module . '/controllers/'. $controllerName . '.php';
			$controllerClass = $controllerName;

			if(!is_file($controllerFile)){
				$controllerFile = APPLICATION_MODULE_DIR . $module . '/controllers/'. $controllerName . CONTROLLER_SUFFIX . '.php';
				$controllerClass = $controllerName . CONTROLLER_SUFFIX;
			}

			if(is_file($controllerFile)){

				require_once($controllerFile);

				$controller = new $controllerClass($module, $controllerName);
			}
		}

		if(!$controller){
			throw new Exception('unable to find the controller -> ' . $controllerName . ' : ' . $controllerFile);
		}

		$isSecure = (bool) $controller->isSecure();

		self::$_logger->info('Is secured page?' . $isSecure);

		if($isSecure){

			self::$_logger->info('Checking if user has logged in...');

			$user = self::$_session->get('user');

			if(empty($user)){
				$securedModule = $controller->getSecuredModule();
				$securedController = $controller->getSecuredController();

				if(!isset($securedController) || !isset($securedModule)){
					$this->_currentController = null;
					$this->forward('Security', 'Login');
				}else{
					$this->_currentController = null;
					$this->forward($securedModule, $securedController);
				}
				exit;
			}
		}

	}

	public function forward($module, $controller, $renderer = false, $urlAlias = false, $parameters = false){

		if($urlAlias){
			self::$_request->setParameter($urlAlias, '');
			self::$_request->remove(MODULE_PARAM);
			self::$_request->remove(CONTROLLER_PARAM);
			self::$_request->remove('submit');
			self::$_request->remove(COMMAND_PARAM);

			// resolve url alias
			self::_resolve();

		}else{
			self::$_request->remove(COMMAND_PARAM);
			self::$_request->setParameter(MODULE_PARAM, $module);
			self::$_request->setParameter(CONTROLLER_PARAM, $controller);
			self::$_session->set('REQUEST_URI', $_SERVER['REQUEST_URI']);
		}

		if($parameters && is_array($parameters)){
			foreach($parameters as $k=>$v){
				self::$_request->setParameters($k, $v);
			}
		}

		if($renderer){
			$this->_currentRenderer = $renderer;
		}

		$this->execute();

	}

	private function _executeController($module, $controllerName, $command){

		try {
			if($module && $controllerName){

				$controller = $this->_currentController;

				// get default view renderer
				if(!$this->_currentRenderer){
					$renderer = $this->_currentRenderer;
				}else{
					$renderer = ViewHelper::getRenderer($module, DEFAULT_RENDERER);
				}

				if(!$command){
					$command = DEFAULT_COMMAND;
				}

				if(!$controller){
					$controllerFile = APPLICATION_MODULE_DIR . $module . '/controllers/'. $controllerName . '.php';

					$controllerClass = $controllerName ;

					if(!is_file($controllerFile)){
						$controllerFile = APPLICATION_MODULE_DIR . $module . '/controllers/'. $controllerName . CONTROLLER_SUFFIX . '.php';
						$controllerClass = $controllerName . CONTROLLER_SUFFIX;
					}


					if(is_file($controllerFile)){

						require_once($controllerFile);
						$controller = new $controllerClass($module, $controllerName, $renderer);
					}
				}else {
					// set the view and renderer to an existing controller
					$controller->setView($module, $controllerName, $renderer);
				}

				if(isset($command)){
					self::$_logger->debug('executing ' . $controllerName . ' - ' . $command);
				}

				if(method_exists($controller, $command)){
					$viewClass = $controller->getView();
					$args = array(self::$_request, self::$_session, $viewClass, self::$_modelAndFlow);
					$object = call_user_func_array(array($controller, $command), $args);

					// call reserved post-execute function
					$args2 = array(self::$_request);
					call_user_func_array(array($controller, self::RESERVED_POST_EXECUTE_COMMAND), $args2);

					self::$_logger->debug($object);

					// get model
					$model = $controller->getModel();

					// check for errors
					$errors = $controller->getErrors();

					if($errors && sizeof($errors) > 0){
						self::$_errors = $errors;
						$model->errors = self::$_errors;
					}

					$viewClassName = $controllerName . VIEW_SUFFIX;

					if(is_file(APPLICATION_MODULE_DIR . $module . '/views/'. $viewClassName . '.php') && $object instanceof $viewClassName){
						$this->_executeView($object, $controllerName, $module, $controller->getRenderer(), $model);
					}else if($object instanceof Template){
						$model->errors = $controller->getErrors();
						$this->_renderTemplate($object, $controllerName, $module, $model);
					}else{
						throw new Exception('No view nor template specified after controller execute method');
					}
				}

			}
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	private function _renderTemplate($template, $controllerName, $module, $model){

		$renderer = $this->_currentRenderer;

		// create a renderer
		if(!$renderer){
			$renderer = new SmartyRenderer($module);
		}

		if(DEFAULT_VIEW_RENDERER){
			require_once(FRAMEWORK_RENDERERS_DIR . DEFAULT_VIEW_RENDERER . '.php');
			$rendererClassName = DEFAULT_VIEW_RENDERER;

			if(!$renderer){
				$renderer = new $rendererClassName($module);
			}
		}

		// set template id to process
		$renderer->setAttribute(TEMPLATE_SET_ID, $template->getTemplateId());

		$renderer->setAttribute('current_module_' . $module, $module);

		$isGlobal = $template->isGlobal();
		
		if($isGlobal == true){
			$renderer->setAttribute(IS_TEMPLATE_GLOBAL, true);
		}
		
		require_once(FRAMEWORK_VIEW_DIR . 'WebView.php');

		$view = new WebView($renderer, $module, $controllerName);

		// set content-type if it's anything other text/html
		$contentType = $template->getContentType();
		if(isset($contentType)){
			header('Content-type: ' . $contentType);
		}

		// render the template and its layouts
		$this->_executeViewLayout($renderer, $view, $controllerName, $module, $model);
	}

	private function _executeView($view, $controllerName, $module, $renderer, $model){
		// get command
		$command = $view->getEntry();

		if(!$command){
			$command = DEFAULT_COMMAND;
		}

		$args = array(self::$_request, self::$_session, $renderer, $model);

		// merge the attributes set from the controller and view
		$attributes1 = $view->getAttributes();
		foreach($attributes1 as $key => $value){
			$renderer->setAttribute($key, $value);
		}
		
		self::$_logger->debug($view);

		$view->setRenderer($renderer);

		// execute view
		call_user_func_array(array($view, $command), $args);

		$renderer = $view->getRenderer();

		self::$_logger->debug($model, false, true);

		$this->_executeViewLayout($renderer, $view, $controllerName, $module, $model);
	}

	private function _executeViewLayout($renderer, $view, $controllerName, $module, $model){

		$resourceBundleData = ResourceBundleHelper::loadResourceBundleForRenderer($renderer, $controllerName, $module, self::$_currentLocale);

		// call process layout command
		$processCommand = PROCESS_LAYOUT_COMMAND;

		// save request info into model
		if(sizeof(self::$_request->getRequest()) > 0){
			foreach(self::$_request->getRequest() as $k => $v) {

				self::$_logger->debug($k . '->' . $v);

				if(is_string($v)){
					$v = trim($v);
				}

				$model->$k = $v;

				// set key/value too
				$viewValue = $renderer->getAttribute($k);
				if(empty($viewValue)){
					$renderer->setAttribute($k, $v);
				}
			}
		}

		$renderer->setAttribute('current_module_' . $module, $module);
		
		$renderer->setAttribute('model', $model);

		//print_r($model);

		self::$_logger->debug($model, null, true);

		// store model in renderer
		$renderer->setAttribute('model', $model);

		$args = array($renderer);

		call_user_func_array(array($view, $processCommand), $args);

		if(defined('BENCHMARK_TIMER') && BENCHMARK_TIMER == 'on') {
			self::startTimer();
			self::displayTimerInfo();
		}
	}

	private function _executeValidator($module, $controller){
					
		try {
			if($module && $controller){
				// check if the validator class exists for this controller
				$validatorFile = APPLICATION_MODULE_DIR . $module . '/controllers/'. $controller . VALIDATOR_SUFFIX . '.php';

				self::$_logger->debug($validatorFile);

				if(is_file($validatorFile)){
					
					require_once($validatorFile);
					
					$validatorClass = $controller . VALIDATOR_SUFFIX;
					$validator = new $validatorClass;

					// inject renderer
					$validator->setRenderer($this->_currentRenderer);

					if(method_exists($validator, VALIDATE_COMMAND)){

						$validator->setRequest(self::$_request);
						$validator->setModule($module);
						$validator->setController($controller);

						$args = array(self::$_request, Session::getInstance());

						call_user_func_array(array($validator, VALIDATE_COMMAND), $args);

						$errors = call_user_func_array(array($validator, GET_ERRORS_COMMAND), null);

						if($errors && sizeof($errors) > 0){

							self::$_logger->info('Validator found errors');

							self::$_errors = $errors;
							$model->errors = self::$_errors;

							$viewClassName = $controller . VIEW_SUFFIX;

							$viewClassFileName = APPLICATION_MODULE_DIR . $module . '/views/'. $viewClassName . '.php';

							// get default view renderer
							$renderer = $this->_currentRenderer;

							if(!isset($renderer)){
								$renderer = ViewHelper::getRenderer($module, DEFAULT_RENDERER);
							}

							if(is_file($viewClassFileName)){
								require_once($viewClassFileName);
								$view = new $viewClassName($renderer, $module);
							}else{
								require_once(FRAMEWORK_VIEW_DIR . 'WebView.php');
								$errorTemplateSetId = $validator->getErrorTemplateSetId();

								if(empty($errorTemplateSetId)){
									throw new Exception('Error: you need to set $_errorTemplateSetId in your '. $controller. 'Validator class!');
								}

								$renderer->setAttribute(TEMPLATE_SET_ID, $errorTemplateSetId);

								$view = new WebView($renderer, $module, $controllerName);

							}

							// execute on error method in view
							$this->_executeOnErrorView($renderer, $view, $controller, $module, $model);

							exit;
						}
					}
				}
			}
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}

	private function _executeOnErrorView($renderer, $view, $controllerName, $module, $model){

		// get command
		$command = 'onError';

		$args = array(self::$_request, self::$_session, $renderer, $model);

		// execute view
		$renderer = call_user_func_array(array($view, $command), $args);

		$this->_executeViewLayout($renderer, $view, $controllerName, $module, $model);

	}

	private function _parseRequest() {

		try {
			// parse incoming request data
		    self::$_request = Request::getInstance();

        	self::$_request->setRequest(array_merge($_GET, $_POST));

		} catch (Exception $ex) {
			self::$_logger->error('Request parse exception', $ex);
		}
	}

	protected function _resolve(){

		$module = self::$_request->getParameter(MODULE_PARAM);
		$controller = self::$_request->getParameter(CONTROLLER_PARAM);

		// resolve url map if module and controller don't exist
		if(empty($module) && empty($controller)){
			UrlMapper::getInfo(self::$_request->getRequest());
		}
	}

	public static function startTimer() {
		require_once(PEAR_BENCHMARK_TIMER);

		self::$_timer = new Benchmark_Timer();
		self::$_timer->start();
		self::$_timer->setMarker('Dispatcher');
	}

	public static function stopTimer() {
		self::$_timer->stop();
		echo time();
	}

	public static function displayTimerInfo() {
		self::$_timer->display();
		echo '<p>Execution time elapsed = ' . self::$_timer->timeElapsed() * 1000 . ' milliseconds';
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
	
	protected function _displayErrorMessage($exception){
		
		echo '<p><b>Error: ' . $exception->getMessage() . '<br>';
		
		
		// only show the stack trace if debug level is 0
		if(strcmp(LOGGER_LEVEL, '0') == 0){
 			echo '<p><table border=1><tr><td bgcolor="#efefef">StackTrace: <pre>';
		
 			echo $exception->getTraceAsString() . '<p>';
 			
			print_r($exception);
		
			echo '</pre></td><tr></table>';
		}
		
		echo '<p><a href="http://www.dragonphp.com">http://www.dragonphp.com</a>';
		
		exit;
	}
}
?>
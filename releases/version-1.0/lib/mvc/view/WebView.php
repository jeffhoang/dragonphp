<?php

/*
 ======================================================================
 DragonPHP - WebView
 
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
 
 @package    mvc/view
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

require_once(DRAGON_REQUEST);
require_once('WebViewIF.php');
require_once(VIEW_HELPER);
require_once(FRAMEWORK_COMMON_DIR . 'LoggerFactory.php');

class WebView implements WebViewIF {

	protected $_globalTemplateDir;
	protected $_headerTemplateName;
	protected $_footerTemplateName;
	protected $_currentTemplateDir;
	protected $_currentModuleName;
	protected $_currentControllerName;
	protected $_entryCommand;
	protected $_templateId;
	protected $_data;
	
	const DEFAULT_SECTION = 'default';
	const USER = 'user';
	
	protected $_renderer;
	
	protected static $_logger;
	
	public function execute(Request $request, Session $session, $renderer, $model = false){}

	public function onError(Request $request, Session $session, $renderer, $model = false){
		
		return $renderer;
	}
	
	public function __construct($renderer = false, $currentModuleName = false, $currentControllerName = false) {

		$currentTemplateDir = $renderer->getCurrentTemplateDir();
		$this->_currentModuleName = $currentModuleName;
		$this->_currentControllerName = $currentControllerName;
		$this->_renderer = $renderer;
		
		$isGlobal = $renderer->getAttribute(IS_TEMPLATE_GLOBAL);
		
		if($isGlobal == true){
			// overide template directory to global
			$currentTemplateDir = BASE_APPLICATION_DIR . 'global_templates/';
		}
		
		$this->config(GLOBAL_TEMPLATE_DIR, $currentTemplateDir, DEFAULT_HEADER, DEFAULT_FOOTER);
		
		if(method_exists($this, 'setLogger')){
			$this->setLogger();
		}else{
			self::$_logger = LoggerFactory::getInstance(get_class());
		}
	}
	
	public function config($globalTemplateDir, $currentTemplateDir, $headerTemplateName, $footerTemplateName){
	
		$this->_globalTemplateDir = $globalTemplateDir;
		$this->_headerTemplateName = $headerTemplateName;
		$this->_footerTemplateName = $footerTemplateName;
		$this->_currentTemplateDir = $currentTemplateDir;
		
	}
	
	public function getRenderedTemplate($renderer, $templateName, $templateDirectory){
	
		try {
			
			$renderer->setTemplateDirectory($templateDirectory);
			
			$result = $renderer->fetch($templateName);
				
			return $result;
			
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage());
		}
		
		return false;
	
	}
	
	public function renderTemplate($renderer, $templateName, $templateDirectory){
	
		try {
			
			$renderer->setTemplateDirectory($templateDirectory);
			
			$result = $renderer->show($templateName);
			
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage());
		}
	}
	
	public function processLayout($renderer, $templateSetId = false) {
		
		try {
			
			$mainTileData = $this->_getLayouts();
			
			// get a particular tile section
			if($templateSetId) {
				$tileData = $mainTileData[$templateSetId];
			} else if($renderer->getAttribute(TEMPLATE_SET_ID)) {
				$tileData = $mainTileData[$renderer->getAttribute(TEMPLATE_SET_ID)];
				$templateSetId = $renderer->getAttribute(TEMPLATE_SET_ID);
			} else if(isset($this->_templateId)){
				$tileData = $mainTileData[$this->_templateId];	
				$templateSetId = $this->_templateId;
			} else {
				$tileData = $mainTileData[DEFAULT_TILE_SECTION];	
				$templateSetId = DEFAULT_TILE_SECTION;		
			}

			if(empty($tileData)){
				$message = 'Renderering a single template (' . $templateSetId . ').';
				self::$_logger->debug($message);
				$tileData[$templateSetId] = $templateSetId;
			}
			
			if (!empty($tileData))
			{
				// first marshal head section
				$renderer->marshalHeadInfo();
				
				$layout = new Model();
				
				foreach($tileData as $templateName => $type) {
					
					// load reference template tiles
					if(strcmp($templateName, 'template_reference') == 0){
						$additionalTiles = $mainTileData[$type];
					}
					
					switch($type) {
						case 'local':
							$result = $this->getRenderedTemplate($renderer, $templateName, $this->_currentTemplateDir);
							$layout->$templateName = $result;
							break;	
						case 'global':
							$result = $this->getRenderedTemplate($renderer, $templateName, $this->_globalTemplateDir);
							$layout->$templateName = $result;
							break;
						case $type != 'global' && $type != 'local':
							$result = $this->getRenderedTemplate($renderer, $templateName, APPLICATION_MODULE_DIR . $type . '/templates/');
							$layout->$templateName = $result;
							break;
					}
				}
				
				if(is_array($additionalTiles) && sizeof($additionalTiles) > 0){
					
					foreach($additionalTiles as $templateName => $type) {
					
					switch($type) {
						case 'local':
							$result = $this->getRenderedTemplate($renderer, $templateName, $this->_currentTemplateDir);
							$layout->$templateName = $result;
							break;	
						case 'global':
							$result = $this->getRenderedTemplate($renderer, $templateName, $this->_globalTemplateDir);
							$layout->$templateName = $result;
							break;
						case $type != 'global' && $type != 'local':
							$result = $this->getRenderedTemplate($renderer, $templateName, APPLICATION_MODULE_DIR . $type . '/templates/');
							$layout->$templateName = $result;
							break;
					}
				}
				}
				
				// render the template with incorporating the layouts
				// deprecated
				$renderer->setAttribute('layout', $layout);
				
				// new sematic
				$renderer->setAttribute('template_tile', $layout);
				
				if(!is_file($this->_currentTemplateDir . $templateSetId . '.tpl')){
				
					// no main template file found so just display each layout sequentially
					foreach($layout->getAllData() as $layoutId=>$content){
						print($content);
					}
					exit;	
				}else{
					$this->renderTemplate($renderer, $templateSetId, $this->_currentTemplateDir);
				}
			}	
			
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage());
		}
	}
	
	protected function _getLayouts($moduleName = false, $layoutFileName = false) {

		try {
			
			if(!$moduleName) {
				$moduleName = $this->_currentModuleName;	
			}
			
			$configDir = APPLICATION_MODULE_DIR . $moduleName . '/conf/';
			
			if(!$layoutFileName) {
				$layoutFileName = DEFAULT_LAYOUT_FILE_NAME . '.ini';	
			} else {
				$layoutFileName = $layoutFileName . '.ini';	
			}
			
			$layoutFileName = $configDir . $layoutFileName;
			
			if(is_file($layoutFileName)) {
				// get data from layouts ini file			
				$data  = IniParser::parse($layoutFileName, true);
			}
			
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage());
		}
		
		return $data;
	}
	
	public function generateHiddenFormFields($renderer, $request, $resourceBundleData) {
		
		try {
			
			$hidden = array();
			
			if(!$resourceBundleData){
				$resourceBundleData = array();	
			}
			
			if($request)
			{
				foreach($request->getRequest() as $k => $v) {
					if(!$renderer->getAttribute($k) && !in_array($k, $resourceBundleData)) {
						$hidden[$k] = $v;
						self::$logger->debug($k . ' = ' . $v);
					} else if ($renderer->getAttribute($k) && !in_array($k, $resourceBundleData)
					&& ($k != 'form_method' && $k != 'resource' && $k != 'statusId')
					) {
						$hidden{$k} = $renderer->getAttribute($k);
					
						self::$logger->debug($k . ' => '. $renderer->getAttribute($k));
					}
				}
				
				self::$logger->debug($hidden, false, false, true);
				
				$renderer->setAttribute(HIDDEN_FIELDS, $hidden);
			}
			
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage());		
		}
		
	}
	
	public function setEntry($command){
		$this->_entryCommand = $command;
	}
	
	public function getEntry(){
		return $this->_entryCommand;
	}
	
	public function sendRedirect($url, $scheme = false) {
		if($scheme == false){
			$protocolScheme = 'http';
		
			$url = $protocolScheme . '://' . $_SERVER['HTTP_HOST'] . $url;
		}
		
		header("Location: $url");
		
		exit;
	}
	
	public function setTemplate($templateId){
		$this->_templateId = $templateId;
	}
	
	public function redirect($module, $controller, $protocolScheme = false) {
		$this->forward($module, $controller, $protocolScheme);
	}
	
	public function forwardToAlias($urlAlias, $parameters = false){
		$this->forward(false, false, false, false, $urlAlias, $parameters);
	}
	
	public function forward($module = false, $controller = false, $protocolScheme = false, $isRedirect = false, $urlAlias = false, $parameters = false){
		
		if($protocolScheme == false){
			$protocolScheme = 'http';
		}
		
		if($isRedirect == true){
			$url = $protocolScheme . '://' . $_SERVER['HTTP_HOST'] . '/?module='. $module . '&controller='. $controller;
			header("Location: $url");
			exit;
		}else{
			$dispatcher = Dispatcher::getInstance();
			$dispatcher->forward($module, $controller, false, $urlAlias, $parameters);
			exit;
		}
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
	
	public function setAttribute($key, $value){
		$this->_renderer->setAttribute($key, $value);
	}
	
	public function getAttribute($key){
		return $this->_renderer->getAttribute($key);
	}
	
	public function getAttributes(){
		return $this->_renderer->getAttributes();
	}
	
	public function getRenderer(){
		return $this->_renderer;
	}
	
	public function setRenderer($renderer){
		$this->_renderer = $renderer;
	}
	
	protected function _getCurrentUser(){
		$session = Session::getInstance();
		return $session->get(self::USER);
	}
}
?>
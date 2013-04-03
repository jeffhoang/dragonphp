<?php

/*
 ======================================================================
 DragonPHP - SmartyRenderer
 
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
 
 @package    plugins/renderers
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

require_once('RendererIF.php');
require_once(SMARTY_CLASS);

class SmartyRenderer implements RendererIF {

	protected $_renderer;
	protected $_currentModule;
	private $_linkHandles = array();
	private $_javascriptHandles = array();
	private $_javascriptDirectHandles = array();
	private $_metaInfo = array();
	private $_pageTitle;
	const HEAD_SECTION_ATTRIBUTE_NAME = "head_section";

	public function __construct($moduleName) {
		
		try {
			$smarty = new Smarty();
			$this->setRenderer($smarty);
			$this->_currentModule = $moduleName;
			
			// configure smarty
			$this->configure();
		} catch (Exception $ex) {
			throw new Exception('Unable to configure Smarty Renderer');		
		}
	}
	
	public function configure() {

		if($this->_renderer) {
			
			if(!is_dir(APPLICATION_SMARTY_CACHE_DIR)){
				mkdir(APPLICATION_SMARTY_CACHE_DIR, 0777);
			}
			
			if(!is_dir(TEMPLATES_COMPILED_DIR)){
				mkdir(TEMPLATES_COMPILED_DIR, 0777);
			}
			
			if(!is_dir(TEMPLATE_CACHE_DIR)){
				mkdir(TEMPLATE_CACHE_DIR, 0777);
			}
			
			$this->_renderer->template_dir = APPLICATION_MODULE_DIR . $this->_currentModule . '/templates/';
			
			// create template_c directory for the current module if it does not exist
			if(!is_dir(TEMPLATES_COMPILED_DIR . $this->_currentModule)){
				mkdir(TEMPLATES_COMPILED_DIR . $this->_currentModule, 0777);
			}
			
			$this->_renderer->compile_dir = TEMPLATES_COMPILED_DIR . $this->_currentModule;
			$this->_renderer->config_dir = APPLICATION_MODULE_DIR . $this->_currentModule . '/conf/';
			
			// create cache directory for the current module if it does not exist
			if(!is_dir(TEMPLATE_CACHE_DIR . $this->_currentModule)){
				mkdir(TEMPLATE_CACHE_DIR . $this->_currentModule, 0777);
			}
			
			$this->_renderer->cache_dir = TEMPLATE_CACHE_DIR . $this->_currentModule;
		}
	}
	
	public function setRenderer($renderer){
		
		$this->_renderer = $renderer;
	}
	
	public function show($templateName){
				
		if($templateName && $this->_renderer) {
			
			try {

				$this->_renderer->display($templateName . DEFAULT_TEMPLATE_EXTENSION);
			
			} catch (Exception $ex) {
			
				throw new Exception('Unable to display template');
			}
			
		}

	}
	
	public function fetch($templateName){
		
		if($templateName && $this->_renderer){
			
			try{
				
				$result = $this->_renderer->fetch($templateName . DEFAULT_TEMPLATE_EXTENSION);
				
				return $result;
				
			}catch(Exception $ex){
				throw new Exception('Could not fetch template');
			}
		}
	
		return false;
	}

	public function setAttribute($key, $value) {
		$this->_renderer->assign($key, $value);
	}
	
	public function getAttribute($key) {
		return $this->_renderer->get_template_vars($key);
	}
	
	public function getAttributes(){
		return $this->_renderer->get_template_vars();
	}
	
	public function setTemplateDirectory($dir) {
		$this->_renderer->template_dir = $dir;
	}
	
	public function setConfigDirectory($dir) {
		$this->_renderer->config_dir = $dir;
	}
	
	public function getCurrentTemplateDir() {
		return $this->_renderer->template_dir;
	}
	
	public function addJavascript($javascriptFileName, $directPath = false){
		
		if($directPath){
			array_push($this->_javascriptDirectHandles, $javascriptFileName);
		}else{
			array_push($this->_javascriptHandles, $javascriptFileName);
		}
	}
	
	public function addHeadLink($href, $rel, $type){
		$link = array('href' => $href, 'rel' => $rel, 'type' => $type);
		array_push($this->_linkHandles, $link);
	}
	
	public function setTitle($pageTitle){
		$this->_pageTitle = $pageTitle;
	}
	public function addMeta($httpEquiv, $content){
		$metaInfo[$httpEquiv] = $content;
		array_push($this->_metaInfo, $metaInfo);
	}

	public function marshalHeadInfo(){
		
		$headSection = "<head>\n";
		
		if(!empty($this->_pageTitle)){
			$headSection .= "<title>" . $this->_pageTitle . "</title>\n";
		}
		
		if(sizeof($this->_metaInfo) > 0){
			foreach($this->_metaInfo as $metaRow){
				foreach($metaRow as $k=>$v){
					$headSection .= "<meta http-equiv='". $k . "' content='" . $v . "'>\n";
				}
			}
		}
		
		if(sizeof($this->_linkHandles) > 0){
			foreach($this->_linkHandles as $handle){
				$href = $handle['href'];
				$type = $handle['type'];
				$rel = $handle['rel'];
				$headSection .= "<link href='" . $href . "' type='" . $type . "' rel='" . $rel . "'>\n";
			}
		}
		
		if(sizeof($this->_javascriptHandles) > 0){
			foreach($this->_javascriptHandles as $handle){
				$headSection .= "<script src='js/". $handle .".js' language='JavaScript' type='text/javascript'></script>\n";
			}
		}
		
		if(sizeof($this->_javascriptDirectHandles) > 0){
			foreach($this->_javascriptDirectHandles as $handle){
				$headSection .= "<script src= '$handle' language='JavaScript' type='text/javascript'></script>\n";
			}
		}
		
		$headSection .= "</head>\n";
		
		$this->_renderer->assign(self::HEAD_SECTION_ATTRIBUTE_NAME,  $headSection);
	}
}
?>
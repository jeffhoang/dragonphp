<?php

/*
 ======================================================================
 DragonPHP - ControllerIF
 
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

interface ControllerIF {
	
	public function isSecure();
	public function setLogger($logFileName);
	public function getView();
	public function setView($module, $controllerName, $renderer = false);	
	public function executeFilter(Request $request, Session $session, $renderer);
	public function execute(Request $request, Session $session, $view);
	public function sendRedirect($url);
	public function setModel($key, $value);
	public function getErrors();
	public function getModel();
	public function setModels($model);
	public function setFormToken();
	public function resetFormToken();
	public function isValidSubmission();
	public function setSecuredModule($module);
	public function setSecuredController($controller);
	public function getSecuredModule();
	public function getSecuredController();
	public function redirect($module, $controller, $protocolScheme = false);
	public function forward($module, $controller, $protocolScheme = false, $isRedirect = false);
	public function setAttribute($key, $value);
	public function addHeadLink($href, $rel, $type);
	public function setTitle($title);
	public function addJavascript($javascriptHandle, $directPath = false);	
	public function addMeta($httpEquiv, $content);
	public function addCss($href);
	public function isFormSubmission(Request $request);
	public function redirectToUrl($url);
	public function getRenderer();
	public function setAttributes($object);
	public function hasAccess($realmHandle);
	public function getRealms();
	public function getAttribute($key);
	public function isSslEnabled();
}
?>
<?php

/*
 ======================================================================
 DragonPHP - RendererIF
 
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

interface RendererIF {

	public function setRenderer($renderer);
	
	public function show($templateName);
	
	public function setAttribute($key, $value);

	public function configure();
	
	public function setTemplateDirectory($dir);
	
	public function setConfigDirectory($dir);
	
	public function getCurrentTemplateDir();
	
	public function addJavascript($javascriptFileName, $directPath = false);
	
	public function addMeta($key, $value);
	
	public function setTitle($pageTitle);
	
	public function addHeadLink($href, $rel, $type);
}
?>
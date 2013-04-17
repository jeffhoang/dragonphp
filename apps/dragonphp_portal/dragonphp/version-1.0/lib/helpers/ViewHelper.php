<?php

/*
 ======================================================================
 DragonPHP - ViewHelper
 
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
 
 @package    helpers
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

class ViewHelper {
	
	public static function getRenderer($moduleName, $name = false) {

		$renderer = null;
		
		if(!$name) {
			$name = DEFAULT_RENDERER;
		}
		
		switch($name) {
			
			case 'SMARTY':

				try {
					
					//echo $moduleName;
					
					$renderer = self::_getSmarty($moduleName);	
	
				} catch (Exception $ex) {
					print_r($ex);
				}
					
				break;
			
			default:

				break;
		}
		
		return $renderer;
	}
	
	private static function _getSmarty($moduleName) {

		try {
			
			// include the smarty renderer class
			require_once(SMARTY_RENDERER);
		
			$renderer = new SmartyRenderer($moduleName);
			
		} catch (Exception $ex) {
			
		}
		
		return $renderer;
	}
	
	
}
?>
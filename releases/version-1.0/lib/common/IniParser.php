<?php

/*
 ======================================================================
 DragonPHP - IniParser
 
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
 
 @package    common
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

class IniParser {
	
	public static function parse($file, $doParseSections = false) {

		try {
		
			if(is_file($file)) {
			
				$data = parse_ini_file($file, $doParseSections);	
				
			}	
			
		} catch (Exception $ex) {
			throw new Exception('error parsing ini file.');
		}
		
		return $data;
	}
	
}
?>
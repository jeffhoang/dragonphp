<?php

/*
 ======================================================================
 DragonPHP - StringUtilHelper
 
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

class StringUtilHelper{
	
	public static function arrayToString($mixed = null) {
		ob_start();
		var_export($mixed);
		$content = ob_get_contents();
		ob_end_clean();
  		return $content;
	}
	
	public static function underscore($word) { 
       $word = preg_replace('/([[:alpha:]][a-z]+)/', "$1_", $word);
       $word = strtolower(preg_replace('/([[:digit:]]+)/', "$1_", $word));
       $length = strlen($word);
       $word = substr($word, 0, $length - 1);
       return $word;
	}
}
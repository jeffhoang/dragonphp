<?php

/*
 ======================================================================
 DragonPHP - FlowHelper
 
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
class FlowHelper {
	
	public static function getFlowData($moduleName, $statusCode, $flowFileName = false) {

		try {
			
			if($flowFileName) {
			
				$file = APPLICATION_MODULE_DIR . $moduleName . '/flows/'. $flowFileName . '.xml';
				
				if(!is_file($file)) {
				
					$file = APPLICATION_MODULE_DIR . $moduleName . '/flows/default.xml';
					
				} 	
			} else {
			
				$file = APPLICATION_MODULE_DIR . $moduleName . '/flows/default.xml';
			
			}
			
			echo $file;
			
			if(is_file($file)) {
				$data = simplexml_load_file($file);
				
				// get the node that with a specific statusId
				$nodeData = $data->xpath("//flow[statusId = '" . $statusCode . "']");
				
				// return only one object
				return $nodeData[0];
			} else {
				return null;
			}	
						
		} catch (Exception $ex) {
				
		}
		
	}
	
}
?>
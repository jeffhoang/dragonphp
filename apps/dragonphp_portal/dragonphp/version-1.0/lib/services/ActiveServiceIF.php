<?php

/*
 ======================================================================
 DragonPHP - ActiveServiceIF
 
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
 
 @package    services
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 
 */

interface ActiveServiceIF{
	
	public function create($updateOnDuplicate = false);
	public function read();
	public function update();		
	public function delete();
	public function getData();
	
	public function getCreateString($updateOnDuplicate = false);
	public function getDeleteString();
	public function getUpdateString();
	public function addToBatch($updateOnDuplicate = false);
	
	public function commitBatch($cleanup = false);
	
	public function initLogger($currentClass = false);
	public function defineCriteria($service, $criteria, $junctions);

}
?>

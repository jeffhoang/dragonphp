<?php


/*
 ======================================================================
 DragonPHP - ActiveRecordIF
 
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
 
 @package    plugins/database
 @author     Jeff Hoang <jdragon@gmail.com>
 @copyright  2006 Jeff Hoang
 */

interface ActiveRecordIF {
	
	public function init();
	
	public function getConnection();
	
	public function beginTransaction();
	
	public function commit();
	
	public function close();
	
	public function endTransaction();
	
	public function rollBack();		
	
	public function update($criteria = false, $returnQueryString = false);

	public function delete($criteria = false, $returnQueryString = false);

	public function create($criteria = false, $updateOnDuplicate = false, $returnQueryString = false, $addToBatch = false);
	
	public function read($criteria = false);
	
	public function getCreateString($criteria = false, $updateOnDuplicate = false);

	public function getDeleteString($criteria = false);
	
	public function getUpdateString($criteria = false);
	
	public function addToBatch($criteria = false, $updateOnDuplicate = false);

	public function commitBatch($cleanup = false, $continueOnError = false);
}
?>
<?php

/**
 * DatabaseConstants
 *
 * This class contains database constants 
 *
 * @link 
 * @copyright 2010 
 * @author Jeff Hoang
 * @package base/tools
 * @version
 */

class DatabaseConstants {

	// Datasource names
	const DRAGONPHP_DS = 'dragonphp';
	const DRAGONPHP_READONLY = 'dragonphp_readonly';

	// Dragonphp tables
	const ORGANIZATIONS_TBL = 'Organization';
	const ROLES_TBL = 'Role';
	const USERS_TBL = 'User';
	const USER_ROLE_TBL = 'UserRole';
	const ROLE_PERMISSIONS_TBL = 'RolePermission';
}
?>
<?php

// dynamically define paths starting from the path of this file
$currentApplicationPath = dirname(__FILE__);
$applicationPath = preg_replace('/conf/', '', $currentApplicationPath);
$applicationName = preg_replace('/\//', '', strrchr(trim($applicationPath, '/'), '/'));
$basePath = preg_replace('/' . $applicationName . '\//', '', $applicationPath);

define('BASE_DIR', $basePath);
define('APPLICATION_NAME', $applicationName);

// change this to any path, be sure to chmod 777 it
define('CACHE_DIR', '/var/tmp/');

define('FRAMEWORK_VERSION', 'version-1.0');

/* 

You may manually override SHARED_DIR and FRAMEWORK_DIR if you don't want to include the framework
within your application path.

*/

if(!defined('SHARED_DIR')){
	define('SHARED_DIR', $applicationPath);	
	define('FRAMEWORK_DIR', SHARED_DIR . 'dragonphp/' . FRAMEWORK_VERSION . '/');	
}

define('PLATFORM_ENCRYPTION_KEY','dyn@m1c n1nj@ c0d1ng styl3');
define('PLATFORM_ENCRYPTION_IV', '0dragonphp0');

define('PLATFORM_BASE_DIR', BASE_DIR . 'base/');
define('PLATFORM_BASE_TOOLS', PLATFORM_BASE_DIR . 'tools/');

define('PLATFORM_DRAGONPHP_LIBRARY', FRAMEWORK_DIR . 'lib/');
define('PLATFORM_DRAGONPHP_SERVICES', PLATFORM_DRAGONPHP_LIBRARY . 'services/');
define('PLATFORM_DRAGONPHP_HELPERS', PLATFORM_DRAGONPHP_LIBRARY . 'helpers/');
define('PLATFORM_DRAGONPHP_TOOLS', PLATFORM_DRAGONPHP_LIBRARY . 'tools/');

/* Set Logger Level
0 = DEBUG
1 = INFO
2 = WARNING
3 = ERROR
*/
define('LOGGER_LEVEL', 0);

?>
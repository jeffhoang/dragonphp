<?php

include_once('env_main.php');

define('BASE_APPLICATION_DIR', BASE_DIR . APPLICATION_NAME . '/');
define('LOG_DIR', CACHE_DIR . APPLICATION_NAME . '/logs/');

// Define logger date format
define('LOGGER_DATE_FORMAT', 'm/d/Y H:i:s');

// IMPORTANT: Define a default module and controller (page and view)
define('DEFAULT_MODULE', 'Security');
define('DEFAULT_CONTROLLER', 'Login');
define('DEFAULT_VIEW_CONTROLLER', 'Login');

define('DEFAULT_TILE_SECTION', 'default');
define('DEFAULT_HEADER', 'header.tpl');
define('DEFAULT_FOOTER', 'footer.tpl');

// Default view renderer
define('DEFAULT_VIEW_RENDERER', 'SmartyRenderer');

// include the framework's definitions
include_once('framework.php');

// include the PLATFORM base definitions
//include_once(PLATFORM_BASE_DIR . 'conf/main.php');

define('ACTIVE_RECORD', ACTIVE_RECORD_MYSQL_PDO);

?>
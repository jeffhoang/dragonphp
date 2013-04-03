<?php

define('I18N_DIR', 'i18n');
define('TEMPLATE_DIR', 'templates');
define('VIEW_DIR', 'views');

define('MODULE_PARAM', 'module');
define('CONTROLLER_PARAM', 'controller');
define('COMMAND_PARAM', 'command');
define('VIEW_PARAM', 'view');
define('STATUS_PARAM', 'status');
define('RESOURCE_PARAM', 'resource');
define('LOCALE_PARAM', 'current_locale');
define('RESOURCE_FILE_NAME_PARAM', 'resource_file_name');
define('DEFAULT_VALIDATE_FUNCTION', 'validate');
define('DEFAULT_COMMAND', 'execute');
define('SUBMIT_PARAM', 'submit');

define('MODULE_DIR', BASE_APPLICATION_DIR . 'modules/');

define('CONTROLLER_SUFFIX', 'Controller');
define('VALIDATOR_SUFFIX', 'Validator');
define('VALIDATE_COMMAND', 'validate');
define('GET_ERRORS_COMMAND', 'getErrors');
define('DEFAULT_CONTROLLER_COMMAND', 'execute');
define('COMMAND_SUFFIX', 'Command');
define('FILTER_COMMAND', 'executeFilter');

define('DEFAULT_VIEW_RENDERER_COMMAND', 'execute');
define('VIEW_SUFFIX', 'View');
define('VIEW_RENDERER_SUFFIX', 'Renderer');

define('SUCCESS_STATUS', 'success');
define('FAILED_STATUS', 'failed');

define('DEFAULT_LOCALE', 'en_US');

// Web application definitions
define('APPLICATION_WEB_DIR', BASE_APPLICATION_DIR . 'web/');
define('APPLICATION_CONFIG_DIR', BASE_APPLICATION_DIR . 'conf/');
define('APPLICATION_MODULE_DIR',  BASE_APPLICATION_DIR . 'modules/');
define('APPLICATION_LIB_DIR', BASE_APPLICATION_DIR . 'lib/');

// Smarty related definitions
define('DEFAULT_RENDERER', 'SMARTY');

if(!defined('APPLICATION_CACHE_DIR')){
	define('APPLICATION_CACHE_DIR', CACHE_DIR . APPLICATION_NAME . '/');
}

define('APPLICATION_SMARTY_CACHE_DIR', APPLICATION_CACHE_DIR . 'smarty/');
define('TEMPLATES_COMPILED_DIR', APPLICATION_SMARTY_CACHE_DIR . 'templates_c/');
define('TEMPLATE_CACHE_DIR', APPLICATION_SMARTY_CACHE_DIR . 'cache/');
define('DEFAULT_TEMPLATE_EXTENSION', '.tpl');

// Rss cache dir
define('RSS_CACHE_DIR', APPLICATION_CACHE_DIR . 'rss_cache');

// Custom Template definitions
define('GLOBAL_TEMPLATE_DIR', BASE_APPLICATION_DIR . 'global_templates/');
define('APPLICATION_VIEW_LIB_DIR', APPLICATION_LIB_DIR . 'view/');
define('APPLICATION_WEB_VIEW', APPLICATION_VIEW_LIB_DIR . 'ApplicationWebView.php');

// Application specific constants
define('SUBMIT_COMMAND', 'submit');

// Create cacheable data directories
if(!is_dir(CACHE_DIR)){
	mkdir(CACHE_DIR, 0777);
}

if(!is_dir(APPLICATION_CACHE_DIR)){
	mkdir(APPLICATION_CACHE_DIR, 0777);
}

if(!is_dir(RSS_CACHE_DIR)){
	mkdir(RSS_CACHE_DIR, 0777);
}

if(!is_dir(APPLICATION_CACHE_DIR)){
	mkdir(APPLICATION_CACHE_DIR, 0777);
}
?>
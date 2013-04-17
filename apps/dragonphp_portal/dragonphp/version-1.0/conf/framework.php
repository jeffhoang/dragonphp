<?php

define('REQ_POST', 'POST');
define('REQ_GET', 'GET');

// Key definitions
define('FRAMEWORK_CONFIG_DIR', FRAMEWORK_DIR . 'conf/');
define('FRAMEWORK_LIB_DIR', FRAMEWORK_DIR . 'lib/');
define('FRAMEWORK_MVC_DIR', FRAMEWORK_LIB_DIR . 'mvc/');
define('FRAMEWORK_CONTROLLER_DIR' , FRAMEWORK_MVC_DIR . 'controller/');
define('FRAMEWORK_MODEL_DIR', FRAMEWORK_MVC_DIR . 'model/');
define('FRAMEWORK_VIEW_DIR', FRAMEWORK_MVC_DIR . 'view/');
define('FRAMEWORK_FLOW_DIR', FRAMEWORK_MVC_DIR . 'flow/');
define('FRAMEWORK_COMMON_DIR', FRAMEWORK_DIR . 'lib/common/');
define('FRAMEWORK_PLUGINS_DIR', FRAMEWORK_LIB_DIR . 'plugins/');
define('FRAMEWORK_HELPERS_DIR', FRAMEWORK_LIB_DIR . 'helpers/');
define('FRAMEWORK_EXTERNAL_DIR', FRAMEWORK_LIB_DIR . 'external/');
define('FRAMEWORK_SERVICES_DIR', FRAMEWORK_LIB_DIR . 'services/');
define('FRAMEWORK_RULES_DIR', FRAMEWORK_LIB_DIR . 'rules/');
define('FRAMEWORK_RENDERERS_DIR', FRAMEWORK_PLUGINS_DIR . 'renderers/');
define('FRAMEWORK_SMARTY_DIR', FRAMEWORK_RENDERERS_DIR . 'smarty/');
define('FRAMEWORK_CONSTANTS_DIR', FRAMEWORK_LIB_DIR . 'constants/');

// external directories
define('SMARTY_DIR', FRAMEWORK_EXTERNAL_DIR . 'smarty/');
define('SMARTY_CLASS', SMARTY_DIR . 'Smarty.class.php');

// locale
define('MAIN_LOCALE', 'en_UR');

define('DRAGON_REQUEST_DISPATCHER', FRAMEWORK_CONTROLLER_DIR . 'Dispatcher.php');
define('DRAGON_CONTROLLER', FRAMEWORK_CONTROLLER_DIR . 'Controller.php');
define('DRAGON_REQUEST', FRAMEWORK_CONTROLLER_DIR . 'Request.php');
define('DRAGON_VIEW', FRAMEWORK_VIEW_DIR . 'View.php');
define('DRAGON_WEB_VIEW', FRAMEWORK_VIEW_DIR . 'WebView.php');
define('DRAGON_FLOW_CONTROLLER', FRAMEWORK_FLOW_DIR . 'FlowController.php');

// Form Related Definitions
define('FORM_VALIDATION_HELPER', 'FormValidationHelper.php');
define('FORM_NAME', 'form_name');
define('HIDDEN_FIELDS', 'hidden_fields');
define('IS_REQUIRED', 'isRequired');
define('VALIDATION_RULES', 'validation_rules');
define('IS_TRUE', 'true');
define('IS_FALSE', 'false');
define('DEFAULT_FRAMEWORK_VALIDATION_RULES', 'FormValidationRules');
define('BASE_FORM_VALIDATION_RULES', FRAMEWORK_RULES_DIR . DEFAULT_FRAMEWORK_VALIDATION_RULES . '.php');

// Template Renderer
define('VIEW_HELPER', FRAMEWORK_HELPERS_DIR . 'ViewHelper.php');
define('DEFAULT_LAYOUT_FILE_NAME', 'layout_definitions');
define('LOAD_RESOURCE_BUNDLE_COMMAND', 'loadResourceBundle');
define('PROCESS_LAYOUT_COMMAND', 'processLayout');
define('GENERATE_HIDDEN_FIELDS_COMMAND', 'generateHiddenFormFields');
define('TEMPLATE_SET_ID', 'templateSetId');
define('ERROR_TEMPLATE_ID', 'errorTemplateSetId');
define('RESOURCE_BUNDLE_HELPER', FRAMEWORK_HELPERS_DIR . 'ResourceBundleHelper.php');
define('IS_TEMPLATE_GLOBAL', 'isTemplateGlobal');

// Plugins
define('PLUGINS_DIR', FRAMEWORK_LIB_DIR . 'plugins/');
define('PLUGINS_RENDERERS_DIR', PLUGINS_DIR . 'renderers/');
define('SMARTY_RENDERER', PLUGINS_RENDERERS_DIR . 'SmartyRenderer.php');

// PEAR Stuff
define('PEAR_BENCHMARK_DIR', FRAMEWORK_EXTERNAL_DIR . 'Benchmark-1.2.4/');
define('PEAR_BENCHMARK_TIMER', PEAR_BENCHMARK_DIR . 'Timer.php');
 
// Framework flow status definitions
define('DEFAULT_STATUS', 'default');

// Validator definitions
define('VALIDATOR_INTERFACE', FRAMEWORK_CONTROLLER_DIR . 'RequestValidatorIF.php');
define('VALIDATOR', FRAMEWORK_CONTROLLER_DIR . 'RequestValidator.php');
define('FRAMEWORK_ERROR_CLASS', FRAMEWORK_CONTROLLER_DIR . 'Error.php');

define('SECURED_CONTROLLER', FRAMEWORK_CONTROLLER_DIR . 'SecuredController.php');

define('ACTIVE_RECORD_MYSQL', FRAMEWORK_PLUGINS_DIR . 'database/mysql/ActiveRecord.php');
define('ACTIVE_RECORD_MYSQL_PDO', FRAMEWORK_PLUGINS_DIR . 'database/mysql_pdo/ActiveRecord.php');

// database types
define('STRING', 'string');
define('DOUBLE','double');
define('LONG','long');
define('FLOAT', 'float');
define('DECIMANL', 'decimal');
define('NATIVE_FUNCTION','function');
define('INTEGER','integer');
define('NUMBER', 'number');

// form token id
define('UNIQUE_FORM_TOKEN', 'double_submission_prevention_token');
?>
<?php 

require_once('FlowControllerIF.php');
require_once(FRAMEWORK_HELPERS_DIR . 'FlowHelper.php');

class FlowController implements FlowControllerIF {
	
	private static $_registry = array();
	
	public static function getNext($moduleName, $statusCode, $flowFileName = false) {
		
		if(!self::$_registry{$moduleName}) {
			
			$data = FlowHelper::getFlowData($moduleName, $statusCode);
			
			echo $data;
			return $data;
			
		} elseif (self::$_registry{$moduleName}) {
			
			$data = self::$_registry{$moduleName};
			return self::_buildArray($data{$statusCode});
		
		}
		
		return null;		
	}
	
	public static function getNextFromIni($moduleName, $statusCode, $flowFileName = false) {
		
		if(!self::$_registry{$moduleName}) {
		
			if($flowFileName) {
			
				$file = APPLICATION_MODULE_DIR . $moduleName . '/flows/'. $flowFileName . '.flw';
				
				if(!is_file($file)) {
				
					$file = APPLICATION_MODULE_DIR . $moduleName . '/flows/default.flw';
					
				} 	
			} else {
			
				$file = APPLICATION_MODULE_DIR . $moduleName . '/flows/default.flw';
					
			}
			
			$data  = IniParser::parse($file);
			
			$_registry{$moduleName} = $data;	
			
			return self::_buildArray($data{$statusCode});
			
		} elseif (self::$_registry{$moduleName}) {
			
			$data = self::$_registry{$moduleName};
			
			return self::_buildArray($data{$statusCode});
		
		}
		
		return null;		
	}
	
	private static function _buildArray($string) {
		
		parse_str($string, $result);
		
		return $result;
		
	}
}

?>
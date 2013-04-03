<?php

interface FlowControllerIF {

	public static function getNext($moduleName, $statusCode, $flowFileName = false);
	
}
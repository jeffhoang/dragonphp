<?php

/*

Use this script to create a new dragonphp application.
 
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package
 * @version
 
 */

$options = 'n:t:';
$inputs = getopt($options);

if (!checkInputs($inputs)) {
        usage();
        exit;
}


function verify($inputs){
                
        if(empty($inputs)){
                return false;
        }
        
        $requiredInputs = array('n', 't');
        
        foreach($requiredInputs as $requiredInput){
                
                if(!isset($inputs[$requiredInput])){
                        return false;
                }
        }
        
        return true;
}


function usage(){
	
echo <<<USAGE

Usage:

php create_app.php -n *Name_of_Application* -t *Base_Path*

Example:

php create_app.php -n portal -t /Users/jdragon/workspace/
 
USAGE;
	
}

?>
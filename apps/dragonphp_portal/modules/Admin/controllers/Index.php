<?php

/**
 * Index
 *
 * This class is the default index controller.
 *
 * @link http://www.dragonphp.com
 * @copyright 2010 Dragonphp
 * @author Jeff Hoang
 * @package
 * @version
 */

require_once(APPLICATION_LIB_DIR . 'mvc/AbstractSecuredController.php');

class Index extends AbstractSecuredController {

	public function execute(Request $request, Session $session, $view){
		
		return new Template('control_panel');
	}
}
?>
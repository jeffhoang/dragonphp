<?php

require_once(DRAGON_CONTROLLER);

class Index extends Controller {

	public function execute(Request $request, Session $session, $view){
		
		return new Template('default');
	}
}
?>
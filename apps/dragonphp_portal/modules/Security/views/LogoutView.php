<?php 

require_once(APPLICATION_WEB_VIEW);

class LogoutView extends ApplicationWebView {
	
	public function execute(Request $request, Session $session, $renderer, $model = false){
	
		return $renderer;
	}
}
?>
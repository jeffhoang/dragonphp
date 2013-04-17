<?php 

require_once(APPLICATION_WEB_VIEW);

class LoginView extends ApplicationWebView {
	
	public function execute(Request $request, Session $session, $renderer, $model = false){
		
		return $renderer;
	}
	
	public function onError(Request $request, Session $session, $renderer, $model = false) {
		
		return $renderer;
	}
	
	public function onSuccess(Request $request, Session $session, $renderer, $model = false) {
		
		$requestedUri = $session->get('REQUEST_URI');
	
		if(!empty($requestedUri) && $requestedUri != '/?'){
			$this->sendRedirect($requestedUri);
		}else{
			// Change this to the module and controller you want to go to
			//$this->forwardToAlias('admin_default');
			$this->sendRedirect('/?control_panel');
		}
	}
}
?>
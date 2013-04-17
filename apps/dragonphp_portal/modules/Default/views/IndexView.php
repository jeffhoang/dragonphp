<?php 

require_once(APPLICATION_WEB_VIEW);

class IndexView extends ApplicationWebView {

	public function execute(Request $request, Session $session, $renderer, $model = false){
	
		$renderer->setAttribute(TEMPLATE_SET_ID, self::DEFAULT_SECTION);
		
		return $renderer;
	}
}

?>
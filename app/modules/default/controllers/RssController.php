<?php
class RssController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

	public function indexAction(){
		
	}
	
	public function laboralAction(){
		$this->_helper->layout()->disableLayout();
		$url = "http://laboral.undac.edu.pe/rslaboral";
		$rss = new Zend_Feed_Rss($url);
		if ($rss) $this->view->rss = $rss;
		
	}
}
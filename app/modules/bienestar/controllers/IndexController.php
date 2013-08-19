<?php

class Pedagogia_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->modulo=="bienestar"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    }
    public function indexAction()
    {
    
    	

    }
}

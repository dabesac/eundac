<?php

class Docente_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion1  = Zend_Auth::getInstance();
    	if(!$sesion1->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$sesion = $sesion1->getStorage()->read();
    	if (!$sesion->modulo=="docente"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $sesion;
    	
    }
    public function indexAction()
    {
    
    }
}

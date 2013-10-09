<?php

class Graduated_FilterController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (!$login->modulo=="graduated"){
    	// 	$this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }

    public function indexAction(){
        try {
            
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
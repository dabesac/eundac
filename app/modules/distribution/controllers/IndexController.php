<?php

class Distribution_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (($login->infouser['teacher']['is_director']<>"S")){
    		$this->_helper->redirector('index','error','default');
    	}
    	$this->sesion = $login;
    }
    public function indexAction()
    {
    	// print_r('Distribución');
    }
}

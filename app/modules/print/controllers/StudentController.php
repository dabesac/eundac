<?php

class Print_StudentController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction(){
    	print_r('Impresiones');
    }

    public function preregisterAction() {
    	print_r('Impresion preregister');
    }
}
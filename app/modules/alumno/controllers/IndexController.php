<?php

class Alumno_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="alumno"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    }
    public function indexAction()
    {
    	try {
            $pid=$this->sesion->pid;
            $this->view->pid=$pid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}

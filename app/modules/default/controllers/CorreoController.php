<?php

class CorreoController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

	public function indexAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$uid=base64_decode($this->_getParam('uid'));
      		$escid=base64_decode($this->_getParam('escid'));
      		$where['eid']=base64_decode($this->_getParam('eid'));
      		$where['oid']=base64_decode($this->_getParam('oid'));
      		$subid=base64_decode($this->_getParam('subid'));
      		$where['pid']=base64_decode($this->_getParam('pid'));
                  
      		
      		$form = new Default_Form_Correo();
                  $this->view->form=$form;

		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	
}
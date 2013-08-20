<?php
 class Rcentral_RecordnotasController extends Zend_Controller_Action{

 	public function init(){
 		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="docente"){
 			$this->_helper->redirector('index','index','default');
 		}
 		$this->sesion = $login;

 	}

 	public function indexAction(){
 		try {
 			$this->sesion->eid;
 			$this->sesion->oid;
 			$this->sesion->rid;
 			
 		} catch (Exception $e) {
 			print ('Error: Mostrar datos'. $e->getMessage());
 			
 		}

 	}
 }
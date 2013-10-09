<?php
 class Report_RecordnotasController extends Zend_Controller_Action{

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
 			$fm=new Report_Form_Buscar();
			$this->view->fm=$fm;
 			
 		} catch (Exception $e) {
 			print ('Error: get datos'. $e->getMessage());
 			
 		}
 	}


 }
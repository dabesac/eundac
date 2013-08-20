<?php
 class Rcentral_RecordnotasController extends Zend_Controller_Action{

 	public function init(){
 		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="rcentral"){
 			$this->_helper->redirector('index','index','default');
 		}
 		$this->sesion = $login;

 	}

 	public function indexAction(){
 		try {
 			$this->sesion->eid;
 			$this->sesion->oid;
 			$this->sesion->rid;
 			$fm=new Rcentral_Form_Buscar();
			$this->view->fm=$fm;
 			
 		} catch (Exception $e) {
 			print ('Error: Mostrar datos'. $e->getMessage());
 			
 		}

 	}

 	public function getstudentnameAction(){
 			$this->_helper->getHelper('layout')->disableLayout();

 	}

 	public function getstudentuidAction(){
 		try{

       		$this->_helper->getHelper('layout')->disableLayout();
       		$where['uid'] = $this->_getParam('uid');
        	// $where['uid'] = '1234567890';
        	$where['eid'] = $this->sesion->eid;
        	$where['oid'] = $this->sesion->oid;
        	$bdu = new Api_Model_DbTable_Users();
        	$data = $bdu->_getUserXUid($where);
        	$this->view->data=$data;

    }catch(Exception $ex ){
        print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
    } 


 	}
 }
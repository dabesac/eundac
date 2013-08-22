<?php
class Register_ChangeratesController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="bienestar"){
 			$this->_helper->redirector('index','index','default');
 		}
		$this->sesion = $login;

	}

	public function indexAction(){
		try {
 			$this->sesion->eid;
 			$this->sesion->oid;
 			$this->sesion->rid;
 			$fm=new Register_Form_Buscar();
			$this->view->fm=$fm;
 			
 		} catch (Exception $e) {
 			print ('Error: Mostrar datos'. $e->getMessage());
 			
 		}

	}

	public function getuserAction(){
		try {
			$this->_helper->layout()->disableLayout();
          	$uid= $this->_getParam('uid');
          	// print ($uid);
       		$where['uid'] = $uid;
        	$where['eid'] = $this->sesion->eid;
        	$where['oid'] = $this->sesion->oid;
        	$bdu = new Api_Model_DbTable_Users();
        	$data = $bdu->_getUserXUid($where);
        	// print_r($data);
        	$this->view->data=$data;
			
		} catch (Exception $e) {
			print ('Error: get data user'.$e->getMessage());
		}

	}

}
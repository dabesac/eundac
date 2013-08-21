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

 	public function getstudentuidAction(){
 		try{
       		$this->_helper->getHelper('layout')->disableLayout();
          $facid=$this->sesion->faculty->facid;
          $this->view->facid=$facid;
          // print ($facid);
          $uid= $this->_getParam('uid');
       		if($uid){
       			$where['uid'] = $uid;
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$bdu = new Api_Model_DbTable_Users();
        		$data = $bdu->_getUserXUid($where);
        		$this->view->data=$data;
       		}
       		$nom = $this->_getParam('last_name0');
       		if($nom){
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$where['rid'] = 'AL';
        		$where['nom'] = trim(strtoupper($nom));
        		$where['nom'] = mb_strtoupper($where['nom'],'UTF-8');
        		$bdu = new Api_Model_DbTable_Users();
        		$da = $bdu->_getUsuarioXNombre($where);
            $where['rid'] = 'EG';            
            $dat = $bdu->_getUsuarioXNombre($where);
            $data = array_merge($da,$dat);
            $this->view->data=$data;
            // print_r($data);  
        	}
     }catch(Exception $ex ){
        print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
    	} 
 	}

 	public function printAction(){
 		try {
 			$this->_helper->layout()->disableLayout();
 			$uid=base64_decode($this->_getParam('uid'));
      $escid=base64_decode($this->_getParam('escid'));
      // $escid = $this->_getParam('escid');
 			// $subid = $this->_getParam('subid');
 			// $this->view->escid=$escid;
 			// $this->view->eid->$eid;
 			// $this->view->oid->$oid;
 			// $this->view->pid->$pid;
 			// $escid='4SI';
 			// $uid='0514403019';
      // echo $uid;
      // echo $escid;
 			$record = new Api_Model_DbTable_Registrationxcourse();
 			$data = $record->_getRecordNotasAlumno($escid,$uid);
 			// print_r($data);
      // $this->view->data=$data;
 			// echo $pid;

 			
 		} catch (Exception $e) {
 			print ("Error: Imprimir Notas: ".$e->getMessage());
 		}

 	}
 }
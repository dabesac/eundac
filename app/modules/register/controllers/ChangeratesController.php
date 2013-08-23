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
 			$perid=$this->sesion->period->perid;
 			$this->view->perid=$perid;
 			$fm=new Register_Form_Buscar();
			$this->view->fm=$fm;
 			
 		} catch (Exception $e) {
 			print ('Error: Mostrar datos'. $e->getMessage());
 			
 		}

	}

	public function getuserAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$perid=$this->sesion->period->perid;
 			$this->view->perid=$perid;
          	$uid= $this->_getParam('uid');
          	$where['uid'] = $uid;
        	$where['eid'] = $this->sesion->eid;
        	$where['oid'] = $this->sesion->oid;
        	$bdu = new Api_Model_DbTable_Users();
        	$data = $bdu->_getUserXUid($where);

        	$info['eid'] = $data[0]['eid'];
            $info['oid'] = $data[0]['oid'];
            $info['escid'] = $data[0]['escid'];
            $info['subid'] = $data[0]['subid'];
        	$facaulm = new Api_Model_DbTable_Speciality();
            $rfacaulm = $facaulm->_getOne($info);

            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['perid']=$perid;
            $where['uid']=$data[0]['uid'];
            // print_r($where); exit();
            $list= new Api_Model_DbTable_Payments();
            $dlist=$list->_getFilter($where);
            print_r($dlist);
        	// print_r($rfacaulm);

        	// print_r($data);
        	$this->view->rfacaulm=$rfacaulm;
        	$this->view->data=$data;
        	$this->view->dlist=$dlist;
			
		} catch (Exception $e) {
			print ('Error: get data user'.$e->getMessage());
		}

	}

}
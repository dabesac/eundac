<?php 
class Admin_UserController extends Zend_Controller_Action{

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
 			$fm=new Admin_Form_Buscar();
			$this->view->fm=$fm;
 		} catch (Exception $e) {
 			print "Error: Person".$e->getMessage();
 		}
	}

	public function getuserAction(){
 		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$pid=$this->_getParam('pid');
			if ($pid) {
				$where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
				$dbuser=new Api_Model_DbTable_Users();
				$datauser=$dbuser->_getUserXPid($where);
				$this->view->datauser=$datauser;
			}
			$name = $this->_getParam('name');
       		if($name){
        		$eid = $this->sesion->eid;
        		$oid = $this->sesion->oid;
        		$name = trim(strtoupper($name));
        		$name = mb_strtoupper($name,'UTF-8');
        		$dbuser=new Api_Model_DbTable_Users();
        		$datauser=$dbuser->_getUserXnameXsinRolAll($name,$eid,$oid);
				// print_r($datauser);exit();        		           
            	$this->view->datauser=$datauser; 
        	}
        	$uid = $this->_getParam('uid');
        	if ($uid) {
        		$where['uid'] = $uid;
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$bduser = new Api_Model_DbTable_Users();
        		$datauser = $bduser->_getUserXUid($where);
			// print_r($datauser);exit();
        		$this->view->datauser=$datauser;			
        	}			
 		} catch (Exception $e) {
 			print "Error: get Person".$e->getMessage();
 		}
 	}

 	public function newAction(){
 		try {
 			$fm= new Admin_Form_Buscar();
 			$this->view->fm=$fm;
 			
 		} catch (Exception $e) {
 			print "Error: User new".$e->getMessage();
 		}
 	}

 	public function getusernewAction(){
 		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$pid=$this->_getParam('pid');
			if ($pid) {
				$where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
				$dbuser=new Api_Model_DbTable_Users();
				$datauser=$dbuser->_getUserXPid($where);
				$c=0;
				foreach ($datauser as $inforol) {
					$info[$c]=$inforol['rid'];
					$c++;
				}
				$this->view->inforol=$info;
				$this->view->datauser=$datauser;
			}			
 		} catch (Exception $e) {
 			print "Error: get Person".$e->getMessage();
 		}
 	}
 	 public function newrolAction(){
 	 		$this->_helper->layout()->disableLayout();
 	 		$fm= new Admin_Form_Usernew();
 	 		$this->view->fm=$fm;
 	 		$info=$this->_getParam('$this->inforol');
 	 		print_r($info);

 	 }
}
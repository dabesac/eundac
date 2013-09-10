<?php
class LogaccessController extends Zend_Controller_Action{

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
			$eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid= $this->sesion->uid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['pid']=$pid;
            $where['uid']=$uid;
            $order=array("datestart desc");
            $log = new Api_Model_DbTable_Logs();
            $data = $log->_getAccess($where,$order,50);
            $this->view->datalog=$data;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function detalleaccessAction(){
		try {
			$eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['pid']=$pid;
            $where['uid']=$uid;
            $order=array("datestart desc");
            $log = new Api_Model_DbTable_Logs();
            $data = $log->_getAccess($where,$order,50);
            $this->view->datalog=$data;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
}
<?php
	class Admin_OpeninfoacademicController extends Zend_Controller_Action{
		public function init()
		{
			$sesion  = Zend_Auth::getInstance();
 			if(!$sesion->hasIdentity() ){
 				$this->_helper->redirector('index',"index",'default');
 			}
 			$login = $sesion->getStorage()->read();
 			$this->sesion = $login;

		}
		public function indexAction()
		{
			$this->sesion;
		$eid = $this->sesion->eid;
 		$oid = $this->sesion->oid;
 		$rid = $this->sesion->rid;
 		$is_director = $this->sesion->infouser['teacher']['is_director'];

		$esc = new Api_Model_DbTable_Speciality();
 		if ($rid == 'RF' || $rid == 'DF') {
 			$facid = $this->sesion->faculty->facid;
 			$where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid,'state' => 'A');
 		}else{
 			if ($rid == 'DC' && $is_director=='S') {
 				$this->view->director = $is_director;
 				$this->view->escid = $this->sesion->escid;
 				$where = array('eid' => $eid, 'oid' => $oid, 'escid' => $this->sesion->escid,'state' => 'A');
 			}else{
		 		$where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
 			}
 		}
		$dataesc = $esc->_getFilter($where,$attrib=null,$orders=array('facid','escid'));
	 	$this->view->speciality = $dataesc;

	 	$this->view->perid = $this->sesion->period->perid;
 		$where = array('eid' => $eid, 'oid' => $oid);
 		$per = new Api_Model_DbTable_Periods();
 		$dataper = $per->_getFilter($where,$attrib=null,$orders=array('perid'));
 		$this->view->periods = $dataper;

		}
		public function listteacherAction()
		{
			$this->_helper->layout()->disableLayout();
 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;
 			$perid = $this->_getParam('perid');
 			$escid = $this->_getParam('escid');
 			$subid = $this->_getParam('subid');
 			$this->view->perid=$perid;
 			$this->view->escid=$escid;
 			$query=new Api_Model_DbTable_Infoacademic();
 			$row=$query->listteacher($escid,$perid);
 			// print_r($row);
 			$this->view->row=$row;

		}

		public function updatestateAction()
		{
			$this->_helper->layout()->disableLayout();
			$perid=base64_decode($this->_getParam('perid'));
			$escid=base64_decode($this->_getParam('escid'));
			$state=base64_decode($this->_getParam('state'));
			$pid=base64_decode($this->_getParam('pid'));
			$query=new Api_Model_DbTable_Infoacademic();
			$query->_update($escid,$perid,$state,$pid);
			
		}
	}
?>
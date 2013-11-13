<?php
class Admin_InfoacademiController extends Zend_Controller_Action{
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
			$fm= new Admin_Form_Openrecords();
			$this->view->fm=$fm;
		} catch (Exception $e) {
			print "Error: Infoacademi".$e->getMessage();
		}
	}

	public function lperiodAction(){
		$this->_helper->layout()->disableLayout();
		$eid=$this->sesion->eid;
		$oid=$this->sesion->oid;
		$anio=$this->_getParam('anio');
		$a= substr($anio, 2, 4);
		$data=array('eid'=>$eid,'oid'=>$oid,'year'=>$a);
		$dbperiod= new Api_Model_DbTable_Periods();
		$dataperiod = $dbperiod->_getPeriodsxYears($data);
		$this->view->dataperiod=$dataperiod;
	}

	public function getAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid = $this->_getParam('perid');
			$subid = $this->_getParam('subid');
			$escid = $this->_getParam('escid');
			$where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'subid'=>$subid,'escid'=>$escid);
			$attrib=array('perid','subid','escid','uid','pid','created','state');
			$dbacademic= new Api_Model_DbTable_Addreportacadadm();
			$datacademic = $dbacademic->_getFilter($where,$attrib);
			$i=0;
			foreach ($datacademic as $data) {
				$pid=$data['pid'];
				$where=array('eid'=>$eid,'pid'=>$pid);
				$attrib=array('pid','last_name0','last_name1','first_name');
				$dbperson= new Api_Model_DbTable_Person();
				$dataperson[$i]= $dbperson->_getFilter($where,$attrib);
				$i++;
			}
			// print_r($dataperson);
			$this->view->dataperson=$dataperson;
			$this->view->datacademic=$datacademic;
		} catch (Exception $e) {
			print "Error: get".$e->getMessage();
		}
	}

	public function updatestateAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid = base64_decode($this->_getParam('perid'));
			$escid = base64_decode($this->_getParam('escid'));
			$pid = base64_decode($this->_getParam('pid'));
			$uid = base64_decode($this->_getParam('uid'));
			$subid = base64_decode($this->_getParam('subid'));
			$state = base64_decode($this->_getParam('state'));
			$pk=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid,'pid'=>$pid,'uid'=>$uid);
			$data=array('state'=>$state);
			// print_r($pk);
			// print_r($data);
			$bdacademic= new Api_Model_DbTable_Addreportacadadm();
			$updatedata= $bdacademic->_update($data,$pk);
		} catch (Exception $e) {
			print "Error: Open Records".$e->getMessage();
		}

	}
}
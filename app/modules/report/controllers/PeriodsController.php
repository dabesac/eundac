<?php
 class Report_PeriodsController extends Zend_Controller_Action{

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
 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;

 			$where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
 			$esc = new Api_Model_DbTable_Speciality();
 			$dataesc = $esc->_getFilter($where,$attrib=null,$orders=array('facid','escid'));
 			$this->view->speciality = $dataesc;

 			$this->view->perid = $this->sesion->period->perid;
 			$where = array('eid' => $eid, 'oid' => $oid);
 			$per = new Api_Model_DbTable_Periods();
 			$dataper = $per->_getFilter($where,$attrib=null,$orders=array('perid'));
 			$this->view->periods = $dataper;
 		} catch (Exception $e) {
 			print "Error: ".$e->getMessage();
 		}
 	}

 	public function listteacherAction(){
 		try {
            $this->_helper->layout()->disableLayout();
 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;
 			$perid = $this->_getParam('perid');
 			$escid = $this->_getParam('escid');
 			$subid = $this->_getParam('subid');

 			$where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid, 'perid' => $perid);
 			$user = new Api_Model_DbTable_Coursexteacher();
 			$data_teacher = $user->_getFilter($where,$attrib=null,$orders=array('uid','courseid','turno'));
 			$wheretea = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'perid' => $perid);
			$allteacher = $user->_getAllTeacherXPeriodXEscid($wheretea);
 			if ($data_teacher) {
 				$cour = new Api_Model_DbTable_Course();
 				$tam = count($data_teacher);
 				for ($i=0; $i < $tam; $i++) { 
 					$wherecour = array(
 						'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
 						'curid' => $data_teacher[$i]['curid'], 'courseid' => $data_teacher[$i]['courseid']);
 					$datacour = $cour->_getOne($wherecour);
 					$data_teacher[$i]['name'] = $datacour['name'];
 				}
 			}
 			$this->view->data_teacher = $data_teacher;
 		} catch (Exception $e) {
 			print "Error: ".$e->getMessage();
 		}
 	}
}
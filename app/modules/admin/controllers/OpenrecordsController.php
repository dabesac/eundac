<?php
class Admin_OpenrecordsController extends Zend_Controller_Action{

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
			print "Error: Openrecords".$e->getMessage();
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

	public function coursesAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid = $this->_getParam('perid');
			$subid = $this->_getParam('subid');
			$escid = $this->_getParam('escid');
			$where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'subid'=>$subid,'escid'=>$escid);
			$attrib=array('perid','semid','escid','subid','courseid','curid','turno','type_rate','closure_date','state','state_record');
			$orders=array('semid','courseid','turno');
			$dbcourses= new Api_Model_DbTable_PeriodsCourses();
			$datacourses = $dbcourses->_getFilter($where,$attrib,$orders);
			$i=0;
			foreach ($datacourses as $course) {
				$curid=$course['curid'];
				$courseid=$course['courseid'];
				$where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name');
				$dbcourse = new Api_Model_DbTable_Course();
				$datacourse[$i]= $dbcourse->_getFilter($where,$attrib);
				$i++;
			}
			// print_r($datacourses);exit();
			$this->view->datacourses=$datacourses;
			$this->view->datacourse=$datacourse;
		} catch (Exception $e) {
			print "Error: get Courses".$e->getMessage();
		}
	}

	public function updatestateAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$courseid = base64_decode($this->_getParam('courseid'));
			$perid = base64_decode($this->_getParam('perid'));
			$escid = base64_decode($this->_getParam('escid'));
			$curid = base64_decode($this->_getParam('curid'));
			$turno = base64_decode($this->_getParam('turno'));
			$subid = base64_decode($this->_getParam('subid'));
			$state = base64_decode($this->_getParam('state'));
			// $closure_date = date('Y m d');
			$pk=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno);
			$data=array('state'=>$state);
			$bdrecords= new Api_Model_DbTable_PeriodsCourses();
			$updatedata= $bdrecords->_update($data,$pk);
		} catch (Exception $e) {
			print "Error: Open Records".$e->getMessage();
		}
	}

	public function getaplazedAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid = $this->_getParam('perid');
			$subid = $this->_getParam('subid');
			$escid = $this->_getParam('escid');
			$where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'subid'=>$subid,'escid'=>$escid);
			$attrib=array('perid','semid','escid','subid','courseid','curid','turno','type_rate','closure_date','state','state_record');
			$orders=array('semid','courseid','turno');
			$dbcourses= new Api_Model_DbTable_PeriodsCourses();
			$datacourses = $dbcourses->_getFilter($where,$attrib,$orders);
			$i=0;
			foreach ($datacourses as $course) {
				$curid=$course['curid'];
				$courseid=$course['courseid'];
				$turno=$course['turno'];
				$where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name');
				$dbcourse = new Api_Model_DbTable_Course();
				$datacourse[$i]= $dbcourse->_getFilter($where,$attrib);
				$wheres=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid,'perid'=>$perid,'turno'=>$turno,'is_main'=>'S');
				$attrib=array('courseid','pid');
				$dbteacher = new Api_Model_DbTable_Coursexteacher();
				$datateacher[$i] = $dbteacher->_getFilter($wheres,$attrib);
				$pid[$i] = $datateacher[$i][0]['pid'];
				$whe = array('eid'=>$eid,'pid'=>$pid[$i]);
				$attrib = array('pid','last_name0','last_name1','first_name');
				$dbteachers = new Api_Model_DbTable_Person();
				$infoteacher[$i] = $dbteachers->_getFilter($whe,$attrib);
				$i++;
			}
				// print_r($closure_date);exit();
			$this->view->infoteacher=$infoteacher;	
			$this->view->datacourses=$datacourses;
			$this->view->datacourse=$datacourse;
		} catch (Exception $e) {
			print "Error: get Courses".$e->getMessage();
		}
	}


}
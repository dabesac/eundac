<?php 
class Admin_OpensillabusController extends Zend_Controller_Action{

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
			print "Error: Opensillabus".$e->getMessage();
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
			$attrib=array('perid','subid','escid','courseid','curid','turno','teach_pid','state','created');
			$orders=array('courseid','turno');
			$dbcourses= new Api_Model_DbTable_Syllabus();
			$datacourses = $dbcourses->_getFilter($where,$attrib,$orders);
			$i=0;
			foreach ($datacourses as $course) {
				$curid=$course['curid'];
				$courseid=$course['courseid'];
				$turno=$course['turno'];
				$where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name','semid');
				$dbcourse = new Api_Model_DbTable_Course();
				$datacourse[$i]= $dbcourse->_getFilter($where,$attrib);
				$whered=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'subid'=>$subid,'escid'=>$escid,'courseid'=>$courseid,'curid'=>$curid,'turno'=>$turno);
				$attrib=array('type_rate');
				$orders=array('courseid','turno');
				$dbcour= new Api_Model_DbTable_PeriodsCourses();
				$datacour[$i] = $dbcour->_getFilter($whered,$attrib,$orders);
				$i++;
			}
				// print_r($datacourses);
			$this->view->datacour=$datacour;	
			$this->view->datacourses=$datacourses;
			$this->view->datacourse=$datacourse;
		} catch (Exception $e) {
			print "Error: get Sillabus".$e->getMessage();
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
			$pk=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno);
			$data=array('state'=>$state);
			// print_r($pk);
			// print_r($data);
			$bdsyllabus= new Api_Model_DbTable_Syllabus();
			$updatedata= $bdsyllabus->_update($data,$pk);
		} catch (Exception $e) {
			print "Error: Open Records".$e->getMessage();
		}

	}
}
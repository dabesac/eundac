<?php 

class Admin_AcademicmanagementController extends Zend_Controller_Action
{
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
            $rid = $this->sesion->rid;
            $uid = $this->sesion->uid;
			$fm= new Admin_Form_Openrecords();
			$this->view->fm=$fm;
			$where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getFilter($where,$attrib=null,$orders=null);
            $this->view->facultades=$facultad;
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

	public function schoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
           // $is_director = $this->sesion->infouser['teacher']['is_director'];
            $facid = $this->_getParam('facid');
            /*if ($rid=="DR" && $is_director=="S"){
                if ($facid=="2") $escid=substr($escid,0,3);
                $this->view->escid=$escid;
            }*/            
                $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid);
                $es = new Api_Model_DbTable_Speciality();
                $escu = $es->_getSchoolXFacultyNOTParent($where);
                $this->view->escuelas=$escu;
            
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function specialityAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = $this->sesion->subid;
            $escid = $this->_getParam('escid');
            if ($escid=="TODOEC") {
                $this->view->escid=$escid;}
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'parent' => $escid);
                $es = new Api_Model_DbTable_Speciality();
                $especia = $es->_getFilter($where,$attrib=null,$orders=null);
                $this->view->especialidad=$especia;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
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
			$attrib=array('perid','escid','semid','subid','courseid','curid','turno','type_rate','closure_date','state','state_record');
			$orders=array('semid','courseid','turno');
			$dbcourses= new Api_Model_DbTable_PeriodsCourses();
			$datacourses = $dbcourses->_getFilter($where,$attrib,$orders);
			if ($datacourses) {
				$i=0;			
				foreach ($datacourses as $course) {
					$curid=$course['curid'];
					$courseid=$course['courseid'];
					$turno=$course['turno'];
					$wher=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'coursoid'=>$courseid,'perid'=>$perid,'turno'=>$turno);
					$dbstate=new Api_Model_DbTable_StudentAssistance();
					$result[$i]= $dbstate->_getState($wher);			
					$where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
					$attrib=array('courseid','name');
					$dbcourse = new Api_Model_DbTable_Course();
					$datacourse[$i]= $dbcourse->_getFilter($where,$attrib);

					$where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid,'perid'=>$perid,'turno'=>$turno);
					$dbcoursesill= new Api_Model_DbTable_Syllabus();
					$datacoursesill = $dbcoursesill->_getOne($where);

					$datacourse[$i]['s_sillabus']=$datacoursesill['state'];
					//print_r($datacourse[$i]);
					$i++;
				}	
			}
			else{
				$datacourse=null;
			}
			//$this->view->sillabus=$datacoursesill;
			$this->view->result=$result;
			$this->view->datacourses=$datacourses;
			$this->view->datacourse=$datacourse;
		} catch (Exception $e) {
			print "Error: get Courses".$e->getMessage();
		}
	}

	public function updatestateAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uid = $this->sesion->uid;
			$courseid = base64_decode($this->_getParam('courseid'));
			$perid = base64_decode($this->_getParam('perid'));
			$escid = base64_decode($this->_getParam('escid'));
			$curid = base64_decode($this->_getParam('curid'));
			$turno = base64_decode($this->_getParam('turno'));
			$subid = base64_decode($this->_getParam('subid'));
			$state = base64_decode($this->_getParam('state'));
			$partial = base64_decode($this->_getParam('partial'));
			$state_record = base64_decode($this->_getParam('state_record'));
			$pk=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno);
			$data=array('state'=>$state,'state_record'=>$state_record);
			$bdrecords= new Api_Model_DbTable_PeriodsCourses();
			$updatedata= $bdrecords->_update($data,$pk);
			$dat = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'perid'=>$perid,'courseid'=>$courseid,'curid'=>$curid,'turno'=>$turno,'document_type'=>$partial,'register'=>$uid);
			//print_r($dat);exit();
			$bdlog= new Api_Model_DbTable_Loginspectionall();
			$insertdata = $bdlog->_save($dat);
		} catch (Exception $e) {
			print "Error: Open Records".$e->getMessage();
		}
	}

	public function updatestateassistanceAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
			$uid = $this->sesion->uid;
			$coursoid = base64_decode($this->_getParam('coursoid'));
			$perid = base64_decode($this->_getParam('perid'));
			$escid = base64_decode($this->_getParam('escid'));
			$curid = base64_decode($this->_getParam('curid'));
			$turno = base64_decode($this->_getParam('turno'));
			$subid = base64_decode($this->_getParam('subid'));
			$state = base64_decode($this->_getParam('state'));
			$partial = base64_decode($this->_getParam('partial'));
			$pk=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'coursoid'=>$coursoid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno);
			$data=array('state'=>$state);
			$bd= new Api_Model_DbTable_StudentAssistance();
			$updatedata= $bd->_updateAll($data,$pk);
			$dat = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'perid'=>$perid,'courseid'=>$coursoid,'curid'=>$curid,'turno'=>$turno,'document_type'=>$partial,'register'=>$uid);
			$bdlog= new Api_Model_DbTable_Loginspectionall();
			$insertdata = $bdlog->_save($dat);
		} catch (Exception $e) {
			print "Error: Open Assistance".$e->getMessage();
		}
	}

	public function updatestatesilabusAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
			$uid = $this->sesion->uid;
			$courseid = base64_decode($this->_getParam('courseid'));
			$perid = base64_decode($this->_getParam('perid'));
			$escid = base64_decode($this->_getParam('escid'));
			$curid = base64_decode($this->_getParam('curid'));
			$turno = base64_decode($this->_getParam('turno'));
			$subid = base64_decode($this->_getParam('subid'));
			$state = base64_decode($this->_getParam('state'));
			$partial = base64_decode($this->_getParam('partial'));
			$pk=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno);
			$data=array('state'=>$state);
			$bdsyllabus= new Api_Model_DbTable_Syllabus();
			$updatedata= $bdsyllabus->_update($data,$pk);
			$dat = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'perid'=>$perid,'courseid'=>$courseid,'curid'=>$curid,'turno'=>$turno,'document_type'=>$partial,'register'=>$uid);
			$bdlog= new Api_Model_DbTable_Loginspectionall();
			$insertdata = $bdlog->_save($dat);
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
<?php

class Record_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
		if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
		}
    		$login = $sesion->getStorage()->read();
    	$this->sesion = $login;
    	if (isset($this->sesion->teacher)){
    		$this->is_director=$this->sesion->teacher['is_director'];
    	}
    }
    
    public function indexAction()
    {
		$speciality= new Api_Model_DbTable_Speciality();
		$where['eid'] = $this->sesion->eid;
		$where['oid'] = $this->sesion->oid;
		$where['state'] ='A';
		if ($this->is_director=="S"){
			$where['escid']=$this->sesion->escid;
			$where['subid']=$this->sesion->subid;
		}
		$data= array("escid","subid","name");
		$rows = $speciality->_getFilter($where,$data);
		if ($rows) $this->view->specialitys=$rows;
		// set speciality for director
		
		
	}
	
	public function listAction()
	{
		$this->_helper->layout()->disableLayout();
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			$formData['eid'] = $this->sesion->eid;
			$formData['oid'] = $this->sesion->oid;
			$tmpescid = split(";--;",$formData['escid']);
			$formData['escid'] = base64_decode($tmpescid[0]);
			$formData['subid'] = base64_decode($tmpescid[1]);
			$formData['perid'] = base64_decode($formData['perid']);
			//verify status period
			$periods = new Api_Model_DbTable_Periods();
			$rowperiod = $periods->_getOne($formData);
			if ($rowperiod ) $this->view->stateperiod=$rowperiod['state'];
			unset($formData['year']);
			$this->view->courses = $this->_loadCourses($formData);
		}
				 
		
	}
	
	private function _loadCourses($formData=null){		
		$records = new Api_Model_DbTable_PeriodsCourses();
		$attris = array("eid","oid","perid","courseid","escid","subid","curid","turno",
				"curid","semid","type_rate","closure_date","state_record","state");
		$orders = array("eid","curid","courseid","turno");
		$coursename = ($formData['coursename']);
		unset($formData['coursename']);
		$rows = $records->_getFilter($formData,$attris,$orders);
		$lscourses=null;
		if ($rows) {
			foreach ($rows as $row){
				$course = new Api_Model_DbTable_Course();
				$where=null;
				$where['eid']=$row['eid'];
				$where['oid']=$row['oid'];
				$where['escid']=$row['escid'];
				$where['curid']=$row['curid'];
				$where['courseid']=$row['courseid'];
				// get info course
				$rowcourse = $course->_getFilter($where,array('name'));
				if ($rowcourse) $row['course']= $rowcourse[0]['name'];
				// get count register course
				$where['subid']=$row['subid'];
				$where['perid']=$row['perid'];
				$where['turno']=$row['turno'];
				if ($row['closure_date']<>""){
					$date = new Zend_Date($row['closure_date']);
					$row['closure_date']=$date->toString('dd/MM/Y');
				}
				$register = new Api_Model_DbTable_Registrationxcourse();
				$countregister = $register->_getCountRegisterCourse($where);
				$row['numregister'] = ($countregister)?$countregister:0;
				if ($coursename){
					if (stristr($row['course'],$coursename)<>"")
						$lscourses[]=$row;
				}else
					$lscourses[]=$row;
			}
		}
		if ($lscourses)return $lscourses;
	}
	
	public function detailAction()
	{
		$this->_helper->layout()->disableLayout();
		// PK
		$formData['eid'] = $this->sesion->eid;
		$formData['oid'] = $this->sesion->oid;
		$formData['escid'] = base64_decode($this->getParam("escid"));
		$formData['subid'] = base64_decode($this->getParam("subid"));
		$formData['perid'] = base64_decode($this->getParam('perid'));
		$formData['courseid'] = base64_decode($this->getParam('courseid'));
		$formData['curid'] = base64_decode($this->getParam('curid'));
		$formData['turno'] = base64_decode($this->getParam('turno'));
		$this->view->print = base64_decode($this->getParam('print'));
		$this->view->typeprint = base64_decode($this->getParam('type'));
		$this->view->urlprint="/record/index/detail/escid/".$this->getParam("escid")."/subid/".$this->getParam("subid").
				"/perid/".$this->getParam("perid")."/courseid/".$this->getParam("courseid")."/curid/".
				$this->getParam("curid")."/turno/".$this->getParam("turno");
		//get Course x Period
		$course = new Api_Model_DbTable_PeriodsCourses();
		$rows = $course->_getOne($formData);
		if ($rows) {
			if ($rows['closure_date']<>""){
				$date = new Zend_Date($rows['closure_date']);
				$rows['closure_date']=$date->toString('dd/MM/Y');
			}
			$name = new Api_Model_DbTable_Course();
			$rname = $name->_getOne($rows);
			if ($rname) $rows['name'] = $rname['name'];  
			$teachers = new Api_Model_DbTable_Coursexteacher();
			$lteachers= $teachers->_getAll($formData);
			if ($lteachers) {
				foreach ($lteachers as $teaches){
					$userinfo = new Api_Model_DbTable_Users();
					$ruser = $userinfo->_getInfoUser($teaches);
					if ($ruser) $rows['teachers'][]=$ruser['last_name0']." ".$ruser['last_name1'].", ".$ruser['first_name'];
				}	
			}
			$register = new Api_Model_DbTable_Registrationxcourse();
			$countregister = $register->_getCountRegisterCourse($formData);
			$rows['numregister'] = ($countregister)?$countregister:0;
			$this->view->course = $rows;
			// get students
			$student = new Api_Model_DbTable_Registrationxcourse();
			$students=$student->_getStudentXcoursesXescidXperiods($formData);
			if ($students) $this->view->students = $students;
			$speciality = new Api_Model_DbTable_Speciality();
			$rows = $speciality->_getOne($formData);
			if ($rows){
				if ($rows->parent){
					$rows->escid=$rows->parent;
					$erows = $speciality->_getOne($rows);
					$this->view->speciality = $erows;
					$this->view->speciality1 = $rows;
				}
				else
					$this->view->speciality = $rows;
			}
			
			$faculty = new Api_Model_DbTable_Faculty();
			$frows = $faculty->_getOne($rows);
			if ($frows) $this->view->faculty = $frows;
			
		}
		
	}
	
	public function printavenceAction()
	{
		$this->_helper->layout()->disableLayout();		
		$formData['eid'] = $this->sesion->eid;
		$formData['oid'] = $this->sesion->oid;
		$tmpescid = split(";--;",$this->getParam('escid'));
		$formData['escid'] = base64_decode($tmpescid[0]);
		$formData['subid'] = base64_decode($tmpescid[1]);
		$formData['perid'] = base64_decode($this->getParam('perid'));
		$this->view->courses = $this->_loadCourses($formData);
		$this->view->perid = $formData['perid'];
		$speciality = new Api_Model_DbTable_Speciality();
		$rows = $speciality->_getOne($formData);
		if ($rows){
			if ($rows->parent){
				$rows->escid=$rows->parent;
				$erows = $speciality->_getOne($rows);
				$this->view->speciality = $erows;
				$this->view->speciality1 = $rows;
			}				
			else
				$this->view->speciality = $rows;
		}
		
		$faculty = new Api_Model_DbTable_Faculty();
		$frows = $faculty->_getOne($rows);
		if ($frows) $this->view->faculty = $frows;
		
		$this->view->printheader = $this->sesion->org['header_print'];
		$this->view->printfooter = $this->sesion->org['footer_print'];
	}
	
	public function periodsAction()
	{
		$this->_helper->layout()->disableLayout();
		$year = base64_decode($this->_getParam("year"));
		$periods = new Api_Model_DbTable_Periods();
		$data['eid'] = $this->sesion->eid;
		$data['oid'] = $this->sesion->oid;
		$data['year'] = substr($year,2,4);
		$rows_periods = $periods->_getPeriodsxYears($data);
		if ($rows_periods) $this->view->periods=$rows_periods;
	}
}

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

	public function modifiedrecordAction(){
		try {

			$params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $eid =	$this->sesion->eid;
            $oid =	$this->sesion->oid;

            $params = $paramsdecode;
            $courseid =	trim($params['courseid']);
            $turno =	trim($params['turno']);
            $curid =	trim($params['curid']);
            $escid =	trim($params['escid']);
            $subid =	trim($params['subid']);
            $perid =	trim($params['perid']);
            $state =	trim($params['state']);
            $closure =	trim($params['closure']);
            $type =	trim($params['typea']);


            $this->view->turno = $turno;

            $where = array(
            	'eid'=>$eid,'oid'=>$oid,
            	'courseid'=>$courseid,'turno'=>$turno,
            	'curid'=>$curid,'escid'=>$escid,
            	'subid'=>$subid,'perid'=>$perid,);
            $base_course_x_teacher =	new Api_Model_DbTable_Coursexteacher();
            $base_speciality = new Api_Model_DbTable_Speciality();
            $base_course =	new Api_Model_DbTable_Course();
            $base_person = new Api_Model_DbTable_Person();
            $info_couser = $base_course->_getOne($where);
            $info_teacher = $base_course_x_teacher->_getFilter($where);
            $speciality = $base_speciality->_getAll($where);

            foreach ($info_teacher as $key => $value) {
            	$where1= array(
            		'eid' => $value['eid'],
            		'oid' => $value['oid'],
            		'pid' => $value['pid']);
            	$name_teacher = $base_person->_getOne($where1);
            	$info_teacher[$key]['name']=$name_teacher['last_name0'].
            								" ".$name_teacher['last_name1'].
            								",".$name_teacher['first_name']; 
            }

            $this->view->state = $state;
            $this->view->closure = $closure;
            $this->view->type = $type;
            $this->view->perid =$perid;
            $this->view->info_couser = $info_couser;
            $this->view->info_teacher = $info_teacher;
            $this->view->speciality = $speciality;
			$this->_helper->layout()->disableLayout();		


						 
		} catch (Exception $e) {
			print "Error modified record".$e->getMessage();
		}
	}

	public function teachersAction(){
		try {
            $eid =	$this->sesion->eid;
            $oid =	$this->sesion->oid;
            $escid = base64_decode($this->_getParam('escid'));
            $state = base64_decode($this->_getParam('state'));
            $date_record = base64_decode($this->_getParam('state'));
            $where = array(
            	'eid'=> $eid,
            	'oid' => $oid,'escid'=>$escid,
            	'rid'=>'DC','state'=>'A');
            $attrib = array('pid','uid','subid');
            $base_user = new Api_Model_DbTable_Users();
            $info_users = $base_user->_getFilter($where,$attrib);

            foreach ($info_users as $key => $value) {
            	$where['pid']=$value['pid'];
            	$where['uid']=$value['uid'];
            	$where['subid']=$value['subid'];
            	$info_user = $base_user->_getInfoUser($where);
            	$info_users [$key]['full_name'] = $info_user[0]['last_name0'].
            									' '.$info_user[0]['last_name1'].
            									", ".$info_user[0]['first_name']; 
            }

            $this->view->state=$state;
            $this->view->date_record=$date_record;
            $this->view->teachers = $info_users;
			$this->_helper->layout()->disableLayout();		
		} catch (Exception $e) {
			print "Error modified teacher".$e->getMessage();
		}
	}

	public function savemodifiedrecordAction()
	{
		try {
				$params = $this->getRequest()->getParams();
	            $paramsdecode = array();
	            foreach ( $params as $key => $value ){
	                if($key!="module" && $key!="controller" && $key!="action"){
	                    $paramsdecode[base64_decode($key)] = base64_decode($value);
	                }
	            }

	            $eid =	$this->sesion->eid;
	            $oid =	$this->sesion->oid;

	            $params = $paramsdecode;
	            print_r($params);exit();
	            
	            $courseid =	trim($params['courseid']);
	            $turno =	trim($params['turno']);
	            $curid =	trim($params['curid']);
	            $escid =	trim($params['escid']);
	            $subid =	trim($params['subid']);
	            $perid =	trim($params['perid']);
	            $state =	trim($params['state']);
	            $closure =	trim($params['closure']);
	            $type =	trim($params['typea']);


		} catch (Exception $e) {
			print "Error modified teacher".$e->getMessage();
		}
	}
}

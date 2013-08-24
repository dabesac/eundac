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
    }
    
    public function indexAction()
    {
		$speciality= new Api_Model_DbTable_Speciality();
		$where['eid'] = $this->sesion->eid;
		$where['oid'] = $this->sesion->oid;
		$where['state'] ='A';
		$data= array("escid","subid","name");
		$rows = $speciality->_getFilter($where,$data);
		if ($rows) $this->view->specialitys=$rows;
		

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
		$formData['coursename'] = ($formData['coursename']);
		if ($formData['coursename']=="") unset($formData['coursename']);
		
		
		$records = new Api_Model_DbTable_PeriodsCourses();
		$attris = array("eid","oid","perid","courseid","escid","subid","curid","turno",
				"curid","semid","type_rate","closure_date","state_record","state");
		$orders = array("eid","curid","courseid","turno");
		$rows = $records->_getFilter($formData,$attris,$orders);
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
				$register = new Api_Model_DbTable_Registrationxcourse();
				$countregister = $register->_getCountRegisterCourse($where);
				$row['numregister'] = ($countregister)?$countregister:0;
				$lscourses[]=$row;
			}
		}
		return $lscourses;
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
		if ($rows) $this->view->speciality = $rows;
		
		$this->view->printheader = $this->sesion->org['header_print'];
		$this->view->printfooter = $this->sesion->org['footer_print	'];
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

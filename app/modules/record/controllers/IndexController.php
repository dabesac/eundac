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

			$params = $this->getRequest()->getParams();
	            $paramsdecode = array();
	            foreach ( $params as $key => $value ){
	                if($key!="module" && $key!="controller" && $key!="action"){
	                    $paramsdecode[base64_decode($key)] = base64_decode($value);
	                }
	        }

			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
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
            $base_register_course = new Api_Model_DbTable_Registrationxcourse();
            $base_speciality = new Api_Model_DbTable_Speciality();
            $base_course =	new Api_Model_DbTable_Course();
            $base_person = new Api_Model_DbTable_Person();
            $info_couser = $base_course->_getOne($where);
            $info_teacher = $base_course_x_teacher->_getFilter($where);
            $speciality = $base_speciality->_getAll($where);
            $data_students = $base_register_course->_getFilter($where);
            foreach ($data_students as $key => $value) {
            	$where2= array(
            		'eid' => $value['eid'],
            		'oid' => $value['oid'],
            		'pid' => $value['pid']); 

            	$name_student=$base_person->_getOne($where2);
            	$data_students [$key]['name'] = $name_student['last_name0'].' '.
            					$name_student['last_name1'].", ".
            					$name_student['first_name'];
            }
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

            if ($data_students) {
            	$num_register = count($data_students);
            }
            else $num_register =0;
            
            $this->view->numregister = $num_register;
            $this->view->data_students = $data_students;
            $this->view->state = $state;
            $this->view->closure = $closure;
            $this->view->type = $type;
            $this->view->perid =$perid;
            $this->view->subid =$subid;
            $this->view->info_couser = $info_couser;
            $this->view->info_teacher = $info_teacher;
            $this->view->speciality = $speciality;
			$this->_helper->layout()->disableLayout();		
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
            $this->view->subid =$subid;
            $this->view->info_couser = $info_couser;
            $this->view->info_teacher = $info_teacher;
            $this->view->speciality = $speciality;
			$this->_helper->layout()->disableLayout();		


						 
		} catch (Exception $e) {
			print "Error modified record".$e->getMessage();
		}
	}
	public function printconstancyAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$curid=base64_decode($this->_getParam('curid'));
			$turno=base64_decode($this->_getParam('turno'));
			
			$base_faculty 	=	new Api_Model_DbTable_Faculty();
			$base_speciality = 	new Api_Model_DbTable_Speciality();
			$base_course = 	new Api_Model_DbTable_Course();
			$base_course_x_teacher = 	new Api_Model_DbTable_Coursexteacher();
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			
			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,); 
			
			$info_speciality = 	$base_speciality->_getOne($where);

			if ($info_speciality['parent'] != "") {
				$where['escid']=$info_speciality['escid'];
				$name_speciality = $base_speciality->_getOne($where);
				$info_speciality['speciality'] = $name_speciality['name'];
			}

			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$info_speciality['name_faculty'] = $name_faculty['name'];

			$this->view->info_speciality = $info_speciality;
			$this->_helper->layout()->disableLayout();		
		} catch (Exception $e) {
			print "Error print constancy".$e->getMessage();
		}
	}
	public function printrecordAction(){
		try {
			
		} catch (Exception $e) {
			print "Error print record".$e->getMessage();
		}
	}

}

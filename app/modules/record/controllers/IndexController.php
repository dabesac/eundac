<?php

class Record_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
		if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
		}
		$login = $sesion->getStorage()->read();
		$this->is_director='N';
    	if (($login->infouser['teacher']['is_director']=='S')){
    		$this->is_director=$login->infouser['teacher']['is_director'];
    	}
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {
		
		$speciality= new Api_Model_DbTable_Speciality();
		$where['eid'] = $this->sesion->eid;
		$where['oid'] = $this->sesion->oid;
		$where['state'] ='A';
		$fac=$this->sesion->faculty->facid;
		$this->view->fac=$fac;
		$this->view->rid= $this->sesion->rid;
		if ($this->is_director=="S"){
			$where['escid']=$this->sesion->escid;
			$where['subid']=$this->sesion->subid;
		}
		if ($this->sesion->rid=="RF" and $this->sesion->subid=="1901"){
			$where['facid']=$this->sesion->faculty->facid;
			//echo $where['facid'];exit();	
		}

		if ($this->sesion->rid=="RF" and $this->sesion->subid<>"1901"){
			//$where['facid']=$this->sesion->faculty->facid;
			$where['subid']=$this->sesion->subid;		
		}
		
		$data= array("escid","subid","name");
		$rows = $speciality->_getFilter($where,$data='');
		if ($rows) $this->view->specialitys=$rows;
		// set speciality for director
		//facultades
		$wher = array('eid' => $where['eid'], 'oid' => $where['oid'], 'state' => 'A');
		$fac= new Api_Model_DbTable_Faculty();
        $facultad=$fac->_getFilter($wher,$attrib=null,$orders=null);
        $this->view->facultades=$facultad;
		
	}
	
	public function schoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $this->view->rid=$rid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $facid = base64_decode($this->_getParam('facid'));
            $esc = $this->_getParam('escid');
            $this->view->esc=$esc;


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
            $data = $this->_getParam('escid');
            $escid = base64_decode($data);
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

	public function listAction()
	{
		$this->_helper->layout()->disableLayout();
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			$formData['eid'] = $this->sesion->eid;
			$formData['oid'] = $this->sesion->oid;
			if((empty($formData['speciality'])==true)){
				$tmpescid = split(";--;",$formData['escid']);
				$formData['escid'] = base64_decode($tmpescid[0]);
				$formData['subid'] = base64_decode($tmpescid[1]);
			}else{
				$tmpescid = split("--",$formData['speciality']);
				$formData['subid'] = base64_decode($tmpescid[0]);
				$formData['escid'] = base64_decode($tmpescid[1]);
			}
			$formData['perid'] = base64_decode($formData['perid']);
			$this->view->escid=$formData['escid'];
			$this->view->subid=$formData['subid'];
			$this->view->perid=$formData['perid'];
			//verify status period
			$periods = new Api_Model_DbTable_Periods();
			$rowperiod = $periods->_getOne($formData);
			if ($rowperiod ) $this->view->stateperiod=$rowperiod['state'];
			unset($formData['year']);
			unset($formData['facid']);
			unset($formData['speciality']);
			$this->view->courses = $this->_loadCourses($formData);
			$this->view->rid =$this->sesion->rid;

		}
				 
		
	}
	
	private function _loadCourses($formData=null){		
		$records = new Api_Model_DbTable_PeriodsCourses();
		$attris = array("eid","oid","perid","courseid","escid","subid","curid","turno",
				"curid","semid","type_rate","closure_date","state_record","state");
		$orders = array("eid","curid","courseid","turno");
		// $coursename = ($formData['coursename']);
		$coursename = (isset($formData['coursename']))?$formData['coursename']:null;
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
			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
			$wheresc['eid']=$eid;
            $wheresc['oid']=$oid;
            $wheresc['state']="A";
            $rescu = new Api_Model_DbTable_Speciality();
            $lista = $rescu->_getFilter($wheresc,$attrib=null,$orders='escid');
            $this->view->lescuelas = $lista;


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
            $data_students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);
            
            if ($info_teacher) {
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
            $rid = $this->sesion->rid;
            $this->view->rid=$rid;
            $this->view->escuela=$escid;
			$this->_helper->layout()->disableLayout();		
	}
	
	public function printavenceAction()
	{
		$this->_helper->layout()->disableLayout();		
		$eid = $this->sesion->eid;
		$oid = $this->sesion->oid;
		$tmpescid = split(";--;",$this->_getParam('escid'));
		$escid = base64_decode($tmpescid[0]);
		$subid = base64_decode($tmpescid[1]);
		$perid = base64_decode($this->_getParam('perid'));
		$formData= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'perid'=>$perid);
		$this->view->courses = $this->_loadCourses($formData);
		$this->view->perid = $perid;

		$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        $base_speciality =  new Api_Model_DbTable_Speciality();        
        $speciality = $base_speciality ->_getOne($where);
     
        $facid = $speciality['facid'];
		$whe=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
		$faculty = new Api_Model_DbTable_Faculty();
		$datafa=$faculty->_getOne($whe);
		$namef=strtoupper($datafa['name']);
     
        $parent=$speciality['parent'];
        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        $parentesc= $base_speciality->_getOne($wher);

        if ($parentesc) {
            $pala='ESPECIALIDAD DE ';
            $spe['esc']=$parentesc['name'];
            $spe['parent']=$pala.$speciality['name'];
        }
        else{
            $spe['esc']=$speciality['name'];
            $spe['parent']='';  
        }
        $names=strtoupper($spe['esc']);
        $namep=strtoupper($spe['parent']);
        $namefinal=$names." <br> ".$namep;

        if ($speciality['header']) {
            $namelogo = $speciality['header'];
        }
        else{
            $namelogo = 'blanco';
        }
        
        $escid=$this->sesion->escid;
        $where['escid']=$escid;

        $dbimpression = new Api_Model_DbTable_Countimpressionall();
        
        $uid=$this->sesion->uid;
        $uidim=$this->sesion->pid;
        $pid=$uidim;

        $data = array(
            'eid'=>$eid,
            'oid'=>$oid,
            'uid'=>$uid,
            'escid'=>$escid,
            'subid'=>$subid,
            'pid'=>$pid,
            'type_impression'=>'avance_de_notas',
            'date_impression'=>date('Y-m-d H:i:s'),
            'pid_print'=>$uidim
            );
        $dbimpression->_save($data);            

        $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'avance_de_notas');
        $dataim = $dbimpression->_getFilter($wheri);
        $co=count($dataim);

        $codigo=$co." - ".$uidim;
        $this->view->codigo=$codigo;
		
		$header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];
        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);
        
        $this->view->header=$header;
        $this->view->footer=$footer;
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
			$base_person =	new Api_Model_DbTable_Person();
			$base_impress_course = new Api_Model_DbTable_Impresscourse();

			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'CONSTANCY'.$perid.$curid.$courseid.$turno,
				);

			$base_impress_course->_save($data_impress);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 
			$data_students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);


			$info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];

	        $speciality = $base_speciality ->_getOne($where);
	          
			$where ['facid'] = $speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$namef = strtoupper($name_faculty['name']);

	        $parent=$speciality['parent'];
	        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
	        $parentesc= $base_speciality->_getOne($wher);

	        if ($parentesc) {
	            $pala='ESPECIALIDAD DE ';
	            $spe['esc']=$parentesc['name'];
	            $spe['parent']=$pala.$speciality['name'];
	        }
	        else{
	            $spe['esc']=$speciality['name'];
	            $spe['parent']='';  
	        }
	        $names=strtoupper($spe['esc']);
	        $namep=strtoupper($spe['parent']);
	        $namefinal=$names." <br> ".$namep;

	        if ($speciality['header']) {
	            $namelogo = $speciality['header'];
	        }
	        else{
	            $namelogo = 'blanco';
	        }
	        
	        $escid=$this->sesion->escid;
	        $where['escid']=$escid;

	        $dbimpression = new Api_Model_DbTable_Impresscourse();
	        
	        $uidim=$this->sesion->pid;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'perid'=>$perid,
                'courseid'=>$courseid,
                'escid'=>$escid,
                'subid'=>$subid,
                'curid'=>$curid,
                'turno'=>$turno,
                'register'=>$uidim,
                'created'=>date('Y-m-d H:i:s'),
                'code'=>'constancia'
                );

            // print_r($data);exit();
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>'constancia');
            $dataim = $dbimpression->_getFilter($wheri);
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

			$this->view->info_couser = $info_couser;
			$this->view->students=$data_students;
			$this->view->perid=$perid;

			$header=$this->sesion->org['header_print'];
		    $footer=$this->sesion->org['footer_print'];
		    $header = str_replace("?facultad",$namef,$header);
		    $header = str_replace("?escuela",$namefinal,$header);
		    $header = str_replace("?logo", $namelogo, $header);
		    $header = str_replace("?codigo", $codigo, $header);
		    
		    $this->view->header=$header;
		    $this->view->footer=$footer;
			$this->_helper->layout()->disableLayout();	

		} catch (Exception $e) {
			print "Error print constancy".$e->getMessage();
		}
	}
	public function printrecordAction(){
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
			$base_person =	new Api_Model_DbTable_Person();
			$base_periods = new Api_Model_DbTable_Periods();
			$base_semester = new Api_Model_DbTable_Semester();
			$base_base_subsidiary = new Api_Model_DbTable_Subsidiary();
			$base_impress_course = new Api_Model_DbTable_Impresscourse();


			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'RECORD'.$perid.$curid.$courseid.$turno,
				);

			$base_impress_course->_save($data_impress);

			

			$where_subid = array(
				'eid'=>$eid,'oid'=>$oid,
				'subid'=>$subid,
				);

			$data_subsidiary = $base_base_subsidiary->_getOne($where_subid);

			$where_period = array(
				'eid'=>$eid,'oid'=>$oid,
				'perid'=>$perid,
				);

			$data_period = $base_periods = $base_periods->_getOnePeriod($where_period);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 
			$data_students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);


			$info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$where_semester = array(
				'eid'=>$eid,'oid'=>$oid,
				'semid'=>$info_couser['semid'],
				);
			$data_semester = $base_semester->_getOne($where_semester);
			$info_couser['name_semester']=$data_semester['name'];
			
			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];
			$info_speciality = 	$base_speciality->_getOne($where);


			if ($info_speciality['parent'] != "") {
				$where['escid']=$info_speciality['parent'];
				$name_speciality = $base_speciality->_getOne($where);
				$info_speciality['speciality'] = $name_speciality['name'];
			}


			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$info_speciality['name_faculty'] = $name_faculty['name'];

			
			$this->view->info_speciality = $info_speciality;
			$this->view->info_couser = $info_couser;
			$this->view->students=$data_students;
			$this->view->perid=$data_period;
			$this->view->subid=$data_subsidiary;
			$this->_helper->layout()->disableLayout();			

		} catch (Exception $e) {
			print "Error print record".$e->getMessage();
		}
	}

	public function backrecordAction(){

		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$curid=base64_decode($this->_getParam('curid'));
			$turno=base64_decode($this->_getParam('turno'));
			
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			$base_periods_course = new Api_Model_DbTable_PeriodsCourses();
			$base_base_subsidiary = new Api_Model_DbTable_Subsidiary();

			$base_impress_course = new Api_Model_DbTable_Impresscourse();


			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'RECORD'.$perid.$curid.$courseid.$turno.'BACK',
				);

			$base_impress_course->_save($data_impress);

			$where_subid = array(
				'eid'=>$eid,'oid'=>$oid,
				'subid'=>$subid,
				);

			$data_subsidiary = $base_base_subsidiary->_getOne($where_subid);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,); 

			$data_period_course = $base_periods_course->_getOne($where);

			$tota_students = $base_register_course->_get_total_students_x_course($where);
			$total_approved = $base_register_course->_get_approved($where);
			$total_disapp = $base_register_course->_get_disapproved_x_course($where);
			$total_retired = $base_register_course->_get_retired_x_course($where);
			$total_NSP = $base_register_course->_get_NSP_x_course($where);

			$tota_students1 = intval($tota_students[0]['count']);
			$total_approved1= intval($total_approved[0]['count']);
			$total_disapp1 = $total_disapp[0]['count'];
			$total_retired1 = $total_retired[0]['count'];
			$total_NSP1 = $total_NSP[0]['count'];
			
			$percentage_apro = round((($total_approved1/$tota_students1)*100),2);
			$percentage_desapro = round((($total_disapp1/$tota_students1)*100),2);
			$percentage_retir = round((($total_retired1/$tota_students1)*100),2);
			$percentage_nsp = round((($total_NSP1/$tota_students1)*100),2);


			$this->view->tota_students=$tota_students1;
			$this->view->total_approved=$total_approved1;
			$this->view->total_disapp=$total_disapp1;
			$this->view->total_retired=$total_retired1;
			$this->view->total_NSP=$total_NSP1;

			$this->view->perid=$data_period_course;
			$this->view->subid=$data_subsidiary;

			$this->view->percentage_apro=$percentage_apro;
			$this->view->percentage_desapro=$percentage_desapro;
			$this->view->percentage_retir=$percentage_retir;
			$this->view->percentage_nsp=$percentage_nsp;

			$this->_helper->layout()->disableLayout();	
			
		} catch (Exception $e) {
			print "Error back record".$e->getMessage();
		}		
	}

	public function printregisterAction()
	{
		try {

			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$curid=base64_decode($this->_getParam('curid'));
			$turno=base64_decode($this->_getParam('turno'));
			$type=base64_decode($this->_getParam('type'));
			
			$base_faculty 	=	new Api_Model_DbTable_Faculty();
			$base_speciality = 	new Api_Model_DbTable_Speciality();
			$base_course = 	new Api_Model_DbTable_Course();
			$base_course_x_teacher = 	new Api_Model_DbTable_Coursexteacher();
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			$base_person =	new Api_Model_DbTable_Person();
			$base_CourseCompetency = new Api_Model_DbTable_CourseCompetency();
			$base_semester = new Api_Model_DbTable_Semester();

			$base_impress_course = new Api_Model_DbTable_Impresscourse();

			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'REGISTER'.$perid.$curid.$courseid.$turno,
				);
			$base_impress_course->_save($data_impress);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 

			$data_students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);
			if ($type=="C") {
				$data_Competecy = $base_CourseCompetency->_getOne($where);
				$this->view->data_Competecy = $data_Competecy;
			}

			$info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$where_semester = array(
				'eid'=>$eid,'oid'=>$oid,
				'semid'=>$info_couser['semid'],
				);

			$data_semester = $base_semester->_getOne($where_semester);
			$info_couser['name_semester']=$data_semester['name'];

			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];
			$info_speciality = 	$base_speciality->_getOne($where);


			if ($info_speciality['parent'] != "") {
				$where['escid']=$info_speciality['parent'];
				$name_speciality = $base_speciality->_getOne($where);
				$info_speciality['speciality'] = $name_speciality['name'];
			}


			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$info_speciality['name_faculty'] = $name_faculty['name'];

			
			$this->view->info_speciality = $info_speciality;
			$this->view->info_couser = $info_couser;
			$this->view->students=$data_students;
			$this->view->perid=$perid;
			$this->view->type=$type;

			$this->_helper->layout()->disableLayout();
			
		} catch (Exception $e) {
			print "Error: print register ".$e->getMessage();
			
		}
	}

	public function backregisterAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$curid=base64_decode($this->_getParam('curid'));
			$turno=base64_decode($this->_getParam('turno'));
			$type=base64_decode($this->_getParam('type'));
			
			$base_faculty 	=	new Api_Model_DbTable_Faculty();
			$base_speciality = 	new Api_Model_DbTable_Speciality();
			$base_course = 	new Api_Model_DbTable_Course();
			$base_course_x_teacher = 	new Api_Model_DbTable_Coursexteacher();
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			$t_base_assitence_student = new Api_Model_DbTable_StudentAssistance();
			$base_person =	new Api_Model_DbTable_Person();
			$base_CourseCompetency = new Api_Model_DbTable_CourseCompetency();
			$base_semester = new Api_Model_DbTable_Semester();

			$base_impress_course = new Api_Model_DbTable_Impresscourse();

			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'REGISTER'.$perid.$curid.$courseid.$turno.'BACK',
				);
			$base_impress_course->_save($data_impress);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 


			$data_students = $t_base_assitence_student->_get_asisstance_backregister($where);


			$tota_students = $base_register_course->_get_total_students_x_course($where);
			$total_approved = $base_register_course->_get_approved($where);
			$total_disapp = $base_register_course->_get_disapproved_x_course($where);
			$total_retired = $base_register_course->_get_retired_x_course($where);
			$total_NSP = $base_register_course->_get_NSP_x_course($where);

			$tota_students1 = intval($tota_students[0]['count']);
			$total_approved1= intval($total_approved[0]['count']);
			$total_disapp1 = $total_disapp[0]['count'];
			$total_retired1 = $total_retired[0]['count'];
			$total_NSP1 = $total_NSP[0]['count'];
			
			$percentage_apro = round((($total_approved1/$tota_students1)*100),2);
			$percentage_desapro = round((($total_disapp1/$tota_students1)*100),2);
			$percentage_retir = round((($total_retired1/$tota_students1)*100),2);
			$percentage_nsp = round((($total_NSP1/$tota_students1)*100),2);


			$info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$where_semester = array(
				'eid'=>$eid,'oid'=>$oid,
				'semid'=>$info_couser['semid'],
				);
			
			$data_semester = $base_semester->_getOne($where_semester);
			$info_couser['name_semester']=$data_semester['name'];

			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];

			$info_speciality = 	$base_speciality->_getOne($where);


			if ($info_speciality['parent'] != "") {
				$where['escid']=$info_speciality['parent'];
				$name_speciality = $base_speciality->_getOne($where);
				$info_speciality['speciality'] = $name_speciality['name'];
			}


			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$info_speciality['name_faculty'] = $name_faculty['name'];

			
			$this->view->info_speciality = $info_speciality;
			$this->view->info_couser = $info_couser;
			$this->view->students=$data_students;
			$this->view->perid=$perid;
			$this->view->type=$type;

			$this->view->tota_students=$tota_students1;
			$this->view->total_approved=$total_approved1;
			$this->view->total_disapp=$total_disapp1;
			$this->view->total_retired=$total_retired1;
			$this->view->total_NSP=$total_NSP1;

			$this->view->percentage_apro=$percentage_apro;
			$this->view->percentage_desapro=$percentage_desapro;
			$this->view->percentage_retir=$percentage_retir;
			$this->view->percentage_nsp=$percentage_nsp;

			$this->_helper->layout()->disableLayout();

		} catch (Exception $e) {
			print "Error: print back register ".$e->getMessage();
			
		}
	}

	public function recordcontrolAction()
	{
		try {	

			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			
			$where = array(
				'eid'=>$eid, 'oid' =>$oid,
				'escid'=>$escid, 'subid'=>$subid,
				'perid'=>$perid,
				);

			$base_faculty 	=	new Api_Model_DbTable_Faculty();
			$base_speciality = 	new Api_Model_DbTable_Speciality();
			$base_course_x_teacher = 	new Api_Model_DbTable_Coursexteacher();
			$base_person =	new Api_Model_DbTable_Person();
			$records = new Api_Model_DbTable_PeriodsCourses();

			

			$attris = array("eid","oid","perid","courseid","escid","subid","curid","turno",
					"curid","semid","type_rate","closure_date","state_record","state");
			$orders = array("eid","curid","courseid","turno");
			$rows = $records->_getFilter($where,$attris,$orders);
			$lscourses=null;

			if ($rows) {
				foreach ($rows as $key => $row){
					$course = new Api_Model_DbTable_Course();
					$where=null;
					$where['eid']=$row['eid'];
					$where['oid']=$row['oid'];
					$where['escid']=$row['escid'];
					$where['curid']=$row['curid'];
					$where['courseid']=$row['courseid'];
					// get info course
					$rowcourse = $course->_getFilter($where,array('name'));
					if ($rowcourse) $rows[$key]['course']= $rowcourse[0]['name'];

					// get count register course
					$where['subid']=$row['subid'];
					$where['perid']=$row['perid'];
					$where['turno']=$row['turno'];
					$where['is_main']='S';
					$register = new Api_Model_DbTable_Registrationxcourse();
					$countregister = $register->_getCountRegisterCourse($where);
					$rows[$key]['numregister'] = ($countregister)?$countregister:0;

					$dni_teacher = $base_course_x_teacher->_getFilter($where);
					$where2 = array(
						'eid'=>$eid,'oid'=>$oid,
						'pid'=> $dni_teacher[0]['pid']);

					$info_teacher = $base_person->_getOne($where2);
					$rows[$key]['name_teacher'] = $info_teacher['last_name0']." ".
													$info_teacher['last_name1'].", ".
													$info_teacher['first_name'];
					
				}
			}

			$info_speciality = 	$base_speciality->_getOne($where);
			// print_r($info_speciality);exit();
			$parent=$info_speciality['parent'];
		    $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);
            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$info_speciality['name'];
            }
            else{
                $spe['esc']=$info_speciality['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names." <br> ".$namep;

            $namelogo = (!empty($info_speciality['header']))?$info_speciality['header']:"blanco";

			// if ($info_speciality['parent'] != "") {
			// 	$where['escid']=$info_speciality['parent'];
			// 	$name_speciality = $base_speciality->_getOne($where);
			// 	$info_speciality['speciality'] = $name_speciality['name'];
			// }


			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$namef = strtoupper($name_faculty['name']);

			$dbimpression = new Api_Model_DbTable_Countimpressionall();

            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;
            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'control_actas',
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            // print_r($data);exit();
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'control_actas');
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

			// print_r($rows); exit();
			$this->view->info_speciality = $info_speciality;
			$this->view->info_couser=$rows;
			$this->view->perid=$perid;

			$header=$this->sesion->org['header_print'];
			$footer=$this->sesion->org['footer_print'];

			$header=str_replace("?facultad", $namef, $header);
			$header=str_replace("?escuela", $namefinal, $header);
			$header=str_replace("?logo", $namelogo, $header);
			$header = str_replace("?codigo", $codigo, $header);

			$header=str_replace("h2", "h3", $header);
			$header=str_replace("h3", "h4", $header);
			$header=str_replace("h4", "h5", $header);

			$footer=str_replace("h4", "h5", $footer);
			$footer=str_replace("h5", "h6", $footer);
			// $header=str_replace("100px", "100px", $header);

			$this->view->header=$header;
			$this->view->footer=$footer;

		} catch (Exception $e) {
			print "Error: print record control ".$e->getMessage();
		}
	}

	public function recordnotnotaAction()
	{
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
			$base_person =	new Api_Model_DbTable_Person();
			$base_periods = new Api_Model_DbTable_Periods();
			$base_semester = new Api_Model_DbTable_Semester();
			$base_base_subsidiary = new Api_Model_DbTable_Subsidiary();

			$base_impress_course = new Api_Model_DbTable_Impresscourse();

			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'NOTNOTESRECORD'.$perid.$curid.$courseid.$turno,
				);
			$base_impress_course->_save($data_impress);

			$where_subid = array(
				'eid'=>$eid,'oid'=>$oid,
				'subid'=>$subid,
				);

			$data_subsidiary = $base_base_subsidiary->_getOne($where_subid);

			$where_period = array(
				'eid'=>$eid,'oid'=>$oid,
				'perid'=>$perid,
				);

			$data_period = $base_periods = $base_periods->_getOnePeriod($where_period);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 
			$data_students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);


			$info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$where_semester = array(
				'eid'=>$eid,'oid'=>$oid,
				'semid'=>$info_couser['semid'],
				);
			$data_semester = $base_semester->_getOne($where_semester);
			$info_couser['name_semester']=$data_semester['name'];
			
			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];
			$info_speciality = 	$base_speciality->_getOne($where);


			if ($info_speciality['parent'] != "") {
				$where['escid']=$info_speciality['parent'];
				$name_speciality = $base_speciality->_getOne($where);
				$info_speciality['speciality'] = $name_speciality['name'];
			}


			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$info_speciality['name_faculty'] = $name_faculty['name'];

			
			$this->view->info_speciality = $info_speciality;
			$this->view->info_couser = $info_couser;
			$this->view->students=$data_students;
			$this->view->perid=$data_period;
			$this->view->subid=$data_subsidiary;
			$this->_helper->layout()->disableLayout();	
						
		} catch (Exception $e) {
			print "Error: print record not nota ".$e->getMessage();
			
		}
	}

	public function recordnotpercentajeAction()
	{
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$curid=base64_decode($this->_getParam('curid'));
			$turno=base64_decode($this->_getParam('turno'));
			
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			$base_periods_course = new Api_Model_DbTable_PeriodsCourses();
			$base_base_subsidiary = new Api_Model_DbTable_Subsidiary();

			$base_impress_course = new Api_Model_DbTable_Impresscourse();

			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'NOTNOTESRECORD'.$perid.$curid.$courseid.$turno.'BACK',
				);
			$base_impress_course->_save($data_impress);

			$where_subid = array(
				'eid'=>$eid,'oid'=>$oid,
				'subid'=>$subid,
				);

			$data_subsidiary = $base_base_subsidiary->_getOne($where_subid);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 

			$data_period_course = $base_periods_course->_getOne($where);

			$tota_students = $base_register_course->_get_total_students_x_course($where);
			$total_approved = $base_register_course->_get_approved($where);
			$total_disapp = $base_register_course->_get_disapproved_x_course($where);
			$total_retired = $base_register_course->_get_retired_x_course($where);
			$total_NSP = $base_register_course->_get_NSP_x_course($where);

			$tota_students1 = intval($tota_students[0]['count']);
			$total_approved1= intval($total_approved[0]['count']);
			$total_disapp1 = $total_disapp[0]['count'];
			$total_retired1 = $total_retired[0]['count'];
			$total_NSP1 = $total_NSP[0]['count'];


			$this->view->tota_students=$tota_students1;
			$this->view->total_approved=$total_approved1;
			$this->view->total_disapp=$total_disapp1;
			$this->view->total_retired=$total_retired1;
			$this->view->total_NSP=$total_NSP1;

			$this->view->perid=$data_period_course;
			$this->view->subid=$data_subsidiary;


			$this->_helper->layout()->disableLayout();
		} catch (Exception $e) {
			print "Error: print record not percentage".$e->getMessage();
		}
	}

	public function registronotnotaAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$curid=base64_decode($this->_getParam('curid'));
			$turno=base64_decode($this->_getParam('turno'));
			$type=base64_decode($this->_getParam('type'));
			
			$base_faculty 	=	new Api_Model_DbTable_Faculty();
			$base_speciality = 	new Api_Model_DbTable_Speciality();
			$base_course = 	new Api_Model_DbTable_Course();
			$base_course_x_teacher = 	new Api_Model_DbTable_Coursexteacher();
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			$base_person =	new Api_Model_DbTable_Person();
			$base_CourseCompetency = new Api_Model_DbTable_CourseCompetency();
			$base_semester = new Api_Model_DbTable_Semester();

			$base_impress_course = new Api_Model_DbTable_Impresscourse();

			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'NOTNOTESREGISTER'.$perid.$curid.$courseid.$turno,
				);
			$base_impress_course->_save($data_impress);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 

			$data_students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);

			if ($type=="C") {
				$data_Competecy = $base_CourseCompetency->_getOne($where);
				$this->view->data_Competecy = $data_Competecy;
			}

			$info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$where_semester = array(
				'eid'=>$eid,'oid'=>$oid,
				'semid'=>$info_couser['semid'],
				);

			$data_semester = $base_semester->_getOne($where_semester);
			$info_couser['name_semester']=$data_semester['name'];

			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];
			$info_speciality = 	$base_speciality->_getOne($where);


			if ($info_speciality['parent'] != "") {
				$where['escid']=$info_speciality['parent'];
				$name_speciality = $base_speciality->_getOne($where);
				$info_speciality['speciality'] = $name_speciality['name'];
			}


			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$info_speciality['name_faculty'] = $name_faculty['name'];

			
			$this->view->info_speciality = $info_speciality;
			$this->view->info_couser = $info_couser;
			$this->view->students=$data_students;
			$this->view->perid=$perid;
			$this->view->type=$type;

			$this->_helper->layout()->disableLayout();
		} catch (Exception $e) {
			print "Error: print register not nota".$e->getMessage();
		}
	}

	public function registernotpercentageAction()
	{
		try {

			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$escid=base64_decode($this->_getParam('escid'));
			$perid=base64_decode($this->_getParam('perid'));
			$subid=base64_decode($this->_getParam('subid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$curid=base64_decode($this->_getParam('curid'));
			$turno=base64_decode($this->_getParam('turno'));
			$type=base64_decode($this->_getParam('type'));
			
			$base_faculty 	=	new Api_Model_DbTable_Faculty();
			$base_speciality = 	new Api_Model_DbTable_Speciality();
			$base_course = 	new Api_Model_DbTable_Course();
			$base_course_x_teacher = 	new Api_Model_DbTable_Coursexteacher();
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			$base_person =	new Api_Model_DbTable_Person();
			$base_CourseCompetency = new Api_Model_DbTable_CourseCompetency();
			$base_semester = new Api_Model_DbTable_Semester();
			$base_impress_course = new Api_Model_DbTable_Impresscourse();

			$data_impress = null;

			$data_impress = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'register'=>$this->sesion->uid,
				'code'=>'NOTNOTESREGISTER'.$perid.$curid.$courseid.$turno.'BACK',
				);
			$base_impress_course->_save($data_impress);

			$where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S'); 

			$data_students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);

			$tota_students = $base_register_course->_get_total_students_x_course($where);
			$total_approved = $base_register_course->_get_approved($where);
			$total_disapp = $base_register_course->_get_disapproved_x_course($where);
			$total_retired = $base_register_course->_get_retired_x_course($where);
			$total_NSP = $base_register_course->_get_NSP_x_course($where);

			$tota_students1 = intval($tota_students[0]['count']);
			$total_approved1= intval($total_approved[0]['count']);
			$total_disapp1 = $total_disapp[0]['count'];
			$total_retired1 = $total_retired[0]['count'];
			$total_NSP1 = $total_NSP[0]['count'];
			
			$info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$where_semester = array(
				'eid'=>$eid,'oid'=>$oid,
				'semid'=>$info_couser['semid'],
				);
			
			$data_semester = $base_semester->_getOne($where_semester);
			$info_couser['name_semester']=$data_semester['name'];

			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];

			$info_speciality = 	$base_speciality->_getOne($where);


			if ($info_speciality['parent'] != "") {
				$where['escid']=$info_speciality['parent'];
				$name_speciality = $base_speciality->_getOne($where);
				$info_speciality['speciality'] = $name_speciality['name'];
			}


			$where ['facid'] = $info_speciality['facid'];
			$name_faculty = $base_faculty->_getOne($where);
			$info_speciality['name_faculty'] = $name_faculty['name'];

			
			$this->view->info_speciality = $info_speciality;
			$this->view->info_couser = $info_couser;
			$this->view->students=$data_students;
			$this->view->perid=$perid;
			$this->view->type=$type;

			$this->view->tota_students=$tota_students1;
			$this->view->total_approved=$total_approved1;
			$this->view->total_disapp=$total_disapp1;
			$this->view->total_retired=$total_retired1;
			$this->view->total_NSP=$total_NSP1;

			$this->_helper->layout()->disableLayout();
			
		} catch (Exception $e) {
			print "Error: print register not percentage".$e->getMessage();
		}
	}

	public function preregisterAction()
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
            $courseid =	trim($params['courseid']);
            $turno =	trim($params['turno']);
            $curid =	trim($params['curid']);
            $escid =	trim($params['escid']);
            $subid =	trim($params['subid']);
            $perid =	trim($params['perid']);
            $state=(isset($params['state']))?trim($params['state']):null;
            // $state =	trim($params['state']);
            $type =	trim($params['type']);

            $this->view->type=$type;
            $where = null;
            $students = null;
            $infouser = null;
            $info_teacher = null;
            $info_speciality = null;

            $where = array(
				'eid' => $eid, 'oid'=>$oid,
				'escid'=> $escid,'subid' => $subid,
				'perid' => $perid,'courseid'=>$courseid,
				'curid' => $curid, 'turno' => $turno,
				'is_main'=>'S');

            $base_faculty 	=	new Api_Model_DbTable_Faculty();
			$base_course = 	new Api_Model_DbTable_Course();
			$base_course_x_teacher = 	new Api_Model_DbTable_Coursexteacher();
			$base_register_course = 	new Api_Model_DbTable_Registrationxcourse();
			$base_person =	new Api_Model_DbTable_Person();
			$base_CourseCompetency = new Api_Model_DbTable_CourseCompetency();
			$base_semester = new Api_Model_DbTable_Semester();


            $students = $base_register_course->_getStudentXcoursesXescidXperiods_sql($where);

            $info_couser = $base_course->_getOne($where);
			$info_couser['turno'] = $turno;

			$dni_teacher = $base_course_x_teacher->_getFilter($where);
			$info_couser['uid']=$dni_teacher[0]['uid'];

			$where1 = array(
				'eid'=>$eid,'oid'=>$oid,
				'pid'=> $dni_teacher[0]['pid']);

			$info_teacher = $base_person->_getOne($where1);
			$info_couser ['name_teacher'] = $info_teacher['last_name0']." ".
											$info_teacher['last_name1'].", ".
											$info_teacher['first_name'];
			
			$wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $dbspeciality = new Api_Model_DbTable_Speciality();
            $speciality = $dbspeciality ->_getOne($wheres);
            $facid=$speciality['facid'];
            $whe=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
            $dataf=$base_faculty->_getOne($whe);
            $namef=strtoupper($dataf['name']);
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $dbspeciality->_getOne($wher);

            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality['name'];
            }
            else{
                $spe['esc']=$speciality['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names." <br> ".$namep;

            if ($speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }

            $dbimpression = new Api_Model_DbTable_Impresscourse();
            
            $uidim=$this->sesion->pid;
            $code='PREREGISTER'.$perid.$curid.$courseid.$turno;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'perid'=>$perid,
                'courseid'=>$courseid,
                'escid'=>$escid,
                'subid'=>$subid,
                'curid'=>$curid,
                'turno'=>$turno,
                'register'=>$uidim,
                'created'=>date('Y-m-d H:i:s'),
				'code'=>$code
                );

            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>$code);
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

			$this->view->info_couser = $info_couser;
			$this->view->students=$students;
			$this->view->perid=$perid;

			$header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
            $header = str_replace("h2", "h3", $header);
            $header = str_replace("h3", "h5", $header);
            $header = str_replace("h4", "h6", $header);
            $header = str_replace("10%", "8%", $header);

            $footer = str_replace("h4", "h5", $footer);
            $footer = str_replace("h5", "h6", $footer);
            
            $this->view->header=$header;
            $this->view->footer=$footer;

			$this->_helper->layout()->disableLayout();	

			
		} catch (Exception $e) {
			print "Error: print pre register ".$e->getMessage();
			
		}
	}


	public function resumenAction()
	{
		$eid=$this->sesion->eid;
        $oid=$this->sesion->oid;

        $perid = base64_decode($this->_getParam("perid"));
        $sedid = base64_decode($this->_getParam("sedid"));
        $escid = base64_decode($this->_getParam("escid"));

        $faculty = $this->sesion->faculty->name;
        $speciality = $this->sesion->speciality->name;
        $fullname = $this->sesion->infouser['fullname'];

        $perid='13B';
        $escid='4SI';
        $subid='1901';

		$this->view->perid=$perid;
		$this->view->subid=$subid;
		$this->view->escid=$escid;


        if ($perid=="" || $escid=="" || $subid=="") return false;
        $this->view->escid = $escid;
		$this->view->subid = $subid;

		$where = array('eid' => $eid, 'oid'=>$oid,				
				'perid' => $perid);


		$base_periods = new Api_Model_DbTable_Periods();
		$period=$base_periods->_getOnePeriod($where);
		//print_r($period);break;
        if ($period) $this->view->infoperiodo= $period;

        $where_periods = array('eid' => $eid, 'oid'=>$oid,'perid' => $perid, 'escid'=>$escid, 'subid'=>$subid);
        ///print_r($where2);break;

        $base_periods_courses = new Api_Model_DbTable_PeriodsCourses();
        $bs_courses=$base_periods_courses->_getFilter($where_periods,$data='');

        //print_r($bs_courses);break;

       
        foreach ($bs_courses as $course)
        {
        	//print_r($course['courseid']);//break;

        	$courseid=$course['courseid'];
        	$curid=$course['curid'];
        	$turno=$course['turno'];

        	$where_reg = array('eid' => $eid, 'oid'=>$oid,'courseid' => $courseid,'curid' => $curid,'perid' => $perid, 'escid'=>$escid, 'subid'=>$subid,'turno'=>$turno);

        	$mat= new Api_Model_DbTable_Registrationxcourse();
        	$numatri=$mat->_getCountRegisterCourse($where_reg);

        	//print_r($numatri);

        	$where_course= array('eid' =>$eid,'oid'=>$oid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid);



        	$ncourse= new Api_Model_DbTable_Course();
        	$dcourse=$ncourse->_getOne($where_course);
        	$tmp=$course;

        	if($dcourse) 
        	$tmp['name']=$dcourse['name'];
        	$tmp['num_mat']=$numatri;
			$lcourses[]=$tmp;

        }       
            $this->view->listacursos = $lcourses;

            //print_r($lcourses);
	}

	public function teacherxschoolAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid= $this->_getParam("escid");
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['rid']='DC';
            $where['state']='A';
            $r = new Api_Model_DbTable_Users();
            $regdoc = $r->_getUsersXEscidXRidXState($where);
            $this->view->docentes=$regdoc;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function updatedatacourseAction(){
    	try{
    		$this->_helper->layout()->disableLayout();
    		$eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $formData = $this->getRequest()->getPost();

            if($formData['opcion']==0){
            	$where = array('eid'=>$eid, 'oid'=>$oid,'perid'=>$formData['perid'],'escid'=>$formData['escid'],
            				'curid'=>$formData['curid'],'subid'=>$formData['subid'],'courseid'=>$formData['courseid'],
            				'turno'=>$formData['turno'],'uid'=>$formData['uid'],'pid'=>$formData['pid'],'is_main'=>'S',
            				'state'=>'A');
            	$coursexteacher = new Api_Model_DbTable_Coursexteacher();
	            $updatedata = $coursexteacher ->_save($where);
	            $this->view->resul=$updatedata;	
    		}else{
    			$where = array('eid'=>$eid, 'oid'=>$oid,'perid'=>$formData['perid'],'escid'=>$formData['escid'],
            				'curid'=>$formData['curid'],'subid'=>$formData['subid'],'courseid'=>$formData['courseid'],
            				'turno'=>$formData['turno']);
            	$data = array('uid'=>$formData['uid'],'pid'=>$formData['pid']);

    			$coursexteacher = new Api_Model_DbTable_Coursexteacher();
	            $updatedata = $coursexteacher ->_updateXcourse($data,$where);
	            $this->view->resul=$updatedata;	
    		}
     
    	} catch (Exception $e){
    		print 'Error: '.$e->getMessage();
    	}
    }
    public function deletecourseAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$formData = $this->getRequest()->getPost();
			$formData['eid'] = $this->sesion->eid;
			$formData['oid'] = $this->sesion->oid;
			//coursexteacher
			$coursexteacher = new Api_Model_DbTable_Coursexteacher();
			$deletecoursexteacher = $coursexteacher ->_deleteadm($formData);
			$this->view->resp=$deletecoursexteacher;
			//periodsxcourse
			$periodscourses= new Api_Model_DbTable_PeriodsCourses();
			$deletepxc = $periodscourses ->_delete($formData);	

		} catch (Excoption $e){
			print "Error: ".$e->getMessage();
		}
		
	}

	public function updatestatecourseAction(){
    	try{
    		$this->_helper->layout()->disableLayout();
    		$eid = $this->sesion->eid;
    		$oid = $this->sesion->oid;
    		$form_Data = $this->getRequest()->getPost();

    		$where['eid'] = $eid;
    		$where['oid'] = $oid;
    		$where['escid'] = $form_Data['escid'];
    		$where['subid'] = $form_Data['subid'];
    		$where['curid'] = $form_Data['curid'];
    		$where['perid'] = $form_Data['perid'];
    		$where['courseid'] = $form_Data['courseid'];
    		$where['turno'] = $form_Data['turno'];

    		$data['closure_date'] = $form_Data['closure_date'];
    		$data['state_record'] = $form_Data['state_record'];
    		$data['state'] = $form_Data['state'];

    		$updatedata = new Api_Model_DbTable_PeriodsCourses();
	    	$updata = $updatedata->_update($data,$where);
	    	$this->view->updata=$updata;
    	} catch(Exception $e){
    		print 'Error: '.$e->getMessage();
    	}
    }

    public function principalteacherAction(){
    	try{
    		$this->_helper->layout()->disableLayout();
    		$eid = $this->sesion->eid;
    		$oid = $this->sesion->oid;
    		$data = $this->getRequest()->getPost();

    		$coursexteacher = new Api_Model_DbTable_Coursexteacher;
    		if($data['pidac']=="" && $data['uidac']==""){
    			$where = array('eid'=>$eid, 'oid'=>$oid,'escid'=>$data['escid'],'subid'=>$data['subid'],
    						'courseid'=>$data['courseid'],'curid'=>$data['curid'],'turno'=>$data['turno'],
    						'perid'=>$data['perid'],'uid'=>$data['uid'],'pid'=>$data['pid']);
    			$mod = array('is_main'=>'S','state'=>'A');
    			$updatedata = $coursexteacher->_update($mod,$where);
    		}else{
    			$where = array('eid'=>$eid, 'oid'=>$oid,'escid'=>$data['escid'],'subid'=>$data['subid'],
    						'courseid'=>$data['courseid'],'curid'=>$data['curid'],'turno'=>$data['turno'],
    						'perid'=>$data['perid'],'uid'=>$data['uidac'],'pid'=>$data['pidac']);
    			$mod = array('is_main'=>'N','state'=>'P');
    			$updatedata = $coursexteacher->_update($mod,$where);
    			$where2 = array('eid'=>$eid, 'oid'=>$oid,'escid'=>$data['escid'],'subid'=>$data['subid'],
    						'courseid'=>$data['courseid'],'curid'=>$data['curid'],'turno'=>$data['turno'],
    						'perid'=>$data['perid'],'uid'=>$data['uid'],'pid'=>$data['pid']);
    			$mod2 = array('is_main'=>'S','state'=>'A');
    			$updatedata2 = $coursexteacher->_update($mod2,$where2);
    		}

    	}catch(Exception $e){
    		print 'Error: '.$e->getMessage();
    	}
    }
	
}

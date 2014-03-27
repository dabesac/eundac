<?php

class Rcentral_EntrantController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;
    	$this->sesion->period->perid = '14A';
    }
    public function indexAction(){	
    	//DataBases
    	$facultyDb = new Api_Model_DbTable_Faculty();

		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;
		$subid = $this->sesion->subid;

    	$perid = $this->sesion->period->perid;
    	$this->view->perid = $perid;

    	$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'state' => 'A');

    	$attrib = array('name', 'facid');

    	$facultiesBefore = $facultyDb->_getFilter($where, $attrib);

    	$c = 0;
    	foreach ($facultiesBefore as $faculty) {
    		if ($faculty['facid'] != 'TODO') {
    			$faculties[$c]['facid'] = $faculty['facid'];
    			$faculties[$c]['name'] = $faculty['name'];
    			$c++;
    		}
    	}
    	$this->view->faculties = $faculties;
    }

    public function listschoolsAction(){
    	$this->_helper->layout()->disableLayout();

    	$schoolDb = new Api_Model_DbTable_Speciality();

    	$facid = $this->_getParam('facid');
		
		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;
		$subid = $this->sesion->subid;

    	$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'facid' => $facid,
						'subid' => $subid,
						'parent' => '',
						'state' => 'A' );

    	$attrib = array('name', 'escid');

    	$schools = $schoolDb->_getFilter($where, $attrib);
    	$this->view->schools = $schools;
    }

    public function listspecialitiesAction(){
    	$this->_helper->layout()->disableLayout();

    	$specialityDb = new Api_Model_DbTable_Speciality();

    	$escid = $this->_getParam('escid');
		
		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;
		$subid = $this->sesion->subid;

    	$where = array(	'eid'    => $eid,
						'oid'    => $oid,
						'subid'  => $subid,
						'parent' => $escid,
						'state'  => 'A' );

    	$attrib = array('name', 'escid');

    	$specialities = $specialityDb->_getFilter($where, $attrib);
    	$this->view->specialities = $specialities;
    }

    public function liststudentsAction(){
    	$this->_helper->layout()->disableLayout();

    	//DataBases
    	$userDb = new Api_Model_DbTable_Users();
    	$registerDb = new Api_Model_DbTable_Registration();
    	$specialityDb = new Api_Model_DbTable_Speciality();
    	//_______________________________________________

    	$facid = $this->_getParam('facid');


		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;
		$subid = $this->sesion->subid;
		$perid = $this->sesion->period->perid;
		
		$where = array(	'eid'            => $eid,
						'oid'            => $oid,
						'left(escid, 1)' => $facid,
						'subid'          => $subid,
						'state'          => 'A',
						'left(uid, 2)'   => $perid['0'].$perid['1'] );

		$attrib = array('uid', 'pid', 'escid');
		$order = array('escid');

		$students = $userDb->_getFilter($where, $attrib, $order);

		$c = 0;
		foreach ($students as $student) {
			//Estado de Matricula
			$attrib = array('state');
			$where = array(	'eid'   => $eid,
							'oid'   => $oid,
							'subid' => $subid,
							'escid' => $student['escid'],
							'uid'   => $student['uid'],
							'pid'   => $student['pid'],
							'perid' => $perid );
			$checkStudent = $registerDb->_getFilter($where, $attrib);
			if ($checkStudent[0]['state'] == 'I' or !$checkStudent[0]['state']) {
				$studentState[$c]['state'] = 'Ingresantes';
			}elseif ($checkStudent[0]['state'] == 'M'){
				$studentState[$c]['state'] = 'Matriculados';
			}elseif ($checkStudent[0]['state'] == 'O'){
				$studentState[$c]['state'] = 'Observados';
			}elseif ($checkStudent[0]['state'] == 'R'){
				$studentState[$c]['state'] = 'Reservados';
			}

			$where  = array('eid'   => $eid,
							'oid'   => $oid,
							'subid' => $subid,
							'escid' => $student['escid'],
							'uid'   => $student['uid'],
							'pid'   => $student['pid'] );

			$studentEntrant[$c] = $userDb->_getInfoUser($where);

			$attrib = array('name', 'escid', 'subid');
			$where = array(	'eid'   => $eid,
							'oid'   => $oid,
							'subid' => $subid,
							'escid' => $student['escid'] );

			$studentSpeciality[$c] = $specialityDb->_getFilter($where, $attrib);

			$c++;
		}

		$this->view->studentEntrant    = $studentEntrant;
		$this->view->studentState      = $studentState;
		$this->view->studentSpeciality = $studentSpeciality;
	}

	public function detailregisterAction(){
		$this->_helper->layout()->disableLayout();

		//DataBases
		$specialityDb   = new Api_Model_DbTable_Speciality();
		$userDb         = new Api_Model_DbTable_Users();
		$paymentDb      = new Api_Model_DbTable_Payments();
		$academicDb     = new Api_Model_DbTable_Academicrecord();
		$rateDb         = new Api_Model_DbTable_Rates();
		$realtionshipDb = new Api_Model_DbTable_Relationship();
		$academicDb     = new Api_Model_DbTable_Academicrecord();
		$statisticDb    = new Api_Model_DbTable_Statistics();
		$interestDb     = new Api_Model_DbTable_Interes();
		//________________________________________________

		$escid = base64_decode($this->_getParam('escid'));
		$uid   = base64_decode($this->_getParam('uid'));
		$pid   = base64_decode($this->_getParam('pid'));

		//print_r($pid);
		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;
		$subid = $this->sesion->subid;
		$perid = $this->sesion->period->perid;
		$dataStudent = array(	'uid'   => $uid,
								'pid'   => $pid,
								'subid' => $subid,
								'escid' => $escid,
								'perid' => $perid );
        $this->view->dataStudent = $dataStudent;

		//Periodo
		$this->view->perid = $perid;
		//Datos del Usuario
		$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'subid' => $subid,
						'escid' => $escid,
						'uid'   => $uid,
						'pid'   => $pid );

		$student = $userDb->_getInfoUser($where);
		$this->view->student = $student;

		//Datos de Colegio
		$where = array(	'eid'   => $eid,
						'pid'   => $pid );
		$academic = $academicDb->_getFilter($where);
		$this->view->academic = $academic;

		//Datos de la Facultad y Escuela
		$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid );

		$infoSpeciality = $specialityDb->_getFacspeciality($where);
		$this->view->infoSpeciality = $infoSpeciality;

		//Información de Pago
        $where = array( 'eid'   => $eid, 
						'oid'   => $oid, 
						'uid'   => $uid, 
						'pid'   => $pid,
						'escid' => $escid, 
						'subid' => $subid,
						'perid' => $perid );


        $attrib = array('date_payment', 'amount', 'ratid');
        $paymentData = $paymentDb->_getFilter($where, $attrib);
        if ($paymentData and $paymentData[0]['amount'] != 0) {
			$paymentData[0]['date_payment'] = substr($paymentData[0]['date_payment'], 0, 10);
			$paymentData[0]['date_fix']     = date('d-m-Y', strtotime($paymentData[0]['date_payment']));
	       	$paymentData[0]['date_payment'] = $paymentData[0]['date_payment'];
	        $this->view->paymentData = $paymentData;
			//Tipo de Pago
			$where = array(	'eid'   => $eid,
							'oid'   => $oid,
							'ratid' => $paymentData[0]['ratid'], 
							'perid' => $perid );
			$rate = $rateDb->_getFilter($where);
			$this->view->rate = $rate;
        }


		//$Comparar fechas y pagos
		$paymentDate = strtotime($paymentData[0]['date_payment']);
		$paymentAmount = $paymentData[0]['amount'];
		$paymentNormal = strtotime($rate[0]['f_fin_tnd']);
		$paymentIncrement1 = strtotime($rate[0]['f_fin_ti1']);
		$paymentIncrement2 = strtotime($rate[0]['f_fin_ti2']);
		$paymentIncrement3 = strtotime($rate[0]['f_fin_ti3']);

		
		if ($paymentDate <= $paymentNormal) {
			$paymentDateData['tiempo'] = 'yes';
			$paymentDateData['cantidad'] = $rate[0]['t_normal'];
		}else if($paymentDate <= $paymentIncrement1){
			$paymentDateData['tiempo'] = 'no';
			$paymentDateData['porcentaje'] = $rate[0]['v_t_incremento1'];
			$paymentDateData['cantidad'] = $rate[0]['t_incremento1'];
		}else if($paymentDate <= $paymentIncrement2){
			$paymentDateData['tiempo'] = 'no';
			$paymentDateData['porcentaje'] = $rate[0]['v_t_incremento2'];
			$paymentDateData['cantidad'] = $rate[0]['t_incremento2'];
		}else if($paymentDate <= $paymentIncrement3){
			$paymentDateData['tiempo'] = 'no';
			$paymentDateData['porcentaje'] = $rate[0]['v_t_incremento3'];
			$paymentDateData['cantidad'] = $rate[0]['t_incremento3'];
		}

		if ($paymentAmount >= $paymentDateData['cantidad']) {
			$paymentDateData['pago'] = 'yes';
		}else {
			$paymentDateData['pago'] = 'no';
		}
		$this->view->paymentDateData = $paymentDateData;

		
		//Relleno Datos del Perfil
        	$dataProfile['registerValidate'] = 'yes	';
        	//Family
	        $where = array(	'eid'   => $eid,
							'pid'   => $pid );
	        $relationship = $realtionshipDb->_getFilter($where);
	        if ($relationship) {
	        	$dataProfile['family'] = 'yes';
	        }else {
	        	$dataProfile['family'] = 'no';
	        	$dataProfile['registerValidate'] = 'no';
	        }

	        //Datos Academicos
	        $academic = $academicDb->_getFilter($where);
         	if ($academic) {
	        	$dataProfile['academic'] = 'yes';
	        }else {
	        	$dataProfile['academic'] = 'no';
	        	$dataProfile['registerValidate'] = 'no';
	        }

         	//Datos de Interes
	        $interest = $interestDb->_getFilter($where);
         	if ($interest) {
	        	$dataProfile['interest'] = 'yes';
	        }else {
	        	$dataProfile['interest'] = 'no';
	        	$dataProfile['registerValidate'] = 'no';
	        }

	        //Datos Estadisticos
	        $where = array(	'eid'   => $eid,
							'oid'   => $oid,
							'escid' => $escid,
							'subid' => $subid,
							'pid'   => $pid,
							'uid'   => $uid );
	        $statistic = $statisticDb->_getFilter($where);
         	if ($statistic) {
	        	$dataProfile['statistic'] = 'yes';
	        }else {
	        	$dataProfile['statistic'] = 'no';
	        	$dataProfile['registerValidate'] = 'no';
	        }


	    $this->view->dataProfile = $dataProfile;

	}

	public function coursespendingAction(){
		$this->_helper->layout()->disableLayout();
		//DataBases
		$curriculaDb       = new Api_Model_DbTable_Studentxcurricula();
		$registerxCourseDb = new Api_Model_DbTable_Registrationxcourse();
		$registerDb 	   = new Api_Model_DbTable_Registration();
		$courseDb          = new Api_Model_DbTable_Course();
		$coursexTeacherDb  = new Api_Model_DbTable_Coursexteacher();
        //________________________________________________________
        $pid   = base64_decode($this->_getParam('pid'));
        $uid   = base64_decode($this->_getParam('uid'));
        $escid = base64_decode($this->_getParam('escid'));
        $subid = base64_decode($this->_getParam('subid'));

        $eid   = $this->sesion->eid;    
        $oid   = $this->sesion->oid;
        $perid = $this->sesion->period->perid;

        

     	//Curricula
        $where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid,
						'subid' => $subid,
						'pid'   => $pid,
						'uid'   => $uid );

        $curricula = $curriculaDb->_getOne($where);
        $curid = $curricula['curid'];

        $dataStudent = array(	'uid'   => $uid,
								'pid'   => $pid,
								'subid' => $subid,
								'escid' => $escid,
								'perid' => $perid,
								'curid' => $curid );
        $this->view->dataStudent = $dataStudent;

        //Estado del Alumno
        $attrib = array('state', 'courseid', 'curid', 'turno');
        $where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid,
						'subid' => $subid,
						'pid'   => $pid,
						'uid' 	=> $uid,
						'perid' => $perid );

        $data = $registerxCourseDb->_getFilter($where, $attrib);
        if ($data[0]['state'] and $data[0]['state'] <> 'I') {
        	$this->view->stateStudent = $data[0]['state'];
        	$this->view->exist = 'Yes';
        	$c = 0;
        	foreach ($data as $course) {
        		//Nombre Cursos
        		$attrib = array('name');
        		$where = array(	'eid'      => $eid, 
								'oid'      => $oid,
								'escid'    => $escid,
								'subid'    => $subid,
								'courseid' => $course['courseid'],
								'curid'    => $course['curid'] );

        		$coursesName[$c] = $courseDb->_getFilter($where, $attrib);

        		//Nombre Profes
        		$attrib = array('pid', 'uid');
        		$where = array(	'eid'      => $eid, 
								'oid'      => $oid,
								'escid'    => $escid,
								'subid'    => $subid,
								'perid'    => $perid,
								'turno'    => $course['turno'],
								'courseid' => $course['courseid'],
								'curid'    => $course['curid'] );

        		$teacher = $coursexTeacherDb->_getFilter($where, $attrib);

        		$where = array(	'eid'      => $eid, 
								'oid'      => $oid,
								'escid'    => $escid,
								'subid'    => $subid,
								'pid' => $teacher[0]['pid'], 
								'uid' => $teacher[0]['uid'], );

        		$coursesTeachers[$c] = $coursexTeacherDb->_getinfoTeacher($where);

        		$teachers = $coursexTeacherDb->_getFilter($where, $attrib);

        		$c++;
        	}
        	$this->view->data = $data;
        	$this->view->coursesName = $coursesName;
        	$this->view->coursesTeachers = $coursesTeachers;
        }elseif ($courses[0]['state'] == 'I' or !$courses[0]['state']){
        	$this->view->stateStudent = 'I';
	       
	       	$request = array( 	'eid'   => base64_encode($eid),
								'oid'   => base64_encode($oid),
								'perid' => base64_encode($perid),
								'pid'   => base64_encode($pid),
								'uid'   => base64_encode($uid),
								'escid' => base64_encode($escid),
								'subid' => base64_encode($subid),
								'curid' => base64_encode($curid) );

	        $server = new Eundac_Connect_Api('pendig_cachimbos', $request);
	        $data = $server->connectAuth();
	        $this->view->data = $data;

	        //Total de Creditos
	        $totalCredits = 0;
	        $courseid = 0;
	        foreach ($data as $course) {
	        	if ($course['courseid'] != $courseid) {
	        		$totalCredits = $totalCredits + $course['credits'];
	        		$courseid = $course['courseid'];
	        	}
	        }
	        $this->view->totalCredits = $totalCredits;
        }
        $c = 0;
        foreach ($data as $course) {
        	$attrib = array('state', 'courseid', 'turno');
        	$where = array(	'eid'      => $eid,
							'oid'      => $oid,
							'escid'    => $escid,
							'subid'    => $subid,
							'perid'    => $perid,
							'courseid' => $course['courseid'],
							'curid'    => $course['curid'],
							'turno'    => $course['turno'],
							'state'    => 'M' );
        	$students = $registerxCourseDb->_getFilter($where, $attrib);
        	if ($students) {
        		$cantStudents[$c]['cantidad'] = count($students);
        	}else {
        		$cantStudents[$c]['cantidad'] = '0';
        	}
        	$cantStudents[$c]['courseid'] = $course['courseid'];
        	$cantStudents[$c]['turno'] = $course['turno'];
        	$c++;
        }
        $this->view->cantStudents = $cantStudents;

	}

	public function validateregisterAction(){
		$this->_helper->layout()->disableLayout();

		//DataBases
		$registerDb = new Api_Model_DbTable_Registration();
		$registerxCourseDb = new Api_Model_DbTable_Registrationxcourse();
		//__________________________________
		$eid = $this->sesion->eid;
		$oid = $this->sesion->oid;
		$uid = $this->sesion->uid;
		$pid = $this->sesion->pid;

		$data = $this->getRequest()->getPost();

		if ($data['whySend'] == 'M') {
			$state = 'M';
		}else if ($data['whySend'] == 'O'){
			$state = 'O';
		}else if ($data['whySend'] == 'R'){
			$state = 'R';
		}else if ($data['whySend'] == 'E'){
			$state = 'E';
		}

		if (!$data['exist'] and $state != 'E') {
			$dataSaveRegister = array(	'eid'           => $eid,
										'oid'           => $oid,
										'regid'         => $data['uid'].$data['perid'],
										'pid'           => $data['pid'],
										'uid'           => $data['uid'],
										'escid'         => $data['escid'],
										'subid'         => $data['subid'],
										'perid'         => $data['perid'],
										'semid'         => '1',
										'date_register' => date('Y-m-d h:m:s'),
										'register'      => $uid,
										'created'       => date('Y-m-d h:m:s'), 
										'updated'       => date('Y-m-d h:m:s'),
										'modified'       => $uid, 
										'state'         => $state,
										'count'         => '0' ); 

			if ($registerDb->_save($dataSaveRegister)) {
				$sizeCourses = $data['CantidadCursos'];
				$interruptor = 0;
				for ($i=1; $i <= $sizeCourses; $i++) { 
					$courseid = substr($data['Course'.$i], 3);
					$turno    = substr($data['Course'.$i], 0 , 1);
					$dataSaveCourse = array(	'eid'      => $eid,
												'oid'      => $oid,
												'perid'    => $data['perid'],
												'courseid' => trim($courseid),
												'escid'    => $data['escid'],
												'subid'    => $data['subid'],
												'curid'    => $data['curid'],
												'turno'    => $turno,
												'regid'    => $data['uid'].$data['perid'],
												'pid'      => $data['pid'],
												'uid'      => $data['uid'],
												'register' => $uid,
												'approved' => $uid,
												'updated'  => date('Y-m-d h:m:s'),
												'modified' => $uid, 
												'created'  => date('Y-m-d h:m:s'), 
												'state'    => $state ); 

					if (!$registerxCourseDb->_save($dataSaveCourse)) {
						$interruptor = 1;
					}
				}
				if ($interruptor == 0) {
					echo 'true';
				}else{
					echo 'false';
				}
			}else{
				echo "false";
			}
		}else if ($state != 'E'){
			$pk = array('eid'   => $eid,
						'oid'   => $oid,
						'regid' => $data['uid'].$data['perid'],
						'pid'   => $data['pid'],
						'uid'   => $data['uid'],
						'escid' => $data['escid'],
						'subid' => $data['subid'],
						'perid' => $data['perid'],
						'state' => $data['stateStudent'] ); 

			$dataUpdateRegister = array( 	'modified' => $uid,
											'updated'  => date('Y-m-d h:m:s'),
											'state'    => $state );

			if ($registerDb->_update($dataUpdateRegister, $pk)) {
				if ($registerxCourseDb->_updatestateregister($dataUpdateRegister, $pk)) {
					echo 'true';
				}else {
					echo 'false';
				}
			}else{
				echo "false";
			}

		}else if ($state == 'E'){
			$pk = array('eid'   => $eid,
						'oid'   => $oid,
						'regid' => $data['uid'].$data['perid'],
						'pid'   => $data['pid'],
						'uid'   => $data['uid'],
						'escid' => $data['escid'],
						'subid' => $data['subid'],
						'perid' => $data['perid'],
						'state' => $data['stateStudent'] ); 

			$dataToDelete =array(	'modified' => $uid,
                        			'updated'  => date('Y-m-d h:m:s') );

			if ($registerxCourseDb->_updatestateregister($dataToDelete, $pk)) {
	            if ($registerDb->_update($dataToDelete, $pk)){
	                $registerDb->_delete($pk);
	                echo 'true';
	            }else{
	                echo 'false';
	            }
	        }else{
	            echo 'false';
	        }
		}
		//If de Existencia de matricula


	}

}
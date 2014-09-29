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
        //DataBases
        $facultyDb = new Api_Model_DbTable_Faculty();
        $schooldDb = new Api_Model_DbTable_Speciality();

        $period = $this->sesion->period->perid;
        $rid    = $this->sesion->rid;
        $eid    = $this->sesion->eid;
        $oid    = $this->sesion->oid;
        $facid  = $this->sesion->faculty->facid;
        $subid  = $this->sesion->subid;
        $escid  = $this->sesion->escid;

        $dataVista['period']    = $period;
        $dataVista['rid']       = $rid;
        $dataVista['escid']     = $escid;
        $dataVista['subid']     = $subid;
        $dataVista['dataEscid'] = base64_encode($escid.'-'.$subid);

        if ($rid == 'RF') {
            $dataVista['dataEscid']  = '';

            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'facid' => $facid,
                            'subid' => $subid,
                            'state' => 'A',
                            'parent' => '' );
            $attrib = array('name', 'escid', 'subid', 'parent');
            $preDataSchool = $schooldDb->_getFilter($where, $attrib);
            foreach ($preDataSchool as $c => $school) {
                $dataSchool[$c]['escid'] = $school['escid'];
                $dataSchool[$c]['subid'] = $school['subid'];
                $dataSchool[$c]['name']  = $school['name'];
            }
            $this->view->dataSchool = $dataSchool;
        }elseif ($rid == 'RC' or $rid == 'ES' or $rid == 'CU' or $rid == 'VA'){
            $dataVista['dataEscid']  = '';

            $preDataFaculty = $facultyDb->_getAll();
            foreach ($preDataFaculty as $c => $faculty) {
                if ($faculty['state'] == 'A' and $faculty['facid'] != 'TODO') {
                    $dataFaculty[$c]['facid'] = $faculty['facid'];
                    $dataFaculty[$c]['name']  = $faculty['name'];
                }
            }
            $this->view->dataFaculty = $dataFaculty;
        }
        $this->view->dataVista = $dataVista;
 	}

 	public function listperiodsAction(){
 		try {
 			$this->_helper->layout()->disableLayout();

 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;

 			$anio = $this->_getParam('anio');
 			$anio = substr($anio, -2);

 			$periodsDb = new Api_Model_DbTable_Periods();
 			$where = array('eid'=>$eid, 'oid'=>$oid, 'year'=>$anio);
 			//print_r($where);
 			$periods = $periodsDb->_getPeriodsxYears($where);
            $this->view->periods = $periods;

 		} catch (Exception $e) {
 			print 'Error '.$e->getMessage();
 		}
 	}

 	public function listteacherAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $personDb         = new Api_Model_DbTable_Person();
        $userDb           = new Api_Model_DbTable_Users();
        $courseDb         = new Api_Model_DbTable_Course();
        $syllabusDb       = new Api_Model_DbTable_Syllabus();
        $specialityDb     = new Api_Model_DbTable_Speciality();
        $courseTeacherDb  = new Api_Model_DbTable_Coursexteacher();
        $coursePeriodsDb  = new Api_Model_DbTable_PeriodsCourses();
        $academicReportDb = new Api_Model_DbTable_Addreportacadadm();

        $data      = $this->_getParam('data');
        $data      = explode('-', $data);
        $perid     = base64_decode($data[0]);
        $dataEscid = base64_decode($data[1]);
        $dataEscid = explode('|', $dataEscid);
        $escid     = $dataEscid[0];
        $subid     = $dataEscid[1];

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $rid   = $this->sesion->rid;


        $dataGlobal['perid'] = $perid;
        $dataGlobal['escid'] = $escid;
        $dataGlobal['subid'] = $subid;

        //Verificar si tiene especialidades
        $dataSpecialities = array();

        $where = array( 'eid'    => $eid,
                        'oid'    => $oid,
                        'parent' => $escid,
                        'subid'  => $subid,
                        'state'  => 'A' );
        $attrib = '';
        $order = array('escid ASC');
        $dataSpecialities = $specialityDb->_getFilter($where, $attrib, $order);

        if ($dataSpecialities) {
            $tamEscid = strlen($escid);
            $where = array( 'eid'                        => $eid,
                            'oid'                        => $oid,
                            'left(escid, '.$tamEscid.')' => $escid,
                            'subid'                      => $subid,
                            'perid'                      => $perid,
                            'is_main'                    => 'S' );
            $tieneEspecialidades = 'yes';
        }else{
            $where = array( 'eid'     => $eid,
                            'oid'     => $oid,
                            'escid'   => $escid,
                            'subid'   => $subid,
                            'perid'   => $perid,
                            'is_main' => 'S' );
            $tieneEspecialidades = 'no';
        }

        $this->view->dataSpecialities = $dataSpecialities;

        //Data Principal

        $order = array('escid ASC', 'pid ASC');
        $attrib = array('pid', 'uid', 'escid', 'subid');
        $preDataTeachers = $courseTeacherDb->_getFilter($where, $attrib, $order);

        //Profesores registrados y dictando en ese periodo
        $cTeachers = 0;
        $pidTeacher = 0;
        if ($preDataTeachers) {
        foreach ($preDataTeachers as $teacher) {
            if ($teacher['pid'] != $pidTeacher) {
                $where = array( 'eid' => $eid,
                                'pid' => $teacher['pid'] );
                $attrib = array('last_name0', 'last_name1', 'first_name');
                $dataPerson = $personDb->_getFilter($where, $attrib);

                $dataDocente[$cTeachers]['fullName'] = $dataPerson[0]['last_name0'].' '.$dataPerson[0]['last_name1'].' '.$dataPerson[0]['first_name'];
                $dataDocente[$cTeachers]['pid']      = $teacher['pid'];
                $dataDocente[$cTeachers]['uid']      = $teacher['uid'];
                $dataDocente[$cTeachers]['escid']    = $teacher['escid'];
                $dataDocente[$cTeachers]['subid']    = $teacher['subid'];
                $dataDocente[$cTeachers]['perid']    = $perid;

                if ($tieneEspecialidades == 'yes') {
                    $dataDocente[$cTeachers]['espid'] = $teacher['escid'];
                }else{
                    $dataDocente[$cTeachers]['espid'] = '-';
                }

                //nombre de la Escuela
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'escid' => $teacher['escid'],
                                'subid' => $teacher['subid'] );

                $attrib = array('name');
                $nameSchool = $specialityDb->_getFilter($where, $attrib);
                $dataDocente[$cTeachers]['nameSchool'] = $nameSchool[0]['name'];

                //verificar a que escuela pertenece
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'pid'   => $teacher['pid'],
                                'uid'   => $teacher['uid'] );
                $attrib = array('escid');
                $preDataUser = $userDb->_getFilter($where, $attrib);

                $dataDocente[$cTeachers]['escidOrigen'] = $preDataUser[0]['escid'];

                //verificar el Informe Academico
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'escid' => $preDataUser[0]['escid'],
                                'subid' => $teacher['subid'],
                                'uid'   => $teacher['uid'],
                                'pid'   => $teacher['pid'],
                                'perid' => $perid );
                $attrib = array('state');
                $preDataReport = $academicReportDb->_getFilter($where, $attrib);

                $dataDocente[$cTeachers]['stateReport'] = 'no';
                if ($preDataReport[0]['state'] and $preDataReport[0]['state'] == 'C') {
                    $dataDocente[$cTeachers]['stateReport'] = 'yes';
                }


                $pidTeacher = $teacher['pid'];
                $cTeachers++;
            }
        }

        //Ordenar por nombre
        foreach ($dataDocente as $c => $docente) {
            $nombreDocente[$c] = $docente['fullName'];
        }
        array_multisort($dataDocente, SORT_ASC, $nombreDocente);

        //Cursos de esos profesores
        foreach ($dataDocente as $cTeachers => $teacher) {
            if ($tieneEspecialidades == 'yes') {
                $escidSearch = $teacher['espid'];
            }else{
                $escidSearch = $teacher['escid'];
            }
            $where = array( 'eid'     => $eid,
                            'oid'     => $oid,
                            'pid'     => $teacher['pid'],
                            'uid'     => $teacher['uid'],
                            'perid'   => $perid,
                            'is_main' => 'S',
                            'escid'   => $escidSearch,
                            'subid'   => $teacher['subid'] );

            $dataCourse = $courseTeacherDb->_getFilter($where);
            foreach ($dataCourse as $cCourses => $course) {
                $where = array( 'eid'      => $eid,
                                'oid'      => $oid,
                                'courseid' => $course['courseid'],
                                'curid'    => $course['curid'] );
                $attrib = array('name');
                $nameCourse = $courseDb->_getFilter($where, $attrib);
                $dataDocente[$cTeachers]['courses'][$cCourses] = array( 'courseid' => $course['courseid'],
                                                                        'curid'    => $course['curid'],
                                                                        'turno'    => $course['turno'],
                                                                        'escid'    => $course['escid'],
                                                                        'subid'    => $course['subid'],
                                                                        'name'     => $nameCourse[0]['name'] );

                //Verificar si lleno Silabo
                $dataDocente[$cTeachers]['courses'][$cCourses]['stateSyllabus'] = 0;

                $where = array(
                                'eid'      => $eid,
                                'oid'      => $oid,
                                'courseid' => $course['courseid'],
                                'curid'    => $course['curid'],
                                'turno'    => $course['turno'],
                                'escid'    => $course['escid'],
                                'subid'    => $course['subid'],
                                'perid'    => $perid );

                $attrib = array('state');
                $stateSyllabus = $syllabusDb->_getFilter($where, $attrib);
                if ($stateSyllabus and $stateSyllabus[0]['state'] == 'C') {
                    $dataDocente[$cTeachers]['courses'][$cCourses]['stateSyllabus'] = 1;
                }

                //Verificar Acta de los parciales
                $attrib = array('state_record', 'state');
                $stateActas = $coursePeriodsDb->_getFilter($where, $attrib);


                $dataDocente[$cTeachers]['courses'][$cCourses]['statePrimerParcial'] = 0;
                if ($stateActas and $stateActas[0]['state'] == 'P') {
                    $dataDocente[$cTeachers]['courses'][$cCourses]['statePrimerParcial'] = 1;
                }

                $dataDocente[$cTeachers]['courses'][$cCourses]['stateSecondParcial'] = $stateActas[0]['state'];
                if ($stateActas and $stateActas[0]['state'] == 'C') {
                    $dataDocente[$cTeachers]['courses'][$cCourses]['statePrimerParcial'] = 1;
                    $dataDocente[$cTeachers]['courses'][$cCourses]['stateSecondParcial'] = 1;
                }


            }
            $dataDocente[$cTeachers]['cantCursos'] = $cCourses + 1;
        }

        }//Fin If Principal
        //print_r($dataDocente);
        $this->view->dataGlobal  = $dataGlobal;
        $this->view->dataDocente = $dataDocente;
    }

    public function shownotasAction(){
        $this->_helper->layout()->disableLayout();

        //dataBases
        $personDb         = new Api_Model_DbTable_Person();
        $courseDb         = new Api_Model_DbTable_Course();
        $periodsCourseDb  = new Api_Model_DbTable_PeriodsCourses();
        $registerCourseDb = new Api_Model_DbTable_Registrationxcourse();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $data = base64_decode($this->_getParam('data'));

        $data     = explode('-', $data);
        $escid    = $data[0];
        $subid    = $data[1];
        $courseid = $data[2];
        $curid    = $data[3];
        $turno    = $data[4];
        $perid    = $data[5];
        $parcial  = $data[6];

        //datos del Curso
        $where = array( 'eid'      => $eid,
                        'oid'      => $oid,
                        'courseid' => $courseid,
                        'curid'    => $curid,
                        'escid'    => $escid,
                        'subid'    => $subid );

        $attrib = array('name', 'semid', 'credits');
        $preDataCourse = $courseDb->_getFilter($where, $attrib);

        $dataCourse = array('courseid' => $courseid,
                            'curid'    => $curid,
                            'name'     => $preDataCourse[0]['name'],
                            'credits'  => $preDataCourse[0]['credits'],
                            'semid'    => $preDataCourse[0]['semid'],
                            'parcial'  => $parcial );

        //verificar si el curso es por objetivo o competencia
        $where = array( 'eid'      => $eid,
                        'oid'      => $oid,
                        'courseid' => $courseid,
                        'curid'    => $curid,
                        'escid'    => $escid,
                        'subid'    => $subid,
                        'turno'    => $turno,
                        'perid'    => $perid );
        $attrib = array('type_rate');
        $typeCourse = $periodsCourseDb->_getFilter($where, $attrib);
        $typeCourse = $typeCourse[0]['type_rate'];
        $dataCourse['type'] = $typeCourse;

        //Estudiantes de ese curso
        $where = array( 'eid'      => $eid,
                        'oid'      => $oid,
                        'courseid' => $courseid,
                        'curid'    => $curid,
                        'escid'    => $escid,
                        'subid'    => $subid,
                        'turno'    => $turno,
                        'perid'    => $perid,
                        'state'    => 'M' );
        $preDataStudents = $registerCourseDb->_getFilter($where);

        if ($preDataStudents) {
            foreach ($preDataStudents as $c => $student) {
                //nombres y Apellidos
                $where = array( 'eid' => $eid,
                                'pid' => $student['pid'] );

                $attrib = array('last_name0', 'last_name1', 'first_name');
                $preDataPerson = $personDb->_getFilter($where, $attrib);

                $dataCourse['students'][$c]['fullName'] = $preDataPerson[0]['last_name0'].' '.$preDataPerson[0]['last_name1'].' '.$preDataPerson[0]['first_name'];
                $dataCourse['students'][$c]['pid']      = $student['pid'];
                $dataCourse['students'][$c]['uid']      = $student['uid'];

                if ($parcial == 1) {
                    if ($typeCourse == 'O') {
                        for ($i=1; $i <= 9; $i++) {
                            if ($student['nota'.$i.'_i'] >= 11) {
                                $dataCourse['students'][$c]['notaClass'.$i] = 'notaApproved';
                            }else{
                                $dataCourse['students'][$c]['notaClass'.$i] = 'notaDisapproved';
                            }
                            $dataCourse['students'][$c]['nota'.$i] = $student['nota'.$i.'_i'];
                        }

                        $dataCourse['students'][$c]['promedio'] = $student['promedio1'];
                        if ($student['promedio1'] == '-3') {
                            $dataCourse['students'][$c]['promedio'] = 'R';
                            $dataCourse['students'][$c]['promClass'] = 'promedioDisapproved';
                        }elseif ($student['promedio1'] >= 11) {
                            $dataCourse['students'][$c]['promClass'] = 'promedioApproved';
                        }else{
                            $dataCourse['students'][$c]['promClass'] = 'promedioDisapproved';
                        }
                    }elseif ($typeCourse == 'C'){
                        for ($i=1; $i <= 9; $i++) {
                            if ($i == 4 or $i == 9) {
                                $dataCourse['students'][$c]['nota'.$i] = $student['nota'.$i.'_i'];
                                if ($student['nota'.$i.'_i'] == '-3') {
                                    $dataCourse['students'][$c]['nota'.$i] = 'R';
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'unityDisapproved';
                                }elseif ($student['nota'.$i.'_i'] >= 11) {
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'unityApproved';
                                }else{
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'unityDisapproved';
                                }
                            }elseif ($i != 5) {
                                if ($student['nota'.$i.'_i'] >= 11) {
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'notaApproved';
                                }else{
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'notaDisapproved';
                                }
                                $dataCourse['students'][$c]['nota'.$i] = $student['nota'.$i.'_i'];
                            }
                        }
                    }
                }elseif ($parcial == 2){
                    if ($typeCourse == 'O') {
                        $dataCourse['students'][$c]['promedio1'] = $student['promedio1'];
                        for ($i=1; $i <= 9; $i++) {
                            if ($student['nota'.$i.'_ii'] >= 11) {
                                $dataCourse['students'][$c]['notaClass'.$i] = 'notaApproved';
                            }else{
                                $dataCourse['students'][$c]['notaClass'.$i] = 'notaDisapproved';
                            }
                            $dataCourse['students'][$c]['nota'.$i] = $student['nota'.$i.'_ii'];
                        }

                        $dataCourse['students'][$c]['promedio'] = $student['promedio2'];
                        if ($student['promedio2'] == '-3') {
                            $dataCourse['students'][$c]['promedio'] = 'R';
                            $dataCourse['students'][$c]['promClass'] = 'promedioDisapproved';
                        }elseif ($student['promedio2'] >= 11) {
                            $dataCourse['students'][$c]['promClass'] = 'promedioApproved';
                        }else{
                            $dataCourse['students'][$c]['promClass'] = 'promedioDisapproved';
                        }
                    }elseif ($typeCourse == 'C'){
                        for ($i=1; $i <= 9; $i++) {
                            if ($i == 4 or $i == 9) {
                                $dataCourse['students'][$c]['nota'.$i] = $student['nota'.$i.'_ii'];
                                if ($student['nota'.$i.'_ii'] == '-3') {
                                    $dataCourse['students'][$c]['nota'.$i] = 'R';
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'unityDisapproved';
                                }elseif ($student['nota'.$i.'_ii'] >= 11) {
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'unityApproved';
                                }else{
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'unityDisapproved';
                                }
                            }elseif ($i != 5) {
                                if ($student['nota'.$i.'_ii'] >= 11) {
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'notaApproved';
                                }else{
                                    $dataCourse['students'][$c]['notaClass'.$i] = 'notaDisapproved';
                                }
                                $dataCourse['students'][$c]['nota'.$i] = $student['nota'.$i.'_ii'];
                            }
                        }
                    }
                    $dataCourse['students'][$c]['notaFinal'] = $student['notafinal'];
                    if ($student['notafinal'] == '-3') {
                        $dataCourse['students'][$c]['notaFinal'] = 'R';
                        $dataCourse['students'][$c]['notaFinalClass'] = 'promedioDisapproved';
                    }elseif ($student['notafinal'] >= 11) {
                        $dataCourse['students'][$c]['notaFinalClass'] = 'promedioApproved';
                    }else{
                        $dataCourse['students'][$c]['notaFinalClass'] = 'promedioDisapproved';
                    }

                    //Buscar Aplazados
                    if ($student['notafinal'] > 0 and $student['notafinal'] < 11) {
                        $anioPeriod   = $perid[0].$perid[1];
                        $letterPeriod = $perid[2];
                        if ($letterPeriod == 'A') {
                            $periodAplazados = $anioPeriod.'D';
                        }elseif ($letterPeriod == 'B'){
                            $periodAplazados = $anioPeriod.'E';
                        }

                        //Buscar Nota
                        $where = array( 'eid'      => $eid,
                                        'oid'      => $oid,
                                        'escid'    => $escid,
                                        'subid'    => $subid,
                                        'courseid' => $courseid,
                                        'curid'    => $curid,
                                        'turno'    => $turno,
                                        'uid'      => $student['uid'],
                                        'pid'      => $student['pid'],
                                        'perid'    => $periodAplazados );
                        $attrib = array('notafinal');

                        $notaAplazado = $registerCourseDb->_getFilter($where, $attrib);
                        if ($notaAplazado[0]['notafinal']) {
                            $dataCourse['students'][$c]['notaAplazados'] = $notaAplazado[0]['notafinal'];
                            if ($notaAplazado[0]['notafinal'] == '-2') {
                                $dataCourse['students'][$c]['classAplazados'] = 'notaDisapproved';
                                $dataCourse['students'][$c]['notaAplazados'] = 'NSP';
                            }elseif ($notaAplazado[0]['notafinal'] >= 11) {
                                $dataCourse['students'][$c]['classAplazados'] = 'notaApproved';
                            }else{
                                $dataCourse['students'][$c]['classAplazados'] = 'notaDisapproved';
                            }
                        }else{
                            $dataCourse['students'][$c]['notaAplazados'] = '';
                            $dataCourse['students'][$c]['classAplazados'] = '';
                        }
                    }else{
                        $dataCourse['students'][$c]['notaAplazados'] = '';
                        $dataCourse['students'][$c]['classAplazados'] = '';
                    }
                }
            }
            //Ordenar por Nombre
            foreach ($dataCourse['students'] as $c => $student) {
                $fullName[$c] = $student['fullName'];
            }
            array_multisort($dataCourse['students'], SORT_ASC, $fullName);
        }
        $this->view->dataCourse = $dataCourse;
    }


    public function printAction(){
        try {
            $this->_helper->layout()->disableLayout();

            //DataBases
            $courseDb         = new Api_Model_DbTable_Course();
            $syllabusDb       = new Api_Model_DbTable_Syllabus();
            $coursePeriodsDb  = new Api_Model_DbTable_PeriodsCourses();
            $courseTeacherDb  = new Api_Model_DbTable_Coursexteacher();
            $specialityDb     = new Api_Model_DbTable_Speciality();
            $personDb         = new Api_Model_DbTable_Person();
            $academicReportDb = new Api_Model_DbTable_Addreportacadadm();


            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;

            $data  = $this->_getParam('data');
            $data  = explode('-', $data);
            $perid = base64_decode($data[0]);
            $escid = base64_decode($data[1]);
            $subid = base64_decode($data[2]);

            $dataGlobal['perid'] = $perid;
            $dataGlobal['escid'] = $escid;
            $dataGlobal['subid'] = $subid;

            //Verificar si tiene especialidades
            $dataSpecialities = array();

            $where = array( 'eid'    => $eid,
                            'oid'    => $oid,
                            'parent' => $escid,
                            'subid'  => $subid,
                            'state'  => 'A' );
            $attrib = '';
            $order = array('escid ASC');
            $dataSpecialities = $specialityDb->_getFilter($where, $attrib, $order);

            if ($dataSpecialities) {
                $tamEscid = strlen($escid);
                $where = array( 'eid'                        => $eid,
                                'oid'                        => $oid,
                                'left(escid, '.$tamEscid.')' => $escid,
                                'subid'                      => $subid,
                                'perid'                      => $perid,
                                'is_main'                    => 'S' );
                $tieneEspecialidades = 'yes';
                $dataGlobal['existEsp'] = 1;
            }else{
                $where = array( 'eid'     => $eid,
                                'oid'     => $oid,
                                'escid'   => $escid,
                                'subid'   => $subid,
                                'perid'   => $perid,
                                'is_main' => 'S' );
                $tieneEspecialidades = 'no';
                $dataGlobal['existEsp'] = 0;
            }

            //Data Principal

            $order = array('pid ASC');
            $attrib = array('pid', 'uid', 'escid', 'subid');
            $preDataTeachers = $courseTeacherDb->_getFilter($where, $attrib, $order);

            //Profesores registrados y dictando en ese periodo
            $cTeachers = 0;
            $pidTeacher = 0;
            if ($preDataTeachers) {
            foreach ($preDataTeachers as $teacher) {
                if ($teacher['pid'] != $pidTeacher) {
                    $where = array( 'eid' => $eid,
                                    'pid' => $teacher['pid'] );
                    $attrib = array('last_name0', 'last_name1', 'first_name');
                    $dataPerson = $personDb->_getFilter($where, $attrib);

                    $dataDocente[$cTeachers]['fullName'] = $dataPerson[0]['last_name0'].' '.$dataPerson[0]['last_name1'].' '.$dataPerson[0]['first_name'];
                    $dataDocente[$cTeachers]['pid']      = $teacher['pid'];
                    $dataDocente[$cTeachers]['uid']      = $teacher['uid'];
                    $dataDocente[$cTeachers]['escid']    = $escid;
                    $dataDocente[$cTeachers]['subid']    = $teacher['subid'];
                    $dataDocente[$cTeachers]['perid']    = $perid;

                    if ($tieneEspecialidades == 'yes') {
                        $dataDocente[$cTeachers]['espid'] = $teacher['escid'];
                    }else{
                        $dataDocente[$cTeachers]['espid'] = '-';
                    }


                    //verificar el Informe Academico
                    $where = array( 'eid'   => $eid,
                                    'oid'   => $oid,
                                    'escid' => $escid,
                                    'subid' => $teacher['subid'],
                                    'uid'   => $teacher['uid'],
                                    'pid'   => $teacher['pid'],
                                    'perid' => $perid );

                    $attrib = array('state');

                    $preDataReport = $academicReportDb->_getFilter($where, $attrib);

                    $dataDocente[$cTeachers]['stateReport'] = 'no';
                    if ($preDataReport[0]['state'] and $preDataReport[0]['state'] == 'C') {
                        $dataDocente[$cTeachers]['stateReport'] = 'yes';
                    }


                    $pidTeacher = $teacher['pid'];
                    $cTeachers++;
                }
            }

            //Ordenar por nombre
            foreach ($dataDocente as $c => $docente) {
                $nombreDocente[$c] = $docente['fullName'];
            }
            array_multisort($dataDocente, SORT_ASC, $nombreDocente);


            //Cursos de esos profesores
            foreach ($dataDocente as $cTeachers => $teacher) {
                if ($tieneEspecialidades == 'yes') {
                    $tamEscid = strlen($escid);
                    $where = array( 'eid'                        => $eid,
                                    'oid'                        => $oid,
                                    'pid'                        => $teacher['pid'],
                                    'uid'                        => $teacher['uid'],
                                    'perid'                      => $perid,
                                    'is_main'                    => 'S',
                                    'left(escid, '.$tamEscid.')' => $teacher['escid'],
                                    'subid'                      => $teacher['subid'] );
                }else{
                    $where = array( 'eid'     => $eid,
                                    'oid'     => $oid,
                                    'pid'     => $teacher['pid'],
                                    'uid'     => $teacher['uid'],
                                    'perid'   => $perid,
                                    'is_main' => 'S',
                                    'escid'   => $teacher['escid'],
                                    'subid'   => $teacher['subid'] );
                }

                $dataCourse = $courseTeacherDb->_getFilter($where);
                foreach ($dataCourse as $cCourses => $course) {
                    $where = array( 'eid'      => $eid,
                                    'oid'      => $oid,
                                    'courseid' => $course['courseid'],
                                    'curid'    => $course['curid'] );
                    $attrib = array('name');
                    $nameCourse = $courseDb->_getFilter($where, $attrib);
                    $dataDocente[$cTeachers]['courses'][$cCourses] = array( 'courseid' => $course['courseid'],
                                                                            'curid'    => $course['curid'],
                                                                            'turno'    => $course['turno'],
                                                                            'name'     => $nameCourse[0]['name'] );

                    //nombre de la Escuela
                    $where = array( 'eid'   => $eid,
                                    'oid'   => $oid,
                                    'escid' => $course['escid'],
                                    'subid' => $teacher['subid'] );


                    $attrib = array('name');
                    $nameSchool = $specialityDb->_getFilter($where, $attrib);
                    $dataDocente[$cTeachers]['courses'][$cCourses]['nameSpecilaity'] = $nameSchool[0]['name'];

                    //Verificar si lleno Silabo
                    $dataDocente[$cTeachers]['courses'][$cCourses]['stateSyllabus'] = 0;

                    $where = array(
                                    'eid'      => $eid,
                                    'oid'      => $oid,
                                    'courseid' => $course['courseid'],
                                    'curid'    => $course['curid'],
                                    'turno'    => $course['turno'],
                                    'escid'    => $course['escid'],
                                    'subid'    => $course['subid'],
                                    'perid'    => $perid );

                    $attrib = array('state');
                    $stateSyllabus = $syllabusDb->_getFilter($where, $attrib);
                    if ($stateSyllabus and $stateSyllabus[0]['state'] == 'C') {
                        $dataDocente[$cTeachers]['courses'][$cCourses]['stateSyllabus'] = 1;
                    }

                    //Verificar Acta de los parciales
                    $attrib = array('state_record', 'state', 'closure_date');
                    $stateActas = $coursePeriodsDb->_getFilter($where, $attrib);

                    $dataDocente[$cTeachers]['courses'][$cCourses]['closureDate'] = '-';

                    $dataDocente[$cTeachers]['courses'][$cCourses]['statePrimerParcial'] = 0;
                    if ($stateActas and $stateActas[0]['state'] == 'P') {
                        $dataDocente[$cTeachers]['courses'][$cCourses]['statePrimerParcial'] = 1;
                    }

                    $dataDocente[$cTeachers]['courses'][$cCourses]['stateSecondParcial'] = 0;
                    if ($stateActas and $stateActas[0]['state'] == 'C') {
                        $dataDocente[$cTeachers]['courses'][$cCourses]['statePrimerParcial'] = 1;
                        $dataDocente[$cTeachers]['courses'][$cCourses]['stateSecondParcial'] = 1;
                        $dataDocente[$cTeachers]['courses'][$cCourses]['closureDate'] = date('d-m-Y', strtotime($stateActas[0]['closure_date']));
                    }


                }
                $dataDocente[$cTeachers]['cantCursos'] = $cCourses + 1;
            }
            }//Fin If Principal

            $this->view->dataGlobal  = $dataGlobal;
            $this->view->dataDocente = $dataDocente;
            // $escid=$this->sesion->escid;
    //         $where['escid']=$escid;

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
                'type_impression'=>'informe_academico_'.$perid,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            $dbimpression->_save($data);

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'informe_academico_'.$perid);
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
            $header = str_replace("10%", "8%", $header);

            $this->view->header=$header;
            $this->view->footer=$footer;
 		} catch (Exception $e) {
 			print "Error: ".$e->getMessage();
 		}
 	}
}
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
        }elseif ($rid == 'RC' or $rid == 'ES'){
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
        $dataEscid = explode('-', $dataEscid);
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
                $dataDocente[$cTeachers]['escid']    = $escid;
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



    public function printAction(){
        try {
            $this->_helper->layout()->disableLayout();

            //DataBases
            $courseDb        = new Api_Model_DbTable_Course();
            $syllabusDb      = new Api_Model_DbTable_Syllabus();
            $coursePeriodsDb = new Api_Model_DbTable_PeriodsCourses();
            $courseTeacherDb = new Api_Model_DbTable_Coursexteacher();
            $specialityDb    = new Api_Model_DbTable_Speciality();        
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
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
        $period = $this->sesion->period->perid;

        $dataVista['period'] = $period;

        $this->view->dataVista = $dataVista;
 	}

 	public function listperiodsAction(){
 		try {
 			$this->_helper->layout()->disableLayout();
 			
 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;

 			$anio = $this->getParam('anio');
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
        $personDb        = new Api_Model_DbTable_Person();
        $courseDb        = new Api_Model_DbTable_Course();
        $syllabusDb      = new Api_Model_DbTable_Syllabus();
        $courseTeacherDb = new Api_Model_DbTable_Coursexteacher();
        $coursePeriodsDb = new Api_Model_DbTable_PeriodsCourses();

        $perid = $this->_getParam('data');

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $escid = $this->sesion->escid;

        $where = array( 'eid'     => $eid,
                        'oid'     => $oid,
                        'escid'   => $escid,
                        'perid'   => $perid,
                        'is_main' => 'S' );
        $order = array('pid ASC');
        $attrib = array('pid', 'uid');
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
                $dataDocente[$cTeachers]['pid'] = $teacher['pid'];
                $dataDocente[$cTeachers]['uid'] = $teacher['uid'];

                $pidTeacher = $teacher['pid'];
                $cTeachers++;
            }
        }

        //Cursos de esos profesores
        foreach ($dataDocente as $cTeachers => $teacher) {
            $where = array( 'eid'     => $eid,
                            'oid'     => $oid,
                            'pid'     => $teacher['pid'],
                            'uid'     => $teacher['uid'],
                            'perid'   => $perid,
                            'is_main' => 'S',
                            'escid'   => $escid );

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
        $this->view->dataDocente = $dataDocente;
 	}



 	public function printAction(){
 		try {
 			$this->_helper->layout()->disableLayout();
 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;
 			$perid = base64_decode($this->_getParam('perid'));
 			$escid = base64_decode($this->_getParam('escid'));
 			$subid = base64_decode($this->_getParam('subid'));
            $this->view->perid = $perid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getOne($where);
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

            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

            $fac = new Api_Model_DbTable_Faculty();
            $data_fac = $fac->_getOne($where = array('eid' => $eid, 'oid' => $oid, 'facid' => $speciality['facid']));
            $namef=strtoupper($data_fac['name']);

            // $where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid, 'perid' => $perid);
            $user = new Api_Model_DbTable_Coursexteacher();

            $wheretea = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid,'subid'=>$subid ,'perid' => $perid);
            $allteacher = $user->_getAllTeacherXPeriodXEscid($wheretea);
            if ($allteacher) {
                $t = count($allteacher);
                for ($i=0; $i < $t; $i++) {
                    $course_tea = array(); 
                    $wherecour = array(
                        'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid, 'perid' => $perid,
                        'uid' => $allteacher[$i]['uid'], 'pid' => $allteacher[$i]['pid']);
                    $course_tea = $user->_getFilter($wherecour,$attrib=null,$orders=array('courseid','turno'));
                    if ($course_tea) {
                        $cour = new Api_Model_DbTable_Course();
                        $syl = new Api_Model_DbTable_Syllabus();
                        $per_cour = new Api_Model_DbTable_PeriodsCourses();

                        $cc = count($course_tea);
                        for ($j=0; $j < $cc; $j++) { 
                            $where_syl = array(
                                'eid' => $eid, 'oid' => $oid, 'subid' => $subid, 'perid' => $perid, 
                                'escid' => $escid, 'curid' => $course_tea[$j]['curid'], 
                                'courseid' => $course_tea[$j]['courseid'], 'turno' => $course_tea[$j]['turno']);
                            $data_syll = $syl->_getOne($where_syl);

                            $course_tea[$j]['create_syllabus'] = $data_syll['created'];
                            $course_tea[$j]['state_syllabus'] = $data_syll['state'];

                            $data_percour = $per_cour->_getOne($where_syl);
                            $course_tea[$j]['state_course'] = $data_percour['state'];
                            $course_tea[$j]['closure_date_course'] = $data_percour['closure_date'];
                            // $course_tea[$j]['state_record'] = $data_percour['state_record'];

                            $wherecour = array(
                                'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                                'curid' => $course_tea[$j]['curid'], 'courseid' => $course_tea[$j]['courseid']);
                            $datacour = $cour->_getOne($wherecour);
                            $course_tea[$j]['name'] = $datacour['name'];
                        }
                        $allteacher[$i]['courses'] = $course_tea;
                        $allteacher[$i]['cantidad_courses'] = $cc;
                    }

                    $person = new Api_Model_DbTable_Person();
                    $data_person = $person->_getOne($where=array('eid' => $eid, 'pid' => $allteacher[$i]['pid']));
                    $allteacher[$i]['full_name'] = $data_person['last_name0']." ".$data_person['last_name1'].", ".$data_person['first_name'];
                }
            }
            $this->view->data_teacher = $allteacher;
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
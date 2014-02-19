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
 		try {
 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;
 			$rid = $this->sesion->rid;
 			$escid = $this->sesion->escid;
 			$is_director = $this->sesion->infouser['teacher']['is_director'];

	 		$esc = new Api_Model_DbTable_Speciality();
 			if ($rid == 'RF' || $rid == 'DF') {
 				$facid = $this->sesion->faculty->facid;
 				$where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid,'state' => 'A');
 			}else{
 				if ($rid == 'DC' && $is_director=='S') {
 					$this->view->director = $is_director;
 					$this->view->escid = $this->sesion->escid;
 					$where = array('eid' => $eid, 'oid' => $oid, 'parent' => $this->sesion->escid,'state' => 'A');
 					$specialities = $esc->_getFilter($where);
 					if ($specialities) {
		 				$allSchool = $escid;
		 			}else{
		 				$where = array('eid' => $eid, 'oid' => $oid, 'escid' => $this->sesion->escid,'state' => 'A');
		 				$allSchool = '';
		 			}
 				}else{
		 			$where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
 				}
 			}
		 	$dataesc = $esc->_getFilter($where,$attrib=null,$orders=array('facid','escid'));
 			$this->view->allSchool = $allSchool;


	 		$this->view->speciality = $dataesc;


 			/*$this->view->perid = $this->sesion->period->perid;
 			$where = array('eid' => $eid, 'oid' => $oid);
 			$per = new Api_Model_DbTable_Periods();
 			$dataper = $per->_getFilter($where,$attrib=null,$orders=array('perid'));
 			$this->view->periods = $dataper;*/
 		} catch (Exception $e) {
 			print "Error: ".$e->getMessage();
 		}
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
 		try {
            $this->_helper->layout()->disableLayout();
 			$eid = $this->sesion->eid;
 			$oid = $this->sesion->oid;
 			$perid = $this->_getParam('perid');
 			$escid = $this->_getParam('escid');
 			$subid = $this->_getParam('subid');
 			$this->view->perid = $perid;
 			$this->view->escid = $escid;
 			$this->view->subid = $subid;

 			$data = array('subid'=>$subid, 'perid'=>$perid);
            $this->view->data = $data;

            $where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid, 'perid' => $perid);
            $user = new Api_Model_DbTable_Coursexteacher();

            $wheretea = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'perid' => $perid);
            $allteacher = $user->_getAllTeacherXPeriodXEscid($wheretea);

            //Verificando Informe Academico
            $reportAcademicDb = new Api_Model_DbTable_Addreportacadadm();
            $attrib = array('uid', 'state');
            $c = 0;
            foreach ($allteacher as $teacher) {
                $where = array( 'eid'=>$eid, 
                                'oid'=>$oid, 
                                'pid'=>$teacher['pid'], 
                                'uid'=>$teacher['uid'],
                                'subid'=>$subid, 
                                'escid'=>$teacher['escid'],
                                'perid'=>$perid );
                $reportAcademic[$c] = $reportAcademicDb->_getFilter($where, $attrib);
                $c++;
            }
            $this->view->reportAcademic = $reportAcademic;
            //___________________________________________________


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

                            $course_tea[$j]['state_syllabus'] = $data_syll['state'];
                            $course_tea[$j]['create_syllabus'] = $data_syll['created'];
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
 		} catch (Exception $e) {
 			print "Error: ".$e->getMessage();
 		}
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

            if ($speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }

 			$fac = new Api_Model_DbTable_Faculty();
 			$data_fac = $fac->_getOne($where = array('eid' => $eid, 'oid' => $oid, 'facid' => $data_esc['facid']));
 			$namef=strtoupper($data_fac['name']);

 			$where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid, 'perid' => $perid);
 			$user = new Api_Model_DbTable_Coursexteacher();

 			$wheretea = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'perid' => $perid);
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
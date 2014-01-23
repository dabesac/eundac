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
 			$is_director = $this->sesion->infouser['teacher']['is_director'];

	 		$esc = new Api_Model_DbTable_Speciality();
 			if ($rid == 'RF' || $rid == 'DF') {
 				$facid = $this->sesion->faculty->facid;
 				$where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid,'state' => 'A');
 			}else{
 				if ($rid == 'DC' && $is_director=='S') {
 					$this->view->director = $is_director;
 					$this->view->escid = $this->sesion->escid;
 					$where = array('eid' => $eid, 'oid' => $oid, 'escid' => $this->sesion->escid,'state' => 'A');
 				}else{
		 			$where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
 				}
 			}
		 	$dataesc = $esc->_getFilter($where,$attrib=null,$orders=array('facid','escid'));
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
 			// print_r($allteacher);

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

 			$whereesc = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid);
 			$esc = new Api_Model_DbTable_Speciality();
 			$data_esc = $esc->_getOne($whereesc);
 			$this->view->speciality = $data_esc;

 			$fac = new Api_Model_DbTable_Faculty();
 			$data_fac = $fac->_getOne($where = array('eid' => $eid, 'oid' => $oid, 'facid' => $data_esc['facid']));
 			$this->view->faculty = $data_fac;

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
 		} catch (Exception $e) {
 			print "Error: ".$e->getMessage();
 		}
 	}
}

<?php

class Acreditacion_TutorshipController extends Zend_Controller_Action {

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction(){

    }

    public function listteachersAction(){
    	$this->_helper->layout()->disableLayout();

    	//DataBase
		$teacherDb  = new Api_Model_DbTable_Users();
		$personDb   = new Api_Model_DbTable_Person();
		$semesterDb = new Api_Model_DbTable_Semester();

		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;
		$escid = $this->sesion->escid;
		$subid = $this->sesion->subid;
		$perid = $this->sesion->period->perid;

    	$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid,
						'subid' => $subid,
						'state' => 'A',
						'rid'   => 'DC' );
    	$attrib = array('uid', 'pid');

    	$teachers = $teacherDb->_getFilter($where, $attrib);
    	foreach ($teachers as $c => $teacher) {
    		$where = array(	'eid' => $eid,
    						'pid' => $teacher['pid'] );
    		$attrib = array('last_name0', 'last_name1', 'first_name');
    		$name = $personDb->_getFilter($where, $attrib);
			$teachersData[$c]['full_name'] = $name[0]['last_name0'].' '.$name[0]['last_name1'].' '.$name[0]['first_name'];
			$teachersData[$c]['uid']       = $teacher['uid'];
    	}
    	$this->view->teachers = $teachersData;

    	$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid,
						'perid' => $perid );
    	$semesters = $semesterDb->_getSemesterXPeriodsXEscid($where);
    	$this->view->semesters = $semesters;
    }

    public function asignardocenteAction(){
    	$this->_helper->layout()->disableLayout();
    	$formData = $this->getRequest()->getPost();
    	print_r($formData);
    }

}

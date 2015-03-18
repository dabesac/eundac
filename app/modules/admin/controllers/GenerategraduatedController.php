<?php
class Admin_GenerategraduatedController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="admin"){
    		$this->_helper->redirector('index','index','admin'); 
    	}
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $esc = new Api_Model_DbTable_Faculty();
            $data_esc = $esc->_getFilter($where,$attrib=null,$orders = array('facid'));
            $this->view->faculty = $data_esc;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function schoolAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $facid = $this->_getParam('facid');
            $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'state' => 'A');
            $escu = new Api_Model_DbTable_Speciality();
            $data = $escu->_getFilter($where,$attrib=null,$orders=array('escid'));
            $this->view->school = $data;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function generatedAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->sesion->period->perid;
            $escid = $this->_getParam("escid");
            $subid = $this->_getParam("subid");

            $where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'state' => 'A', 'rid' => 'AL');
            $user = new Api_Model_DbTable_Users();
            $students = $user->_getUsersXEscidXRidXState($where);
            $where['state'] = 'I';
            $studentI = $user->_getUsersXEscidXRidXState($where);
            if ($studentI) $student = array_merge($students,$studentI);
            foreach ($students as $student){
                $pid = $student['pid'];
                $uid = $student['uid'];
                $wherecurr = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $student['subid'], 'uid' => $uid, 'pid' => $pid);
                $curr = new Api_Model_DbTable_Studentxcurricula();
                $data_cur = $curr->_getOne($wherecurr);
                $curid = $data_cur['curid'];
                $course = new Api_Model_DbTable_Course();
                $data_courses = $course->_getCountCoursesxSemester($wherecant=array('escid' => $escid, 'curid' => $curid));
                $courses = $course->_getCountCoursesxApproved($wherecour=array('uid' => $uid, 'curid' => $curid));
                // print_r($courses);echo "\t .... . . .";
                if ($data_courses){
                    if($courses){
                        $sum = 0;
                        $cont = 1;  
                        while ($cont <= 12) {
                            foreach ($data_courses as $datos) { 
                                $nombre = $datos['semid']; 
                                if ($nombre == $cont){
                                    $total = $datos['cantidad_cursos']; 
                                    $a = 0;
                                    foreach ($courses as $data_cour) {
                                        if($data_cour['semid'] == $cont){
                                            $cant = $data_cour['cantidad_cursos'];
                                            $a++;  
                                        }                            
                                    }
                                    if($a == 0) $cant = 0;
                                    $tot = $total - $cant;
                                    $sum = $sum + $tot;
                                }
                            }
                            $cont++; 
                        } 
                        if($sum==0){
                            $pk = array(
                                'eid' => $eid, 'oid' => $oid, 'uid' => $uid, 'pid' => $pid, 
                                'escid' => $escid, 'subid' => $student['subid']);
                            $data = array('rid' => 'EG', 'state' => 'A', 'password' => md5($uid));
                            $user->_update($data,$pk);
                        }
                    }
                }
            } 
            $this->view->band = 1;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
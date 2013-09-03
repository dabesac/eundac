<?php

class Docente_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="docente"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    
    }
    
    public function indexAction()
    {
   	    $pid=$this->sesion->pid;
        $this->view->pid=$pid;
    }
    public function subjectsAction()
    {
        try {

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->sesion->period->perid;
            $perid_netx = $this->sesion->period->next;
            $uid = $this->sesion->uid;
            $pid = $this->sesion->infouser['pid'];
            
            $this->view->perid_netx = $perid_netx;
            $where = array(
                'eid'=>$eid,'oid'=>$oid,
                'perid'=>$perid,'uid'=>$uid,
                'pid'=>$pid,);
            $base_course_x_teacher = new Api_Model_DbTable_Coursexteacher();
            $base_periods_courses = new Api_Model_DbTable_PeriodsCourses();
            $base_curses = new Api_Model_DbTable_Course();
            $base_especiality = new Api_Model_DbTable_Speciality();
            $base_faculty = new Api_Model_DbTable_Faculty();


            $subjects = $base_course_x_teacher ->_getFilter($where);
            
            foreach ($subjects as $key => $subject) {
                $where = array(
                    'eid' => $eid,'oid' => $oid,
                    'escid' => $subject['escid'],
                    'subid' => $subject['subid'],
                    'curid' => $subject['curid'],
                    'courseid' => $subject['courseid'],
                    'turno' => $subject['turno'],
                    );
                $type_rate =    $base_periods_courses->_getOne($where);
                $subjects[$key]['type_rate'] = $info_subject['type_rate'];

                $info_subject = $base_curses->_getOne($where);
                $subjects[$key]['name'] = $info_subject['name'];

                $info_speciality = $base_especiality->_getOne($where);
                $where['facid'] = $info_speciality['facid'];

                $info_faculty = $base_faculty   ->_getOne($where);
                $subjects[$key]['faculty'] =$info_faculty['name'];
            }
            
            // print_r($subjects);
            $this->view->subjects=$subjects;

        } catch (Exception $e) {
            
        }
    }
}

<?php

class Docente_FillnotesController extends Zend_Controller_Action {

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
    
    public function targetAction()
    {
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
        $courseid = trim($params['courseid']);
        $turno = trim($params['turno']);
        $perid = trim($params['perid']);
        $curid = trim($params['curid']);
        $escid = trim($params['escid']);
        $subid = trim($params['subid']);
        $state_record = trim($params['state_record']);
        $state = trim($params['state']);
        $this->view->turno=$turno;

        $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'courseid' => $courseid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,
            );

        $base_courses = new Api_Model_DbTable_Course();
        $infocourse = $base_courses->_getOne($where);
        if ($infocourse) {
            $this->view->infocourse = $infocourse;
        }

        $base_students = new Api_Model_DbTable_Registrationxcourse();
        $data_notes_students = $base_students ->_getStudentXcoursesXescidXperiods($where);
        print_r($data_notes_students);
        if ($data_notes_students) {
            $this->view->students = $data_notes_students;
        }

        $base_period = new Api_Model_DbTable_Periods();
        $data_period = $base_period->_getOne($where);
        if ($data_period) {
            $time = time();
            //primer pa
            if($time >= strtotime($data_period['start_register_note_p'])  && $time <= strtotime($data_period['end_register_note_p'])){
               $this->view->partial = 1;
            }else{
                //segundo parcial
                if($time >= strtotime($data_period['start_register_note_s'])  && $time <= strtotime($data_period['end_register_note_s'])){
                    $this->view->partial = 2; 
                }
            }
        }
    }

    public function savetagetnotesAction(){

    }

    public function competitionAction(){

    }
}

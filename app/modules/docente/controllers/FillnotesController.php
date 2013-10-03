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
        $params = $this->getRequest()->getParams();

        if(count($params) > 3){

            $paramsdecode = array();
            
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }
            $params = $paramsdecode;
        }
        /***********paramets***********/
        $curid        = trim($params['curid']);
        $escid        = trim($params['escid']);
        $cursoid    = trim($params['cursoid']);
        $perid        = trim($params['perid']);
        $turno        = trim($params['turno']);
        $eid            = trim($params['eid']);
        $oid            = trim($params['oid']);
        $sedid        = trim($params['sedid']);                    
        $matid       = trim($params['matid']);
        $uid            = trim($params['uid']);
        $partial      = trim($params['partial']);

        /***********notes by partial*********/
        $nota1_i            = ((isset($params['nota1_i']) == true && (!empty($params['nota1_i']) || (intval($params['nota1_i'])== 0) ) )?trim($params['nota1_i']):'');
        $nota2_i            = ((isset($params['nota2_i']) == true && (!empty($params['nota2_i']) || (intval($params['nota2_i'])== 0) ) )?trim($params['nota2_i']):'');
        $nota3_i            = ((isset($params['nota3_i']) == true && (!empty($params['nota3_i']) || (intval($params['nota3_i'])== 0) ) )?trim($params['nota3_i']):'');
        $nota4_i            = ((isset($params['nota4_i']) == true && (!empty($params['nota4_i']) || (intval($params['nota4_i'])== 0) ) )?trim($params['nota4_i']):'');
        $nota5_i            = ((isset($params['nota5_i']) == true && (!empty($params['nota5_i']) || (intval($params['nota5_i'])== 0) ) )?trim($params['nota5_i']):'');
        $nota6_i            = ((isset($params['nota6_i']) == true && (!empty($params['nota6_i']) || (intval($params['nota6_i'])== 0) ) )?trim($params['nota6_i']):'');
        $nota7_i            = ((isset($params['nota7_i']) == true && (!empty($params['nota7_i']) || (intval($params['nota7_i'])== 0) ) )?trim($params['nota7_i']):'');
        $nota8_i            = ((isset($params['nota8_i']) == true && (!empty($params['nota8_i']) || (intval($params['nota8_i'])== 0) ) )?trim($params['nota8_i']):'');
        $nota9_i            = ((isset($params['nota9_i']) == true && (!empty($params['nota9_i']) || (intval($params['nota9_i'])== 0) ) )?trim($params['nota9_i']):'');
        $nota1_ii            = ((isset($params['nota1_ii']) == true && (!empty($params['nota1_ii']) || (intval($params['nota1_ii'])== 0) ) )?trim($params['nota1_ii']):'');
        $nota2_ii            = ((isset($params['nota2_ii']) == true && (!empty($params['nota2_ii']) || (intval($params['nota2_ii'])== 0) ) )?trim($params['nota2_ii']):'');
        $nota3_ii            = ((isset($params['nota3_ii']) == true && (!empty($params['nota3_ii']) || (intval($params['nota3_ii'])== 0) ) )?trim($params['nota3_ii']):'');
        $nota4_ii            = ((isset($params['nota4_ii']) == true && (!empty($params['nota4_ii']) || (intval($params['nota4_ii'])== 0) ) )?trim($params['nota4_ii']):'');
        $nota5_ii            = ((isset($params['nota5_ii']) == true && (!empty($params['nota5_ii']) || (intval($params['nota5_ii'])== 0) ) )?trim($params['nota5_ii']):'');
        $nota6_ii            = ((isset($params['nota6_ii']) == true && (!empty($params['nota6_ii']) || (intval($params['nota6_ii'])== 0) ) )?trim($params['nota6_ii']):'');
        $nota7_ii            = ((isset($params['nota7_ii']) == true && (!empty($params['nota7_ii']) || (intval($params['nota7_ii'])== 0) ) )?trim($params['nota7_ii']):'');
        $nota8_ii            = ((isset($params['nota8_ii']) == true && (!empty($params['nota8_ii']) || (intval($params['nota8_ii'])== 0) ) )?trim($params['nota8_ii']):'');
        $nota9_ii            = ((isset($params['nota9_ii']) == true && (!empty($params['nota9_ii']) || (intval($params['nota9_ii'])== 0) ) )?trim($params['nota9_ii']):'');
        $notafinal          = ((isset($params['notafinal']) == true && !empty($params['notafinal']))?trim($params['notafinal']):'');


        $data = null;
        /******************notes partial one**********************/
        if($partial==1){
            $data = array(
                'nota1_i' => $nota1_i,
                'nota2_i' => $nota2_i,
                'nota3_i' => $nota3_i,
                'nota4_i' => $nota4_i,
                'nota6_i' => $nota6_i,
                'nota7_i' => $nota7_i,
                'nota8_i' => $nota8_i,
                'nota9_i' => $nota9_i,
                'nota1_ii' => $nota1_ii,
                'nota2_ii' => $nota2_ii,
                'nota3_ii' => $nota3_ii,
                'nota4_ii' => $nota4_ii,
                'nota6_ii' => $nota6_ii,
                'nota7_ii' => $nota7_ii,
                'nota8_ii' => $nota8_ii,
                'nota9_ii' => $nota9_ii,
                'notafinal' => $notafinal
            );
        }else{
            if ($partial == 2) {
                
                $data = array(
                        'nota1_ii' => $nota1_ii,
                        'nota2_ii' => $nota2_ii,
                        'nota3_ii' => $nota3_ii,
                        'nota4_ii' => $nota4_ii,
                        'nota6_ii' => $nota6_ii,
                        'nota7_ii' => $nota7_ii,
                        'nota8_ii' => $nota8_ii,
                        'nota9_ii' => $nota9_ii,
                        'notafinal' => $notafinal
                    );
            }
        }

        $pk = array(
                'eid' => $eid,
                'oid' => $oid,
                'escid' => $escid,
                'subid' => $subid,
                'courseid' => $courseid,
                'curid' => $curid,
                'turno' => $turno,
                'regid' => $regid,
                'pid' => $pid,
                'uid' => $uid,
                'perid' => $perid,
            );

        $pk_record = array(
                'eid' => $eid,
                'oid' => $oid,
                'escid' => $escid,
                'subid' => $subid,
                'courseid' => $courseid,
                'curid' => $curid,
                'turno' => $turno,
                'perid' => $perid,
            );
        $data_record = array(
                'state_record' =>'B',
            );

        try {

            $base_period_course = new Api_Model_DbTable_PeriodsCourses();
            $base_registration_course = new Api_Model_DbTable_Registrationxcourse();

            if ($base_period_course->_update($data_record,$pk_record)) {
                if ($base_registration_course->_updatenoteregister($data,$pk)) {
                    $json = array(
                        'status' => true,
                        );
                }
                else{
                    $json = array(
                        'status'=>false,
                        );
                }
            }else{
                    $json = array(
                        'status'=>false,
                        );
            }
            
        } catch (Exception $e) {
            
        }

    }

    public function competitionAction(){

    }
}

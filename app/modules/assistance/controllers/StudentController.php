<?php

class Assistance_StudentController extends Zend_Controller_Action {

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

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        
        $params = $this->getRequest()->getParams();
        $paramsdecode = array();
        foreach ( $params as $key => $value ){
            if($key!="module" && $key!="controller" && $key!="action"){
                $paramsdecode[base64_decode($key)] = base64_decode($value);
            }
        }

        $params = $paramsdecode;
        $escid= trim($params['escid']);
        $subid= trim($params['subid']);
        $courseid= trim($params['courseid']);
        $turno= trim($params['turno']);
        $perid = trim($params['perid']);
        $curid = trim($params['curid']);
        $partial = trim($params['partial']);

        $this->view->turno = $turno;
        $this->view->perid = $perid;
        $base_courses = new Api_Model_DbTable_Course();
        $base_person = new Api_Model_DbTable_Person();
        $base_assistance = new Api_Model_DbTable_StudentAssistance();
        $where = null;
        $infocurso = null;
        $infoassist = null;

        $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'courseid' => $courseid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,);

        


        if ($base_courses->_getOne($where)) {
            $infocurso = $base_courses->_getOne($where);
            $this->view->infocurso = $infocurso;
        }

        $where['coursoid']=$courseid;

        $infoassist = $base_assistance ->_getinfoasisstance($where);
        $state_assistence = $this->verify_closure_assistence($partial,$infoassist);


        if ($state_assistence == 'P' || $state_assistence == 'C') {
            $url_assit ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                        base64_encode('eid')."/".base64_encode($eid)."/".
                        base64_encode('escid')."/".base64_encode($escid)."/".
                        base64_encode('subid')."/".base64_encode($subid)."/".
                        base64_encode('coursoid')."/".base64_encode($courseid)."/".
                        base64_encode('curid')."/".base64_encode($curid)."/".
                        base64_encode('turno')."/".base64_encode($turno)."/".
                        base64_encode('perid')."/".base64_encode($perid)."/".
                        base64_encode('state')."/".base64_encode($state_assistence)."/".
                        base64_encode('partial')."/".base64_encode($partial);

            $this->_redirect('/assistance/student/assistence'.$url_assit);

        }

        if ($infoassist) {
            foreach ($infoassist as $key => $value) {
                $where['pid']=$value['pid'];
                $info_student = $base_person->_getOne($where);
                $infoassist[$key]['name'] = $info_student['last_name0']." ".
                                            $info_student['last_name1'].", ".
                                            $info_student['first_name'];
            }
            $this->view->infoassist = $infoassist;
            $this->view->state=$infoassist[0]['state'];
        }
        // exit();
        $this->view->turno = $turno;
        $this->view->partial=$partial;
    }

    public function savefileAction()
    {
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
        $where = null;
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $coursoid = trim($params['courseid']);
        $curid = trim($params['curid']);
        $turno = trim($params['turno']);
        $regid = trim($params['regid']);
        $uid = trim($params['uid']);
        $pid = trim($params['pid']);
        $escid = trim($params['escid']);
        $subid = trim($params['subid']);
        $perid = trim($params['perid']);
        $partial = trim($params['partial']);

        /***********sesion partial 1*******************/
        $a_sesion_1     = ((isset($params['a_sesion_1']) == true && (!empty($params['a_sesion_1']) ) )?trim($params['a_sesion_1']):'');
        $a_sesion_2     = ((isset($params['a_sesion_2']) == true && (!empty($params['a_sesion_2']) ) )?trim($params['a_sesion_2']):'');
        $a_sesion_3     = ((isset($params['a_sesion_3']) == true && (!empty($params['a_sesion_3']) ) )?trim($params['a_sesion_3']):'');
        $a_sesion_4     = ((isset($params['a_sesion_4']) == true && (!empty($params['a_sesion_4']) ) )?trim($params['a_sesion_4']):'');
        $a_sesion_5     = ((isset($params['a_sesion_5']) == true && (!empty($params['a_sesion_5']) ) )?trim($params['a_sesion_5']):'');
        $a_sesion_6     = ((isset($params['a_sesion_6']) == true && (!empty($params['a_sesion_6']) ) )?trim($params['a_sesion_6']):'');
        $a_sesion_7     = ((isset($params['a_sesion_7']) == true && (!empty($params['a_sesion_7']) ) )?trim($params['a_sesion_7']):'');
        $a_sesion_8     = ((isset($params['a_sesion_8']) == true && (!empty($params['a_sesion_8']) ) )?trim($params['a_sesion_8']):'');
        $a_sesion_9     = ((isset($params['a_sesion_9']) == true && (!empty($params['a_sesion_9']) ) )?trim($params['a_sesion_9']):'');
        $a_sesion_10     = ((isset($params['a_sesion_10']) == true && (!empty($params['a_sesion_10']) ) )?trim($params['a_sesion_10']):'');
        $a_sesion_11     = ((isset($params['a_sesion_11']) == true && (!empty($params['a_sesion_11']) ) )?trim($params['a_sesion_11']):'');
        $a_sesion_12     = ((isset($params['a_sesion_12']) == true && (!empty($params['a_sesion_12']) ) )?trim($params['a_sesion_12']):'');
        $a_sesion_13     = ((isset($params['a_sesion_13']) == true && (!empty($params['a_sesion_13']) ) )?trim($params['a_sesion_13']):'');
        $a_sesion_14     = ((isset($params['a_sesion_14']) == true && (!empty($params['a_sesion_14']) ) )?trim($params['a_sesion_14']):'');
        $a_sesion_15     = ((isset($params['a_sesion_15']) == true && (!empty($params['a_sesion_15']) ) )?trim($params['a_sesion_15']):'');

        /***********sesion partial 2*******************/
        $a_sesion_16     = ((isset($params['a_sesion_16']) == true && (!empty($params['a_sesion_16']) ) )?trim($params['a_sesion_16']):'');
        $a_sesion_17     = ((isset($params['a_sesion_17']) == true && (!empty($params['a_sesion_17']) ) )?trim($params['a_sesion_17']):'');
        $a_sesion_18     = ((isset($params['a_sesion_18']) == true && (!empty($params['a_sesion_18']) ) )?trim($params['a_sesion_18']):'');
        $a_sesion_19     = ((isset($params['a_sesion_19']) == true && (!empty($params['a_sesion_19']) ) )?trim($params['a_sesion_19']):'');
        $a_sesion_20     = ((isset($params['a_sesion_20']) == true && (!empty($params['a_sesion_20']) ) )?trim($params['a_sesion_20']):'');
        $a_sesion_21     = ((isset($params['a_sesion_21']) == true && (!empty($params['a_sesion_21']) ) )?trim($params['a_sesion_21']):'');
        $a_sesion_22     = ((isset($params['a_sesion_22']) == true && (!empty($params['a_sesion_22']) ) )?trim($params['a_sesion_22']):'');
        $a_sesion_23     = ((isset($params['a_sesion_23']) == true && (!empty($params['a_sesion_23']) ) )?trim($params['a_sesion_23']):'');
        $a_sesion_24     = ((isset($params['a_sesion_24']) == true && (!empty($params['a_sesion_24']) ) )?trim($params['a_sesion_24']):'');
        $a_sesion_25     = ((isset($params['a_sesion_25']) == true && (!empty($params['a_sesion_25']) ) )?trim($params['a_sesion_25']):'');
        $a_sesion_26     = ((isset($params['a_sesion_26']) == true && (!empty($params['a_sesion_26']) ) )?trim($params['a_sesion_26']):'');
        $a_sesion_27     = ((isset($params['a_sesion_27']) == true && (!empty($params['a_sesion_27']) ) )?trim($params['a_sesion_27']):'');
        $a_sesion_28     = ((isset($params['a_sesion_28']) == true && (!empty($params['a_sesion_28']) ) )?trim($params['a_sesion_28']):'');
        $a_sesion_29     = ((isset($params['a_sesion_29']) == true && (!empty($params['a_sesion_29']) ) )?trim($params['a_sesion_29']):'');
        $a_sesion_30     = ((isset($params['a_sesion_30']) == true && (!empty($params['a_sesion_30']) ) )?trim($params['a_sesion_30']):'');
        $a_sesion_31     = ((isset($params['a_sesion_31']) == true && (!empty($params['a_sesion_31']) ) )?trim($params['a_sesion_31']):'');
        $a_sesion_32     = ((isset($params['a_sesion_32']) == true && (!empty($params['a_sesion_32']) ) )?trim($params['a_sesion_32']):'');
        $a_sesion_33     = ((isset($params['a_sesion_33']) == true && (!empty($params['a_sesion_33']) ) )?trim($params['a_sesion_33']):'');
        $a_sesion_34     = ((isset($params['a_sesion_34']) == true && (!empty($params['a_sesion_34']) ) )?trim($params['a_sesion_34']):'');

        /***********************************keeping-assistance**********************************************/

        $data = null;         
        if ($partial==1) {
            $data = array(
                'a_sesion_1' => $a_sesion_1,
                'a_sesion_2' => $a_sesion_2,
                'a_sesion_3' => $a_sesion_3,
                'a_sesion_4' => $a_sesion_4,
                'a_sesion_5' => $a_sesion_5,
                'a_sesion_6' => $a_sesion_6,
                'a_sesion_7' => $a_sesion_7,
                'a_sesion_8' => $a_sesion_8,
                'a_sesion_9' => $a_sesion_9,
                'a_sesion_10' => $a_sesion_10,
                'a_sesion_11' => $a_sesion_11,
                'a_sesion_12' => $a_sesion_12,
                'a_sesion_13' => $a_sesion_13,
                'a_sesion_14' => $a_sesion_14,
                'a_sesion_15' => $a_sesion_15,
                'a_sesion_16' => $a_sesion_16,
                'a_sesion_17' => $a_sesion_17,
                'a_sesion_18' => $a_sesion_18,
                'a_sesion_19' => $a_sesion_19,
                'a_sesion_20' => $a_sesion_20,
                'a_sesion_21' => $a_sesion_21,
                'a_sesion_23' => $a_sesion_23,
                'a_sesion_24' => $a_sesion_24,
                'a_sesion_25' => $a_sesion_25,
                'a_sesion_26' => $a_sesion_26,
                'a_sesion_27' => $a_sesion_27,
                'a_sesion_28' => $a_sesion_28,
                'a_sesion_29' => $a_sesion_29,
                'a_sesion_30' => $a_sesion_30,
                'a_sesion_31' => $a_sesion_31,
                'a_sesion_32' => $a_sesion_32,
                'a_sesion_33' => $a_sesion_33,
                'a_sesion_34' => $a_sesion_34,
                );
        }
        if ($partial == 2) {
            $data = array(
                'a_sesion_16' => $a_sesion_16,
                'a_sesion_17' => $a_sesion_17,
                'a_sesion_18' => $a_sesion_18,
                'a_sesion_19' => $a_sesion_19,
                'a_sesion_20' => $a_sesion_20,
                'a_sesion_21' => $a_sesion_21,
                'a_sesion_22' => $a_sesion_22,
                'a_sesion_23' => $a_sesion_23,
                'a_sesion_24' => $a_sesion_24,
                'a_sesion_25' => $a_sesion_25,
                'a_sesion_26' => $a_sesion_26,
                'a_sesion_27' => $a_sesion_27,
                'a_sesion_28' => $a_sesion_28,
                'a_sesion_29' => $a_sesion_29,
                'a_sesion_30' => $a_sesion_30,
                'a_sesion_31' => $a_sesion_31,
                'a_sesion_32' => $a_sesion_32,
                'a_sesion_33' => $a_sesion_33,
                'a_sesion_34' => $a_sesion_34,
                );
            
        }
        if ($data) {

            try {

                $pk = array( 
                'eid' => $eid,'oid'=>$oid,
                'coursoid' =>$coursoid, 'turno' => $turno,
                'curid' =>$curid, 'regid' => $regid,
                'uid' => $uid, 'pid' =>$pid,
                'escid'=>$escid, 'subid'=>$subid,
                'perid'=>$perid,
                );
                $base_assistance = new Api_Model_DbTable_StudentAssistance();
                if ($base_assistance->_update($data,$pk)) {
                    $json = array(
                        'status'=>true,
                        );   
                }
                else{
                    $json = array(
                        'status'=>false,
                        );
                }
                
            } catch (Exception $ex) {
                $json = array(
                    'status' => false,
                    'error' => $ex, 
                    );
            }
        }
        else{

            $json = array(
                    'status'=>false,
                );
        }

        // $a_sesion_1 = trim($params['']);
        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->data = $json; 
    }
    public function retiredstudentAction()
    {
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
        $where = null;
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $coursoid = trim($params['courseid']);
        $curid = trim($params['curid']);
        $turno = trim($params['turno']);
        $regid = trim($params['regid']);
        $uid = trim($params['uid']);
        $pid = trim($params['pid']);
        $escid = trim($params['escid']);
        $subid = trim($params['subid']);
        $perid = trim($params['perid']);
        $partial = trim($params['partial']);
        $action = trim($params['action']);

        /***********sesion partial 1*******************/
        $data = null;
        $data_1 = null;
        $type_course_1 = null;
        $attib = array(
            'type_rate'
        );
        $where = array( 
                'eid' => $eid,'oid'=>$oid,
                'courseid' =>$coursoid, 'turno' => $turno,
                'curid' =>$curid, 
                'escid'=>$escid, 'subid'=>$subid,
                'perid'=>$perid,
            );
        $base_period_course = new Api_Model_DbTable_PeriodsCourses();
        $type_course = $base_period_course->_getFilter($where,$attib);
        $type_course_1 = $type_course[0]['type_rate']; 
        if ($partial==1 && $action == 0) {
            $data = array(
                'a_sesion_1' => "R",
                'a_sesion_2' => "R",
                'a_sesion_3' => "R",
                'a_sesion_4' => "R",
                'a_sesion_5' => "R",
                'a_sesion_6' => "R",
                'a_sesion_7' => "R",
                'a_sesion_8' => "R",
                'a_sesion_9' => "R",
                'a_sesion_10' => "R",
                'a_sesion_11' => "R",
                'a_sesion_12' => "R",
                'a_sesion_13' => "R",
                'a_sesion_14' => "R",
                'a_sesion_15' => "R",
                'a_sesion_16' => "R",
                'a_sesion_17' => "R",
                'a_sesion_18' => "R",
                'a_sesion_19' => "R",
                'a_sesion_20' => "R",
                'a_sesion_21' => "R",
                'a_sesion_22' => "R",
                'a_sesion_23' => "R",
                'a_sesion_24' => "R",
                'a_sesion_25' => "R",
                'a_sesion_26' => "R",
                'a_sesion_27' => "R",
                'a_sesion_28' => "R",
                'a_sesion_29' => "R",
                'a_sesion_30' => "R",
                'a_sesion_31' => "R",
                'a_sesion_32' => "R",
                'a_sesion_33' => "R",
                'a_sesion_34' => "R",
                );
            if ($type_course_1) {
                if ($type_course_1 == 'O') {
                    $data_1 = array(
                        'nota1_i'       => '',
                        'nota2_i'       => '',
                        'nota3_i'       => '',
                        'nota4_i'       => '',
                        'nota5_i'       => '',
                        'nota6_i'       => '',
                        'nota7_i'       => '',
                        'nota8_i'       => '',
                        'nota9_i'       => '',
                        'promedio1' => '-3',
                        'nota1_ii'      => '',
                        'nota2_ii'      => '',
                        'nota3_ii'      => '',
                        'nota4_ii'      => '',
                        'nota5_ii'      => '',
                        'nota6_ii'      => '',
                        'nota7_ii'      => '',
                        'nota8_ii'      => '',
                        'nota9_ii'      => '',
                        'promedio2' => '-3',
                        'notafinal'    => '-3'
                    );
                }
            if ($type_course_1 == 'C') {
                    $data_1 = array(
                        'nota1_i'       => '',
                        'nota2_i'       => '',
                        'nota3_i'       => '',
                        'nota4_i'       => '-3',
                        'nota5_i'       => '',
                        'nota6_i'       => '',
                        'nota7_i'       => '',
                        'nota8_i'       => '',
                        'nota9_i'       => '-3',
                        'promedio1' => '',
                        'nota1_ii'      => '',
                        'nota2_ii'      => '',
                        'nota3_ii'      => '',
                        'nota4_ii'      => '-3',
                        'nota5_ii'      => '',
                        'nota6_ii'      => '',
                        'nota7_ii'      => '',
                        'nota8_ii'      => '',
                        'nota9_ii'      => '-3',
                        'promedio2' => '',
                        'notafinal'    => '-3'
                    );
                }
            }

        }
        if ($partial==1 && $action == 1) {
            $data = array(
                'a_sesion_1' => "",
                'a_sesion_2' => "",
                'a_sesion_3' => "",
                'a_sesion_4' => "",
                'a_sesion_5' => "",
                'a_sesion_6' => "",
                'a_sesion_7' => "",
                'a_sesion_8' => "",
                'a_sesion_9' => "",
                'a_sesion_10' => "",
                'a_sesion_11' => "",
                'a_sesion_12' => "",
                'a_sesion_13' => "",
                'a_sesion_14' => "",
                'a_sesion_15' => "",
                'a_sesion_16' => "",
                'a_sesion_17' => "",
                'a_sesion_18' => "",
                'a_sesion_19' => "",
                'a_sesion_20' => "",
                'a_sesion_21' => "",
                'a_sesion_23' => "",
                'a_sesion_24' => "",
                'a_sesion_25' => "",
                'a_sesion_26' => "",
                'a_sesion_27' => "",
                'a_sesion_28' => "",
                'a_sesion_29' => "",
                'a_sesion_30' => "",
                'a_sesion_31' => "",
                'a_sesion_32' => "",
                'a_sesion_33' => "",
                'a_sesion_34' => "",
                );
            if ($type_course_1) {
                if ($type_course_1 == 'O') {
                    $data_1 = array(
                        'promedio1'=>"",
                        'promedio2'=>"",
                        'notafinal'=>"",
                    );
                }
            if ($type_course_1 == 'C') {
                    $data_1 = array(
                        'nota4_i'=>"",
                        'nota9_i'=>"",
                        'nota4_ii'=>"",
                        'nota9_ii'=>"",
                        'notafinal'=>"",
                    );
                }
            }
        }
        if ($partial == 2 && $action == 0) {
            $data = array(
                'a_sesion_16' => "R",
                'a_sesion_17' => "R",
                'a_sesion_18' => "R",
                'a_sesion_19' => "R",
                'a_sesion_20' => "R",
                'a_sesion_21' => "R",
                'a_sesion_22' => "R",
                'a_sesion_23' => "R",
                'a_sesion_24' => "R",
                'a_sesion_25' => "R",
                'a_sesion_26' => "R",
                'a_sesion_27' => "R",
                'a_sesion_28' => "R",
                'a_sesion_29' => "R",
                'a_sesion_30' => "R",
                'a_sesion_31' => "R",
                'a_sesion_32' => "R",
                'a_sesion_33' => "R",
                'a_sesion_34' => "R",
                );
            if ($type_course_1) {
                if ($type_course_1 == 'O') {
                    $data_1 = array(
                        'nota1_ii'      => '',
                        'nota2_ii'      => '',
                        'nota3_ii'      => '',
                        'nota4_ii'      => '',
                        'nota5_ii'      => '',
                        'nota6_ii'      => '',
                        'nota7_ii'      => '',
                        'nota8_ii'      => '',
                        'nota9_ii'      => '',
                        'promedio2' => '-3',
                        'notafinal'    => '-3'
                    );
                }
            if ($type_course_1 == 'C') {
                    $data_1 = array(
                        'nota1_ii'      => '',
                        'nota2_ii'      => '',
                        'nota3_ii'      => '',
                        'nota4_ii'      => '-3',
                        'nota5_ii'      => '',
                        'nota6_ii'      => '',
                        'nota7_ii'      => '',
                        'nota8_ii'      => '',
                        'nota9_ii'      => '-3',
                        'promedio2' => '',
                        'notafinal'    => '-3'
                    );
                }
            }
        }
        if ($partial == 2 && $action == 1) {
            $data = array(
                'a_sesion_16' => "",
                'a_sesion_17' => "",
                'a_sesion_18' => "",
                'a_sesion_19' => "",
                'a_sesion_20' => "",
                'a_sesion_21' => "",
                'a_sesion_22' => "",
                'a_sesion_23' => "",
                'a_sesion_24' => "",
                'a_sesion_25' => "",
                'a_sesion_26' => "",
                'a_sesion_27' => "",
                'a_sesion_28' => "",
                'a_sesion_29' => "",
                'a_sesion_30' => "",
                'a_sesion_31' => "",
                'a_sesion_32' => "",
                'a_sesion_33' => "",
                'a_sesion_34' => "",
                );
            if ($type_course_1) {
                if ($type_course_1 == 'O') {
                    $data_1 = array(
                        'promedio2'=>"",
                        'notafinal'=>"",
                    );
                }
            if ($type_course_1 == 'C') {
                    $data_1 = array(
                        'nota4_ii'=>"",
                        'nota9_ii'=>"",
                        'notafinal'=>"",
                    );
                }
            }
        }

        if ($data) {

            try {

                $pk = array( 
                'eid' => $eid,'oid'=>$oid,
                'coursoid' =>$coursoid, 'turno' => $turno,
                'curid' =>$curid, 'regid' => $regid,
                'uid' => $uid, 'pid' =>$pid,
                'escid'=>$escid, 'subid'=>$subid,
                'perid'=>$perid,
                );
                $pk_1 = array( 
                    'eid' => $eid,'oid'=>$oid,
                    'courseid' =>$coursoid, 'turno' => $turno,
                    'curid' =>$curid, 'regid' => $regid,
                    'uid' => $uid, 'pid' =>$pid,
                    'escid'=>$escid, 'subid'=>$subid,
                    'perid'=>$perid,
                );
                $base_assistance = new Api_Model_DbTable_StudentAssistance();
                $base_registration = new Api_Model_DbTable_Registrationxcourse();
                if ($base_registration->_updatenoteregister($data_1,$pk_1)) {
                    if ($base_assistance->_update($data,$pk)) {
                        $json = array(
                            'status'=>true,
                        );  
                     } 
                }
                else{
                    $json = array(
                        'status'=>false,
                        );
                }
                
            } catch (Exception $ex) {
                $json = array(
                    'status' => false,
                    'error' => $ex, 
                    );
            }
        }
        else{

            $json = array(
                    'status'=>false,
                );
        }

        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->data = $json; 
    }

    public function closureassistanceAction()
    {
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
        $where = null;
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $coursoid = trim($params['courseid']);
        $curid = trim($params['curid']);
        $turno = trim($params['turno']);
        $escid = trim($params['escid']);
        $subid = trim($params['subid']);
        $perid = trim($params['perid']);
        $partial = trim($params['partial']);

        $base_assistance = new Api_Model_DbTable_StudentAssistance();
        $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'coursoid' => $coursoid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,);

        $infoassist_t = $base_assistance ->_getAll($where);
        if ($infoassist_t) {
            $count = count($infoassist_t); 
            $assist_1 = 0; $assist_2 = 0; $assist_3 = 0;$assist_4 = 0;$assist_5 = 0;
            $assist_6 = 0; $assist_7 = 0; $assist_8 = 0;$assist_9 = 0;$assist_10 = 0;
            $assist_11 = 0; $assist_12 = 0; $assist_13 = 0;$assist_14 = 0;$assist_15 = 0;
            $assist_16 = 0; $assist_17 = 0; $assist_18 = 0;$assist_19 = 0;$assist_20 = 0;
            $assist_21 = 0; $assist_22 = 0; $assist_23 = 0;$assist_24 = 0;$assist_25 = 0;
            $assist_25 = 0; $assist_27 = 0; $assist_28 = 0;$assist_29 = 0;$assist_30 = 0;
            $assist_31 = 0; $assist_32 = 0; $assist_33 = 0;$assist_34 = 0;

            foreach ($infoassist_t as $key => $infoassist) {

                if ($partial==1) {
                    if ($infoassist['a_sesion_1']=='R' || $infoassist['a_sesion_1']=='A' || $infoassist['a_sesion_1']=='F' || $infoassist['a_sesion_1']=='T') {
                        $assist_1++;
                    }
                     if ($infoassist['a_sesion_2']=='R' || $infoassist['a_sesion_2']=='A' || $infoassist['a_sesion_2']=='F' || $infoassist['a_sesion_2']=='T') {
                        $assist_2++;
                    }
                     if ($infoassist['a_sesion_3']=='R' || $infoassist['a_sesion_3']=='A' || $infoassist['a_sesion_3']=='F' || $infoassist['a_sesion_3']=='T') {
                        $assist_3++;
                    }
                     if ($infoassist['a_sesion_4']=='R' || $infoassist['a_sesion_4']=='A' || $infoassist['a_sesion_4']=='F' || $infoassist['a_sesion_4']=='T') {
                        $assist_4++;
                    } if ($infoassist['a_sesion_5']=='R' || $infoassist['a_sesion_5']=='A' || $infoassist['a_sesion_5']=='F' || $infoassist['a_sesion_5']=='T') {
                        $assist_5++;
                    } if ($infoassist['a_sesion_6']=='R' || $infoassist['a_sesion_6']=='A' || $infoassist['a_sesion_6']=='F' || $infoassist['a_sesion_6']=='T') {
                        $assist_6++;
                    } if ($infoassist['a_sesion_7']=='R' || $infoassist['a_sesion_7']=='A' || $infoassist['a_sesion_7']=='F' || $infoassist['a_sesion_7']=='T') {
                        $assist_7++;
                    } if ($infoassist['a_sesion_8']=='R' || $infoassist['a_sesion_8']=='A' || $infoassist['a_sesion_8']=='F' || $infoassist['a_sesion_8']=='T') {
                        $assist_8++;
                    } if ($infoassist['a_sesion_9']=='R' || $infoassist['a_sesion_9']=='A' || $infoassist['a_sesion_9']=='F' || $infoassist['a_sesion_9']=='T') {
                        $assist_9++;
                    } if ($infoassist['a_sesion_10']=='R' || $infoassist['a_sesion_10']=='A' || $infoassist['a_sesion_10']=='F' || $infoassist['a_sesion_10']=='T') {
                        $assist_10++;
                    } if ($infoassist['a_sesion_11']=='R' || $infoassist['a_sesion_11']=='A' || $infoassist['a_sesion_11']=='F' || $infoassist['a_sesion_11']=='T') {
                        $assist_11++;
                    } if ($infoassist['a_sesion_12']=='R' || $infoassist['a_sesion_12']=='A' || $infoassist['a_sesion_12']=='F' || $infoassist['a_sesion_12']=='T') {
                        $assist_12++;
                    }if ($infoassist['a_sesion_13']=='R' || $infoassist['a_sesion_13']=='A' || $infoassist['a_sesion_13']=='F' || $infoassist['a_sesion_13']=='T') {
                        $assist_13++;
                    }if ($infoassist['a_sesion_14']=='R' || $infoassist['a_sesion_14']=='A' || $infoassist['a_sesion_14']=='F' || $infoassist['a_sesion_14']=='T') {
                        $assist_14++;
                    }if ($infoassist['a_sesion_15']=='R' || $infoassist['a_sesion_15']=='A' || $infoassist['a_sesion_15']=='F' || $infoassist['a_sesion_15']=='T') {
                        $assist_15++;
                    }
                }
                if ($partial == 2) {

                    if ($infoassist['a_sesion_16']=='R' || $infoassist['a_sesion_16']=='A' || $infoassist['a_sesion_16']=='F' || $infoassist['a_sesion_16']=='T') {
                        $assist_16++;
                    }if ($infoassist['a_sesion_17']=='R' || $infoassist['a_sesion_17']=='A' || $infoassist['a_sesion_17']=='F' || $infoassist['a_sesion_17']=='T') {
                        $assist_17++;
                    }
                    if ($infoassist['a_sesion_18']=='R' || $infoassist['a_sesion_18']=='A' || $infoassist['a_sesion_18']=='F' || $infoassist['a_sesion_18']=='T') {
                        $assist_18++;
                    }
                     if ($infoassist['a_sesion_19']=='R' || $infoassist['a_sesion_19']=='A' || $infoassist['a_sesion_19']=='F' || $infoassist['a_sesion_19']=='T') {
                        $assist_19++;
                    }
                     if ($infoassist['a_sesion_20']=='R' || $infoassist['a_sesion_20']=='A' || $infoassist['a_sesion_20']=='F' || $infoassist['a_sesion_20']=='T') {
                        $assist_20++;
                    }
                     if ($infoassist['a_sesion_21']=='R' || $infoassist['a_sesion_21']=='A' || $infoassist['a_sesion_21']=='F' || $infoassist['a_sesion_21']=='T') {
                        $assist_21++;
                    } if ($infoassist['a_sesion_22']=='R' || $infoassist['a_sesion_22']=='A' || $infoassist['a_sesion_22']=='F' || $infoassist['a_sesion_22']=='T') {
                        $assist_22++;
                    } if ($infoassist['a_sesion_23']=='R' || $infoassist['a_sesion_23']=='A' || $infoassist['a_sesion_23']=='F' || $infoassist['a_sesion_23']=='T') {
                        $assist_23++;
                    } if ($infoassist['a_sesion_24']=='R' || $infoassist['a_sesion_24']=='A' || $infoassist['a_sesion_24']=='F' || $infoassist['a_sesion_24']=='T') {
                        $assist_24++;
                    } if ($infoassist['a_sesion_25']=='R' || $infoassist['a_sesion_25']=='A' || $infoassist['a_sesion_25']=='F' || $infoassist['a_sesion_25']=='T') {
                        $assist_25++;
                    } if ($infoassist['a_sesion_26']=='R' || $infoassist['a_sesion_26']=='A' || $infoassist['a_sesion_26']=='F' || $infoassist['a_sesion_26']=='T') {
                        $assist_26++;
                    } if ($infoassist['a_sesion_27']=='R' || $infoassist['a_sesion_27']=='A' || $infoassist['a_sesion_27']=='F' || $infoassist['a_sesion_27']=='T') {
                        $assist_27++;
                    } if ($infoassist['a_sesion_28']=='R' || $infoassist['a_sesion_28']=='A' || $infoassist['a_sesion_28']=='F' || $infoassist['a_sesion_28']=='T') {
                        $assist_28++;
                    } if ($infoassist['a_sesion_29']=='R' || $infoassist['a_sesion_29']=='A' || $infoassist['a_sesion_29']=='F' || $infoassist['a_sesion_29']=='T') {
                        $assist_29++;
                    }if ($infoassist['a_sesion_30']=='R' || $infoassist['a_sesion_30']=='A' || $infoassist['a_sesion_30']=='F' || $infoassist['a_sesion_30']=='T') {
                        $assist_30++;
                    }if ($infoassist['a_sesion_31']=='R' || $infoassist['a_sesion_31']=='A' || $infoassist['a_sesion_31']=='F' || $infoassist['a_sesion_31']=='T') {
                        $assist_31++;
                    }if ($infoassist['a_sesion_32']=='R' || $infoassist['a_sesion_32']=='A' || $infoassist['a_sesion_32']=='F' || $infoassist['a_sesion_32']=='T') {
                        $assist_32++;
                    }
                }
            }
            $data = null;
            if ($partial == 1) {
                if (
                    $count == $assist_1 && $count == $assist_2 &&  $count == $assist_3 && $count == $assist_4 && 
                    $count == $assist_5 && $count == $assist_6  && $count == $assist_7 && $count == $assist_8 &&
                    $count == $assist_9 && $count == $assist_10 && $count == $assist_11 && $count == $assist_12 &&
                    $count == $assist_13 && $count == $assist_14 && $count == $assist_15 
                    ) {
                        $data = array(
                            'state' => 'P',
                            );
                    }
            }
            if ($partial == 2) {
                if (
                    $count == $assist_16 &&  $count == $assist_17 && $count == $assist_18 && $count == $assist_19 && $count == $assist_20 && $count == $assist_21 &&
                    $count == $assist_22 && $count == $assist_23 && $count == $assist_24 && $count == $assist_25 &&
                    $count == $assist_26 && $count == $assist_27 && $count == $assist_28 && $count == $assist_29 && 
                    $count == $assist_30 && $count == $assist_31 && $count == $assist_32 
                    ) {
                        $data = array(
                            'state' => 'C',
                            );   
                    }
            }
            if ($data) {
                try {
                    if ($base_assistance->_updateAll($data,$where)) {
                        $json = array(
                            'status' => true,
                            );
                    }
                    else{
                        $json = array(
                            'status' => false,
                            );
                    }
                } catch (Exception $e) {
                    $json = array(
                        'status'=>false,

                        );
                }
            }else{
                $json = array(
                    'status' => false,
                    'error' =>true,
                    );
            }
        
        }else{

            $json = array(
                'status'=>false,
                );
        }

        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->data = $json;
    }


     public function assistenceAction(){
        $params = $this->getRequest()->getParams();
        $paramsdecode = array();
        foreach ( $params as $key => $value ){
            if($key!="module" && $key!="controller" && $key!="action"){
                $paramsdecode[base64_decode($key)] = base64_decode($value);
            }
        }

        $params = $paramsdecode;
        $oid= trim($params['oid']);
        $eid= trim($params['eid']);
        $escid= trim($params['escid']);
        $subid= trim($params['subid']);
        $coursoid= trim($params['coursoid']);
        $turno= trim($params['turno']);
        $perid = trim($params['perid']);
        $curid = trim($params['curid']);
        $state = trim($params['state']);
        $partial = trim($params['partial']);
        $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'coursoid' => $coursoid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,);
        $where['courseid']=$coursoid;

         $url_assit ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                        base64_encode('eid')."/".base64_encode($eid)."/".
                        base64_encode('escid')."/".base64_encode($escid)."/".
                        base64_encode('subid')."/".base64_encode($subid)."/".
                        base64_encode('coursoid')."/".base64_encode($coursoid)."/".
                        base64_encode('curid')."/".base64_encode($curid)."/".
                        base64_encode('turno')."/".base64_encode($turno)."/".
                        base64_encode('perid')."/".base64_encode($perid)."/".
                        base64_encode('state')."/".base64_encode($state)."/".
                        base64_encode('partial')."/".base64_encode($partial);

        $base_assistance = new Api_Model_DbTable_StudentAssistance();
        $infoassist = $base_assistance ->_getinfoasisstance($where);
        $this->view->infoassist=$infoassist;
        $base_courses = new Api_Model_DbTable_Course();
        $infocurso=$base_courses->_getOne($where);
        $this->view->turno=$turno;
        $this->view->infocurso=$infocurso;
        $this->view->partial=$partial;
        $this->view->state=$state;
        $this->view->url=$url_assit;
        

    }

    public function printAction(){
                $this->_helper->layout->disableLayout();
                $params = $this->getRequest()->getParams();
                $paramsdecode = array();
                foreach ( $params as $key => $value ){
                    if($key!="module" && $key!="controller" && $key!="action"){
                        $paramsdecode[base64_decode($key)] = base64_decode($value);
                    }
                }

                $params = $paramsdecode;
                $oid= trim($params['oid']);
                $eid= trim($params['eid']);
                $escid= trim($params['escid']);
                $subid= trim($params['subid']);
                $coursoid= trim($params['coursoid']);
                $turno= trim($params['turno']);
                $perid = trim($params['perid']);
                $curid = trim($params['curid']);
                $state = trim($params['state']);
                $partial = trim($params['partial']);
                $where = array(
                        'eid' => $eid, 'oid' => $oid,
                        'escid' => $escid,'subid' => $subid,
                        'coursoid' => $coursoid,'turno' => $turno,
                        'perid' => $perid,'curid'=>$curid,);

                $where['courseid']=$coursoid;

                $base_faculty   =   new Api_Model_DbTable_Faculty();
                
                $base_assistance = new Api_Model_DbTable_StudentAssistance();
                $infoassist = $base_assistance ->_getinfoasisstance($where);
                $this->view->infoassist=$infoassist;
                $base_courses = new Api_Model_DbTable_Course();
                $infocurso=$base_courses->_getOne($where);
                $this->view->turno=$turno;
                $this->view->infocourse=$infocurso;
                $this->view->partial=$partial;
                $this->view->state=$state;
                $this->view->perid=$perid;
                $this->view->lasname= $this->sesion->infouser['fullname'];

                $base_speciality =  new Api_Model_DbTable_Speciality();
                $info_speciality =  $base_speciality->_getOne($where);
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

                // $escid=$this->sesion->escid;
                // $where['escid']=$escid;

                $dbimpression = new Api_Model_DbTable_Impresscourse();
                
                $uidim=$this->sesion->pid;
                $code="asistencia - ".$partial;
                $data = array(
                    'eid'=>$eid,
                    'oid'=>$oid,
                    'perid'=>$perid,
                    'courseid'=>$coursoid,
                    'escid'=>$escid,
                    'subid'=>$subid,
                    'curid'=>$curid,
                    'turno'=>$turno,
                    'register'=>$uidim,
                    'created'=>date('Y-m-d H:i:s'),
                    'code'=>$code
                    );
                $dbimpression->_save($data);            

                $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$coursoid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>$code);
                $dataim = $dbimpression->_getFilter($wheri);
                $co=count($dataim);
                $codigo=$co." - ".$uidim;
                $this->view->codigo=$codigo;
                

                $header=$this->sesion->org['header_print'];
                $footer=$this->sesion->org['footer_print'];
                $namef = strtoupper($this->sesion->faculty->name);
                $header = str_replace("?facultad",$namef,$header);
                $header = str_replace("?escuela",$namefinal,$header);
                $header = str_replace("?logo", $namelogo, $header);
                $header = str_replace("?codigo", $codigo, $header);
                $header = str_replace("10%", "8%", $header);
                
                $this->view->header=$header;
                $this->view->footer=$footer;
                
            }

    /*verificar asistencia llenada*/
    public function verify_closure_assistence($partial,$infoassist_t){
               $data = null;
        if ($partial && $infoassist_t) {

            $count = count($infoassist_t); 
            $assist_1 = 0; $assist_2 = 0; $assist_3 = 0;$assist_4 = 0;$assist_5 = 0;
            $assist_6 = 0; $assist_7 = 0; $assist_8 = 0;$assist_9 = 0;$assist_10 = 0;
            $assist_11 = 0; $assist_12 = 0; $assist_13 = 0;$assist_14 = 0;$assist_15 = 0;
            $assist_16 = 0; $assist_17 = 0; $assist_18 = 0;$assist_19 = 0;$assist_20 = 0;
            $assist_21 = 0; $assist_22 = 0; $assist_23 = 0;$assist_24 = 0;$assist_25 = 0;
            $assist_25 = 0; $assist_27 = 0; $assist_28 = 0;$assist_29 = 0;$assist_30 = 0;
            $assist_31 = 0; $assist_32 = 0; $assist_33 = 0;$assist_34 = 0;$state=0;

            foreach ($infoassist_t as $key => $infoassist) {

                if ($partial==1) {

                    if ($infoassist['a_sesion_1']=='R' || $infoassist['a_sesion_1']=='A' || $infoassist['a_sesion_1']=='F' || $infoassist['a_sesion_1']=='T') {
                        $assist_1++;
                    }
                    if ($infoassist['a_sesion_2']=='R' || $infoassist['a_sesion_2']=='A' || $infoassist['a_sesion_2']=='F' || $infoassist['a_sesion_2']=='T') {
                        $assist_2++;
                    }
                     if ($infoassist['a_sesion_3']=='R' || $infoassist['a_sesion_3']=='A' || $infoassist['a_sesion_3']=='F' || $infoassist['a_sesion_3']=='T') {
                        $assist_3++;
                    }
                     if ($infoassist['a_sesion_4']=='R' || $infoassist['a_sesion_4']=='A' || $infoassist['a_sesion_4']=='F' || $infoassist['a_sesion_4']=='T') {
                        $assist_4++;
                    } if ($infoassist['a_sesion_5']=='R' || $infoassist['a_sesion_5']=='A' || $infoassist['a_sesion_5']=='F' || $infoassist['a_sesion_5']=='T') {
                        $assist_5++;
                    } if ($infoassist['a_sesion_6']=='R' || $infoassist['a_sesion_6']=='A' || $infoassist['a_sesion_6']=='F' || $infoassist['a_sesion_6']=='T') {
                        $assist_6++;
                    } if ($infoassist['a_sesion_7']=='R' || $infoassist['a_sesion_7']=='A' || $infoassist['a_sesion_7']=='F' || $infoassist['a_sesion_7']=='T') {
                        $assist_7++;
                    } if ($infoassist['a_sesion_8']=='R' || $infoassist['a_sesion_8']=='A' || $infoassist['a_sesion_8']=='F' || $infoassist['a_sesion_8']=='T') {
                        $assist_8++;
                    } if ($infoassist['a_sesion_9']=='R' || $infoassist['a_sesion_9']=='A' || $infoassist['a_sesion_9']=='F' || $infoassist['a_sesion_9']=='T') {
                        $assist_9++;
                    } if ($infoassist['a_sesion_10']=='R' || $infoassist['a_sesion_10']=='A' || $infoassist['a_sesion_10']=='F' || $infoassist['a_sesion_10']=='T') {
                        $assist_10++;
                    } if ($infoassist['a_sesion_11']=='R' || $infoassist['a_sesion_11']=='A' || $infoassist['a_sesion_11']=='F' || $infoassist['a_sesion_11']=='T') {
                        $assist_11++;
                    } if ($infoassist['a_sesion_12']=='R' || $infoassist['a_sesion_12']=='A' || $infoassist['a_sesion_12']=='F' || $infoassist['a_sesion_12']=='T') {
                        $assist_12++;
                    }if ($infoassist['a_sesion_13']=='R' || $infoassist['a_sesion_13']=='A' || $infoassist['a_sesion_13']=='F' || $infoassist['a_sesion_13']=='T') {
                        $assist_13++;
                    }if ($infoassist['a_sesion_14']=='R' || $infoassist['a_sesion_14']=='A' || $infoassist['a_sesion_14']=='F' || $infoassist['a_sesion_14']=='T') {
                        $assist_14++;
                    }if ($infoassist['a_sesion_15']=='R' || $infoassist['a_sesion_15']=='A' || $infoassist['a_sesion_15']=='F' || $infoassist['a_sesion_15']=='T') {
                        $assist_15++;
                    }if ($infoassist['state']=='P') {
                        $state++;
                    }

                }
                if ($partial == 2) {
                    if ($infoassist['a_sesion_16']=='R' || $infoassist['a_sesion_16']=='A' || $infoassist['a_sesion_16']=='F' || $infoassist['a_sesion_16']=='T') {
                        $assist_16++;
                    }if ($infoassist['a_sesion_17']=='R' || $infoassist['a_sesion_17']=='A' || $infoassist['a_sesion_17']=='F' || $infoassist['a_sesion_17']=='T') {
                        $assist_17++;
                    }
                    if ($infoassist['a_sesion_18']=='R' || $infoassist['a_sesion_18']=='A' || $infoassist['a_sesion_18']=='F' || $infoassist['a_sesion_18']=='T') {
                        $assist_18++;
                    }
                     if ($infoassist['a_sesion_19']=='R' || $infoassist['a_sesion_19']=='A' || $infoassist['a_sesion_19']=='F' || $infoassist['a_sesion_19']=='T') {
                        $assist_19++;
                    }
                     if ($infoassist['a_sesion_20']=='R' || $infoassist['a_sesion_20']=='A' || $infoassist['a_sesion_20']=='F' || $infoassist['a_sesion_20']=='T') {
                        $assist_20++;
                    }
                     if ($infoassist['a_sesion_21']=='R' || $infoassist['a_sesion_21']=='A' || $infoassist['a_sesion_21']=='F' || $infoassist['a_sesion_21']=='T') {
                        $assist_21++;
                    } if ($infoassist['a_sesion_22']=='R' || $infoassist['a_sesion_22']=='A' || $infoassist['a_sesion_22']=='F' || $infoassist['a_sesion_22']=='T') {
                        $assist_22++;
                    } if ($infoassist['a_sesion_23']=='R' || $infoassist['a_sesion_23']=='A' || $infoassist['a_sesion_23']=='F' || $infoassist['a_sesion_23']=='T') {
                        $assist_23++;
                    } if ($infoassist['a_sesion_24']=='R' || $infoassist['a_sesion_24']=='A' || $infoassist['a_sesion_24']=='F' || $infoassist['a_sesion_24']=='T') {
                        $assist_24++;
                    } if ($infoassist['a_sesion_25']=='R' || $infoassist['a_sesion_25']=='A' || $infoassist['a_sesion_25']=='F' || $infoassist['a_sesion_25']=='T') {
                        $assist_25++;
                    } if ($infoassist['a_sesion_26']=='R' || $infoassist['a_sesion_26']=='A' || $infoassist['a_sesion_26']=='F' || $infoassist['a_sesion_26']=='T') {
                        $assist_26++;
                    } if ($infoassist['a_sesion_27']=='R' || $infoassist['a_sesion_27']=='A' || $infoassist['a_sesion_27']=='F' || $infoassist['a_sesion_27']=='T') {
                        $assist_27++;
                    } if ($infoassist['a_sesion_28']=='R' || $infoassist['a_sesion_28']=='A' || $infoassist['a_sesion_28']=='F' || $infoassist['a_sesion_28']=='T') {
                        $assist_28++;
                    } if ($infoassist['a_sesion_29']=='R' || $infoassist['a_sesion_29']=='A' || $infoassist['a_sesion_29']=='F' || $infoassist['a_sesion_29']=='T') {
                        $assist_29++;
                    }if ($infoassist['a_sesion_30']=='R' || $infoassist['a_sesion_30']=='A' || $infoassist['a_sesion_30']=='F' || $infoassist['a_sesion_30']=='T') {
                        $assist_30++;
                    }if ($infoassist['a_sesion_31']=='R' || $infoassist['a_sesion_31']=='A' || $infoassist['a_sesion_31']=='F' || $infoassist['a_sesion_31']=='T') {
                        $assist_31++;
                    }if ($infoassist['a_sesion_32']=='R' || $infoassist['a_sesion_32']=='A' || $infoassist['a_sesion_32']=='F' || $infoassist['a_sesion_32']=='T') {
                        $assist_32 ++;
                    }if ($infoassist['state']=='C') {
                        $state++;
                    }
                }
            }
            
            if ($partial == 1) {
                if (
                    $count == $assist_1 && $count == $assist_2 &&  $count == $assist_3 && $count == $assist_4 && 
                    $count == $assist_5 && $count == $assist_6  && $count == $assist_7 && $count == $assist_8 &&
                    $count == $assist_9 && $count == $assist_10 && $count == $assist_11 && $count == $assist_12 &&
                    $count == $assist_13 && $count == $assist_14 && $count == $assist_15  && $count == $state
                    ) {
                        $data = 'P';
                    }
            }
            if ($partial == 2) {
                if (
                    $count == $assist_16 && $count == $assist_17 && 
                    $count == $assist_18 && $count == $assist_19 && $count == $assist_20 && $count == $assist_21 &&
                    $count == $assist_22 && $count == $assist_23 && $count == $assist_24 && $count == $assist_25 &&
                    $count == $assist_26 && $count == $assist_27 && $count == $assist_28 && $count == $assist_29 && 
                    $count == $assist_30 && $count == $assist_31 && $count == $assist_32  && $count == $state
                    ) {
                        $data = 'C';
                    }
            }

        }

        return $data;

    }

}
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
        $state = trim($params['state']);

        $base_courses = new Api_Model_DbTable_Course();
        $base_person = new Api_Model_DbTable_Person();
        $base_assistance = new Api_Model_DbTable_StudentAssistance();
        $base_period = new Api_Model_DbTable_Periods();
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
        
        $infoassist = $base_assistance ->_getAll($where);
        if ($infoassist) {
            foreach ($infoassist as $key => $value) {
                $where['pid']=$value['pid'];
                $info_student = $base_person->_getOne($where);
                $infoassist[$key]['name'] = $info_student['last_name0']." ".
                                            $info_student['last_name1'].", ".
                                            $info_student['first_name'];
            }
            $this->view->infoassist = $infoassist;
        }

        $data_period = $base_period->_getOne($where);
        if ($data_period) {
             $time = time();
            //primer parcial
            if($time >= strtotime($data_period['start_register_note_p'])  && $time <= strtotime($data_period['end_register_note_p'])){
               $this->view->partial = 1;
            }else{
                //segundo parcial
                if($time >= strtotime($data_period['start_register_note_s'])  && $time <= strtotime($data_period['end_register_note_s'])){
                    $this->view->partial = 2; 
                }
            }
        }

        $this->view->turno = $turno;
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
        $a_sesion_9     = ((isset($params['a_sesion_9']) == true && (!empty($params['a_sesion_9']) ) )?trim($params['a_sesion_9']):'');
        $a_sesion_10     = ((isset($params['a_sesion_10']) == true && (!empty($params['a_sesion_10']) ) )?trim($params['a_sesion_10']):'');
        $a_sesion_11     = ((isset($params['a_sesion_11']) == true && (!empty($params['a_sesion_11']) ) )?trim($params['a_sesion_11']):'');
        $a_sesion_12     = ((isset($params['a_sesion_12']) == true && (!empty($params['a_sesion_12']) ) )?trim($params['a_sesion_12']):'');
        $a_sesion_13     = ((isset($params['a_sesion_13']) == true && (!empty($params['a_sesion_13']) ) )?trim($params['a_sesion_13']):'');
        $a_sesion_14     = ((isset($params['a_sesion_14']) == true && (!empty($params['a_sesion_14']) ) )?trim($params['a_sesion_14']):'');
        $a_sesion_15     = ((isset($params['a_sesion_15']) == true && (!empty($params['a_sesion_15']) ) )?trim($params['a_sesion_15']):'');
        $a_sesion_16     = ((isset($params['a_sesion_16']) == true && (!empty($params['a_sesion_16']) ) )?trim($params['a_sesion_16']):'');
        $a_sesion_17     = ((isset($params['a_sesion_17']) == true && (!empty($params['a_sesion_17']) ) )?trim($params['a_sesion_17']):'');

        /***********sesion partial 2*******************/
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
}

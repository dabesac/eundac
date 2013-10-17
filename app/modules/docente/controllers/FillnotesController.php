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
        $partial = trim($params['partial']);
        $state_record = trim($params['state']);

        $this->view->partial=$partial;
        $this->view->turno=$turno;
        $this->view->state_record = $state_record;
        $this->view->perid=$perid;

        $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'courseid' => $courseid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,
            );

        $base_period_course = new Api_Model_DbTable_PeriodsCourses();
        $state_record_c = $base_period_course ->_getOne($where);

        $urlpersentage ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                        base64_encode('eid')."/".base64_encode($eid)."/".
                        base64_encode('escid')."/".base64_encode($escid)."/".
                        base64_encode('subid')."/".base64_encode($subid)."/".
                        base64_encode('courseid')."/".base64_encode($courseid)."/".
                        base64_encode('curid')."/".base64_encode($curid)."/".
                        base64_encode('turno')."/".base64_encode($turno)."/".
                        base64_encode('perid')."/".base64_encode($perid)."/".
                        base64_encode('partial')."/".base64_encode($partial);

        if ($state_record_c) {
            if ($partial==1 && $state_record_c['state_record'] == 'A' && $state_record_c['state'] == 'P') {
                $this->_redirect('/docente/register/registertarget'.$urlpersentage."/".base64_encode('action')."/".base64_encode('N'));
            }
            if ($partial == 2 && $state_record_c['state_record'] == 'C' && $state_record_c['state'] == 'C') {
                $this->_redirect('/docente/register/registertarget'.$urlpersentage."/".base64_encode('action')."/".base64_encode('N'));
            }
        }

        $base_syllabus = new Api_Model_DbTable_Syllabus();
        $units = $base_syllabus->_getOne($where);
        $this->view->closure_syllabus = $units['state'];

        $base_courses = new Api_Model_DbTable_Course();
        $infocourse = $base_courses->_getOne($where);
        if ($infocourse) {
            $this->view->infocourse = $infocourse;
        }

        $base_students = new Api_Model_DbTable_Registrationxcourse();
        $data_notes_students = $base_students ->_getStudentXcoursesXescidXperiods($where);
        if ($data_notes_students) {
            $this->view->students = $data_notes_students;
        }

       
    }

    public function savetargetnotesAction(){
        
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
        $courseid    = trim($params['courseid']);
        $perid        = trim($params['perid']);
        $turno        = trim($params['turno']);
        $eid            = trim($params['eid']);
        $oid            = trim($params['oid']);
        $subid        = trim($params['subid']);                    
        $regid       = trim($params['regid']);
        $uid            = trim($params['uid']);
        $pid            = trim($params['pid']);
        $partial      = trim($params['partial']);

        /***********notes by partial*********/
        $nota1_i            = ((isset($params['nota1_i']) == true && ( !empty($params['nota1_i']) || (intval($params['nota1_i'])== 0) ) )?trim($params['nota1_i']):'');
        $nota2_i            = ((isset($params['nota2_i']) == true && ( !empty($params['nota2_i']) || (intval($params['nota2_i'])== 0) ) )?trim($params['nota2_i']):'');
        $nota3_i            = ((isset($params['nota3_i']) == true && ( !empty($params['nota3_i']) || (intval($params['nota3_i'])== 0) ) )?trim($params['nota3_i']):'');
        $nota4_i            = ((isset($params['nota4_i']) == true && ( !empty($params['nota4_i']) || (intval($params['nota4_i'])== 0) ) )?trim($params['nota4_i']):'');
        $nota5_i            = ((isset($params['nota5_i']) == true && ( !empty($params['nota5_i']) || (intval($params['nota5_i'])== 0) ) )?trim($params['nota5_i']):'');
        $nota6_i            = ((isset($params['nota6_i']) == true && ( !empty($params['nota6_i']) || (intval($params['nota6_i'])== 0) ) )?trim($params['nota6_i']):'');
        $nota7_i            = ((isset($params['nota7_i']) == true && ( !empty($params['nota7_i']) || (intval($params['nota7_i'])== 0) ) )?trim($params['nota7_i']):'');
        $nota8_i            = ((isset($params['nota8_i']) == true && ( !empty($params['nota8_i']) || (intval($params['nota8_i'])== 0) ) )?trim($params['nota8_i']):'');
        $nota9_i            = ((isset($params['nota9_i']) == true && ( !empty($params['nota9_i']) || (intval($params['nota9_i'])== 0) ) )?trim($params['nota9_i']):'');
        $promedio1       = ((isset($params['promedio1']) == true && (!empty($params['promedio1']) || (intval($params['promedio1'])== 0)) )?trim($params['promedio1']):'');
        
        $nota1_ii            = ((isset($params['nota1_ii']) == true && ( !empty($params['nota1_ii']) || (intval($params['nota1_ii'])== 0) ) )?trim($params['nota1_ii']):'');
        $nota2_ii            = ((isset($params['nota2_ii']) == true && ( !empty($params['nota2_ii']) || (intval($params['nota2_ii'])== 0) ) )?trim($params['nota2_ii']):'');
        $nota3_ii            = ((isset($params['nota3_ii']) == true && ( !empty($params['nota3_ii']) || (intval($params['nota3_ii'])== 0) ) )?trim($params['nota3_ii']):'');
        $nota4_ii            = ((isset($params['nota4_ii']) == true && ( !empty($params['nota4_ii']) || (intval($params['nota4_ii'])== 0) ) )?trim($params['nota4_ii']):'');
        $nota5_ii            = ((isset($params['nota5_ii']) == true && ( !empty($params['nota5_ii']) || (intval($params['nota5_ii'])== 0) ) )?trim($params['nota5_ii']):'');
        $nota6_ii            = ((isset($params['nota6_ii']) == true && ( !empty($params['nota6_ii']) || (intval($params['nota6_ii'])== 0) ) )?trim($params['nota6_ii']):'');
        $nota7_ii            = ((isset($params['nota7_ii']) == true && ( !empty($params['nota7_ii']) || (intval($params['nota7_ii'])== 0) ) )?trim($params['nota7_ii']):'');
        $nota8_ii            = ((isset($params['nota8_ii']) == true && ( !empty($params['nota8_ii']) || (intval($params['nota8_ii'])== 0) ) )?trim($params['nota8_ii']):'');
        $nota9_ii            = ((isset($params['nota9_ii']) == true && ( !empty($params['nota9_ii']) || (intval($params['nota9_ii'])== 0) ) )?trim($params['nota9_ii']):'');
        $promedio2       = ((isset($params['promedio2']) == true && (!empty($params['promedio2']) || (intval($params['promedio2'])== 0)))?trim($params['promedio2']):'');
        $notafinal           = ((isset($params['notafinal']) == true && (!empty($params['notafinal']) || (intval($params['notafinal'])== 0)) )?trim($params['notafinal']):'');

        $data = null;
        /******************notes partial one**********************/
        if($partial==1){
            $data = array(
                'modified' => $this->sesion->uid,
                'nota1_i'       => $nota1_i,
                'nota2_i'       => $nota2_i,
                'nota3_i'       => $nota3_i,
                'nota4_i'       => $nota4_i,
                'nota5_i'       => $nota5_i,
                'nota6_i'       => $nota6_i,
                'nota7_i'       => $nota7_i,
                'nota8_i'       => $nota8_i,
                'nota9_i'       => $nota9_i,
                'promedio1' => $promedio1,
                'nota1_ii'      => $nota1_ii,
                'nota2_ii'      => $nota2_ii,
                'nota3_ii'      => $nota3_ii,
                'nota4_ii'      => $nota4_ii,
                'nota5_ii'      => $nota5_ii,
                'nota6_ii'      => $nota6_ii,
                'nota7_ii'      => $nota7_ii,
                'nota8_ii'      => $nota8_ii,
                'nota9_ii'      => $nota9_ii,
                'promedio2' => $promedio2,
                'notafinal'    => $notafinal
            );
        }else{
            if ($partial == 2) {
                
                $data = array(
                    'modified' => $this->sesion->uid,
                    'nota1_ii'      => $nota1_ii,
                    'nota2_ii'      => $nota2_ii,
                    'nota3_ii'      => $nota3_ii,
                    'nota4_ii'      => $nota4_ii,
                    'nota5_ii'      => $nota5_ii,
                    'nota6_ii'      => $nota6_ii,
                    'nota7_ii'      => $nota7_ii,
                    'nota8_ii'      => $nota8_ii,
                    'nota9_ii'      => $nota9_ii,
                    'promedio2' => $promedio2,
                    'notafinal'    => $notafinal
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
            $json = array(
                    'status' => true,
                );
        }
        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->data = $json; 
    }

    public function closuretargetAction(){

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
        $courseid    = trim($params['courseid']);
        $perid        = trim($params['perid']);
        $turno        = trim($params['turno']);
        $eid            = trim($params['eid']);
        $oid            = trim($params['oid']);
        $subid        = trim($params['subid']);                    
        $partial      = trim($params['partial']);

        $where = null;
        $result_target = null;
        $notes_target = null;
        $where = array(
                'eid' => $eid,
                'oid' => $oid,
                'escid' => $escid,
                'subid' => $subid,
                'courseid' => $courseid,
                'curid' => $curid,
                'turno' => $turno,
                'perid' => $perid,
            );
        $base_registration_course = new Api_Model_DbTable_Registrationxcourse();
        $result_target = $base_registration_course->_closuretarget($where);

        $notes_target = (isset($result_target) && count($result_target)>0)?$result_target[0]:array();
        $validate = false;

        $closure_assistence = $this->closure_assistence($where,$partial);

        if ($partial == 1) {
            if(
                    (empty($notes_target['num_reg'])) 
                ){
                    $validate = false;
                }elseif(
                    (
                    (($notes_target['num_reg'] > $notes_target['nota1_i'] && $notes_target['nota1_i'] >= 0 )) ||  
                    (($notes_target['num_reg'] > $notes_target['nota2_i'] && $notes_target['nota2_i']  >= 0 )) || 
                    (($notes_target['num_reg'] > $notes_target['nota3_i'] && $notes_target['nota3_i'] > 0 ))  ||
                    (($notes_target['num_reg'] > $notes_target['nota4_i'] && $notes_target['nota4_i'] > 0 ))  ||  
                    (($notes_target['num_reg'] > $notes_target['nota5_i'] && $notes_target['nota5_i'] > 0 ))  || 
                    (($notes_target['num_reg'] > $notes_target['nota6_i'] && $notes_target['nota6_i'] > 0 ))  ||
                    (($notes_target['num_reg'] > $notes_target['nota7_i'] && $notes_target['nota7_i'] > 0 ))  ||  
                    (($notes_target['num_reg'] > $notes_target['nota8_i'] && $notes_target['nota8_i'] > 0 ))  || 
                    (($notes_target['num_reg'] > $notes_target['nota9_i'] && $notes_target['nota9_i'] > 0 ))
                    ) ||
                    (count($notes_target) == 0)
                ){
                    $validate = false;

                }else{
                    
                    $data2 = array(
                            'state' => 'P','state_record'=>'A',
                            'closure_date' => date('Y-m-d'),
                            'updated' => date('Y-m-d H:m:s'),
                            'modified' => $this->sesion->uid
                    ) ;
                    
                    $pk = array(
                        'curid' => $curid,
                        'escid' => $escid,
                        'courseid' => $courseid,
                        'perid' => $perid,
                        'turno' => $turno,
                        'eid' => $eid,
                        'oid' => $oid,
                        'subid' => $subid,
                        );
                    
                    $validate = true;
                    
                }
        }elseif ($partial==2) {
            if(
                    (empty($notes_target['num_reg']))
                ){
                    $validate = false;

                }elseif(
                    (
                    (($notes_target['num_reg'] > $notes_target['nota1_ii'] && $notes_target['nota1_ii']   >= 0  )) ||  
                    (($notes_target['num_reg']  > $notes_target['nota2_ii'] && $notes_target['nota2_ii']  >= 0  ))  || 
                    (($notes_target['num_reg']  > $notes_target['nota3_ii'] && $notes_target['nota3_ii']  > 0 ))  ||
                    (($notes_target['num_reg']  > $notes_target['nota4_ii'] && $notes_target['nota4_ii']  > 0 ))  ||  
                    (($notes_target['num_reg']  > $notes_target['nota5_ii'] && $notes_target['nota5_ii']  > 0 ))  || 
                    (($notes_target['num_reg']  > $notes_target['nota6_ii'] && $notes_target['nota6_ii']  > 0 ))  ||
                    (($notes_target['num_reg']  > $notes_target['nota7_ii'] && $notes_target['nota7_ii']  > 0 ))  ||  
                    (($notes_target['num_reg']  > $notes_target['nota8_ii'] && $notes_target['nota8_ii']  > 0 ))  || 
                    (($notes_target['num_reg']  > $notes_target['nota9_ii'] && $notes_target['nota9_ii']  > 0 ))
                    ) ||
                    (count($notes_target) == 0)
                ){
                    $validate = false;
                }else{
                    
                    $data2 = array(
                           'state' => 'C','state_record'=>'C',
                            'closure_date' => date('Y-m-d'),
                            'updated' => date('Y-m-d H:m:s'),
                            'modified' => $this->sesion->uid
                            );
                    $pk = array(
                        'curid' => $curid,
                        'escid' => $escid,
                        'courseid' => $courseid,
                        'perid' => $perid,
                        'turno' => $turno,
                        'eid' => $eid,
                        'oid' => $oid,
                        'subid' => $subid,
                        );
                    
                    $validate = true;
                    
                }
        }


        if ($validate == true && $closure_assistence == true) {

             $base_period_course = new Api_Model_DbTable_PeriodsCourses();

            try {
                if ($base_period_course->_update($data2,$pk)) {
                    $json = array(
                        'status' => true
                    );
                }

            } catch (Exception $e) {
                $json = array(
                    'status' => false
                    );
            }
        }
        else{
            $json = array(
                'status'=>false,
                'info'=>$notes_target,
                'closure' =>$closure_assistence
            );
        }

        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->data = $json; 
    }

    public function persentagecompetitionAction()
    {   
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $this->_helper->layout->disableLayout();
        $form = new Docente_Form_Persentage();

        $base_persentage = new Api_Model_DbTable_CourseCompetency();
        $request = $this->getRequest();

        $this->view->errorglobal = false;
        $this->view->ejcutarcerrar = false;

        if ($request->isPost()) {
            
            $curid         = $this->getRequest()->getPost('hdcurid', null);
            $escid         = $this->getRequest()->getPost('hdescid', null);
            $courseid      = $this->getRequest()->getPost('hdcourseid', null);
            $perid         = $this->getRequest()->getPost('hdperid', null);
            $turno        = $this->getRequest()->getPost('hdturno', null);
            $eid            = $this->getRequest()->getPost('hdeid', null);
            $oid            = $this->getRequest()->getPost('hdoid', null);
            $subid        = $this->getRequest()->getPost('hdsubid', null);
            $partial      = $this->getRequest()->getPost('hdpartial', null);
            $value_units      = $this->getRequest()->getPost('hdunits', null);
            $sil_state      = $this->getRequest()->getPost('hdsilstate', null);


            if ($partial == 1 || $partial == 2 && $value_units != 3 ) {
                $form->Persentages();
            }

            $form->setInputHidden($curid,$escid,$courseid,$perid,$turno,$eid,$oid,$subid,$partial,$value_units,$sil_state);
            $form->addInputHidden();


            if ($form->isValid($request->getPost())) { 


                $save = true;
                $txtppporcentaje1 = $form->getValue('txtppporcentaje1');
                $txtppporcentaje2 = $form->getValue('txtppporcentaje2');
                $txtppporcentaje3 = $form->getValue('txtppporcentaje3');
                
                $txtspporcentaje1 = $form->getValue('txtspporcentaje1');
                $txtspporcentaje2 = $form->getValue('txtspporcentaje2');
                $txtspporcentaje3 = $form->getValue('txtspporcentaje3');

                $addition1 = $txtppporcentaje1 + $txtppporcentaje2 + $txtppporcentaje3;
                $addition2 = $txtspporcentaje1 + $txtspporcentaje2 + $txtspporcentaje3;

                if($addition1 <> 100){
                    $form->addError('1');
                    $save = false;
                }

                if ($partial ==1 || $partial == 2 && $value_units != 3 ){
                    if($addition2 <> 100){
                        $form->addError('2');
                        $save = false;
                    }
                }

                if ($save) {
                    $where = array(
                            'eid'=>$eid,'oid'=>$oid,
                            'escid'=>$escid,'subid'=>$subid,
                            'courseid'=>$courseid,'turno'=>$turno,
                            'curid'=>$curid,'perid'=>$perid
                        );
                    $result = $base_persentage->_exists_persentage($where);
                    $action = $result[0]['cantidad'];
                    if ($action == 0) {
                        if($partial == 1){
                            $data = array(
                                'oid' => $oid,
                                'eid' => $eid,
                                'turno' => $turno,
                                'courseid' =>$courseid,
                                'escid'=>$escid,
                                'curid'=>$curid,
                                'perid'=>$perid,
                                'subid'=>$subid,
                                'porc1_u1' => $txtppporcentaje1,
                                'porc2_u1' => $txtppporcentaje2,
                                'porc3_u1' => $txtppporcentaje3,
                                'porc1_u2' => $txtspporcentaje1,
                                'porc2_u2' => $txtspporcentaje2,
                                'porc3_u2' => $txtspporcentaje3
                                );
                        }
                        else{
                            $data = array(
                                'oid' => $oid,
                                'eid' => $eid,
                                'turno' => $turno,
                                'courseid' =>$courseid,
                                'escid'=>$escid,
                                'curid'=>$curid,
                                'perid'=>$perid,
                                'subid'=>$subid,
                                'porc1_u3' => $txtppporcentaje1,
                                'porc2_u3' => $txtppporcentaje2,
                                'porc3_u3' => $txtppporcentaje3,
                                'porc1_u4' => $txtspporcentaje1,
                                'porc2_u4' => $txtspporcentaje2,
                                'porc3_u4' => $txtspporcentaje3
                                );
                        }
                        $base_persentage->_save($data);
                    }else{
                         $pk = array(
                                'oid' => $oid,
                                'eid' => $eid,
                                'turno' => $turno,
                                'courseid' =>$courseid,
                                'escid'=>$escid,
                                'curid'=>$curid,
                                'perid'=>$perid,
                                'subid'=>$subid,
                            );
                        if ($partial == 1) {
                            $data = array(
                                'porc1_u1' => $txtppporcentaje1,
                                'porc2_u1' => $txtppporcentaje2,
                                'porc3_u1' => $txtppporcentaje3,
                                'porc1_u2' => $txtspporcentaje1,
                                'porc2_u2' => $txtspporcentaje2,
                                'porc3_u2' => $txtspporcentaje3
                            );
                        }else{
                            $data = array(
                                'porc1_u3' => $txtppporcentaje1,
                                'porc2_u3' => $txtppporcentaje2,
                                'porc3_u3' => $txtppporcentaje3,
                                'porc1_u4' => $txtspporcentaje1,
                                'porc2_u4' => $txtspporcentaje2,
                                'porc3_u4' => $txtspporcentaje3
                            );
                        }
                        $base_persentage->_update($data,$pk);
                    }
                    $this->view->ejcutarcerrar = true;
                }else{
                    $this->view->errorglobal = true;
                    $this->view->units = $value_units;
                    $this->view->state_syllabus=$sil_state;
                    $this->view->partial = $partial;
                    $form->populate($request->getPost());               
                }
            }else{
                
                $this->view->units = $value_units;
                $this->view->state_syllabus=$sil_state;
                $this->view->partial = $partial;
                $form->populate($request->getPost());

            }
        }else
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
            /***********paramets***********/
            $eid   = $this->sesion->eid;
            $oid   = $this->sesion->oid;
            $escid        = trim($params['escid']);
            $subid        = trim($params['subid']);                    
            $courseid    = trim($params['courseid']);
            $curid        = trim($params['curid']);
            $turno        = trim($params['turno']);
            $perid        = trim($params['perid']);
            $partial      = trim($params['partial']); 

            $persentage_course = null;

            $this->view->partial=$partial;

            $where = array(
                'eid' =>$eid,'oid'=>$oid,
                'escid' =>$escid,'subid'=>$subid,
                'courseid' =>$courseid,'curid'=>$curid,
                'turno' =>$turno,'perid'=>$perid,
                );

            $base_persentage = new Api_Model_DbTable_CourseCompetency();
            $result = $base_persentage->_getOne($where);

            $base_syllabus = new Api_Model_DbTable_Syllabus();
            $units = $base_syllabus->_getOne($where);
            $this->view->units=$units['units'];
            $this->view->state_syllabus=$units['state'];

            $persetage = (isset($result) && count($result)>0)?$result:array();
            // print_r($persetage);

            if ($partial == 1) {
                $porc1 = (!isset($persetage['porc1_u1']) || empty($persetage['porc1_u1']))?'':$persetage['porc1_u1'];
                $porc2 = (!isset($persetage['porc2_u1']) || empty($persetage['porc2_u1']))?'':$persetage['porc2_u1'];
                $porc3 = (!isset($persetage['porc3_u1']) || empty($persetage['porc3_u1']))?'':$persetage['porc3_u1'];
                $porc4 = (!isset($persetage['porc1_u2']) || empty($persetage['porc1_u2']))?'':$persetage['porc1_u2'];
                $porc5 = (!isset($persetage['porc2_u2']) || empty($persetage['porc2_u2']))?'':$persetage['porc2_u2'];
                $porc6 = (!isset($persetage['porc3_u2']) || empty($persetage['porc3_u2']))?'':$persetage['porc3_u2'];
            }
            elseif ($partial==2) {
                $porc1 = (!isset($persetage['porc1_u3']) || empty($persetage['porc1_u3']))?'':$persetage['porc1_u3'];
                $porc2 = (!isset($persetage['porc2_u3']) || empty($persetage['porc2_u3']))?'':$persetage['porc2_u3'];
                $porc3 = (!isset($persetage['porc3_u3']) || empty($persetage['porc3_u3']))?'':$persetage['porc3_u3'];
                $porc4 = (!isset($persetage['porc1_u4']) || empty($persetage['porc1_u4']))?'':$persetage['porc1_u4'];
                $porc5 = (!isset($persetage['porc2_u4']) || empty($persetage['porc2_u4']))?'':$persetage['porc2_u4'];
                $porc6 = (!isset($persetage['porc3_u4']) || empty($persetage['porc3_u4']))?'':$persetage['porc3_u4'];
            }

            if (intval($partial) == 1 || intval($partial) == 2 && intval($units['units']) != 3 ) {
                $form->Persentages();
                $form->txtppporcentaje1->setValue($porc1);
                $form->txtppporcentaje2->setValue($porc2);
                $form->txtppporcentaje3->setValue($porc3);
                $form->txtspporcentaje1->setValue($porc4);
                $form->txtspporcentaje2->setValue($porc5);
                $form->txtspporcentaje3->setValue($porc6);
            }else{
                $form->txtppporcentaje1->setValue($porc1);
                $form->txtppporcentaje2->setValue($porc2);
                $form->txtppporcentaje3->setValue($porc3);
            }
            
            $value_units = null;
            $sil_state = null;
            $value_units = $units['units'];
            $sil_state = $units['state'];

            $form->setInputHidden($curid,$escid,$courseid,$perid,$turno,$eid,$oid,$subid,$partial,$value_units,$sil_state);
            $form->addInputHidden();

        }
        
        $this->view->form = $form;
    }

    public function competitionAction(){
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
        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $escid        = trim($params['escid']);
        $subid        = trim($params['subid']);                    
        $courseid    = trim($params['courseid']);
        $curid        = trim($params['curid']);
        $turno        = trim($params['turno']);
        $perid        = trim($params['perid']);
        $partial      = trim($params['partial']); 
        $state_record = trim($params['state']);


        $this->view->state_record=$state_record;
        $this->view->partial=$partial;
        $this->view->perid = $perid;
        $this->view->turno = $turno;

        $where = null;
        $urlpersentage = null;
        $where = array(
            'oid'=>$oid, 
            'eid'=>$eid,
            'escid'=>$escid,
            'subid'=>$subid,
            'courseid'=>$courseid,
            'curid'=>$curid,
            'turno'=>$turno,
            'perid'=>$perid,
            );
        $attrib = array(
                    'porc1_u1',
                    'porc2_u1',    
                    'porc3_u1',
                    'porc1_u2',
                    'porc2_u2',
                    'porc3_u2',
                    'porc1_u3',
                    'porc2_u3',
                    'porc3_u3',
                    'porc1_u4',
                    'porc2_u4',
                    'porc3_u4' 
            );


        $base_period_course = new Api_Model_DbTable_PeriodsCourses();
        $state_record_c = $base_period_course ->_getOne($where);

        $urlpersentage ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                        base64_encode('eid')."/".base64_encode($eid)."/".
                        base64_encode('escid')."/".base64_encode($escid)."/".
                        base64_encode('subid')."/".base64_encode($subid)."/".
                        base64_encode('courseid')."/".base64_encode($courseid)."/".
                        base64_encode('curid')."/".base64_encode($curid)."/".
                        base64_encode('turno')."/".base64_encode($turno)."/".
                        base64_encode('perid')."/".base64_encode($perid)."/".
                        base64_encode('partial')."/".base64_encode($partial);

        if ($state_record_c) {
            if ($partial == 1 && $state_record_c['state_record'] == 'A' && $state_record_c['state'] == 'P') {
                $this->_redirect('/docente/register/registerconpetency'.$urlpersentage."/".base64_encode('action')."/".base64_encode('N'));
            }
            if ($partial == 2 && $state_record_c['state_record'] == 'C' && $state_record_c['state'] == 'C') {
                $this->_redirect('/docente/register/registerconpetency'.$urlpersentage."/".base64_encode('action')."/".base64_encode('N'));
            }
        }

        $base_syllabus = new Api_Model_DbTable_Syllabus();
        $units = $base_syllabus->_getOne($where);
        $this->view->units=$units['units'];
        $this->view->state_syllabus = $units['state'];

        $base_persentage = new Api_Model_DbTable_CourseCompetency();
        $result1 = $base_persentage->_getFilter($where,$attrib);
        

        $persetage = (isset($result1) && count($result1)>0)?$result1[0]:array();


        $persetage_complte = true;
        

        if (intval($partial) == 1 && count($persetage) > 0) {
            if(
                    (!isset($persetage['porc1_u1']) || empty($persetage['porc1_u1']) || intval($persetage['porc1_u1']<=0) ) ||
                    (!isset($persetage['porc2_u1']) || empty($persetage['porc2_u1']) || intval($persetage['porc2_u1']<=0) ) ||
                    (!isset($persetage['porc3_u1']) || empty($persetage['porc3_u1']) || intval($persetage['porc3_u1']<=0) ) ||
                    (!isset($persetage['porc1_u2']) || empty($persetage['porc1_u2']) || intval($persetage['porc1_u2']<=0) ) ||
                    (!isset($persetage['porc2_u2']) || empty($persetage['porc2_u2']) || intval($persetage['porc2_u2']<=0) ) ||
                    (!isset($persetage['porc3_u2']) || empty($persetage['porc3_u2']) || intval($persetage['porc3_u2']<=0) )

            ){
                $persetage_complte = false;
            
            }     
        }elseif (intval($partial)==2 && count($persetage) > 0 && intval($units['units']) == 3 ) {
            if(
                (!isset($persetage['porc1_u3']) || empty($persetage['porc1_u3']) || intval($persetage['porc1_u3']<=0) ) ||
                (!isset($persetage['porc2_u3']) || empty($persetage['porc2_u3']) || intval($persetage['porc2_u3']<=0) ) ||
                (!isset($persetage['porc3_u3']) || empty($persetage['porc3_u3']) || intval($persetage['porc3_u3']<=0) )
            ){
                $persetage_complte = false;
            }
        }else{
            if(
                (!isset($persetage['porc1_u3']) || empty($persetage['porc1_u3']) || intval($persetage['porc1_u3']<=0) ) ||
                (!isset($persetage['porc2_u3']) || empty($persetage['porc2_u3']) || intval($persetage['porc2_u3']<=0) ) ||
                (!isset($persetage['porc3_u3']) || empty($persetage['porc3_u3']) || intval($persetage['porc3_u3']<=0) ) ||
                (!isset($persetage['porc1_u4']) || empty($persetage['porc1_u4']) || intval($persetage['porc1_u4']<=0) ) ||
                (!isset($persetage['porc2_u4']) || empty($persetage['porc2_u4']) || intval($persetage['porc2_u4']<=0) ) ||
                (!isset($persetage['porc3_u4']) || empty($persetage['porc3_u4']) || intval($persetage['porc3_u4']<=0) )
            ){

                $persetage_complte = false;

            }
        }



        $base_courses = new Api_Model_DbTable_Course();
        $infocourse = $base_courses->_getOne($where);
        $this->view->infocourse = $infocourse;

     

        $base_students = new Api_Model_DbTable_Registrationxcourse();
        $data_notes_students = $base_students ->_getStudentXcoursesXescidXperiods($where);
        $this->view->students = $data_notes_students;

        $this->view->persetage_complte = $persetage_complte;
        $this->view->persetage = $persetage;
        $this->view->urlpersentage = $urlpersentage;
    }

    public function savecompettitionAction(){
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
        $courseid    = trim($params['courseid']);
        $perid        = trim($params['perid']);
        $turno        = trim($params['turno']);
        $eid            = trim($params['eid']);
        $oid            = trim($params['oid']);
        $subid        = trim($params['subid']);                    
        $regid       = trim($params['regid']);
        $uid            = trim($params['uid']);
        $pid            = trim($params['pid']);
        $partial      = trim($params['partial']);


        /***********************notes first and  second partial***********************/
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

        if($partial == 1){
            $data =  array(
                    'modified'=>$this->sesion->uid,
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
        }
        if ($partial == 2) {
            $data = array(
                    'modified'=>$this->sesion->uid,
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
            $json = array(
                    'status' => true,
                );
        }
        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->data = $json; 

    }


    function closurerecordcompetitionAction(){
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
        $courseid    = trim($params['courseid']);
        $perid        = trim($params['perid']);
        $turno        = trim($params['turno']);
        $eid            = trim($params['eid']);
        $oid            = trim($params['oid']);
        $subid        = trim($params['subid']);                    
        $partial      = trim($params['partial']);

        $where = null;
        $result_conpetency = null;
        $notes_conpetency = null;
        $where = array(
                'eid' => $eid,
                'oid' => $oid,
                'escid' => $escid,
                'subid' => $subid,
                'courseid' => $courseid,
                'curid' => $curid,
                'turno' => $turno,
                'perid' => $perid,
            );

        $validate_assit = $this->closure_assistence($where,$partial);

        $base_registration_course = new Api_Model_DbTable_Registrationxcourse();
        $result_conpetency = $base_registration_course->_closureconpetency($where);
        

        $notes_conpetency = (isset($result_conpetency) && count($result_conpetency)>0)?$result_conpetency[0]:array();
        $validate = false;

        if ($partial == 1) {
            if(
                    (empty($notes_conpetency['num_reg'])) 
                ){
                    $validate = false;
                }elseif(
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota1_i'] ) ||  
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota2_i'] ) || 
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota3_i'] ) ||
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota6_i'] ) || 
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota7_i'] ) ||
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota8_i'] ) || 
                    (count($notes_conpetency) == 0)
                ){
                    $validate = false;
                }else{
                    
                    $data2 = array(
                            'state' => 'P','state_record'=>'A',
                            'closure_date' => date('Y-m-d'),
                            'updated' => date('Y-m-d H:m:s'),
                            'modified' => $this->sesion->uid
                    ) ;
                    
                    $pk = array(
                        'curid' => $curid,
                        'escid' => $escid,
                        'courseid' => $courseid,
                        'perid' => $perid,
                        'turno' => $turno,
                        'eid' => $eid,
                        'oid' => $oid,
                        'subid' => $subid,
                        );

                    
                    $validate = true;
                    
                }
        }elseif ($partial==2) {
            if(
                    (empty($notes_target['num_reg']))
                ){
                    $validate = false;
                }elseif(
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota1_ii'] ) ||  
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota2_ii'] ) || 
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota3_ii'] ) ||
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota6_ii'] ) || 
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota7_ii'] ) ||
                    ( $notes_conpetency['num_reg'] > $notes_conpetency['nota8_ii'] ) || 
                    (count($notes_conpetency) == 0)
                ){
                    $validate = false;
                }else{
                    
                    $data2 = array(
                           'state' => 'C','state_record'=>'C',
                            'closure_date' => date('Y-m-d'),
                            'updated' => date('Y-m-d H:m:s'),
                            'modified' => $this->sesion->uid
                            );

                    $pk = array(
                        'curid' => $curid,
                        'escid' => $escid,
                        'courseid' => $courseid,
                        'perid' => $perid,
                        'turno' => $turno,
                        'eid' => $eid,
                        'oid' => $oid,
                        'subid' => $subid,
                        );
                    
                    $validate = true;
                    
                }
        }


        if ($validate == true && $validate_assit == true) {
             $base_period_course = new Api_Model_DbTable_PeriodsCourses();
            try {
                if ($base_period_course->_update($data2,$pk)) {
                    $json = array(
                        'status' => true
                    );
                }
            } catch (Exception $e) {
                $json = array(
                    'status' => false
                    );
            }

        }
        else{
            $json = array(
                'status'=>false,
                'info'=>$notes_conpetency,
                'closure' =>$validate_assit
            );
        }

        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->data = $json; 


    }


    public function closure_assistence($where=array(),$partial){

        $base_assistance = new Api_Model_DbTable_StudentAssistance();
        $where['coursoid']=$where['courseid'];
        $assistence = $base_assistance ->_getAll($where);
         if ($assistence) {

            $count = count($assistence); 
            $assist_1 = 0; $assist_2 = 0; $assist_3 = 0;$assist_4 = 0;$assist_5 = 0;
            $assist_6 = 0; $assist_7 = 0; $assist_8 = 0;$assist_9 = 0;$assist_10 = 0;
            $assist_11 = 0; $assist_12 = 0; $assist_13 = 0;$assist_14 = 0;$assist_15 = 0;
            $assist_16 = 0; $assist_17 = 0; $assist_18 = 0;$assist_19 = 0;$assist_20 = 0;
            $assist_21 = 0; $assist_22 = 0; $assist_23 = 0;$assist_24 = 0;$assist_25 = 0;
            $assist_25 = 0; $assist_27 = 0; $assist_28 = 0;$assist_29 = 0;$assist_30 = 0;
            $assist_31 = 0; $assist_32 = 0; $assist_33 = 0;$assist_34 = 0;$state = 0;

            foreach ($assistence as $key => $infoassist) {

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
                    }if ($infoassist['a_sesion_16']=='R' || $infoassist['a_sesion_16']=='A' || $infoassist['a_sesion_16']=='F' || $infoassist['a_sesion_16']=='T') {
                        $assist_16++;
                    }if ($infoassist['a_sesion_17']=='R' || $infoassist['a_sesion_17']=='A' || $infoassist['a_sesion_17']=='F' || $infoassist['a_sesion_17']=='T') {
                        $assist_17++;
                    }
                    if ($infoassist['state']=='P') {
                        $state++;
                    }
                }
                if ($partial == 2) {

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
                    }if ($infoassist['a_sesion_33']=='R' || $infoassist['a_sesion_33']=='A' || $infoassist['a_sesion_33']=='F' || $infoassist['a_sesion_33']=='T') {
                        $assist_33++;
                    }if ($infoassist['a_sesion_34']=='R' || $infoassist['a_sesion_34']=='A' || $infoassist['a_sesion_34']=='F' || $infoassist['a_sesion_34']=='T') {
                        $assist_34++;
                    }
                    if ($infoassist['state']=='C') {
                        $state++;
                    }
                }

                }
            }

            $data = false; 

            if ($partial == 1) {
                if (
                    $count == $assist_1 && $count == $assist_2 &&  $count == $assist_3 && $count == $assist_4 && 
                    $count == $assist_5 && $count == $assist_6  && $count == $assist_7 && $count == $assist_8 &&
                    $count == $assist_9 && $count == $assist_10 && $count == $assist_11 && $count == $assist_12 &&
                    $count == $assist_13 && $count == $assist_14 && $count == $assist_15 && $count == $assist_16 &&
                    $count == $assist_17 && $count == $state
                    ) {
                        $data = true;
                    }
            }
            if ($partial == 2) {
                if (
                    $count == $assist_18 && $count == $assist_19 && $count == $assist_20 && $count == $assist_21 &&
                    $count == $assist_22 && $count == $assist_23 && $count == $assist_24 && $count == $assist_25 &&
                    $count == $assist_26 && $count == $assist_27 && $count == $assist_28 && $count == $assist_29 && 
                    $count == $assist_30 && $count == $assist_31 && $count == $assist_32 && $count == $assist_33 && 
                    $count == $assist_34 && $count == $state
                    ) {
                        $data = true; 
                    }
            }

            return $data;
    }

    

}

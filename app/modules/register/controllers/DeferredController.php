<?php

class Register_DeferredController extends Zend_Controller_Action {

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
        try {
            
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

            $this->view->state=$state;

            $urlpersentage ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                base64_encode('eid')."/".base64_encode($eid)."/".
                base64_encode('escid')."/".base64_encode($escid)."/".
                base64_encode('subid')."/".base64_encode($subid)."/".
                base64_encode('courseid')."/".base64_encode($courseid)."/".
                base64_encode('curid')."/".base64_encode($curid)."/".
                base64_encode('turno')."/".base64_encode($turno)."/".
                base64_encode('perid')."/".base64_encode($perid);

            $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'courseid' => $courseid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,);

            $base_period_course = new Api_Model_DbTable_PeriodsCourses();
            $state_record_c = $base_period_course ->_getOne($where);
            $this->view->state_record = $state_record_c['state_record'];
            $this->view->state_course = $state_record_c['state'];

            if ($state_record_c) {
                if ($state_record_c['state_record'] == 'C' && $state_record_c['state'] == 'C') {
                    $this->_redirect('/register/deferred/registerdeferred'.$urlpersentage);
                }
            }

            $base_courses = new Api_Model_DbTable_Course();
            $base_courses_registration = new Api_Model_DbTable_Registrationxcourse();
            $base_person = new Api_Model_DbTable_Person();
            $base_bankreceipts = new Api_Model_DbTable_Bankreceipts();
            $students_register = $base_courses_registration->_getFilter($where);

            if ($students_register) {

                foreach ($students_register as $key => $student) {
                $where['pid']=$student['pid'];
                $infostudent = $base_person->_getOne($where);
                $students_register[$key]['name'] = $infostudent['last_name0']." ".
                                            $infostudent['last_name1'].", ".
                                        $infostudent['first_name'];
                $where1 = array(
                    'code_student'=>$student['uid'],
                    'perid'=>$perid,'processed'=>'N');
                $receipts = $base_bankreceipts->_getFilter($where1);
                $students_register[$key]['receipts']= $receipts; 
                } 
            }
            else
            {
                $this->view->erro_data = "No tiene Registros";
            }

            $infocurso = $base_courses->_getOne($where);
            $this->view->students_register=$students_register;
            $this->view->infocurso = $infocurso;
            $this->view->perid = $perid;
        } catch (Exception $e) {
            print "Error index Registration ".$e->$getMessage();
        }
    }

    public function loadnotasAction()
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

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;

            /****parametros get***/
            $courseid = trim($params['courseid']);
            $curid =    trim($params['curid']);
            $turno  =   trim($params['turno']);
            $perid  =   trim($params['perid']);
            $escid =    trim($params['escid']);
            $subid  =   trim($params['subid']);
            $regid =    trim($params['regid']);
            $uid   =    trim($params['uid']);
            $pid   =    trim($params['pid']);

            /** data **/
            $receipt    = ((isset($params['receipt']) == true && (!empty($params['receipt']) || 
                            (intval($params['receipt'])== 0) ) )?trim($params['receipt']):'');

            $notafinal    = ((isset($params['notafinal']) == true && (!empty($params['notafinal']) || 
                            (intval($params['notafinal'])== 0) ) )?trim($params['notafinal']):'');
            
            $data =null;
            $data2 =null;

            $data = array(
                'receipt' =>$receipt,
                'notafinal' => $notafinal,
                );
            $where =  
                " eid='$eid' and oid= '$oid' and 
                courseid= '$courseid' and curid= '$curid' and
                turno= '$turno' and perid = '$perid' and 
                escid = '$escid' and  subid= '$subid' and
                regid= '$regid' and  uid = '$uid' and 
                pid = '$pid'
                 ";

            $data2 = array(
                'state'=>'B',
                'updated'=>date('Y-m-d H:m:s'),
                'modified' => $this->sesion->uid,
                );

            $where1 = array(
                'eid'=>$eid , 'oid'=>$oid,
                'courseid'=>$courseid, 'curid'=>$curid,
                'turno'=>$turno,'perid'=>$perid,
                'escid'=>$escid, 'subid'=>$subid,
                );


                try {

                    $base_courses_registration = new Api_Model_DbTable_Registrationxcourse();
                    $base_periods_courses = new Api_Model_DbTable_PeriodsCourses();

                    if ($base_courses_registration->_updatestr($data,$where)) {
                        if ($base_periods_courses->_update($data2,$where1)) {
                            $json = array(
                                'status' =>true,
                                );
                        }                        
                    }

                    
                } catch (Exception $e) {
                    $json = array(
                        'status'=>false,
                        );
                    
                }

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json; 
    }
    
    public function closerecordAction()
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

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        /****parametros get***/
        $courseid = trim($params['courseid']);
        $curid =    trim($params['curid']);
        $turno  =   trim($params['turno']);
        $perid  =   trim($params['perid']);
        $escid =    trim($params['escid']);
        $subid  =   trim($params['subid']);

        $where = array(
            'eid' => $eid, 'oid' => $oid,
            'escid' => $escid,'subid' => $subid,
            'courseid' => $courseid,'turno' => $turno,
            'perid' => $perid,'curid'=>$curid,);

        $base_courses_registration = new Api_Model_DbTable_Registrationxcourse();
        $students_register = $base_courses_registration->_getFilter($where);


        $i=0;
        $j=0;
        $k=0;
        $total=count($students_register);

        foreach ($students_register as $key => $value) {
                $nota= intval($value['notafinal']);
                $receipt = intval($value['receipt']);
                // $nota = 
                switch ($nota) {
                    case $nota == -2 || $nota >= 0 :
                        $i += 1;
                        if ($nota == -2) {
                            $k++;
                        }
                        break;
                }
                switch ($receipt) {
                    case $receipt >= 0:
                        $j += 1;
                        break;
                }
        }

        $j=$j+$k;

           try {

                 if ( $i==$j && $total==$i && $total==$j) {

                $base_periods_courses = new Api_Model_DbTable_PeriodsCourses();
                $base_bankreceipts = new Api_Model_DbTable_Bankreceipts();

                $data1 = array(
                    'state'=>'C',
                    'state_record'=>'C',
                    'closure_date'=>date('Y-m-d'),
                    'updated'=>date('Y-m-d H:m:s'),
                    'modified' => $this->sesion->uid,
                    );

                $where1 = array(
                    'eid'=>$eid , 'oid'=>$oid,
                    'courseid'=>$courseid, 'curid'=>$curid,
                    'turno'=>$turno,'perid'=>$perid,
                    'escid'=>$escid, 'subid'=>$subid,
                    );

                if ($base_periods_courses->_update($data1,$where1)) {

                        foreach ($students_register as $key => $value) {

                            $where_bank['code_student']=$value['uid'];
                            $where_bank['perid'] = $perid;
                            $where_bank['concept']= "00000021";
                            $where_bank['operation']=$value['receipt'];

                            $data = array('processed'=>'S');    
                            $base_bankreceipts ->_update($data,$where_bank);

                        }
                            $json = array(
                                'status'=>true,
                                );
                    # code...
                }

            }
            else{
                    
                    $json = array(
                        'status'=>false,
                        );
            }
               
           } catch (Exception $e) {
               
               $json = array(
                'status'=>false,
                );
           }


        
        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');   
        $this->view->data = $json; 

    }

    public function printdeferredAction(){
        
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
        $oid        = trim($params['oid']);
        $eid        = trim($params['eid']);
        $escid        = trim($params['escid']);
        $subid        = trim($params['subid']);                    
        $courseid    = trim($params['courseid']);
        $curid        = trim($params['curid']);
        $turno        = trim($params['turno']);
        $perid        = trim($params['perid']);
        $partial      = trim($params['partial']); 
        $action      = trim($params['action']);

        $where = null;
        $url = null;
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
        $base_courses = new Api_Model_DbTable_Course();
        $infocourse = $base_courses->_getOne($where);
        $this->view->infocourse = $infocourse;

        $base_students = new Api_Model_DbTable_Registrationxcourse();
        $data_notes_students = $base_students ->_getStudentXcoursesXescidXperiods_sql($where);

        $base_faculty   =   new Api_Model_DbTable_Faculty();
        $base_speciality =  new Api_Model_DbTable_Speciality();
        $info_speciality =  $base_speciality->_getOne($where);

        if ($info_speciality['parent'] != "") {
            $where['escid']=$info_speciality['parent'];
            $name_speciality = $base_speciality->_getOne($where);
            $info_speciality['speciality'] = $name_speciality['name'];
        }

        $this->view->info_speciality = $info_speciality;
        $this->view->name_speciality = $name_speciality;
        $this->view->turno = $turno;
        $this->view->perid = $perid;
        $this->view->partial = $partial;
        $this->view->students = $data_notes_students;
        $this->view->faculty = $this->sesion->faculty->name;
        $this->view->lasname= $this->sesion->infouser['fullname'];
        $this->_helper->layout->disableLayout();
    }
    public function registerdeferredAction(){
        try {
            
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

            $this->view->state=$state;

            $urlpersentage ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                base64_encode('eid')."/".base64_encode($eid)."/".
                base64_encode('escid')."/".base64_encode($escid)."/".
                base64_encode('subid')."/".base64_encode($subid)."/".
                base64_encode('courseid')."/".base64_encode($courseid)."/".
                base64_encode('curid')."/".base64_encode($curid)."/".
                base64_encode('turno')."/".base64_encode($turno)."/".
                base64_encode('perid')."/".base64_encode($perid);

            $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'courseid' => $courseid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,);

            $base_period_course = new Api_Model_DbTable_PeriodsCourses();
            $state_record_c = $base_period_course ->_getOne($where);
            $this->view->state_record = $state_record_c['state_record'];
            $this->view->state_course = $state_record_c['state'];

            
            $base_courses = new Api_Model_DbTable_Course();
            $base_courses_registration = new Api_Model_DbTable_Registrationxcourse();
            $base_person = new Api_Model_DbTable_Person();
            $base_bankreceipts = new Api_Model_DbTable_Bankreceipts();
            $students_register = $base_courses_registration->_getFilter($where);

            if ($students_register) {

                foreach ($students_register as $key => $student) {
                $where['pid']=$student['pid'];
                $infostudent = $base_person->_getOne($where);
                $students_register[$key]['name'] = $infostudent['last_name0']." ".
                                            $infostudent['last_name1'].", ".
                                        $infostudent['first_name'];
                $where1 = array(
                    'code_student'=>$student['uid'],
                    'perid'=>$perid,'processed'=>'N');
                $receipts = $base_bankreceipts->_getFilter($where1);
                $students_register[$key]['receipts']= $receipts; 
                } 
            }
            else
            {
                $this->view->erro_data = "No tiene Registros";
            }

            $infocurso = $base_courses->_getOne($where);
            $this->view->students_register=$students_register;
            $this->view->infocurso = $infocurso;
            $this->view->perid = $perid;
            $this->view->urlpersentage=$urlpersentage;
        } catch (Exception $e) {
            print "Error index Registration ".$e->$getMessage();
        }
    }
}

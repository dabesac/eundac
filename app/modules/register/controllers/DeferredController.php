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
        // print_r($this->sesion);
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
            $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'courseid' => $courseid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid,);

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
                    'state'=>'S',
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
        try {
            $this->_helper->layout->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uid = $this->sesion->uid;
            $perid = base64_decode($this->_getParam('perid'));
            $curid = base64_decode($this->_getParam('curid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $courseid = base64_decode($this->_getParam('courseid'));
            $turno = base64_decode($this->_getParam('turno'));
            $this->view->turno=$turno;
            $this->view->perid=$perid;
            $this->view->uid=$uid;

            $where = array(
                'eid' => $eid, 'oid' => $oid,
                'escid' => $escid,'subid' => $subid,
                'courseid' => $courseid,'turno' => $turno,
                'perid' => $perid,'curid'=>$curid);

            $wherefac['eid'] = $whereesc['eid'] = $eid;
            $wherefac['oid'] = $whereesc['oid'] = $oid;
            $whereesc['escid'] = $escid;
            $whereesc['subid'] = $subid;
            $esc = new Api_Model_DbTable_Speciality();
            $dataesc = $esc->_getOne($whereesc);
            $this->view->speciality = $dataesc;

            $wherefac['facid'] = substr($escid, 0, 1);
            $fac = new Api_Model_DbTable_Faculty();
            $datafac = $fac->_getOne($wherefac);
            $this->view->faculty = $datafac;

            $whereuser['eid'] = $eid;
            $whereuser['oid'] = $oid;
            $whereuser['uid'] = $uid;
            $user = new Api_Model_DbTable_Users();
            $datauser = $user->_getUserXUid($whereuser);
            $this->view->person = $datauser;

            $wherecour['eid']=$eid;
            $wherecour['oid']=$oid;
            $wherecour['curid']=$curid;
            $wherecour['escid']=$escid;
            $wherecour['subid']=$subid;
            $wherecour['courseid']=$courseid;
            $cour = new Api_Model_DbTable_Course();
            $datacour = $cour->_getOne($wherecour);
            $this->view->course = $datacour;

            $wherepercour['eid']=$eid;
            $wherepercour['oid']=$oid;
            $wherepercour['courseid']=$courseid;
            $wherepercour['escid']=$escid;
            $wherepercour['perid']=$perid;
            $wherepercour['turno']=$turno;
            $wherepercour['subid']=$subid;
            $wherepercour['curid']=$curid;
            $percour = new Api_Model_DbTable_PeriodsCourses();
            $datapercour = $percour->_getOne($wherepercour);
            $this->view->periodcourse = $datapercour;

            $bdregcourse = new Api_Model_DbTable_Registrationxcourse();
            $dataregcour = $bdregcourse->_getFilter($where);
            if ($dataregcour) {
                $tam=count($dataregcour);
                $wherestu['eid']=$eid;
                $wherestu['oid']=$oid;
                for ($i=0; $i < $tam; $i++) { 
                    $wherestu['uid']=$dataregcour[$i]['uid'];
                    $datstudent = $user->_getUserXUid($wherestu);
                    $dataregcour[$i]['fullname'] = $datstudent[0]['last_name0']." ".$datstudent[0]['last_name1'].", ".$datstudent[0]['first_name'];
                }
            }
            $this->view->datastudent=$dataregcour;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}

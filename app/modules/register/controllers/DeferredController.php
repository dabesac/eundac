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
                $students_register[$key]['receipt']= $receipts; 
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
            // print_r($students_register);
            echo md5("123456");
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

            $courseid = trim($params['courseid']);
            $curid =    trim($params['curid']);
            $turno  =   trim($params['turno']);
            $perid  =   trim($params['perid']);
            $escid =    trim($params['escid']);
            $subid  =   trim($params['subid']);
            $regid =    trim($params['regid']);
            $uid   =    trim($params['uid']);

            /** notas **/

            $receipt    = ((isset($params['receipt']) == true && (!empty($params['receipt']) || (intval($params['receipt'])== 0) ) )?trim($params['receipt']):'');
            $notafinal    = ((isset($params['notafinal']) == true && (!empty($params['notafinal']) || (intval($params['notafinal'])== 0) ) )?trim($params['notafinal']):'');
            
            print($receipt); exit();


            // $

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json; 
        try {
            
        } catch (Exception $e) {
            
        }
    }
    

}

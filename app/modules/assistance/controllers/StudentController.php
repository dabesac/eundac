<?php

class Assistance_StudentController extends Zend_Controller_Action {

    public function init()
    {
    	
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
    }
}

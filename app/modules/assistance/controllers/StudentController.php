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
        }
        $this->view->infocurso = $infocurso;
        $this->view->infoassist = $infoassist;

        $this->view->turno = $turno;
    }
}

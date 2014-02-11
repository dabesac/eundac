<?php

class Docente_RegisterController extends Zend_Controller_Action {

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

    public function registertargetAction(){
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
        $url ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                        base64_encode('eid')."/".base64_encode($eid)."/".
                        base64_encode('escid')."/".base64_encode($escid)."/".
                        base64_encode('subid')."/".base64_encode($subid)."/".
                        base64_encode('courseid')."/".base64_encode($courseid)."/".
                        base64_encode('curid')."/".base64_encode($curid)."/".
                        base64_encode('turno')."/".base64_encode($turno)."/".
                        base64_encode('perid')."/".base64_encode($perid)."/".
                        base64_encode('action')."/".base64_encode('I')."/".
                        base64_encode('partial')."/".base64_encode($partial);

        $base_courses = new Api_Model_DbTable_Course();
        $infocourse = $base_courses->_getOne($where);
        $this->view->infocourse = $infocourse;

        $base_students = new Api_Model_DbTable_Registrationxcourse();
        $data_notes_students = $base_students ->_getStudentXcoursesXescidXperiods_sql($where);

        $this->view->turno = $turno;
        $this->view->partial = $partial;
        $this->view->students = $data_notes_students;
        $this->view->url = $url;

    }
    public function targetprintAction(){
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

            if ($info_speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }

        if ($info_speciality['parent'] != "") {
            $where['escid']=$info_speciality['parent'];
            $name_speciality = $base_speciality->_getOne($where);
            $info_speciality['speciality'] = $name_speciality['name'];
        }

        $this->view->namelogo=$namelogo;
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
    public function registerconpetencyAction(){
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
        $units      = trim($params['units']);

        $this->view->units=$units;
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

        $base_persentage = new Api_Model_DbTable_CourseCompetency();
        $result1 = $base_persentage->_getFilter($where,$attrib);

        $url ="/".base64_encode('oid')."/".base64_encode($oid)."/".
                        base64_encode('eid')."/".base64_encode($eid)."/".
                        base64_encode('escid')."/".base64_encode($escid)."/".
                        base64_encode('subid')."/".base64_encode($subid)."/".
                        base64_encode('courseid')."/".base64_encode($courseid)."/".
                        base64_encode('curid')."/".base64_encode($curid)."/".
                        base64_encode('turno')."/".base64_encode($turno)."/".
                        base64_encode('perid')."/".base64_encode($perid)."/".
                        base64_encode('units')."/".base64_encode($units)."/".
                        base64_encode('action')."/".base64_encode('I')."/".
                        base64_encode('partial')."/".base64_encode($partial);

        $base_courses = new Api_Model_DbTable_Course();
        $infocourse = $base_courses->_getOne($where);
        $this->view->infocourse = $infocourse;

        $base_students = new Api_Model_DbTable_Registrationxcourse();
        $data_notes_students = $base_students ->_getStudentXcoursesXescidXperiods_sql($where);


        $this->view->turno = $turno;
        $this->view->partial = $partial;
        $this->view->persetage = $result1[0];
        $this->view->url=$url;
        $this->view->action=$action;
        $this->view->students = $data_notes_students;
    }

    public function conpetencyprintAction(){
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
        $units      = trim($params['units']);
        $this->view->units=$units;
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

        $base_persentage = new Api_Model_DbTable_CourseCompetency();
        $result1 = $base_persentage->_getFilter($where,$attrib);

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
        $this->view->persetage = $result1[0];
        $this->view->students = $data_notes_students;
        $this->view->faculty = $this->sesion->faculty->name;
        $this->view->lasname= $this->sesion->infouser['fullname'];
        $this->_helper->layout->disableLayout();
    }
}
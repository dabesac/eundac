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
        $this->view->perid=$perid;

    }

    public function registertargetdirectedAction(){
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
        $this->view->perid=$perid;

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

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
        
        // $escid=$this->sesion->escid;
        // $where['escid']=$escid;
        $this->view->turno = $turno;
        $this->view->perid = $perid;
        $this->view->partial = $partial;
        $this->view->students = $data_notes_students;
        $this->view->lasname= $this->sesion->infouser['fullname'];
        $namef = strtoupper($this->sesion->faculty->name);

        $dbimpression = new Api_Model_DbTable_Impresscourse();
        
        $uidim=$this->sesion->pid;
        $code="notas_objetivo - ".$partial;
        $data = array(
            'eid'=>$eid,
            'oid'=>$oid,
            'perid'=>$perid,
            'courseid'=>$courseid,
            'escid'=>$escid,
            'subid'=>$subid,
            'curid'=>$curid,
            'turno'=>$turno,
            'register'=>$uidim,
            'created'=>date('Y-m-d H:i:s'),
            'code'=>$code
            );
        $dbimpression->_save($data);            

        $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>$code);
        $dataim = $dbimpression->_getFilter($wheri);
        $co=count($dataim);
        $codigo=$co." - ".$uidim;
        $this->view->codigo=$codigo;


        $header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];
        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);
        $header = str_replace("h2", "h3", $header);
        $header = str_replace("h3", "h5", $header);
        $header = str_replace("h4", "h6", $header);
        $header = str_replace("11%", "9%", $header);

        $footer = str_replace("h4", "h5", $footer);
        $footer = str_replace("h5", "h6", $footer);
        
        $this->view->header=$header;
        $this->view->footer=$footer;
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

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

        // $escid=$this->sesion->escid;
        // $where['escid']=$escid;
        $this->view->turno = $turno;
        $this->view->perid = $perid;
        $this->view->partial = $partial;
        $this->view->persetage = $result1[0];
        $this->view->students = $data_notes_students;
        $this->view->lasname= $this->sesion->infouser['fullname'];

        $dbimpression = new Api_Model_DbTable_Impresscourse();
        $uidim=$this->sesion->pid;
        $code="notas_competencia - ".$partial;
        $data = array(
            'eid'=>$eid,
            'oid'=>$oid,
            'perid'=>$perid,
            'courseid'=>$courseid,
            'escid'=>$escid,
            'subid'=>$subid,
            'curid'=>$curid,
            'turno'=>$turno,
            'register'=>$uidim,
            'created'=>date('Y-m-d H:i:s'),
            'code'=>$code
            );
        $dbimpression->_save($data); 

        $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>$code);
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
        $header = str_replace("h2", "h3", $header);
        $header = str_replace("h3", "h5", $header);
        $header = str_replace("h4", "h6", $header);
        $header = str_replace("11%", "9%", $header);

        $footer = str_replace("h4", "h5", $footer);
        $footer = str_replace("h5", "h6", $footer);
        
        $this->view->header=$header;
        $this->view->footer=$footer;
        $this->_helper->layout->disableLayout();
    }
}
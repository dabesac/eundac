<?php

class Docente_InformacademicController extends Zend_Controller_Action {

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

    public function indexAction(){
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->sesion->period->perid;
            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
            $this->view->infouser = $this->sesion->infouser['fullname'];
            $whereinf = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                    'perid' => $perid, 'pid' => $pid, 'uid' => $uid);
    
            $inform = new Api_Model_DbTable_Addreportacadadm();
            $informedoc = $inform->_getOne($whereinf);

            if ($informedoc['state']=='C') {
                $this->_helper->redirector('viewimpression','informacademic','docente');
            }

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function saveinfoAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->sesion->period->perid;
                
            $whereinf = array(
                    'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                    'perid' => $perid, 'pid' => $pid, 'uid' => $uid);
    
            $inform = new Api_Model_DbTable_Addreportacadadm();
            $informedoc = $inform->_getOne($whereinf);
            

            if ($this->getRequest()->isPost()){
                $formData = $this->getRequest()->getPost();
                if ($formData) {                    
                    // $cont=count($formData['courseid']);
                    // $courtea = new Api_Model_DbTable_Coursexteacher();
                    // for ($i=0; $i < $cont ; $i++){ 
                    //     $pk = array(
                    //         'eid' => $eid, 'oid' => $oid, 'perid' => $perid,
                    //         'escid' => $formData['escid'][$i], 'subid' => $formData['subid'][$i], 
                    //         'courseid' => $formData['courseid'][$i], 'curid' => $formData['curid'][$i], 'turno' => $formData['turno'][$i]);
                    //     $datacourtea = array('percentage' => $formData['percentage'][$i]);
                    //     $courtea ->_updateXcourse($datacourtea,$pk);
                    // }
                
                    $data = array(
                    'acad_tutoria' => $formData['acad_tutoria'], 'acad_medios' => $formData['acad_medios'],
                    'adm_acreditacion' => $formData['adm_acreditacion'], 'adm_labores' => $formData['adm_labores'], 'adm_asesoria' => $formData['adm_asesoria'], 'adm_investigacion' => $formData['adm_investigacion']);
                    // if (!is_null($formData['enviar'])) {

                        if (!$informedoc){
                            $data['oid']=$oid;
                            $data['eid']=$eid;
                            $data['perid']=$perid;
                            $data['subid']=$subid;
                            $data['pid']=$pid;
                            $data['uid']=$uid;
                            $data['escid']=$escid;
                            $data['number']="001 - ".$perid;
                            $data['created']=date("Y-m-d");
                            $data['state']='B';
                            $inform ->_save($data);
                            // print_r($data);exit();
                        }
                        else{
                            $pk = array(
                                'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                                'perid' => $perid, 'uid' => $uid, 'pid' => $pid);
                            $inform ->_update($data,$pk);
                        }
                        // $form->populate($data); 
                }
            }
            // $wherecour = array('eid' => $eid, 'oid' => $oid, 
            //     'perid' => $perid, 'uid' => $uid, 'pid' => $pid);
            // $percour= new Api_Model_DbTable_PeriodsCourses();
            // $coursesdoc=$percour->_getInfoCourseXTeacher($wherecour);
            // if ($coursesdoc) {
            //     $tam = count($coursesdoc);
            //     $wherecours = array('eid' => $eid, 'oid' => $oid);
            //     $cour = new Api_Model_DbTable_Course();
            //     for ($i=0; $i < $tam; $i++) { 
            //         $wherecours['curid'] = $coursesdoc[$i]['curid'];
            //         $wherecours['escid'] = $coursesdoc[$i]['escid'];
            //         $wherecours['subid'] = $coursesdoc[$i]['subid'];
            //         $wherecours['courseid'] = $coursesdoc[$i]['courseid'];
            //         $datacourse = $cour->_getOne($wherecours);
            //         $coursesdoc[$i]['name'] = $datacourse['name'];
            //     }
            // }
            // $this->view->datacourses=$coursesdoc;
            // $this->view->form=$form;
        } catch (Exception $e) {
            print "Error ".$e->getMessage();
        }
    }
    public function closeinfoAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->sesion->period->perid;
            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
            $this->view->infouser = $this->sesion->infouser['fullname'];
            $this->view->perid = $perid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->pid = $pid;
            $this->view->uid = $uid;
                
            $whereinf = array(
                    'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                    'perid' => $perid, 'pid' => $pid, 'uid' => $uid);
            $form = new Docente_Form_Informacademic();
            $inform = new Api_Model_DbTable_Addreportacadadm();
            $informedoc = $inform->_getOne($whereinf);
            if ($informedoc) {
                $this->view->informedoc = $informedoc;
                $form->populate($informedoc);
            }

            if ($this->getRequest()->isPost()){
                $formData = $this->getRequest()->getPost();
                if ($formData) {                    
                    $cont=count($formData['courseid']);
                    $courtea = new Api_Model_DbTable_Coursexteacher();
                    for ($i=0; $i < $cont ; $i++){ 
                        $pk = array(
                            'eid' => $eid, 'oid' => $oid, 'perid' => $perid,
                            'escid' => $formData['escid'][$i], 'subid' => $formData['subid'][$i], 
                            'courseid' => $formData['courseid'][$i], 'curid' => $formData['curid'][$i], 'turno' => $formData['turno'][$i]);
                        $datacourtea = array('percentage' => $formData['percentage'][$i]);
                        $courtea ->_updateXcourse($datacourtea,$pk);
                    }
                
                    $data = array(
                    'acad_tutoria' => $formData['acad_tutoria'], 'acad_medios' => $formData['acad_medios'],
                    'adm_acreditacion' => $formData['adm_acreditacion'], 'adm_labores' => $formData['adm_labores'], 'adm_asesoria' => $formData['adm_asesoria'], 'adm_investigacion' => $formData['adm_investigacion']);
                    // if (!is_null($formData['enviar'])) {
                        if ($form->isValid($formData)) {
                            // print_r($formData);exit();
                            $pk = array(
                                'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                                'perid' => $perid, 'uid' => $uid, 'pid' => $pid);
                            $inform ->_update($data,$pk);
                            $dataend = array('state' => 'C');
                            if ($inform ->_update($dataend,$pk)) $this->view->cierre=1;
                        }
                    }
                }
            $wherecour = array('eid' => $eid, 'oid' => $oid,'perid' => $perid, 'uid' => $uid, 'pid' => $pid);
            $percour= new Api_Model_DbTable_PeriodsCourses();
            $coursesdoc=$percour->_getInfoCourseXTeacher($wherecour);
            if ($coursesdoc) {
                $tam = count($coursesdoc);
                $wherecours = array('eid' => $eid, 'oid' => $oid);
                $cour = new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecours['curid'] = $coursesdoc[$i]['curid'];
                    $wherecours['escid'] = $coursesdoc[$i]['escid'];
                    $wherecours['subid'] = $coursesdoc[$i]['subid'];
                    $wherecours['courseid'] = $coursesdoc[$i]['courseid'];
                    $datacourse = $cour->_getOne($wherecours);
                    $coursesdoc[$i]['name'] = $datacourse['name'];
                }
            }
            $this->view->datacourses=$coursesdoc;
            $this->view->form=$form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function viewimpressionAction(){
        try{
            // $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->sesion->period->perid;
            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
            $this->view->infouser = $this->sesion->infouser['fullname'];
            $this->view->perid = $perid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->pid = $pid;
            $this->view->uid = $uid;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }

    }
    public function printAction(){
        try {
            
            // $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $formData = $this->getRequest()->getPost();
            print_r($formData);exit();
            $pid = base64_decode($this->_getParam('pid'));
            $uid = base64_decode($this->_getParam('uid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $perid = base64_decode($this->_getParam('perid'));
            $this->view->speciality = $this->sesion->speciality->name;
            $namef = strtoupper($this->sesion->faculty->name);
            $this->view->infouser = $this->sesion->infouser['fullname'];
            $this->view->perid = $perid;

            $wherecour = array('eid' => $eid, 'oid' => $oid, 
                'perid' => $perid, 'uid' => $uid, 'pid' => $pid);
            $percour= new Api_Model_DbTable_PeriodsCourses();
            $coursesdoc=$percour->_getInfoCourseXTeacher($wherecour);
            if ($coursesdoc) {
                $tam = count($coursesdoc);
                $wherecours = array('eid' => $eid, 'oid' => $oid);
                $cour = new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecours['curid'] = $coursesdoc[$i]['curid'];
                    $wherecours['escid'] = $coursesdoc[$i]['escid'];
                    $wherecours['subid'] = $coursesdoc[$i]['subid'];
                    $wherecours['courseid'] = $coursesdoc[$i]['courseid'];
                    $datacourse = $cour->_getOne($wherecours);
                    $coursesdoc[$i]['name'] = $datacourse['name'];
                }
            }
            $this->view->datacourses=$coursesdoc;


            $whereinf = array(
                    'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                    'perid' => $perid, 'pid' => $pid, 'uid' => $uid);
            $inform = new Api_Model_DbTable_Addreportacadadm();
            $informedoc = $inform->_getOne($whereinf);
            $this->view->informedoc = $informedoc;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
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

            if ($speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }
            
            // $escid=$this->sesion->escid;
            // $where['escid']=$escid;

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            // $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            // $pid=$uidim;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'informe_academico',
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );

            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'informe_academico');
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=count($dataim);            
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
            $header = str_replace("h2", "h3", $header);
            $header = str_replace("h3", "h5", $header);
            $header = str_replace("h4", "h6", $header);
            $header = str_replace("10%", "8%", $header);

            $footer = str_replace("h4", "h5", $footer);
            $footer = str_replace("h5", "h6", $footer);
            
            $this->view->header=$header;
            $this->view->footer=$footer;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


    public function printpruebaAction(){
        try {
            
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = base64_decode($this->_getParam('pid'));
            $uid = base64_decode($this->_getParam('uid'));
            echo $escid = base64_decode($this->_getParam('escid'));
            echo $subid = base64_decode($this->_getParam('subid'));
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            // $escid='2EI';
            // $subid='1901';

            $perid = base64_decode($this->_getParam('perid'));
            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
            $this->view->infouser = $this->sesion->infouser['fullname'];
            $this->view->perid = $perid;

            $wherescid= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            //print_r($wherescid);
            $esc= new Api_Model_DbTable_Speciality();
            $datescid=$esc->_getOne($wherescid);      

            $date=$datescid['header'];
            $this->view->header=$date;

            

            
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
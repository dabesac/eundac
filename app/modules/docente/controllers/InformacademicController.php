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
            
            // -----------------------------------------
            if ($this->getRequest()->isPost()){
                $formData = $this->getRequest()->getPost();
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
                
                if ($formData['enviar']<>'') {
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
                    }else{
                        $pk = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                            'perid' => $perid, 'uid' => $uid, 'pid' => $pid);
                        $inform ->_update($data,$pk);
                    }
                    $form->populate($data);
                }else{
                    $formData['enviar']='Guardar Avance';
                    if ($form->isValid($formData)) {
                        $pk = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid,
                            'perid' => $perid, 'uid' => $uid, 'pid' => $pid);
                        $inform ->_update($data,$pk);
                        $dataend = array('state' => 'C');
                        if ($inform ->_update($dataend,$pk)) $this->view->cierre=1;
                    }
                }
            }

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
            $this->view->form=$form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function printAction(){
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = base64_decode($this->_getParam('pid'));
            $uid = base64_decode($this->_getParam('uid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $perid = base64_decode($this->_getParam('perid'));
            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
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
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
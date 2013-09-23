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
            $perid = $this->sesion->period->perid;
            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
            $this->view->infouser = $this->sesion->infouser['fullname'];
            $this->view->perid=$perid;
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

            $form = new Docente_Form_Informacademic();
            $inform = new Api_Model_DbTable_Addreportacadadm();
            // $informedoc=$informe->_getInformeXDocente($eid,$oid,$pid,$uid,$perid,$sedid,$escid);
            // if ($informedoc){
            //     $frminforme->populate($informedoc);
            //     if ($informedoc['estado']=='B')
            //     {
            //     $flag='S';
            //     $this->view->flag=$flag;
            //     $this->view->vista='E';
            //     }
                
            //     if ($informedoc['estado']=='C')
            //     {
            //     $flag='C';
            //     $this->view->flag=$flag;
            //     $this->view->vista='I';
                
            //     }
                        
            //     //$this->frm->porcentaje_desarrollo->setValue($data['porcentaje_desarrollo']);
            // }
            // else {
            //     $flag='N';
            //     $this->view->flag=$flag;
            //     $this->view->vista='E';
            // }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
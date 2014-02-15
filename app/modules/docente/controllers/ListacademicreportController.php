<?php

class Docente_ListacademicreportController extends Zend_Controller_Action {

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
   	    // 
        // $date =new  Zend_Date();
        // $valor=$date->toString('Y');
        // $this->view->anio=$valor;
    
    }

     public function listperiodsAction()
    {
    	try{
    		$this->_helper->layout()->disableLayout();
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$where['eid']=$eid;
    		$where['oid']=$oid;
	        $anio = $this->_getParam("anio");
	        if ($eid=="" || $oid==""||$anio=="") return false;
    		$anior = substr($anio, 2, 4);
    		$where['year']=$anior;
	        $periods = new Api_Model_DbTable_Periods();
	        $per=$periods->_getPeriodsxYears($where);
    		//print_r($per);
	        $this->view->listper=$per;
		}catch(exception $ex){
    		print "Error en listar Periodo";	
    	}
    }

    public function listteachersAction()
    {
    	try{
    		//$this->_helper->layout()->disableLayout();
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
            $subid=$this->sesion->subid;
            $escid=$this->sesion->escid;
            //print_r($this->sesion);
            $where['eid']=$eid;
            $where['oid']=$oid;
            $perid=$this->_getParam("perid");
            $where['perid']=$perid;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->escid=$escid;     
            $this->view->eid=$eid;     
            $this->view->oid=$oid;     
            $dbteachers= new Api_Model_DbTable_Coursexteacher();        

            $teachers=$dbteachers->_getTeachersXPeridXEscid($eid,$oid,$escid,$perid);
            //print_r($teachers);
    		$this->view->packdatat=$teachers;		
    		
    	}catch(exception $ex){
    		print "Error en listar Cursos";		
    	}
    }


    public function printAction(){
        try {
            
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = base64_decode($this->_getParam('pid'));
            $uid = base64_decode($this->_getParam('uid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $perid = base64_decode($this->_getParam('perid'));
            $nombre = base64_decode($this->_getParam('nombre'));
            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
            //$this->view->infouser = $this->sesion->infouser['fullname'];
            $this->view->infouser = $nombre;
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

    public function printreportAction(){
        try 
        {
            //$this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $subid=$this->sesion->subid;
            $escid=$this->sesion->escid;
            //print_r($this->sesion);
            $where['eid']=$eid;
            $where['oid']=$oid;
            $perid=$this->_getParam("perid");
            
            $where['perid']=$perid;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->escid=$escid;     
            $this->view->eid=$eid;     
            $this->view->oid=$oid;     

            $this->view->speciality = $this->sesion->speciality->name;
            $this->view->faculty = $this->sesion->faculty->name;
            $this->view->infouser = $this->sesion->infouser['fullname'];

            $dbteachers= new Api_Model_DbTable_Coursexteacher();        

            $teachers=$dbteachers->_getTeachersXPeridXEscid($eid,$oid,$escid,$perid);
            //print_r($teachers);
            $this->view->packdatat=$teachers;   

        }
        catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

}
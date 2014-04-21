<?php

class Graduated_GraphicgraduatedController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (!$login->modulo=="graduated"){
    	// 	$this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $facid = $this->sesion->faculty->facid;
            $escid = $this->sesion->escid;
            $is_director = $this->sesion->infouser['teacher']['is_director'];
            if ($rid=="DC" && $is_director=="S"){
                $rid="DIREC";
                if ($facid=="2") $escid=substr($escid,0,3);
                $this->view->escid=$escid;        
            }
            if ($rid=="RF" || $rid=="DIREC") $this->view->facid=$facid;
            $where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getFilter($where,$attrib=null,$orders=null);
            $this->view->facultades=$facultad;
            $anio = date('Y');
            $this->view->anio = $anio;
            $this->view->rid = $rid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function graphicsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $facid = $this->_getParam('facid');
            $escid = $this->_getParam('escid');
            $espec = $this->_getParam('especialidad');
            $perid = $this->_getParam('perid');
            $anho = $this->_getParam('anho');
            $this->view->facid = $facid;
            $this->view->escid = $escid;
            $this->view->perid = $perid;
            $this->view->especialidad = $espec;
            $this->view->anho = $anho;

            $fac = new Api_Model_DbTable_Faculty();
            $datafac = $fac->_getOne($where=array('eid' => $eid, 'oid' => $oid, 'facid' => $facid));
            $this->view->faculty = $datafac;

            $whereesc = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid);
            $esc = new Api_Model_DbTable_Speciality();
            $dataesc = $esc->_getFilter($whereesc,$attrib=null,$orders=null);
            $this->view->school = $dataesc[0];
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function loadgraphicsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $this->view->eid = $eid;
            $this->view->oid = $oid;
            $facid = $this->_getParam('facid');
            $escid = $this->_getParam('escid');
            $espec = $this->_getParam('especialidad');
            $perid = $this->_getParam('perid');
            $anho = $this->_getParam('anho');

            $user = new Api_Model_DbTable_Users();
            $left='';
            if($perid!='T'){
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") { 
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'perid' => $perid);
                    }else{
                        if ($espec<>"") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'facid' => $facid, 
                            'perid' => $perid, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'perid' => $perid);
                $egre=$user->_getTotalGraduatedXFacultyXSchoolXPeriod($where);
            }else{
                $ano=substr($anho,2,4);
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") {
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'anio' => $ano);
                    }else{
                        if ($espec<>"") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                            'anio' => $ano, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'anio' => $ano);
                $egre=$user->_getTotalGraduatedXFacultyXSchoolXAnho($where);
            }
            $this->view->egresados = $egre;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
<?php

class Admin_GeneratedeferredController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="admin"){
    		$this->_helper->redirector('index','index','admin');
    	}
    	$this->sesion = $login;
       
    }
    public function indexAction()
    {
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $esc = new Api_Model_DbTable_Faculty();
            $data_esc = $esc->_getFilter($where,$attrib=null,$orders = array('facid'));
            $this->view->faculty = $data_esc;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function periodsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $perid = $this->sesion->period->perid;
            $this->view->perid = $perid;
            $anio = $this->_getParam('anio');
            $per = new Api_Model_DbTable_Periods();
            $dat_per = $per->_getPeriodsXAyB($where = array('eid' => $eid, 'oid' => $oid, 'p1' => $anio."A", 'p2' => $anio."B"));
            $this->view->period = $dat_per;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function schoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $facid = $this->_getParam('facid');
            $perid = $this->_getParam('perid');
            $this->view->eid = $eid;
            $this->view->oid = $oid;
            $this->view->perid = $perid;
            $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'state' => 'A');
            $esc = new Api_Model_DbTable_Speciality();
            $data = $esc->_getFilter($where, $attrib=null, $orders = array('escid'));
            $this->view->data_school = $data;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function generateAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = ($this->_getParam("escid")); 
            $perid = ($this->_getParam("perid"));

            if(substr($perid,2,1)=='A') $peridapla = substr($perid,0,2).'D';
            else $peridapla = substr($perid,0,2).'E';

            $where = array('perid' => $perid, 'escid' => $escid, 'perid_apla' => $peridapla);
            $matcur = new Api_Model_DbTable_Registrationxcourse();
            $data = $matcur->_generateDeferred($where);
            $this->view->data = $data[0]['aplazados2'];
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
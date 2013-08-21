<?php

class RegisternotesactasController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

	public function indexAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$where['eid']=$eid;
			$where['oid']=$oid;
			$escu= new Api_Model_DbTable_Speciality();
			$escuela=$escu->_getAll($where,$order="escid asc",$start=0,$limit=0);
			$this->view->escuelas=$escuela;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function lperiodsAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid= $this->sesion->eid;
			$oid= $this->sesion->oid;
			$anio = $this->_getParam("anio");
	        $anior = substr($anio, 2, 3);
	        $data['eid']=$eid;
	        $data['oid']=$oid;
	        $data['year']=$anior;
	        $periodos = new Api_Model_DbTable_Periods();
            $this->view->lper = $periodos->_getPeriodsxYears($data);
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function lsemesterAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=$this->_getParam('escid');
			$perid=$this->_getParam('perid');
			$sem= new Api_Model_DbTable_Semester();
			$semestre=$sem->_getSemestreXPer($eid,$oid,$perid,$escid);
			$this->view->semestres=$semestre;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
}
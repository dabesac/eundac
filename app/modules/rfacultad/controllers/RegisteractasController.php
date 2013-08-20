<?php
class Rfacultad_RegisteractasController extends Zend_Controller_Action
{
	public function init()
	{
		$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="rfacultad"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
	}

	public function indexAction(){
		try {
			$eid= $this->sesion->eid;
			$oid= $this->sesion->oid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $escuelas = new Api_Model_DbTable_Speciality();
            $lescuelas = $escuelas->_getAll($where,$order="escid asc",$start=0,$limit=0);
            $this->view->lescuelas=$lescuelas;
            $semestre= new Api_Model_DbTable_Semester();
            $lsemestre = $semestre->_getAll($where,$order='cast(semid as integer)',$start=0,$limit=0);
            $this->view->lsemestre=$lsemestre;
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
}
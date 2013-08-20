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

	public function lcurriculaAction(){
		try {
			$this->_helper->layout()->disableLayout();
    		$eid= $this->sesion->eid;
	        $oid= $this->sesion->oid;
	        $escid = $this->_getParam("escid");
	        $where['eid']=$eid;
	        $where['oid']=$oid;
	        $where['escid']=$escid;
	        $curricula = new Api_Model_DbTable_Curricula();
	        $lista=$curricula->_getFilter($where,$attrib=null,$orders=null);
            $this->view->lista=$lista;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function coursesxcurriculaAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $curid= $this->_getParam("curid");
            $escid= $this->_getParam("escid");
            $semid= $this->_getParam("semid");
            $perid= $this->_getParam("perid");
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['curid']=$curid;
            $where['escid']=$escid;
            $where['semid']=$semid;
            $where['perid']=$perid;
            $dbcurso = new Api_Model_DbTable_PeriodsCourses();
            $curso = $dbcurso->_getAllcoursesXescidXsemester($where);
            $this->view->cursito=$curso;  
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function registerAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $curid= $this->_getParam("curid");
            $escid= $this->_getParam("escid");
            $perid= $this->_getParam("perid");
            $courseid= $this->_getParam("courseid");
            $turno= $this->_getParam("turno");
            $subid= $this->_getParam("subid");
            $this->view->curid=$curid;
            $this->view->subid=$subid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->courseid=$courseid;
            $this->view->turno=$turno;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['curid']=$curid;
            $where['escid']=$escid;
            $where['perid']=$perid;
            $where['courseid']=$courseid;
            $where['turno']=$turno;
            $where['subid']=$subid;
            $dbalumnos = new Api_Model_DbTable_Registrationxcourse();
            $datos = $dbalumnos->_getStudentXcoursesXescidXperiods($where);
            $this->view->datos=$datos;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function searchuserAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $rid='AL';
            $uid= $this->_getParam("uid");
            $curid= $this->_getParam("curid");
            $escid= $this->_getParam("escid");
            $perid= $this->_getParam("perid");
            $courseid= $this->_getParam("courseid");
            $turno= $this->_getParam("turno");
            $subid= $this->_getParam("subid");   
            $this->view->curid=$curid;
            $this->view->subid=$subid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->courseid=$courseid;
            $this->view->turno=$turno;        
            $this->view->uid=$uid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['rid']=$rid;
            $where['uid']=$uid;
            $where['escid']=$escid;
            $al = new Api_Model_DbTable_Users();
            $dato = $al->_getUserXUidXEscidXRid($where);
            $this->view->dato=$dato;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
}
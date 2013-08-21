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
			$where['eid']=$eid;
			$where['oid']=$oid;
			$where['escid']=$escid;
			$where['perid']=$perid;
			$sem= new Api_Model_DbTable_Semester();
			$semestre=$sem->_getSemesterXPeriodsXEscid($where);
			$this->view->semestres=$semestre;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function lcurriculasAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=$this->_getParam('escid');
			$where['eid']=$eid;
			$where['oid']=$oid;
			$where['escid']=$escid;
			$curr= new Api_Model_DbTable_Curricula();
			$curricula=$curr->_getFilter($where,$attrib=null,$orders=null);
			$this->view->curriculas=$curricula;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function lcoursesAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$curid=$this->_getParam('curid');
			$escid=$this->_getParam('escid');
			$semid=$this->_getParam('semid');
			$perid=$this->_getParam('perid');
			$where['eid']=$eid;
            $where['oid']=$oid;
            $where['curid']=$curid;
            $where['escid']=$escid;
            $where['semid']=$semid;
            $where['perid']=$perid;
			$cur= new Api_Model_DbTable_PeriodsCourses();
			$curso=$cur->_getAllcoursesXescidXsemester($where);
			$this->view->cursos=$curso;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function studentsAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$curid=$this->_getParam('curid');
			$escid=$this->_getParam('escid');
			$courseid=$this->_getParam('courseid');
			$perid=$this->_getParam('perid');
			$turno=$this->_getParam('turno');
			$subid=$this->_getParam('subid');
			$this->view->curid=$curid;
			$this->view->escid=$escid;
			$this->view->courseid=$courseid;
			$this->view->perid=$perid;
			$this->view->turno=$turno;
			$this->view->subid=$subid;
			$whereper['eid']=$eid;
			$whereper['oid']=$oid;
			$whereper['courseid']=$courseid;
			$whereper['escid']=$escid;
			$whereper['perid']=$perid;
			$whereper['turno']=$turno;
			$whereper['subid']=$subid;
			$whereper['curid']=$curid;
			$cur= new Api_Model_DbTable_PeriodsCourses();
			$datpercur=$cur->_getOne($whereper);
			$this->view->periodocurso=$datpercur;
			$wheremat['eid']=$eid;
			$wheremat['oid']=$oid;
			$wheremat['perid']=$perid;
			$wheremat['curid']=$curid;
			$wheremat['escid']=$escid;
			$wheremat['courseid']=$courseid;
			$wheremat['turno']=$turno;
			$wheremat['subid']=$subid;
			$mat= new Api_Model_DbTable_Registrationxcourse();
			$alumnos=$mat->_getStudentXcoursesXescidXperiods($wheremat);
			$this->view->alumnos=$alumnos;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function savenotesAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->_getParam('eid');
			$oid=$this->_getParam('oid');
			$uid=$this->_getParam('uid');
			$pid=$this->_getParam('pid');
			$subid=$this->_getParam('subid');
			$escid=$this->_getParam('escid');
			$perid=$this->_getParam('perid');
			$curid=$this->_getParam('curid');
			$courseid=$this->_getParam('courseid');
			$turno=$this->_getParam('turno');
			$regid=$this->_getParam('regid');
			$notafinal=$this->_getParam('notafinal');
			$tam=count($notafinal);
			for ($i=0; $i < $tam; $i++) { 
				$pk['eid']=$eid[$i];
				$pk['oid']=$oid[$i];
				$pk['escid']=$escid[$i];
				$pk['subid']=$subid[$i];
				$pk['courseid']=$courseid[$i];
				$pk['curid']=$curid[$i];
				$pk['regid']=$regid[$i];
				$pk['turno']=$turno[$i];
				$pk['pid']=$pid[$i];
				$pk['uid']=$uid[$i];
				$pk['perid']=$perid[$i];
				$data['notafinal']=$notafinal[$i];
				$mat= new Api_Model_DbTable_Registrationxcourse();
				$mat->_update($data,$pk);
				if ($i==$tam-1) $this->view->bandera=1;
			}
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function closeactaAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$curid=$this->_getParam('curid');
			$escid=$this->_getParam('escid');
			$courseid=$this->_getParam('courseid');
			$perid=$this->_getParam('perid');
			$turno=$this->_getParam('turno');
			$subid=$this->_getParam('subid');
			$this->view->curid=$curid;
			$this->view->escid=$escid;
			$this->view->courseid=$courseid;
			$this->view->perid=$perid;
			$this->view->turno=$turno;
			$this->view->subid=$subid;
			$pk['eid']=$eid;
			$pk['oid']=$oid;
			$pk['perid']=$perid;
			$pk['courseid']=$courseid;
			$pk['escid']=$escid;
			$pk['subid']=$subid;
			$pk['curid']=$curid;
			$pk['turno']=$turno;
	        $data['state_record']='C';
	        $data['state']='C';
			$cur= new Api_Model_DbTable_PeriodsCourses();
			if($cur->_update($data,$pk)) $this->view->flag=1;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
}
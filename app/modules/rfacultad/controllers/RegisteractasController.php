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
            $escid= $this->sesion->escid;
            $this->view->escid=$escid;
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

	public function addstudentAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $uid= $this->_getParam("uid");
            $pid= $this->_getParam("pid");
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
            $regid=$uid.$perid;
            $wherereg['eid']=$eid;
            $wherereg['oid']=$oid;
            $wherereg['escid']=$escid;
            $wherereg['subid']=$subid;
            $wherereg['regid']=$regid;
            $wherereg['pid']=$pid;
            $wherereg['uid']=$uid;
            $wherereg['perid']=$perid;
            $mat = new Api_Model_DbTable_Registration();
            $datmat=$mat->_getOne($wherereg);
            if (!$datmat) {
                $datamat['eid']=$eid;
                $datamat['oid']=$oid;
                $datamat['regid']=$regid;
                $datamat['pid']=$pid;
                $datamat['escid']=$escid;
                $datamat['uid']=$uid;
                $datamat['perid']=$perid;
                $datamat['subid']=$subid;
                $datamat['semid']='0';
                $datamat['credits']='0';
                $datamat['date_register']=date('Y-m-d');
                $datamat['register']=$this->sesion->uid;
                $datamat['created']=date('Y-m-d');
                $datamat['state']='M';
                $datamat['count']=0;
                $mat->_save($datamat);
            }
            $exiswhere['eid']=$eid;
            $exiswhere['oid']=$oid;
            $exiswhere['escid']=$escid;
            $exiswhere['subid']=$subid;
            $exiswhere['courseid']=$courseid;
            $exiswhere['curid']=$curid;
            $exiswhere['regid']=$regid;
            $exiswhere['turno']=$turno;
            $exiswhere['pid']=$pid;
            $exiswhere['uid']=$uid;
            $exiswhere['perid']=$perid;
            
            $matcur= new Api_Model_DbTable_Registrationxcourse();
            $exismat=$matcur->_getOne($exiswhere);
            if ($exismat) $this->view->bandera=0;
            else{
            	$data=$exiswhere;
            	$data['created']=date('Y-m-d');
            	$data['register']=$this->sesion->uid;
            	$data['state']='M';
                $data['approved']=$this->sesion->uid;
            	$matcur->_save($data);
            	$this->view->bandera=1;
            }
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function addcourseAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $curid= $this->_getParam("curid");
            $escid= $this->_getParam("escid");
            $semid= $this->_getParam("semid");
            $perid= $this->_getParam("perid");
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->curid=$curid;
            $this->view->escid=$escid;
            $this->view->semid=$semid;
            $this->view->perid=$perid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['curid']=$curid;
            $dbcurso = new Api_Model_DbTable_Course();
            $curso = $dbcurso->_getFilter($where,$attrib=null,$orders='courseid');
            $this->view->curso=$curso;
            $wherespe['eid']=$eid;
            $wherespe['oid']=$oid;
            $escuelas = new Api_Model_DbTable_Speciality();
            $lescuelas = $escuelas->_getAll($wherespe,$order="escid asc",$start=0,$limit=0);
            $this->view->lescuelas=$lescuelas;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function lteachersAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid = $this->_getParam("escid");
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['rid']="DC";
            $where['state']="A";
            $docentes = new Api_Model_DbTable_Users();
            $lista=$docentes->_getUsersXEscidXRidXState($where);
            $this->view->lista=$lista;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function savecourseAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid = $this->_getParam("escid");
            $courseid = $this->_getParam("courseid");
            $turno = $this->_getParam("turno");
            $tipo = $this->_getParam("tipo");
            $recibo = $this->_getParam("recibo");
            $p_uid = $this->_getParam("uid");
            $p_pid = $this->_getParam("pid");
            $curid = $this->_getParam("curid");
            $semid = $this->_getParam("semid");
            $perid = $this->_getParam("perid");
            $subid = $this->_getParam("subid");
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['courseid']=$courseid;
            $where['escid']=$escid;
            $where['perid']=$perid;
            $where['turno']=$turno;
            $where['subid']=$subid;
            $where['curid']=$curid;
            $vpercurso = new Api_Model_DbTable_PeriodsCourses();
            $lcurso= $vpercurso->_getOne($where);
            if ($lcurso){
                $this->view->msg=0;
            }else{
                $data['eid']=$eid;
                $data['oid']=$oid;
                $data['perid']=$perid;
                $data['curid']=$curid;
                $data['turno']=$turno;
                $data['courseid']=$courseid;
                $data['semid']=$semid;
                $data['escid']=$escid;
                $data['subid']=$subid;                                                                          
                $data['type_rate']=$tipo;
                $data['receipt']=$recibo;
                $data['state_record']='A';
                $data['register']=$this->sesion->uid;
                $data['created']=date('Y-m-d');
                $data['state']='A';
                $vpercurso->_save($data);
                $curexis= $vpercurso->_getOne($where);
                if ($curexis) {
                    $datadocente['subid']=$subid;
                    $datadocente['eid']=$eid;
                    $datadocente['oid']=$oid;
                    $datadocente['escid']=$escid;
                    $datadocente['turno']=$turno;
                    $datadocente['courseid']=$courseid;
                    $datadocente['curid']=$curid;
                    $datadocente['perid']=$perid;
                    $datadocente['uid']=$p_uid;
                    $datadocente['pid']=$p_pid;
                    $datadocente['state']="A";
                    $datadocente['is_main']="S";
                    $datadocente['semid']=$semid;
                    $docentecurso = new Api_Model_DbTable_Coursexteacher();
                    $docentecurso->_save($datadocente);
                    $wheredoc=$where;
                    $wheredoc['uid']=$p_uid;
                    $wheredoc['pid']=$p_pid;
                    $regdoc = $docentecurso->_getOne($wheredoc);
                    if ($regdoc) $this->view->msg=1;
                }
            }
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
}
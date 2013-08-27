<?php
class Record_DirectedController extends Zend_Controller_Action {

    public function init(){
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (!$login->modulo=="record"){
    	// 	$this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }
    
    public function indexAction(){
    }

    public function studentAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->_getParam('uid');
            $this->view->uid=$uid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['uid']=$uid;
            $user= new Api_Model_DbTable_Users();
            $datauser=$user->_getUserXUid($where);
            $this->view->user=$datauser[0];
            
            $year=date('Y');
            $anio=substr($year,2,3);
            $whereper1['eid']=$eid;
            $whereper1['oid']=$oid;
            $whereper1['perid']=$anio.'J';
            $peri= new Api_Model_DbTable_Periods();
            $periods[0]=$peri->_getOnePeriod($whereper1);
            $whereper1['perid']=$anio.'S';
            $periods[1]=$peri->_getOnePeriod($whereper1);
            $this->view->periods=$periods;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function registerAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->_getParam('uid');
            $pid=$this->_getParam('pid');
            $subid=$this->_getParam('subid');
            $escid=$this->_getParam('escid');
            $perid=$this->_getParam('perid');
            $this->view->uid = $uid;
            $this->view->pid = $pid;
            $this->view->perid = $perid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;

            $wherecur['eid']=$eid;
            $wherecur['oid']=$oid;
            $wherecur['escid']=$escid;
            $wherecur['state']="A";
            $curiculas = new Api_Model_DbTable_Curricula();
            $cur0=$curiculas->_getFilter($wherecur,$attrib=null,$orders='curid');
            $wherecur['state']="T";
            $cur1=$curiculas->_getFilter($wherecur,$attrib=null,$orders='curid');
            $cur=array_merge($cur0,$cur1);
            $this->view->curriculas=$cur;

            $wheresc['eid']=$eid;
            $wheresc['oid']=$oid;
            $wheresc['state']="A";
            $rescu = new Api_Model_DbTable_Speciality();
            $lista = $rescu->_getFilter($wheresc,$attrib=null,$orders='escid');
            $this->view->lescuelas = $lista;
            
            $wheresub['eid']=$eid;
            $wheresub['oid']=$oid;
            $wheresub['subid']=$subid;
            $wheresub['escid']=$escid;
            $wheresub['uid']=$uid;
            $wheresub['pid']=$pid;
            $wheresub['perid']=$perid;            
            $dblistarcon = new Api_Model_DbTable_Registrationxcourse();
            $convalidados = $dblistarcon->_getFilter($wheresub,$attrib=null,$orders='courseid');
            if ($convalidados) {
                $tamm=count($convalidados);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['subid']=$subid;
                for ($i=0; $i < $tamm; $i++) { 
                    $wherecourse['curid']=$convalidados[$i]['curid'];
                    $wherecourse['courseid']=$convalidados[$i]['courseid'];
                    $cours= new Api_Model_DbTable_Course();
                    $dbcourse=$cours->_getOne($wherecourse);
                    $convalidados[$i]['name_course']=$dbcourse['name'];
                    $convalidados[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->cursosconvalidados = $convalidados;
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
            $subid= $this->_getParam("subid");
          
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['curid']=$curid;
            $where['subid']=$subid;
            $where['state']="A";
            $dbcurso = new Api_Model_DbTable_Course();
            $curso=  $dbcurso->_getFilter($where,$attrib=null,$orders='semid');
            $this->view->cursos = $curso;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function viewregisteredcourseAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $subid=$this->_getParam("subid");
            $escid=$this->_getParam("escid");
            $curid=$this->_getParam("curid");
            $courseid=$this->_getParam("courseid");
            $perid=$this->_getParam("perid");

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['subid']=$subid;
            $where['perid']=$perid;
            $where['curid']=$curid;
            $where['escid']=$escid;
            $where['courseid']=$courseid;
            $dbcurso = new Api_Model_DbTable_Registrationxcourse();
            $curso=  $dbcurso->_getFilter($where,$attrib=null,$orders=null);
            if ($curso) {
                $wherecour['eid']=$eid;
                $wherecour['oid']=$oid;
                $wherecour['curid']=$curid;
                $wherecour['escid']=$escid;
                $wherecour['subid']=$subid;
                $wherecour['courseid']=$courseid;
                $cour= new Api_Model_DbTable_Course();
                $datacour= $cour->_getOne($wherecour);
                $this->view->datacourse=$datacour;
                $per= new Api_Model_DbTable_Person();
                $tam=count($curso);
                $whereper['eid']=$eid;
                for ($i=0; $i < $tam; $i++) { 
                    $whereper['pid']=$curso[$i]['pid'];
                    $person=$per->_getOne($whereper);
                    $curso[$i]['fullname']=$person['last_name0']." ".$person['last_name1'].", ".$person['first_name'];
                }
            }
            $this->view->cursitoutilizado = $curso;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function teacherxschoolAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid= $this->_getParam("escid");
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['rid']='DC';
            $where['state']='A';
            $r = new Api_Model_DbTable_Users();
            $regdoc = $r->_getUsersXEscidXRidXState($where);
            $this->view->docentes=$regdoc;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function savedirectedAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $uidreg= $this->sesion->uid;
            $uid = $this->_getParam("uid");
            $pid = $this->_getParam("pid");
            $escid = $this->_getParam("escid");
            $subid = $this->_getParam("subid");
            $perid = $this->_getParam("perid");
            $curid = $this->_getParam("curid");
            $nota = $this->_getParam("nota");
            $semid = $this->_getParam("semid");
            $reso = $this->_getParam("reso");
            $recibo = $this->_getParam("recibo");
            $courseid = $this->_getParam("courseid");
            $turno = $this->_getParam("turno");
            $credits = $this->_getParam("credits");
            $uid_doc = $this->_getParam("uid_doc");
            $pid_doc = $this->_getParam("pid_doc");
         
            $regid=$uid.$perid;
            $this->view->uid = $uid;
            $this->view->pid = $pid;
            $this->view->perid = $perid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->eid = $eid;
            $this->view->oid = $oid;

            // $wherecourse['eid']=$eid;
            // $wherecourse['oid']=$oid;
            // $wherecourse['subid']=$subid;
            // $wherecourse['curid']=$curid;
            // $wherecourse['escid']=$escid;
            // $wherecourse['courseid']=$courseid;
            // $reqcourse = new Api_Model_DbTable_Course();
            // $req = $reqcourse->_getOne($wherecourse);
            // $inforequisitos = $req['req_1']." | ".$req['req_2']." | ".$req['req_3'];
            // print_r($inforequisitos);
              
            // $dbveces = new Admin_Model_DbTable_Cursos();       
            // $vecesllevadas=  $dbveces->_getCursosXAlumnoXVeces($escid,$uid,$curricula,$curso);
            // print_r($vecesllevadas);
      
            // $dbcursopen = new Admin_Model_DbTable_Cursos();     
            // $cursoapto=  $dbcursopen->_getCursoXllevo($escid,$uid,$curricula,$curso);
            // print_r($cursoapto);


            // ------------------------------------------------------------------------------------
            $wherepercour['eid']=$eid;
            $wherepercour['oid']=$oid;
            $wherepercour['courseid']=$courseid;
            $wherepercour['escid']=$escid;
            $wherepercour['perid']=$perid;
            $wherepercour['turno']=$turno;
            $wherepercour['subid']=$subid;
            $wherepercour['curid']=$curid;
            $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();
            $periodcourse=$dbperiodocurso->_getOne($wherepercour);
            if (!$periodcourse) {
                $datapercour=$wherepercour;
                $datapercour['state_record']= 'A';
                $datapercour['type_rate']= 'O';
                $datapercour['receipt']= 'N';
                $datapercour['resolution']= $reso;
                $datapercour['semid']= $semid;
                $datapercour['closure_date']= date("Y-m-d");
                $datapercour['register']=$uidreg;
                $datapercour['created']= date("Y-m-d");
                $datapercour['state']= 'A';
                $dbperiodocurso->_save($datapercour);
            }

            $wheredoccour['eid']=$eid;
            $wheredoccour['oid']=$oid;
            $wheredoccour['escid']=$escid;
            $wheredoccour['subid']=$subid;
            $wheredoccour['courseid']=$courseid;
            $wheredoccour['curid']=$curid;
            $wheredoccour['turno']=$turno;
            $wheredoccour['perid']=$perid;
            $wheredoccour['uid']=$uid_doc;
            $wheredoccour['pid']=$pid_doc;
            $dbdocentes = new Api_Model_DbTable_Coursexteacher();
            $doccourse=$dbdocentes->_getOne($wheredoccour);
            if (!$doccourse) {
                $datadoccourse=$wheredoccour;
                $datadoccourse['state']= 'A';
                $datadoccourse['semid']= $semid;
                $datadoccourse['is_main']= 'S';
                $datadoccourse['is_com']= 'N';
                $dbdocentes->_save($datadoccourse);
            }

            $wherereg['eid']=$eid;
            $wherereg['oid']=$oid;
            $wherereg['escid']=$escid;
            $wherereg['subid']=$subid;
            $wherereg['regid']=$regid;
            $wherereg['pid']=$pid;
            $wherereg['uid']=$uid;
            $wherereg['perid']=$perid;
            $dbreg = new Api_Model_DbTable_Registration();
            $registration = $dbreg->_getOne($wherereg);
            if (!$registration) {
                $datareg=$wherereg;
                $datareg['semid']='0';
                $datareg['credits']= $credits;
                $datareg['date_register']= date("Y-m-d");
                $datareg['register']= $uidreg;
                $datareg['created']= date("Y-m-d");
                $datareg['state']= "M";
                $datareg['document_auth']= $reso;
                $dbreg->_save($datareg);
            }

            $whereregcour['eid']=$eid;
            $whereregcour['oid']=$oid;
            $whereregcour['escid']=$escid;
            $whereregcour['subid']=$subid;
            $whereregcour['courseid']=$courseid;
            $whereregcour['curid']=$curid;
            $whereregcour['regid']=$regid;
            $whereregcour['turno']=$turno;
            $whereregcour['pid']=$pid;
            $whereregcour['uid']=$uid;
            $whereregcour['perid']=$perid;
            $dbregistercourse = new Api_Model_DbTable_Registrationxcourse();
            $regcourse=$dbregistercourse->_getOne($whereregcour);
            if (!$regcourse) {
                $dataregcourse=$whereregcour;
                $dataregcourse['notafinal']= $nota;
                $dataregcourse['receipt']= $recibo;
                $dataregcourse['document_auth']= $reso;
                $dataregcourse['register']= $uidreg;
                $dataregcourse['created']= date("Y-m-d");
                $dataregcourse['state']= 'M';
                $dbregistercourse->_save($dataregcourse);
            }

            $wheresub['eid']=$eid;
            $wheresub['oid']=$oid;
            $wheresub['subid']=$subid;
            $wheresub['escid']=$escid;
            $wheresub['uid']=$uid;
            $wheresub['pid']=$pid;
            $wheresub['perid']=$perid;
            $convalidados = $dbregistercourse->_getFilter($wheresub,$attrib=null,$orders='courseid');
            if ($convalidados) {
                $tamm=count($convalidados);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['subid']=$subid;
                for ($i=0; $i < $tamm; $i++) { 
                    $wherecourse['curid']=$convalidados[$i]['curid'];
                    $wherecourse['courseid']=$convalidados[$i]['courseid'];
                    $cours= new Api_Model_DbTable_Course();
                    $dbcourse=$cours->_getOne($wherecourse);
                    $convalidados[$i]['name_course']=$dbcourse['name'];
                    $convalidados[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->cursosconvalidados = $convalidados;
            // ------------------------------------------------------------------------------------
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function modifyregisterAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $regid=$this->_getParam("regid");
            $escid=$this->_getParam("escid");
            $curid=$this->_getParam("curid");
            $courseid=$this->_getParam("courseid");
            $uid=$this->_getParam("uid");
            $pid=$this->_getParam("pid");
            $turno=$this->_getParam("turno");
            $subid=$this->_getParam("subid");
            $perid=$this->_getParam("perid");
            $notafinal=$this->_getParam("notafinal");
            $receipt=$this->_getParam("receipt");
            $resolution=$this->_getParam("resolution");

            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['escid']=$escid;
            $pk['subid']=$subid;
            $pk['courseid']=$courseid;
            $pk['curid']=$curid;
            $pk['regid']=$regid;
            $pk['turno']=$turno;
            $pk['pid']=$pid;
            $pk['uid']=$uid;
            $pk['perid']=$perid;
            $data['notafinal']=$notafinal;
            $data['receipt']=$receipt;
            $data['document_auth']=$resolution;
            $bdmatricurso=new Api_Model_DbTable_Registrationxcourse();
            if ($bdmatricurso->_update($data,$pk)) {
                $this->view->msg=1;
                $wheresub['eid']=$eid;
                $wheresub['oid']=$oid;
                $wheresub['subid']=$subid;
                $wheresub['escid']=$escid;
                $wheresub['uid']=$uid;
                $wheresub['pid']=$pid;
                $wheresub['perid']=$perid;
                $convalidados = $bdmatricurso->_getFilter($wheresub,$attrib=null,$orders='courseid');
                if ($convalidados) {
                    $tamm=count($convalidados);
                    $wherecourse['eid']=$eid;
                    $wherecourse['oid']=$oid;
                    $wherecourse['escid']=$escid;
                    $wherecourse['subid']=$subid;
                    for ($i=0; $i < $tamm; $i++) { 
                        $wherecourse['curid']=$convalidados[$i]['curid'];
                        $wherecourse['courseid']=$convalidados[$i]['courseid'];
                        $cours= new Api_Model_DbTable_Course();
                        $dbcourse=$cours->_getOne($wherecourse);
                        $convalidados[$i]['name_course']=$dbcourse['name'];
                        $convalidados[$i]['credits']=$dbcourse['credits'];
                    }
                }
                $this->view->cursosconvalidados = $convalidados;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
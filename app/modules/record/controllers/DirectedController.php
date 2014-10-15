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
        $this->view->usuario=$this->sesion->rid;
    }

    public function studentAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->_getParam('uid');
            $perid = $this->_getParam('perid');
            $this->view->uid=$uid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['uid']=$uid;
            $where['state']='A';
            $user= new Api_Model_DbTable_Users();
            $datauser=$user->_getUserXUid_state($where);
            $this->view->user=$datauser[0];

            $rid=$this->sesion->rid;
            if ($rid == 'AD') {
                //solo si es admin
                $anio=$perid;
                $a= substr($anio, 2, 4);
                $data=array('eid'=>$eid,'oid'=>$oid,'year'=>$a);
                $dbperiod= new Api_Model_DbTable_Periods();
                $dataperiod = $dbperiod->_getPeriodsxYears($data);
                $this->view->periods=$dataperiod;

            }else{
                //si no es adim
                $anio=$perid;
                $whereper1['eid']=$eid;
                $whereper1['oid']=$oid;
                $whereper1['perid']=$anio.'J';
                $peri= new Api_Model_DbTable_Periods();
                $periods[0]=$peri->_getOnePeriod($whereper1);
                $whereper1['perid']=$anio.'S';
                $periods[1]=$peri->_getOnePeriod($whereper1);
                // $whereper1['perid']=$anio.'C';
                // $periods[2]=$peri->_getOnePeriod($whereper1);
                $this->view->periods=$periods;
            }


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
            $this->view->eid = $eid;
            $this->view->oid = $oid;
            $this->view->uid = $uid;
            $this->view->pid = $pid;
            $this->view->perid = $perid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;


            //identificando una curricula asignada al alumno
            $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid,'pid'=>$pid);
            $DBCurricula = new Api_Model_DbTable_Studentxcurricula();
            $datacurri = $DBCurricula->_getsearch($wheres);
            $curid=$datacurri['curid'];

            $dataStudent=array('pid'=>$pid,'uid'=>$uid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid);
            $this->view->dataStudent=$dataStudent;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['curid']=$curid;
            $where['state']="A";
            $dbcurso = new Api_Model_DbTable_Course();
            $orders=array('semid','courseid');
            $curso=  $dbcurso->_getFilter($where,$attrib=null,$orders);

            $wheress=array('escid'=>$escid,'uid'=>$uid,'curid'=>$curid);
            foreach ($curso as $c => $data) {
                $wheress['courseid']=$data['courseid'];
                $data=$dbcurso->_getCourseLlevo($wheress);

                if ($data[0]['apto']==1) {
                    unset($curso[$c]);
                }
                elseif ($data[0]['apto']==2) {
                    $curso[$c]['no_apto']=1;
                }
                else{
                    $curso[$c]['no_apto']=0;
                }
            }
            $this->view->cursos = $curso;
            // $wherecur['eid']=$eid;
            // $wherecur['oid']=$oid;
            // $wherecur['escid']=$escid;
            // $wherecur['state']="A";
            // $curiculas = new Api_Model_DbTable_Curricula();
            // $cur0=$curiculas->_getFilter($wherecur,$attrib=null,$orders='curid');
            // $wherecur['state']="T";
            // $cur1=$curiculas->_getFilter($wherecur,$attrib=null,$orders='curid');
            // $wherecur['state']="C";
            // $cur2=$curiculas->_getFilter($wherecur,$attrib=null,$orders='curid');
            // $rol=$this->sesion->rid;

            // if ($rol =='AD') {
            //     if($cur0 && !$cur1 && !$cur2){
            //         $cur=$cur0;
            //     }else{
            //         $cur=array_merge($cur0,$cur1,$cur2);
            //     }
            // }else{
            //     if ($cur0 && !$cur1) {
            //         $cur=$cur0;
            //     }elseif ($cur0 && $cur1) {
            //         $cur=array_merge($cur0,$cur1);
            //     }
            // }


            // $this->view->curriculas=$cur;

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
                $cours= new Api_Model_DbTable_Course();
                $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();
                for ($i=0; $i < $tamm; $i++) {
                    $wherepercourse=array('eid' => $eid, 'oid' => $oid,
                                'courseid' => $convalidados[$i]['courseid'], 'escid' => $escid,
                                'perid' => $perid, 'turno' => $convalidados[$i]['turno'],
                                'subid' => $subid, 'curid' => $convalidados[$i]['curid']);
                    $wherecourse['curid']=$convalidados[$i]['curid'];
                    $wherecourse['courseid']=$convalidados[$i]['courseid'];

                    $dbcourse=$cours->_getOne($wherecourse);
                    $datacourseperiod=$dbperiodocurso->_getOne($wherepercourse);

                    $convalidados[$i]['state_record']=$datacourseperiod['state_record'];
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
            $orders=array('semid','courseid');
            $curso=  $dbcurso->_getFilter($where,$attrib=null,$orders);

            foreach ($curso as $c => $data) {
                $wheress=array('escid'=>$escid,'uid'=>'0924401019','curid'=>$curid,'courseid'=>$data['courseid']);
                $data=$dbcurso->_getCourseLlevo($wheress);

                if ($data[0]['apto']==1) {
                    unset($curso[$c]);
                }
                elseif ($data[0]['apto']==2) {
                    $curso[$c]['no_apto']=1;
                }
                else{
                    $curso[$c]['no_apto']=0;
                }
            }

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
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                $uid = $formdata['uid'];
                $pid = $formdata['pid'];
                $escid = $formdata['escid'];
                $subid = $formdata['subid'];
                $perid = $formdata['perid'];
                $turno = $formdata['turno'];
                $nota = $formdata['nota'];
                $recibo = $formdata['recibo'];
                $reso = $formdata['resolucion'];
                $tmpcourseid = split('--', $formdata['courseid']);
                $courseid = $tmpcourseid[0];
                $curid = $tmpcourseid[1];
                $semid = $tmpcourseid[2];
                $credits = $tmpcourseid[3];

                $tmpdocente_reg = split(';--;', $formdata['docente_reg']);
                $uid_doc = $tmpdocente_reg[0];
                $pid_doc = $tmpdocente_reg[1];

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

                $wheredoccour=array();
                $wheredoccour['eid']=$eid;
                $wheredoccour['oid']=$oid;
                $wheredoccour['escid']=$escid;
                $wheredoccour['subid']=$subid;
                $wheredoccour['courseid']=$courseid;
                $wheredoccour['curid']=$curid;
                $wheredoccour['turno']=$turno;
                $wheredoccour['perid']=$perid;
                $dbdocentes = new Api_Model_DbTable_Coursexteacher();
                $infodoccourse=$dbdocentes->_getFilter($wheredoccour,$attrib=null,$orders=null);
                if ($infodoccourse) {
                    if ($infodoccourse[0]['uid']<>$uid_doc and $infodoccourse[0]['pid']<>$pid_doc) { ?>
                        <script type="text/javascript">
                            alert("Esta Asignatura ya se encuentra asignada a Otro Docente en este Turno, verifique los datos del Docente o cambie de Turno.");
                        </script>
                        <?php
                        exit();
                    }
                }else{
                    $wheredoccour['uid']=$uid_doc;
                    $wheredoccour['pid']=$pid_doc;
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
                    $datareg['modified']= $uidreg;
                    $datareg['created']= date("Y-m-d");
                    $datareg['state']= "M";
                    $datareg['document_auth']= $reso;
                    $datareg['count']= 0;
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
                    $dataregcourse['approved']= $this->sesion->uid;
                    $dataregcourse['approved_date']= date("Y-m-d h:m:s");
                    $dbregistercourse->_save($dataregcourse);
                }
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
            $option=$this->_getParam("option");
            $f_acta=$this->_getParam("f_acta");

            $this->view->uid = $uid;
            $this->view->pid = $pid;
            $this->view->perid = $perid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;

            $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();
            $bdmatricurso=new Api_Model_DbTable_Registrationxcourse();
            if ($option=="M") {
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
                if ($bdmatricurso->_updatenoteregister($data,$pk)) {
                    $this->view->msg=1;
                }
            }elseif ($option=="C") {
                $pk1['eid']=$eid;
                $pk1['oid']=$oid;
                $pk1['escid']=$escid;
                $pk1['subid']=$subid;
                $pk1['courseid']=$courseid;
                $pk1['curid']=$curid;
                $pk1['regid']=$regid;
                $pk1['turno']=$turno;
                $pk1['pid']=$pid;
                $pk1['uid']=$uid;
                $pk1['perid']=$perid;
                $data1['notafinal']=$notafinal;
                $data1['receipt']=$receipt;
                $data1['document_auth']=$resolution;

                if ($bdmatricurso->_updatenoteregister($data1,$pk1)) {
                    $pk=array('eid' => $eid, 'oid' => $oid, 'courseid' => $courseid,
                            'perid' => $perid, 'escid' => $escid, 'subid' => $subid,
                            'curid' => $curid, 'turno' => $turno);
                    $data=array('state' => 'C', 'state_record' => 'C', 'closure_date' => $f_acta,
                            'modified' => $this->sesion->uid, 'updated' => date('Y-m-d'));
                    if ($dbperiodocurso->_update($data,$pk)){
                        $this->view->msg=1;
                    }
                }
            }
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
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tamm; $i++) {
                    $wherepercourse=array('eid' => $eid, 'oid' => $oid,
                                'courseid' => $convalidados[$i]['courseid'], 'escid' => $escid,
                                'perid' => $perid, 'turno' => $convalidados[$i]['turno'],
                                'subid' => $subid, 'curid' => $convalidados[$i]['curid']);
                    $wherecourse['curid']=$convalidados[$i]['curid'];
                    $wherecourse['courseid']=$convalidados[$i]['courseid'];

                    $dbcourse=$cours->_getOne($wherecourse);
                    $datacourseperiod=$dbperiodocurso->_getOne($wherepercourse);

                    $convalidados[$i]['state_record']=$datacourseperiod['state_record'];
                    $convalidados[$i]['name_course']=$dbcourse['name'];
                    $convalidados[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->cursosconvalidados = $convalidados;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function printAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uid = base64_decode($this->_getParam('uid'));
            $pid = base64_decode($this->_getParam('pid'));
            $escid = base64_decode($this->_getParam('escid'));
            $perid = base64_decode($this->_getParam('perid'));
            $subid = base64_decode($this->_getParam('subid'));
            $courseid = base64_decode($this->_getParam('courseid'));
            $turno = base64_decode($this->_getParam('turno'));
            $curid = base64_decode($this->_getParam('curid'));
            $this->view->courseid = $courseid;
            $this->view->turno = $turno;
            $this->view->perid = $perid;

            $wheremat = array(
                'eid' => $eid, 'oid' => $oid, 'perid' => $perid, 'curid' => $curid,
                'escid' => $escid, 'subid' => $subid, 'courseid' => $courseid, 'turno' => $turno);
            $matcur = new Api_Model_DbTable_Registrationxcourse();
            $convalidados = $matcur->_getStudentXcoursesXescidXperiods($wheremat);
            $this->view->cursosconvalidados = $convalidados;

            $whereesp = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid);
            $esp = new Api_Model_DbTable_Speciality();
            $dataesc = $esp->_getOne($whereesp);
            $this->view->speciality = $dataesc;

            $wherefac = array('eid' => $eid, 'oid' => $oid, 'facid' => substr($escid, 0,1));
            $fac = new Api_Model_DbTable_Faculty();
            $datafac = $fac->_getOne($wherefac);
            $this->view->faculty = $datafac;

            $wherecour = array(
                'eid' => $eid, 'oid' => $oid, 'curid' => $curid,
                'escid' => $escid, 'subid' => $subid, 'courseid' => $courseid);
            $cour = new Api_Model_DbTable_Course();
            $datacour = $cour->_getOne($wherecour);
            $this->view->course = $datacour;

            $percour = new Api_Model_DbTable_PeriodsCourses();
            $periodcourse = $percour->_getOne($wheremat);
            $this->view->periodcourse = $periodcourse;

            $wherecourtea = array();
            $courteac = new Api_Model_DbTable_Coursexteacher();
            $datacourtea = $courteac->_getFilter($wheremat,$attrib=null,$orders=null);
            if ($datacourtea) {
                $whereper = array('eid' => $eid, 'pid' => $datacourtea[0]['pid']);
                $per = new Api_Model_DbTable_Person();
                $dataper = $per->_getOne($whereper);
                $this->view->dataper = $dataper;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
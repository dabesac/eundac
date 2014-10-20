<?php
class Register_ValidationController extends Zend_Controller_Action
{
	public function init()
	{
		$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();

    	$this->sesion = $login;
	}

    public function indexAction(){
      // $this->_helper->redirector("addcourse");
    }

    public function addcourseAction(){
        try {
            $this->_helper->layout()->disableLayout();

            $facidSesion=$this->sesion->faculty->facid;
            $subidSesion=$this->sesion->subid;

            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            // $temp = ($this->_getParam("temp"));
            $uid= base64_decode($this->_getParam('uid'));
            $anio=date('Y');
            $anio=substr($anio,2,2);
            $perid=$anio.'C';

            $dbusuario = new Api_Model_DbTable_Users();//ADMIN
            $data['eid']=$eid;
            $data['oid']=$oid;
            $data['uid']=$uid;
            $data['state']='A';
            $usuario=  $dbusuario->_getFilter($data);
            if ($usuario) {
                $speciality = new Api_Model_DbTable_Speciality();
                $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$usuario[0]['escid'],'subid'=>$usuario[0]['subid']);
                $datas = $speciality->_getOne($wheres);

                if ($facidSesion==$datas['facid']) {
                    $usuario[0]['name_speciality']=$datas['name'];
                    $persona = new Api_Model_DbTable_Person();
                    $data['eid']=$eid;
                    $data['pid']=$usuario[0]['pid'];
                    $list_p = $persona ->_getOne($data);
                    $usuario[0]['full_name']=$list_p['last_name0'].' '.$list_p['last_name1'].', '.$list_p['first_name'];


                    unset($wheres['escid']);
                    $subsidiary = new Api_Model_DbTable_Subsidiary();
                    $datass = $subsidiary->_getOne($wheres);
                    $usuario[0]['name_subsidiary']=$datass['name'];
                }
                elseif ($facidSesion=='TODO') {
                    $subidStudent=$datas['subid'];
                    if ($subidSesion==$subidStudent) {
                        $usuario[0]['name_speciality']=$datas['name'];
                        $persona = new Api_Model_DbTable_Person();
                        $data['eid']=$eid;
                        $data['pid']=$usuario[0]['pid'];
                        $list_p = $persona ->_getOne($data);
                        $usuario[0]['full_name']=$list_p['last_name0'].' '.$list_p['last_name1'].', '.$list_p['first_name'];


                        unset($wheres['escid']);
                        $subsidiary = new Api_Model_DbTable_Subsidiary();
                        $datass = $subsidiary->_getOne($wheres);
                        $usuario[0]['name_subsidiary']=$datass['name'];
                    }
                    elseif ($subidSesion=='1901' && $facidSesion='TODO' ) {
                        $usuario[0]['name_speciality']=$datas['name'];
                        $persona = new Api_Model_DbTable_Person();
                        $data['eid']=$eid;
                        $data['pid']=$usuario[0]['pid'];
                        $list_p = $persona ->_getOne($data);
                        $usuario[0]['full_name']=$list_p['last_name0'].' '.$list_p['last_name1'].', '.$list_p['first_name'];


                        unset($wheres['escid']);
                        $subsidiary = new Api_Model_DbTable_Subsidiary();
                        $datass = $subsidiary->_getOne($wheres);
                        $usuario[0]['name_subsidiary']=$datass['name'];
                    }
                    else{
                        $this->view->notuser='N';
                    }
                }
                else{
                    $this->view->notuser='N';
                }
            }
            $this->view->usuario = $usuario;
            $this->view->perid = $perid;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }

    }

    public function lcontainerAction(){
        $this->_helper->layout()->disableLayout();

        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $escid = base64_decode($this->_getParam("escid"));
        $subid = base64_decode($this->_getParam("subid"));
        $uid = base64_decode($this->_getParam("uid"));
        $pid = base64_decode($this->_getParam("pid"));
        $perid = base64_decode($this->_getParam("perid"));
        // $nota = ($this->_getParam("nota"));

        $userregistra= $this->sesion->uid;
        $temp = ($this->_getParam("temp"));

        //identificando una curricula asignada al alumno
        $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid,'pid'=>$pid);
        $DBCurricula = new Api_Model_DbTable_Studentxcurricula();
        $datacurri = $DBCurricula->_getsearch($wheres);
        if ($datacurri) {
            $curid=$datacurri['curid'];

            $this->view->pid = $pid;
            $this->view->uid = $uid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->perid = $perid;
            $this->view->user = $userregistra;
            $peridSession=$this->sesion->period->perid;

            try {
                // $request = array(   'eid' => base64_encode($eid),
                //                     'oid' => base64_encode($oid),
                //                     'perid' => base64_encode($peridSession),
                //                     'pid' => base64_encode($pid),
                //                     'uid' => base64_encode($uid),
                //                     'escid' => base64_encode($escid),
                //                     'subid' => base64_encode($subid),
                //                     'curid' => base64_encode($curid));

                // require_once 'Zend/Loader.php';
                // Zend_Loader::loadClass('Zend_Rest_Client');

                // $base_url = 'http://api.undac.edu.pe:8080/';
                // $endpoint = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/pending_validate';
                // $client = new Zend_Rest_Client($base_url);
                // $httpClient = $client->getHttpClient();
                // $httpClient->setConfig(array("timeout" => 30000));
                // $response = $client->restget($endpoint,$request);
                // $lista=$response->getBody();

                // if ($lista){
                //     $data = Zend_Json::decode($lista);
                // }
                $where=array('escid'=>$escid,'uid'=>$uid,'curid'=>$curid);
                $dbregistrationxcourse = new Api_Model_DbTable_Registrationxcourse();
                $data=$dbregistrationxcourse->_getCoursesPerCurriculum($where);
                $this->view->data=$data;
                $this->view->notcurid='0';

            } catch (Exception $e) {
                $this->view->msg='1';
                $this->view->data='1';
            }
        }
        else{
            $curiculas = new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $cur=$curiculas->_getCurriculasXSchoolXstateAT($where);
            $this->view->curriculas=$cur;
            $this->view->notcurid='1';
            $this->view->msg='0';
            $this->view->data='1';
            $this->view->pid = $pid;
            $this->view->uid = $uid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->perid = $perid;

        }
    }

    public function viewallcoursesAction(){
        $this->_helper->layout()->disableLayout();

        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $escid = base64_decode($this->_getParam("escid"));
        $subid = base64_decode($this->_getParam("subid"));
        $uid = base64_decode($this->_getParam("uid"));
        $pid = base64_decode($this->_getParam("pid"));
        $perid = base64_decode($this->_getParam("perid"));

        $this->view->escid=$escid;
        $this->view->subid=$subid;
        $this->view->uid=$uid;
        $this->view->pid=$pid;
        $this->view->perid=$perid;

        $dblistarconvalidacion = new Api_Model_DbTable_Registrationxcourse();

        $wheress=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid,'pid'=>$pid,'perid'=>$perid);
        $convalidados = $dblistarconvalidacion->_getFilter($wheress);
        if ($convalidados) {
            $read=array('eid'=>$eid,'oid'=>$oid,'curid'=>$info['curid'],'escid'=>$escid,'subid'=>$subid);
            foreach ($convalidados as $g => $info) {
                $read['curid']=$info['curid'];
                $read['courseid']=$info['courseid'];
                $dbcourse = new Api_Model_DbTable_Course();
                $datainfo = $dbcourse->_getFilter($read);
                $convalidados[$g]['namecourse'] = $datainfo[0]['name'];
            }
        }
        $this->view->cursosconvalidados = $convalidados;
    }

    public function coursexcurriculaAction(){
        $this->_helper->layout()->disableLayout();
        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $curid= base64_decode($this->_getParam("curid"));
        $escid= base64_decode($this->_getParam("escid"));
        $subid= base64_decode($this->_getParam("subid"));
          $uid= base64_decode($this->_getParam("uid"));
          $pid= base64_decode($this->_getParam("pid"));
         // $nota= base64_decode($this->_getParam("nota"));
        $peridSession= $this->sesion->period->perid;

        // $request = array(   'eid' => base64_encode($eid),
       //                      'oid' => base64_encode($oid),
       //                      'perid' => base64_encode($peridSession),
       //                      'pid' => base64_encode($pid),
       //                      'uid' => base64_encode($uid),
       //                      'escid' => base64_encode($escid),
       //                      'subid' => base64_encode($subid),
       //                      'curid' => base64_encode($curid));

       //  require_once 'Zend/Loader.php';
       //  Zend_Loader::loadClass('Zend_Rest_Client');

       //  $base_url = 'http://api.undac.edu.pe:8080/';
       //  $endpoint = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/pending_validate';
       //  $client = new Zend_Rest_Client($base_url);
       //  $httpClient = $client->getHttpClient();
       //  $httpClient->setConfig(array("timeout" => 30000));
       //  $response = $client->restget($endpoint,$request);
       //  $lista=$response->getBody();

       //  if ($lista){
       //      $data = Zend_Json::decode($lista);
       //  }

        $where=array('escid'=>$escid,'uid'=>$uid,'curid'=>$curid);
        $dbregistrationxcourse = new Api_Model_DbTable_Registrationxcourse();
        $data=$dbregistrationxcourse->_getCoursesPerCurriculum($where);
        $this->view->data=$data;
    }

    public function saveAction(){
        $this->_helper->layout()->disableLayout();

        $json['status']=false;
        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $uidreg = $this->sesion->uid;

        $pid = base64_decode($this->_getParam("pid"));
        $uid = base64_decode($this->_getParam("uid"));
        $escid = base64_decode($this->_getParam("escid"));
        $subid = base64_decode($this->_getParam("subid"));
        $perid = base64_decode($this->_getParam("perid"));
        $curid = base64_decode($this->_getParam("curid"));
        $nota = base64_decode($this->_getParam("nota"));
        $semid = base64_decode($this->_getParam("semid"));
        $resolution = base64_decode($this->_getParam("resolution"));
        $courseid = base64_decode($this->_getParam("courseid"));
        $credits = base64_decode($this->_getParam("credits"));
        $type_rate = base64_decode($this->_getParam("type_rate"));

        $cont=0;

        //verificando acta del curso.
        $wherepc=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,
                     'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>'A');
        $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();
        $dataperiodocurso = $dbperiodocurso->_getOne($wherepc);

        if (!$dataperiodocurso) {
            $wherepc['state_record']='C';
            $wherepc['type_rate']=$type_rate;
            $wherepc['receipt']='N';
            $wherepc['resolution']='NULL';
            $wherepc['semid']=$semid;
            $wherepc['closure_date']=date('Y-m-d');
            $wherepc['register']=$uidreg;
            $wherepc['state']='C';
            if ($dbperiodocurso->_save($wherepc)) {
                $cont++;
            }
        }

        //verificando asignacion del curso a un docente.
        $wherecxt=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid,
                        'curid'=>$curid,'turno'=>'A','perid'=>$perid,'uid'=>'DOCCONV01','pid'=>'CONV01');

        $dbcoursexteacher = new Api_Model_DbTable_Coursexteacher();
        $datacoursexteacher = $dbcoursexteacher->_getOne($wherecxt);
        if (!$datacoursexteacher) {
            $wherecxt['is_main']='S';
            $wherecxt['semid']=$semid;
            $wherecxt['state']='A';
            if ($dbcoursexteacher->_save($wherecxt)) {
                $cont++;
            }
        }

        //verificando matricula del alumno
        $whererg=array('eid'=>$eid,'oid'=>$oid,'regid'=>$uid.$perid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,
                        'subid'=>$subid,'perid'=>$perid);

        $dbregistration = new Api_Model_DbTable_Registration();
        $dataregistration = $dbregistration->_getOne($whererg);

        if (!$dataregistration) {
            $whererg['semid']='0';
            $whererg['credits']=$credits;
            $whererg['date_register']=date('Y-m-d');
            $whererg['document_auth']=$resolution;
            $whererg['register']=$uidreg;
            $whererg['created']=date('Y-m-d');
            $whererg['state']='M';
            $whererg['count']='0';
            if ($dbregistration->_save($whererg)) {
                $cont++;
            }
        }

        //verificando matricula por cursos del alumno
        $wherergxc=array('eid'=>$eid,'oid'=>$oid,'regid'=>$uid.$perid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,
                        'subid'=>$subid,'courseid'=>$courseid,'curid'=>$curid,'turno'=>'A','perid'=>$perid);

        $dbregistrationxcourse = new Api_Model_DbTable_Registrationxcourse();
        $dataregistrationxcourse = $dbregistrationxcourse->_getOne($wherergxc);

        if (!$dataregistrationxcourse) {
            $wherergxc['notafinal']=$nota;
            $wherergxc['receipt']='N';
            $wherergxc['register']=$uidreg;
            $wherergxc['created']=date('Y-m-d');
            $wherergxc['approved']=$uidreg;
            $wherergxc['approved_date']=date('Y-m-d');
            $wherergxc['document_auth']=$resolution;
            $wherergxc['state']='M';
            if ($dbregistrationxcourse->_save($wherergxc)) {
                $cont++;
                $json['status']=true;
            }
        }
        elseif ($dataregistrationxcourse['notafinal']<11) {
            $dataupdated=array('notafinal'=>$nota,'document_auth'=>$resolution,'modified'=>$uidreg,'updated'=>date('Y-m-d'));

            if ($dbregistrationxcourse->_update($dataupdated,$wherergxc)) {
                $json['status']=true;

                $dataupdated1=array('document_auth'=>$resolution,'modified'=>$uidreg,'updated'=>date('Y-m-d'));
                $dbregistration->_update($dataupdated1,$whererg);
            }

        }

        if (!($cont>0 && $json['status'])) {
            $json['status']=false;
        }
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->data = $json;
    }

    public function printAction(){
        $this->_helper->layout()->disableLayout();

        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;

        $uid = base64_decode($this->_getParam("uid"));
        $pid = base64_decode($this->_getParam("pid"));
        $escid = base64_decode($this->_getParam("escid"));
        $subid = base64_decode($this->_getParam("subid"));
        $perid = base64_decode($this->_getParam("perid"));

        $this->view->perid=$perid;
        $this->view->perid=$perid;
        //$curid = $this->_getParam("curid");
        //echo $perid;
        $print['eid']=$eid;
        $print['oid']=$oid;
        $print['perid']=$perid;
        $print['uid']=$uid;
        $print['escid']=$escid;
        $print['subid']=$subid;

        $dblistarconvalidacion = new Api_Model_DbTable_Registrationxcourse();
        $convalidados = $dblistarconvalidacion->_getFilter($print);

        if ($convalidados) {
            $read=array('eid'=>$eid,'oid'=>$oid,'curid'=>$info['curid'],'escid'=>$escid,'subid'=>$subid);
            foreach ($convalidados as $g => $info) {
                $read['curid']=$info['curid'];
                $read['courseid']=$info['courseid'];
                $dbcourse = new Api_Model_DbTable_Course();
                $datainfo = $dbcourse->_getFilter($read);
                $convalidados[$g]['namecourse'] = $datainfo[0]['name'];
            }
        }

        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['escid']=$escid;
        $where['subid']=$subid;

        $spe=array();
        $dbspeciality = new Api_Model_DbTable_Speciality();
        $speciality = $dbspeciality ->_getOne($where);
        $parent=$speciality['parent'];
        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        $parentesc= $dbspeciality->_getOne($wher);
        if ($parentesc) {
            $pala='ESPECIALIDAD DE ';
            $spe['esc']=$parentesc['name'];
            $spe['parent']=$pala.$speciality['name'];
        }
        else{
            $spe['esc']=$speciality['name'];
            $spe['parent']='';
        }
        $names=strtoupper($spe['esc']);
        $namep=strtoupper($spe['parent']);
        $namefinal=$names." <br> ".$namep;

        $whered['eid']=$eid;
        $whered['oid']=$oid;
        $whered['facid']= $speciality['facid'];
        $dbfaculty = new Api_Model_DbTable_Faculty();
        $faculty = $dbfaculty ->_getOne($whered);
        $namef = strtoupper($faculty['name']);

        $wheres=array('eid'=>$eid,'pid'=>$pid);
        $dbperson = new Api_Model_DbTable_Person();
        $person= $dbperson ->_getOne($wheres);

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

        $dbimpression = new Api_Model_DbTable_Countimpressionall();

        $uidim=$this->sesion->pid;

        $data = array(
            'eid'=>$eid,
            'oid'=>$oid,
            'uid'=>$uid,
            'escid'=>$escid,
            'subid'=>$subid,
            'pid'=>$pid,
            'type_impression'=>'registro_notas_'.$perid,
            'date_impression'=>date('Y-m-d H:i:s'),
            'pid_print'=>$uidim
            );
        $dbimpression->_save($data);

        $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'registro_notas_'.$perid);
        $dataim = $dbimpression->_getFilter($wheri);

        $co=count($dataim);
        $codigo=$co." - ".$uidim;

        $header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];
        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);

        $this->view->uid=$uid;
        $this->view->person=$person;
        $this->view->header=$header;
        $this->view->footer=$footer;
        $this->view->cursosconvalidados = $convalidados;
    }

    public function updateAction(){
      try {
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid = ($this->_getParam("escid"));
            $subid = ($this->_getParam("subid"));
            $uid = ($this->_getParam("uid"));
            $pid = ($this->_getParam("pid"));
            $perid = ($this->_getParam("perid"));
            $regid = ($this->_getParam("regid"));
            $courseid = ($this->_getParam("courseid"));
            $curid = ($this->_getParam("curid"));
            $turno = ($this->_getParam("turno"));
            $this->view->uid=$uid;
            $this->view->curid=$curid;
            $this->view->courseid=$courseid;
            $d['eid']=$eid;
            $d['oid']=$oid;
            $d['escid']=$escid;
            $d['subid']=$subid;
            $d['uid']=$uid;
            $d['pid']=$pid;
            $d['perid']=$perid;
            $d['regid']=$regid;
            $d['courseid']=$courseid;
            $d['curid']=$curid;
            $d['turno']=$turno;
            $dblista = new Api_Model_DbTable_Registrationxcourse();
            $lista = $dblista ->_getOne($d);
            $form=new Register_Form_Changenotes;
            $form->populate($lista);
            $this->view->form=$form;
            if ($this->getRequest()->isPost()) {
              $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata)) {
                  unset($frmdata['Guardar']);
                   $frmdata['modified']=$this->sesion->uid;

                    $reg_= new Api_Model_DbTable_Registrationxcourse();
                    $reg_->_updatenoteregister($frmdata,$d);
                    $this->_redirect("/register/validation");
                }
                    else
                {
                    echo "Ingrese nuevamente por favor";
                }
      }





          }catch (Exception $e) {
            print "Error index Registration ".$e->$getMessage();
        }

     }



}
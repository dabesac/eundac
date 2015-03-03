<?php

class IndexController extends Zend_Controller_Action {

    public function init()
    {
        $options = array(
            'layout' => 'login',
        );
        Zend_Layout::startMvc($options);
    }

    public function indexAction()
    {
    	try{
    	$sesion1  = Zend_Auth::getInstance();
        if($sesion1->hasIdentity()){
            $sesion = $sesion1->getStorage()->read();
    		$this->_helper->redirector('index','index',($sesion->rol['module']));
    	}

    	$form = new Default_Form_Login();
    	$this->view->form = $form;
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		if ($form->isValid($formData)) {
    			$eid = "20154605046";//base64_decode($this->_getParam('eid'));
    			$oid = "1";//base64_decode($this->_getParam('oid'));
    			$rid_ =split(";--;",$this->_getParam('rid'));
    			$rid = base64_decode($rid_[0]);
    			$prefix = trim($rid_[1]);
    			$cod = ($uid = $form->getValue('usuario').$prefix);
    			$clavecampus = $form->getValue('clave');
    			$pass = md5($clavecampus);
    			$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
    			$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter,'base_users','uid','password','rid');
    			$authAdapter->getDbSelect()->where("state = 'A' and eid='$eid' and oid='$oid' and rid='$rid'");
    			$authAdapter->setIdentity($cod)->setCredential($pass);
    			$auth = Zend_Auth::getInstance();
    			$result = $auth->authenticate($authAdapter);
    			if ($result->isValid()) {
    				$per = new Api_Model_DbTable_Periods();
    				$t = $per->_getPeriodsNext(array("eid"=> $eid, "oid"=>$oid));

    				if ($t){
    					$periodnext = $t['perid'];
    					$name_periodnext = $t['name'];
    					$rper = $per->_getPeriodsCurrent(array("eid"=> $eid, "oid"=>$oid));
    					if ($rper){
    						$period = $rper['perid'];
    						$name_period = $rper['name'];
    					}else{
    						$period = $periodnext;
    						$name_period = $name_periodnext;
    					}
    				}else{
    					$msg = "Error en la seccion del periodo siguiente para la plataforma";
    					$this->_redirect("/error/msg/msg/'$msg'");
    				}


                    // set value Period System
                    $data  = $authAdapter->getResultRowObject(array('eid','oid','uid','escid','pid','rid','subid'));
                    $userinfo  = $authAdapter->getResultRowObject(array('eid','oid','uid','escid','pid','rid','subid'));
                    // Begin Var
                    $data->period      = new stdClass();
                    $data->faculty     = new stdClass();
                    $data->speciality  = new stdClass();
                    $data->infouser    = new stdClass();
                    $data->rol         = new stdClass();
                    $data->org         = new stdClass();
                    $data->fullProfile = new stdClass();
                    $data->encuesta    = new stdClass();

                    $data->period->perid     = $period;
                    $data->period->name      = $name_period;
                    $data->period->next      = $periodnext ;
                    $data->period->name_next = $name_periodnext;


                    // set Speciality
                    $esc = new Api_Model_DbTable_Speciality();
                    $esc = $esc->_getOne(array("eid" => $eid, "oid" => $oid,"escid" => $data->escid,"subid" => $data->subid));

                    if ($esc){
                        $facu  = new Api_Model_DbTable_Faculty();
                        $nf = $facu->_getOne(array("eid" => $eid, "oid" => $oid,"facid" => $esc['facid']));
                        if ($nf){
                            $data->faculty->name=$nf['name'];
                            $data->faculty->facid=$nf['facid'];
                        }
                        if ($esc['parent']) {
                            $data->speciality->name=  ($esc['name']);
                            $data->speciality->escid=  ($esc['escid']);
                            $escuela = new Api_Model_DbTable_Speciality();
                            $tmpo = array("eid"=>$eid,"oid"=>$oid,"escid"=>$esc['parent'],"subid"=>$esc['subid']);
                            $te = $escuela->_getOne($tmpo);
                            if ($te){
                                $data->speciality->name=$te['name'];
                                $data->speciality->escid=  ($esc['escid']);
                            }
                        }else{
                            $data->speciality->name=$esc['name'];
                        }
                    }
                    //Verificar Si Lleno su perfil
                    $realtionshipDb = new Api_Model_DbTable_Relationship();
                    $academicDb     = new Api_Model_DbTable_Academicrecord();
                    $statisticDb    = new Api_Model_DbTable_Statistics();
                    $interestDb     = new Api_Model_DbTable_Interes();

                    $data->fullProfile->success = 'yes';
                    //Family
                    $where = array( 'eid'   => $eid,
                                    'pid'   => $data->pid );
                    $relationship = $realtionshipDb->_getFilter($where);
                    if ($relationship) {
                        $data->fullProfile->family = 'yes';
                    }else {
                        $data->fullProfile->family = 'no';
                        $data->fullProfile->success = 'no';
                    }

                    //Datos Academicos
                    $academic = $academicDb->_getFilter($where);
                    if ($academic) {
                        $data->fullProfile->academic = 'yes';
                    }else {
                        $data->fullProfile->academic = 'no';
                        $data->fullProfile->success = 'no';
                    }

                    //Datos de interes
                    $interest = $interestDb->_getFilter($where);
                    if ($interest) {
                        $data->fullProfile->interes = 'yes';
                    }else {
                        $data->fullProfile->interes = 'no';
                        $data->fullProfile->success = 'no';
                    }

                    //Datos Estadisticos
                    $where = array( 'eid'   => $eid,
                                    'oid'   => $oid,
                                    'escid' => $data->escid,
                                    'subid' => $data->subid,
                                    'pid'   => $data->pid,
                                    'uid'   => $uid );
                    $statistic = $statisticDb->_getFilter($where);
                    if ($statistic) {
                        $data->fullProfile->statistic = 'yes';
                    }else {
                        $data->fullProfile->statistic = 'no';
                        $data->fullProfile->success = 'no';
                    }

                    //Verificar si hay encuesta activa
                    $data->encuesta->existeEncuesta  = 'No';
                    $data->encuesta->rellenoEncuesta = '-';

                    if ($rid == 'AL') {
                        $pollDb         = new Api_Model_DbTable_Poll();
                        $pollQuestionDb = new Api_Model_DbTable_PollQuestion();
                        $pollResultsDb  = new Api_Model_DbTable_PollResults();
                        $registerDb     = new Api_Model_DbTable_Registration();
                        $where = array( 'eid'   => $eid,
                                        'oid'   => $oid,
                                        'state' => 'A' );
                        $attrib = array('pollid', 'perid');

                        $dataPoll = $pollDb->_getFilter($where, $attrib);

                        if ($dataPoll) {
                            //Verificar si se matriculo al peridodo de la encuesta
                            $periodEncuesta = $dataPoll[0]['perid'];

                            $where = array( 'eid'   => $eid,
                                            'oid'   => $oid,
                                            'pid'   => $data->pid,
                                            'uid'   => $data->uid,
                                            'perid' => $periodEncuesta,
                                            'state' => 'M');

                            $isRegister = $registerDb->_getFilter($where);
                            if ($isRegister) {
                                $where = array( 'eid'    => $eid,
                                                'oid'    => $oid,
                                                'pollid' => $dataPoll[0]['pollid'],
                                                'state'  => 'A' );
                                $attrib = array('qid');

                                $dataQuestion = $pollQuestionDb->_getFilter($where, $attrib);

                                if ($dataQuestion) {
                                    $data->encuesta->existeEncuesta = 'Yes';

                                    $where = array(
                                                    'eid' => $eid,
                                                    'oid' => $oid,
                                                    'pid' => $data->pid,
                                                    'qid' => $dataQuestion[0]['qid']);

                                    $pollResult = $pollResultsDb->_getFilter($where);
                                    if ($pollResult) {
                                       $data->encuesta->rellenoEncuesta = 'Yes';
                                    }else{
                                       $data->encuesta->rellenoEncuesta = 'No';
                                    }
                                }
                            }
                        }
                    }

                    //Insertar pago y Matricula Para Cachimbos
                    $paymentDb = new Api_Model_DbTable_Payments();

                    $cachimbo = substr($uid, 0, 2);
                    if ($cachimbo == '14') {
                        $where = array( 'eid'   => $eid,
                                        'oid'   => $oid,
                                        'escid' => $data->escid,
                                        'subid' => $data->subid,
                                        'pid'   => $data->pid,
                                        'uid'   => $uid,
                                        'perid' => $data->period->perid );

                        $payment = $paymentDb->_getFilter($where);

                        if(!$payment){
                            $dataPayment = array(   'eid'      => $eid,
                                                    'oid'      => $oid,
                                                    'escid'    => $data->escid,
                                                    'subid'    => $data->subid,
                                                    'pid'      => $data->pid,
                                                    'uid'      => $data->uid,
                                                    'perid'    => $data->period->perid,
                                                    'ratid'    => '10',
                                                    'amount'   => '0',
                                                    'register' => $data->uid );
                            $paymentDb->_save($dataPayment);
                        }
                    }

                    // Set info User
                    $user = new Api_Model_DbTable_Users();
                    $row = $user->_getInfoUser(array("eid"=>$eid,"oid"=>$oid,"uid"=>$data->uid,
                                    "pid"=>$data->pid,"escid"=>$data->escid,"subid"=>$data->subid));
                    $full = $row[0]['last_name0']." ".$row[0]['last_name1'].", ".$row[0]['first_name'];
                    $row[0]['fullname']=$full;
                    $data->sex=$row[0]['sex'];
                    $data->infouser=$row[0];

                    $datate['eid']= $data->eid;
                    $datate['oid']= $data->oid;
                    $datate['uid']= $data->uid;
                    $datate['pid']= $data->pid;
                    $datate['escid']= $data->escid;
                    $datate['subid']= $data->subid;
                    $teacher = new Api_Model_DbTable_Infoteacher();
                    $rowteacher = $teacher->_getOne($datate);

                    $data->infouser['teacher']=$rowteacher;

                    if ($data->infouser['teacher']['is_director']=='S') {
                        $data->rid='DR';
                    }

                    $rols_ = new Api_Model_DbTable_Rol();
                    $rol_ = $rols_->_getOne(array("eid"=>$data->eid,"oid"=>$data->oid,"rid"=>$data->rid));
                    if ($rol_)
                        $data->rol = $rol_;
                    else{
                        $msg = "Error no se encontro un rol epecifico el usuario";
                        $this->_redirect("/error/msg/msg/'$msg'");
                    }
                    // Select infoteacher


                    $data->infouser['teacher']=$rowteacher;

                    // Set ACL
                    //$tmpacl = $this->_aclCreated($data->rid,$data);
                    //$data->acls= $tmpacl['module'];
                    //$data->resources=$tmpacl['list'];

                    // Set Header and Footer Print Org
                    $orgs = new Api_Model_DbTable_Org();
                    $rorg = $orgs->_getOne(array("eid" => $data->eid,"oid"=>$data->oid));

                    if ($rorg) $data->org = $rorg;
                    // Register access
                    $clientIp = $this->getRequest()->getClientIp();
                    $log = new Api_Model_DbTable_Logs();
                    $aleatorio = rand(10,100);
                    $datalog['tokenid']= time()+$aleatorio;
                    $data->tokenid=$datalog['tokenid'];
                    $datalog['year']= date('Y');
                    $datalog['ip']= $clientIp;
                    $datalog['eid']= $data->eid;
                    $datalog['oid']= $data->oid;
                    $datalog['uid']= $data->uid;
                    $datalog['pid']= $data->pid;
                    $datalog['rid']= $data->rid;
                    $datalog['datestart']= date(DATE_RFC822);
                    $datalog['dateend']= date(DATE_RFC822);
                    $datalog['state']= 'A';
                    $datalog['keysession']= $datalog['tokenid'];

                    $userAgent = new Zend_Http_UserAgent();
                    $device = $userAgent->getDevice();
                    $datalog['browse'] = $device->getBrowser();
                    $datalog['vbrowser'] = $device->getBrowserVersion();
                    $datalog['browserinfo'] = $device->getUserAgent();
                    if ($log->_save($datalog)){
                        $auth->getStorage()->write($data);
                        //Verify unique user connect
                        $logdata=null;
                        $logs = new Api_Model_DbTable_Logs();
                        $logdata['eid']=$eid;
                        $logdata['oid']=$oid;
                        $logdata['uid']=$cod;
                        $rlogs = $logs->_getConnect($logdata);
                        if (count($rlogs)>2){
                            //echo "Existe otra sesion abierta en algun otro lugar";exit();
                            //$this->_redirect("/default/index/salir");
                        }
                        $urlmod = $data->rol['module'];
                        //$passn= base64_encode($clavecampus);
                        //$urllogin  = "key/$passn/mod/".$data->rol['module'];
                        //$urllogin  = array("key"=>$passn, "mod" => $data->rol['module']);
                        //if (trim($data->rid)=='AL' || $data->rid=='DC')
                        //  $this->_forward("ajax", "index", "default", $urllogin );
                        //else
                        $this->_redirect($urlmod);
                    }
                }else {
                    switch ($result->getCode()) {
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            $this->view->msgerror="El código que ingreso no existe";
                            break;
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $this->view->msgerror="Su contraseña es incorrecta";
                            break;
                        default:
                            $this->view->msgerror="Se produjo un error al ingreso, intentelo nuevamente";
                            break;
                    }
    			}
    		}else{
    			$form->populate($formData);
    		}
    	}
    	}  catch (Exception $ex){
    		print "Error Login access ".$ex->getMessage();
    	}
    }

    public function rolesAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data['eid']= base64_decode($this->_getParam('eid'));
    	$data['oid']= base64_decode($this->_getParam('oid'));
    	$roles = new Api_Model_DbTable_Rol();
    	$rol_ids = $roles->_getAll($data);
    	$this->view->roles=$rol_ids ;
    }

    public function salirAction()
    {
    	$sesion  = Zend_Auth::getInstance();
    	$sesion_ = $sesion->getStorage()->read();
    	//print_r($sesion_);
    	$log = new Api_Model_DbTable_Logs();
    	$data['eid'] =$sesion_->eid;
    	$data['oid']=$sesion_->oid;
    	$data['tokenid']=$sesion_->tokenid;
    	$data['uid']=$sesion_->uid;
    	$data['pid']=$sesion_->pid;
    	$r=$log->_getOne($data);
    	if ($r){
    		$datalog['dateend']= date(DATE_RFC822);
    		$datalog['state']= 'C';
    		$log->_update($datalog,$data);
    		Zend_Auth::getInstance()->clearIdentity();
    		$this->_redirect("/");
    	}
    	$this->_redirect("/");
    }

    public function cerrarAction()
    {
    	$sesion  = Zend_Auth::getInstance();
    	$sesion_ = $sesion->getStorage()->read();
    	$data['eid'] =$sesion_->eid;
    	$data['oid']=$sesion_->oid;
    	$data['uid']=$sesion_->uid;
    	$data['state']="A";
    	$log = new Api_Model_DbTable_Logs();
    	$r=$log->_getFilter($data,array("pid","uid"));
    	if ($r){
    		$datalog['dateend']= date(DATE_RFC822);
    		$datalog['state']= 'C';
    		echo $sql="eid='".$data['eid']."' and oid='".$data['oid']."' and uid='".$data['uid']."'";
    		echo "";

    		$log->update($datalog,$sql);
    		//Zend_Auth::getInstance()->clearIdentity();
    		$this->_redirect("/");
    	}
    }

    public function ajaxAction(){
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"salir",'default');
    	}

    	$this->view->http = "http";
    	if($_SERVER['SERVER_PORT'] == '443') {
    		$this->view->http = "https";
    	}

    	$sesion_ = $sesion->getStorage()->read();
    	$pass= base64_decode($this->_getParam("key"));
    	$mod= ($this->_getParam("mod"));


    	$this->view->uid= $sesion_->uid;
    	$this->view->pass= $pass;
    	$this->view->mod= $mod;
    }

    public function recoverpasswordAction(){
        $this->_helper->layout()->disableLayout();
        $eid = "20154605046";
        $oid = "1";
        $dbuser = new Api_Model_DbTable_Users();
        $dbperson = new Api_Model_DbTable_Person();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            $where = array('eid'=>$eid,'oid'=>$oid,'uid'=>$data['user1'],'state'=>'A');
            $duser = $dbuser->_getFilter($where);

            if ($duser) {
                $where1 = array('eid'=>$eid,'pid'=>$duser[0]['pid']);
                $per = $dbperson->_getFilter($where1);

                if ($per[0]['mail_person']) {
                    if ($per[0]['mail_person']==$data['mail']) {
                        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                        $cad = "";
                        for($i=0;$i<7;$i++) {
                            $cad .= substr($str,rand(0,62),1);
                        }

                        $dir = APPLICATION_LIBRARY . "/mail/Enviarmail.php";
                        include ($dir);
                        $r[] = array('correo'=>$per[0]['mail_person'],'apenom'=>$per[0]['last_name0'],'escuela'=>"Bienvenido a la recuperacion del password");
                        $f = new Enviarmail();
                        $html = '
                                <div>
                                    <u><b>Recuperación de Contraseña</b></u>

                                    <div>Usted está solicitando el cambio de su contraseña</div><br><br>
                                    <div>Ingrese el siguiente código en el <b>PASO 2</b></div>
                                    <br>
                                    <div style="margin-left:10%; background:#EEEEEE; width:130px; padding:0.7em;">
                                        <b>CODIGO</b><b style="background:#bfd3f2; padding:0.5em; width: 0.6">'.$cad.'</b>
                                    </div>
                                    <br><br>
                                    <div>
                                        PD: Si no solicito el cambio de su contraseña, ignore este correo.
                                    </div>
                                    <br><br>
                                    Atentamente.
                                </div>';
                        $f->scorreo($r,$html,"Recuperación de Contraseña"); 

                        if ($f) {
                            $data_a= array('token_password'=>$cad);
                            $where_a =array('eid'=>$eid,'oid'=>$oid,'escid'=>$duser[0]['escid'],'subid'=>$duser[0]['subid'],'pid'=>$duser[0]['pid'],'uid'=>$duser[0]['uid']);
                            $dbuser->_update($data_a,$where_a);
                            $json = array('status'=>true,'sms'=>"Se le envió un <b class='text-primary'>CODIGO DE VERIFICACION</b> al E-mail ingresado",
                                    'eid'=>$eid,'oid'=>$oid,'escid'=>$duser[0]['escid'],'subid'=>$duser[0]['subid'],'pid'=>$duser[0]['pid'],'uid'=>$duser[0]['uid']);                        
                        }   
                    }
                    else{
                        $json = array('status'=>false,'mail'=>true,'smsmail'=>"El e-mail ingresado no esta registrado en el sistema");    
                    }

                }
                else{
                    $json = array('status'=>false,'sms'=>"No cuenta con ningun e-mail en el sistema");
                }
            }
            else{
                $json = array('fail'=>true,'sms'=>"El usuario no esta registrado en el sistema");
            }
        }    
        $this->_response->setHeader('Content-Type', 'application/json');
        print(json_encode($json));
    }

    public function showtokenAction(){
        $this->_helper->layout()->disableLayout();
        $eid = base64_decode($this->_getParam('eid'));
        $oid = base64_decode($this->_getParam('oid'));
        $escid = base64_decode($this->_getParam('escid'));
        $subid = base64_decode($this->_getParam('subid'));
        $pid = base64_decode($this->_getParam('pid'));
        $uid = base64_decode($this->_getParam('uid'));

        $where = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'pid'=>$pid,'uid'=>$uid);
        $this->view->where=$where;
    }

    public function verificationtokenAction(){
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            $where1 = array('eid'=>$data['eid'],
                            'oid'=>$data['oid'],
                            'escid'=>$data['escid'],
                            'subid'=>$data['subid'],
                            'pid'=>$data['pid'],
                            'uid'=>$data['uid']);
            $dbuser = new Api_Model_DbTable_Users();
            $result = $dbuser->_getFilter($where1);
            $result = $result[0];

            if ($result['token_password']==$data['token']) {
                $json = array('status'=>true,'sms'=>" El código de verificación es correcto",
                                'eid'=>$result['eid'],
                                'oid'=>$result['oid'],
                                'escid'=>$result['escid'],
                                'subid'=>$result['subid'],
                                'pid'=>$result['pid'],
                                'uid'=>$result['uid']);                        

                $data_a= array('token_password'=>null);
                $where_a =array('eid'=>$result['eid'],'oid'=>$result['oid'],'escid'=>$result['escid'],'subid'=>$result['subid'],'pid'=>$result['pid'],'uid'=>$result['uid']);
                $dbuser->_update($data_a,$where_a);
            }
            else{
                $json = array('status'=>false,'sms'=>" El código de verificación no es válido <br> <b>Revise otra vez su e-mail</b>");
            }
        }
        $this->_response->setHeader('Content-Type', 'application/json');
        print(json_encode($json));
    }


    public function showpasswordAction(){
        $this->_helper->layout()->disableLayout();
        $eid = base64_decode($this->_getParam('eid'));
        $oid = base64_decode($this->_getParam('oid'));
        $escid = base64_decode($this->_getParam('escid'));
        $subid = base64_decode($this->_getParam('subid'));
        $pid = base64_decode($this->_getParam('pid'));
        $uid = base64_decode($this->_getParam('uid'));

        $where1 = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'pid'=>$pid,'uid'=>$uid);
        $this->view->where=$where1;   
    }

    public function passwordsaveAction(){
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($data['password1']==$data['password2']) {
                $where2 = array('eid'=>$data['eid'],'oid'=>$data['oid'],'escid'=>$data['escid'],
                        'subid'=>$data['subid'],'pid'=>$data['pid'],'uid'=>$data['uid']);

                $dbuser_ = new Api_Model_DbTable_Users();

                $dupdate = array('password'=>md5($data['password1']));

                if ($dbuser_->_update($dupdate,$where2)) {
                    $json = array('status'=>true,'sms'=>" ¡ Su contraseña fué cambiada con éxito");
                }
                else{
                    $json = array('status'=>false,'sms'=>"Hubo un problema al cambiar su contraseña");
                }
            }
            else{
                $json = array('fail'=>true,'sms1'=>"La contraseñas no coinciden <br> Intente de nuevo");               
            }
        }

        $this->_response->setHeader('Content-Type', 'application/json');
        print(json_encode($json));

    }
}

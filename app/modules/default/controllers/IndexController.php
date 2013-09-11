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
    			$pass = md5($form->getValue('clave'));
    			$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
    			$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter,'base_users','uid','password');
    			$authAdapter->getDbSelect()->where("state = 'A' and eid='$eid' and oid='$oid'");
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
    				$data->period = new stdClass();
    				$data->faculty = new stdClass();
    				$data->speciality = new stdClass();
    				$data->infouser = new stdClass();
    				$data->rol = new stdClass();
    				$data->org = new stdClass();
    				
    				$data->period->perid = $period;
    				$data->period->name = $name_period;
    				$data->period->next= $periodnext ;
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
    				
    				// Set info User
    				$user = new Api_Model_DbTable_Users();
    				$row = $user->_getInfoUser(array("eid"=>$eid,"oid"=>$oid,"uid"=>$data->uid,
    								"pid"=>$data->pid,"escid"=>$data->escid,"subid"=>$data->subid));
    				$full = $row[0]['last_name0']." ".$row[0]['last_name1'].", ".$row[0]['first_name'];
    				$row[0]['fullname']=$full;
    				$data->sex=$row[0]['sex'];
    				$data->infouser=$row[0];
    				
    				$rols_ = new Api_Model_DbTable_Rol();
    				$rol_ = $rols_->_getOne(array("eid"=>$data->eid,"oid"=>$data->oid,"rid"=>$data->rid));
    				if ($rol_)
    					$data->rol = $rol_;
    				else{ 
    					$msg = "Error nose especifico un rol para el usuario";
    					$this->_redirect("/error/msg/msg/'$msg'");
    				}
    				// Select infoteacher
    				$datate['eid']= $data->eid;
    				$datate['oid']= $data->oid;
    				$datate['uid']= $data->uid;
    				$datate['pid']= $data->pid;
    				$datate['escid']= $data->escid;
    				$datate['subid']= $data->subid;
    				$teacher = new Api_Model_DbTable_Infoteacher();
    				$rowteacher = $teacher->_getOne($datate);
    				
    				$data->infouser['teacher']=$rowteacher;
					// Set ACL
    				$tmpacl = $this->_aclCreated($data->rid,$data);
    				$data->acls= $tmpacl['module'];
    				$data->resources=$tmpacl['list'];
    				
    				/*
    				
    				$acl = new Api_Model_DbTable_Acl();
    				$data_ = array("eid"=>$data->eid,"oid"=>$data->oid,"rid"=>$data->rid);
    				$rowacl = $acl->_getACL($data_);
    				
    				if ($rowacl) {
    					$modules = new Api_Model_DbTable_Module();
    					$rmodules = $modules->_getAll(array("eid"=>$data->eid,"oid"=>$data->oid));
    					if ($rmodules){
    						$f=0;
    						$dataacl=null;
    						foreach ($rmodules as $mod){
    							$mod['acls']=null;
    							foreach ($rowacl as $mods){
    								if ($mod['mid']==$mods['mid']){
    									 $mod['acls'][]=$mods;
    									 $dataresource[] = $mods['controller'];
    								}
    							}
    							if ($mod['acls']<>null){
    								$dataacl[]=$mod;    								
    							} 
    						}
    						$data->acls =$dataacl;
    						$data->resources = $dataresource;
    					}
    					
    				} */
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
    						$this->_redirect("/index/cerrar");
    					}
    					$urlmod = $data->rol['module'];
    					$this->_redirect($urlmod);
    					//Falta direccionar
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
    	$data['state']="A";
    	$log = new Api_Model_DbTable_Logs();
    	$r=$log->_getFilter($data,array("pid","uid"));
    	if ($r){
    		$datalog['dateend']= date(DATE_RFC822);
    		$datalog['state']= 'C';
    		$sql="eid='".$data['eid']."' and oid='".$data['oid']."'";
    		$log->update($datalog,$sql);
    		Zend_Auth::getInstance()->clearIdentity();
    		$this->_redirect("/");
    	}
    	
    } 
    
    private function _aclCreated($rid='',$login=''){
    	if ($rid=="" || $login=="") return false;    	 
    	$modules = null; 
    	$acls = null;
    	//ACL comunes y que sirven para todos
    	$resource1[]="index/salir";
    	$resource1[]="index/cerrar";
    	$resource1[]="register/deferred";
    	$resource1[]="profile/public";
    	$resource1[]="report/recordnotas";
    	$resource1[]="default/password";
    	
    	switch ($rid){
    		case "AD":{
    			$modules[0] = array ("name" =>"Administración Plataforma", "imgicon"=>"book");
    			$acls[]= array("controller"=>"admin/receipts","name"=>"Cargar Recibos","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/password/search","name"=>"Cambiar Clave","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/bankpayments","name"=>"Pagos del Banco","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/rateregister","name"=>"Adm. Tasas","imgicon"=>"calendar");
    			
    			$resource1[]="admin/receipts";
    			$resource1[]="admin/password/search";
    			$resource1[]="admin/bankpayments";
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			
    			break;
    		}
    		case "SO":{
    			$modules[0] = array ("name" =>"Administración Plataforma", "imgicon"=>"book");
    			$acls[]= array("controller"=>"admin/receipts","name"=>"Cargar Recibos","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/password/search","name"=>"Cambiar Clave","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/bankpayments","name"=>"Pagos del Banco","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/rateregister","name"=>"Adm. Tasas","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"register/changerates","name"=>"Cambio de Tasas","imgicon"=>"calendar");
    			$resource1[]="admin/receipts";
    			$resource1[]="admin/password/search";
    			$resource1[]="admin/bankpayments";
    			$resource1[]="admin/rateregister";
    			$resource1[]="register/changerates";
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			
    			$modules[1] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"record/index","name"=>"ASIGNATURAS(ACTAS)","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");
    			$resource1[]="record/index";
    			$resource1[]="curricula/show";
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			
    			$modules[2] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
    			$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    			$acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
    			$resource1[]="report/performance";
    			$resource1[]="report/recordnotas";
    			$resource1[]="report/registration";
    			$modules[2]['acls'] = $acls;
    			$acls = null;
    			break;
    		}
    		case "SP": {
    			$resource1[]="soporte/index";
    			$modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"register/listcurrentnotes","name"=>"Asignaturas Actuales","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"horary/consolidated","name"=>"Ver Horario","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"horary/semester","name"=>"Horarios Semes.","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"register/changerates","name"=>"Cambio de Tasas","imgicon"=>"calendar");
    			$resource1[]="horary/consolidated";
    			$resource1[]="register/listcurrentnotes";
    			$resource1[]="horary/semester";
    			$resource1[]="register/changerates";
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			 
    			$modules[1] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"record/index","name"=>"ASIGNATURAS(ACTAS)","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");
    			$resource1[]="record/index";
    			$resource1[]="curricula/show";
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			
    			$modules[2] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
    			$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    			$acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
    			$resource1[]="report/performance";
    			$resource1[]="report/recordnotas";
    			$resource1[]="report/registration";
    			$modules[2]['acls'] = $acls;
    			$acls = null;
    			break;
    		}
    		
    		case "AL": {
    			$resource1[]="alumno/index";
    			$resource1[]="assistence/student";
    			$modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"register/listcurrentnotes","name"=>"Asignaturas Actuales","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"horary/consolidated","name"=>"Ver Horario","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"horary/semester","name"=>"Horarios Semes.","imgicon"=>"calendar");
    			$resource1[]="horary/consolidated";
    			$resource1[]="register/listcurrentnotes";
    			$resource1[]="horary/semester";
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			
    			$modules[1] = array ("name" =>"Matrícula", "imgicon"=>"ok");
    			$acls[]= array("controller"=>"register/student","name"=>"Prematricula","imgicon"=>"edit");
    			$resource1[]="register/student";
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			break;
    		}
    		case "DC":{
    			$resource1[]="docente/index";
    			$resource1[]="syllabus/syllabus";
    			
    			$modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"docente/notas","name"=>"Asignaturas a Cargo","imgicon"=>"list");
    			$acls[]= array("controller"=>"horary/semester","name"=>"Ver Horarios Sem.","imgicon"=>"calendar");
    			$resource1[]="docente/notas";
    			$resource1[]="horary/semester";

    			if (($login->infouser['teacher']['is_director']=="S")){
    				$acls[]= array("controller"=>"horary/nhorary","name"=>"Crear Horario","imgicon"=>"file");
    				$resource1[]="horary/nhorary";
    			}
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			    			
    			$modules[1] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
    			$acls[]= array("controller"=>"#","name"=>"Evaluación Rendimiento","imgicon"=>"edit");
    			$resource1[]="report/performance";
    			if (($login->infouser['teacher']['is_director']=="S")){    	
    				$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    				$resource1[]="report/performance";
    			}
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			
    			if (($login->infouser['teacher']['is_director']=="S")){
    				$modules[2] = array ("name" =>"Periodo Académico", "imgicon"=>"folder");
    				$acls[]= array("controller"=>"distribution/distribution","name"=>"Distribución","imgicon"=>"folder-close");
    				$resource1[]="distribution/distribution";
    				$modules[2]['acls'] = $acls;
    				$acls = null;
    			}
    			break;
    		}
    		case "RF":{
    			$resource1[]="rfacultad/index";
    			$resource1[]="profile/search";
    			$resource1[]="profile/changecurricula";
    			$modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"record/index","name"=>"ASIGNATURAS(ACTAS)","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");
    			$resource1[]="record/index";
    			$resource1[]="curricula/show";
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			
    			$modules[1] = array ("name" =>"Matricula", "imgicon"=>"ok");
    			$acls[]= array("controller"=>"rfacultad/condition","name"=>"Condición Matricula","imgicon"=>"saved");
    			$acls[]= array("controller"=>"record/directed","name"=>"Subsanación/Dirigido.","imgicon"=>"file");
    			$acls[]= array("controller"=>"register/registerstudent","name"=>"Validación Matricula.","imgicon"=>"ok");
    			$acls[]= array("controller"=>"register/validation/addcourse","name"=>"Convalidación.","imgicon"=>"file");
    			$resource1[]="rfacultad/condition";
    			$resource1[]="record/directed";
    			$resource1[]="register/registerstudent";
    			$resource1[]="register/validation/addcourse";
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			
    			$modules[2] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
    			$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    			$acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
    			$resource1[]="report/performance";
    			$resource1[]="report/recordnotas";
    			$resource1[]="report/registration";
    			$modules[2]['acls'] = $acls;
    			$acls = null;
    			break;
    		}
    		case "RC":{
    			$resource1[]="rcentral/index";
    			$resource1[]="profile/search";
    			$modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"record/index","name"=>"ASIGNATURAS(ACTAS)","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"curricula/curricula","name"=>"Adm. Curriculas.","imgicon"=>"list");
    			$acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");
    			$resource1[]="record/index";
    			$resource1[]="curricula/curricula";
    			$resource1[]="curricula/show";
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			 
    			$modules[1] = array ("name" =>"Matricula", "imgicon"=>"ok");
    			$acls[]= array("controller"=>"#","name"=>"Matricula Ingresantes","imgicon"=>"saved");
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			 
    			$modules[2] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
    			$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    			$acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
    			$acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
    			$resource1[]="report/performance";
    			$resource1[]="report/recordnotas";
    			$resource1[]="report/registration";
    			$modules[2]['acls'] = $acls;
    			$acls = null;
    			break;
    		}
    	}
    	return array("module"=>$modules,"list"=>$resource1);
    }
}

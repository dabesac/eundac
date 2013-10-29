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
    					$passn= base64_encode($clavecampus);
    					$urllogin  = "key/$passn/mod/".$data->rol['module'];
    					$urllogin  = array("key"=>$passn, "mod" => $data->rol['module']);
    					if (trim($data->rid)=='AL' || $data->rid=='DC')
    						$this->_forward("ajax", "index", "default", $urllogin );
    					else
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
        $resource1[]="register/registerealized";
        
        
        switch ($rid){
            case "AD":{
                $resource1[]="profile/search";
                $resource1[]="profile/changecurricula";
                
                $modules[0] = array ("name" =>"Plataforma", "imgicon"=>"book");
                $acls[]= array("controller"=>"admin/receiptsup","name"=>"Cargar Recibos","imgicon"=>"calendar");
                $acls[]= array("controller"=>"admin/password","name"=>"Cambiar Clave","imgicon"=>"calendar");
                $acls[]= array("controller"=>"admin/bankpayments","name"=>"Pagos del Banco","imgicon"=>"calendar");
                $acls[]= array("controller"=>"admin/person","name"=>"Crear Personas","imgicon"=>"user");
                $acls[]= array("controller"=>"admin/user","name"=>"Crear Usuario","imgicon"=>"user");

                $acls[]= array("controller"=>"admin/rol","name"=>"Crear Rol","imgicon"=>"user");
                $acls[]= array("controller"=>"admin/faculty","name"=>"Crear Facultad","imgicon"=>"edit");
                $acls[]= array("controller"=>"admin/org","name"=>"Crear Organización","imgicon"=>"edit");
                $acls[]= array("controller"=>"admin/opensillabus","name"=>"Abrir Silabus","imgicon"=>"user");
                $acls[]= array("controller"=>"admin/openrecords","name"=>"Abrir Actas","imgicon"=>"folder-close");
                $acls[]= array("controller"=>"admin/rateregister","name"=>"Tasas Matricula","imgicon"=>"user");

                $resource1[]="admin/receiptsup";
                $resource1[]="admin/password";
                $resource1[]="admin/bankpayments";
                $resource1[]="admin/person";
                $resource1[]="admin/user";

                $resource1[]="admin/rol";
                $resource1[]="admin/faculty";
                $resource1[]="admin/org";
                $resource1[]="admin/opensillabus";
                $resource1[]="admin/openrecords";
                $resource1[]="admin/rateregister";
                
                $modules[0]['acls'] = $acls;
                $acls = null;
                
                
                $modules[1] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
                $acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
                $acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
                $acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
                $resource1[]="report/performance";
                $resource1[]="report/recordnotas";
                $resource1[]="report/registration";
                $modules[1]['acls'] = $acls;
                $acls = null;
                break;
            }
    		case "SP":{
    			$resource1[]="profile/search";
    			$resource1[]="profile/changecurricula";
    			
    			$modules[0] = array ("name" =>"Plataforma", "imgicon"=>"book");
    			$acls[]= array("controller"=>"admin/receipts","name"=>"Cargar Recibos","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/password/search","name"=>"Cambiar Clave","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/bankpayments","name"=>"Pagos del Banco","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/rateregister","name"=>"Adm. Tasas","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"register/changerates","name"=>"Cambio de Tasas","imgicon"=>"calendar");
    			$acls[]= array("controller"=>"admin/person","name"=>"Crear Personas","imgicon"=>"user");
    			$acls[]= array("controller"=>"admin/user","name"=>"Crear Usuario","imgicon"=>"user");
    			$acls[]= array("controller"=>"admin/opensillabus","name"=>"Abrir Silabus","imgicon"=>"user");
    			$acls[]= array("controller"=>"admin/openrecords","name"=>"Abrir Actas","imgicon"=>"folder-close");
    			
    			$resource1[]="admin/receipts";
    			$resource1[]="admin/password";
    			$resource1[]="admin/bankpayments";
    			$resource1[]="admin/rateregister";
    			$resource1[]="register/changerates";
    			$resource1[]="admin/opensillabus";
    			$resource1[]="admin/openrecords";
    			$resource1[]="admin/person";
    			$resource1[]="admin/user";
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
    			//$modules[1]['acls'] = $acls;
    			$acls = null;
    			break;
    		}
    		case "DC":{
    			$resource1[]="docente/index";
    			$resource1[]="syllabus/syllabus";
    			$resource1[]="syllabus/print";                
    			$resource1[]="assistance/student";
    			
    			$modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
    			$acls[]= array("controller"=>"docente/notas","name"=>"Asignaturas a Cargo","imgicon"=>"list");
                $resource1[]="docente/notas";
                $acls[]= array("controller"=>"horary/seehorary","name"=>"Ver Horario","imgicon"=>"calendar");
                $resource1[]="horary/seehorary";
                $acls[]= array("controller"=>"docente/informacademic","name"=>"Informe Acad. Adm.","imgicon"=>"file");
                $resource1[]="docente/informacademic";
                $resource1[]="docente/fillnotes";
                $resource1[]="docente/register";
                
                
                if (($login->infouser['teacher']['is_director']=="S")){
                    $acls[]= array("controller"=>"record/index","name"=>"ASIGNATURAS(ACTAS)","imgicon"=>"folder-close");
                    $resource1[]="record/index";
                    $acls[]= array("controller"=>"horary/nhorary","name"=>"Crear Horario","imgicon"=>"file");
                    $resource1[]="horary/nhorary";
    			    $acls[]= array("controller"=>"horary/semester","name"=>"Ver Horarios Sem.","imgicon"=>"calendar");
    			    $resource1[]="horary/semester";
    			}
    			$modules[0]['acls'] = $acls;
    			$acls = null;
    			    			
    			$modules[1] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
    			if (($login->infouser['teacher']['is_director']=="S")){  
                    $acls[]= array("controller"=>"report/periods","name"=>"Avance Academico","imgicon"=>"list-alt");  
                    $resource1[]="report/periods";	
    				$acls[]= array("controller"=>"syllabus/director/listsyllabus","name"=>"Llenado Silabus","imgicon"=>"edit");
    				$resource1[]="syllabus/director"; 
                    $acls[]= array("controller"=>"report/consolidated","name"=>"Reporte General","imgicon"=>"folder-open"); 
                    $resource1[]="report/consolidated";				
    				$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    				$resource1[]="report/performance";
                    $acls[]= array("controller"=>"graduated/reportgraduated","name"=>"Egresados","imgicon"=>"edit");
                    $resource1[]="graduated/reportgraduated";
    				$acls[]= array("controller"=>"graduated/graphicgraduated","name"=>"Grafica Egresados","imgicon"=>"edit");
                    $resource1[]="graduated/graphicgraduated";
    			}
    			$acls[]= array("controller"=>"docente/index/poll","name"=>"Evaluación Rendimiento","imgicon"=>"edit");
    			$resource1[]="report/performance";
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			
    			if (($login->infouser['teacher']['is_director']=="S")){
    				$modules[2] = array ("name" =>"Periodo Académico", "imgicon"=>"folder");
    				$acls[]= array("controller"=>"distribution/distribution","name"=>"Distribución","imgicon"=>"folder-close");
    				$resource1[]="distribution/distribution";
                    $acls[]= array("controller"=>"curricula/show","name"=>"Currícula","imgicon"=>"book");
                    $resource1[]="curricula/show";
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
    			$acls[]= array("controller"=>"register/validation","name"=>"Convalidación.","imgicon"=>"file");
    			$resource1[]="rfacultad/condition";
    			$resource1[]="record/directed";
    			$resource1[]="register/registerstudent";
    			$resource1[]="register/validation";
    			$modules[1]['acls'] = $acls;
    			$acls = null;
    			
    			$modules[2] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
                $acls[]= array("controller"=>"report/periods","name"=>"Avance Academico","imgicon"=>"list-alt");
    			$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    			$acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
                $acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
                $acls[]= array("controller"=>"report/consolidated","name"=>"Reporte General","imgicon"=>"folder-open");
                $acls[]= array("controller"=>"graduated/reportgraduated","name"=>"Reporte Egresados","imgicon"=>"list");
    			$acls[]= array("controller"=>"graduated/graphicgraduated","name"=>"Grafica Egresados","imgicon"=>"signal");
    			$resource1[]="report/periods";
                $resource1[]="report/performance";
    			$resource1[]="report/recordnotas";
                $resource1[]="report/registration";
                $resource1[]="report/consolidated";
                $resource1[]="graduated/reportgraduated";
    			$resource1[]="graduated/graphicgraduated";
    			$modules[2]['acls'] = $acls;
    			$acls = null;
    			break;
    		}
    		
    		case "RC":{
    			$resource1[]="rcentral/index";
    			$resource1[]="profile/search";
                $resource1[]="profile/changecurricula";
                $resource1[]="profile/privateadm/student";
    			
    			
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
                $acls[]= array("controller"=>"report/periods","name"=>"Avance Academico","imgicon"=>"list-alt");
    			$acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
    			$acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
                $acls[]= array("controller"=>"report/consolidated","name"=>"Reporte General","imgicon"=>"folder-open");
                $acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
                $acls[]= array("controller"=>"graduated/reportgraduated","name"=>"Reporte Egresados","imgicon"=>"list");
    			$acls[]= array("controller"=>"graduated/graphicgraduated","name"=>"Grafica Egresados","imgicon"=>"signal");
            	$resource1[]="report/periods";
                $resource1[]="report/performance";
    			$resource1[]="report/recordnotas";
                $resource1[]="report/consolidated";
                $resource1[]="report/registration";
                $resource1[]="graduated/reportgraduated";
    			$resource1[]="graduated/graphicgraduated";
    			$modules[2]['acls'] = $acls;
    			$acls = null;
    			break;

    		}


            case "RE":{
                $resource1[]="vacademico/index";
                $resource1[]="profile/search";
                $resource1[]="profile/changecurricula";
                $resource1[]="rcentral/code";
                
                $modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
                $acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");

                $resource1[]="curricula/show";
                $modules[0]['acls'] = $acls;
                $acls = null;
                break;
                }   

            case "PD":{
                 
                $modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
            
                $acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");
                $resource1[]="curricula/show";
                $modules[0]['acls'] = $acls;
                $acls = null;

                $modules[1] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
                $acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
                $acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
                $acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");

                $resource1[]="report/performance";
                $resource1[]="report/recordnotas";
                $resource1[]="report/registration";
                $modules[1]['acls'] = $acls;
                $acls = null;
                break;
            }

            case "BU":{
                              
                $modules[0] = array ("name" =>"Plataforma", "imgicon"=>"book");
                $acls[]= array("controller"=>"register/changerates","name"=>"Cambio de Tasas","imgicon"=>"screenshot");
                $resource1[]="register/changerates";
                $modules[0]['acls'] = $acls;
                $acls = null;
                break;
            }

            case "DF":{
                $resource1[]="profile/search";

                $modules[0] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
                $acls[]= array("controller"=>"report/periods","name"=>"Avance Academico","imgicon"=>"list-alt");
                $acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
                $acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
                $acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
                $acls[]= array("controller"=>"graduated/reportgraduated","name"=>"Reporte Egresados","imgicon"=>"list");
                $acls[]= array("controller"=>"graduated/graphicgraduated","name"=>"Grafica Egresados","imgicon"=>"signal");
                $resource1[]="report/periods";
                $resource1[]="report/performance";
                $resource1[]="report/recordnotas";
                $resource1[]="report/registration";
                $resource1[]="graduated/reportgraduated";
                $resource1[]="graduated/graphicgraduated";

                $modules[1]['acls'] = $acls;
                $acls = null;

                $modules[0]['acls'] = $acls;
                $acls = null;
                break;
            }

            case "EG":{
                $resource1[]="graduated/index";
                $resource1[]="profile/search";
                //$resource1[]="profile/changecurricula";
                $resource1[]="alumno/index";



                $modules[0] = array ("name" =>"Perfil", "imgicon"=>"book");            
                $acls[]= array("controller"=>"profile/public/student","name"=>"Historial","imgicon"=>"calendar");
             
                $resource1[]="profile/public/student";
                $modules[0]['acls'] = $acls;
                $acls = null;              
               
                break;
            }

            case "VA":{
                $resource1[]="rcentral/index";
                $resource1[]="profile/search";                
                
                $modules[0] = array ("name" =>"Gestión Asignaturas", "imgicon"=>"book");
            
                $acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");
                $resource1[]="curricula/show";
                $modules[0]['acls'] = $acls;
                $acls = null;
                 
                // $modules[1] = array ("name" =>"Matricula", "imgicon"=>"ok");
                // $acls[]= array("controller"=>"#","name"=>"Matricula Ingresantes","imgicon"=>"saved");
                // $modules[1]['acls'] = $acls;
                // $acls = null;
                 
                $modules[2] = array ("name" =>"Reportes", "imgicon"=>"list-alt");
                $acls[]= array("controller"=>"report/periods","name"=>"Avance Academico","imgicon"=>"list-alt");
                $acls[]= array("controller"=>"report/performance","name"=>"Rendimiento","imgicon"=>"edit");
                $acls[]= array("controller"=>"report/recordnotas","name"=>"Record Notas","imgicon"=>"folder-close");
                $acls[]= array("controller"=>"report/registration","name"=>"Reporte Matriculados","imgicon"=>"signal");
                $acls[]= array("controller"=>"graduated/reportgraduated","name"=>"Reporte Egresados","imgicon"=>"list");
                $acls[]= array("controller"=>"graduated/graphicgraduated","name"=>"Grafica Egresados","imgicon"=>"signal");
                $resource1[]="report/periods";
                $resource1[]="report/performance";
                $resource1[]="report/recordnotas";
                $resource1[]="report/registration";
                $resource1[]="graduated/reportgraduated";
                $resource1[]="graduated/graphicgraduated";
                $modules[2]['acls'] = $acls;
                $acls = null;                           
               
                break;
            }

            case "ES":{
                $resource1[]="rcentral/index";
                $resource1[]="profile/search";
                //$resource1[]="profile/changecurricula";
                $resource1[]="alumno/index";

                $modules[0] = array ("name" =>"Perfil", "imgicon"=>"book");            
                //$acls[]= array("controller"=>"profile/public/student","name"=>"Historial","imgicon"=>"calendar");
                $acls[]= array("controller"=>"graduated/reportgraduated","name"=>"Reporte Egresados","imgicon"=>"list");
                $acls[]= array("controller"=>"graduated/graphicgraduated","name"=>"Grafica Egresados","imgicon"=>"signal");
             
                //$resource1[]="profile/public/student";
                $resource1[]="graduated/reportgraduated";
                $resource1[]="graduated/graphicgraduated";
                $modules[0]['acls'] = $acls;
                $acls = null;              
               
                break;
            }
            case "RI":{
                $resource1[]="rinternational/index";
                $resource1[]="profile/search";

                $modules[0] = array ("name" =>"Gestión Académica", "imgicon"=>"book");
                $acls[]= array("controller"=>"curricula/show","name"=>"Curriculas.","imgicon"=>"tasks");
                $resource1[]="curricula/show";
                $modules[0]['acls'] = $acls;
                $acls = null;
               
                break;
            }

    	}
    	return array("module"=>$modules,"list"=>$resource1);
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
}

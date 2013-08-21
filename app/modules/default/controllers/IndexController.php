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
    		$this->_helper->redirector('index','index',base64_decode($sesion->rol['module']));
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
    				// Begin Variables
    				$data->period = new stdClass();
    				$data->faculty = new stdClass();
    				$data->speciality = new stdClass();
    				$data->infouser = new stdClass();
    				$data->rol = new stdClass();
    				
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
    						$te = $escuela->_getOne(array("eid"=>$eid,"oid"=>$oid,"escid"=>$esc['parent']));
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
    				if ($rowteacher) $data->infouser['teacher']=$rowteacher;
    				
    				
    				
    				// Register access
    				$clientIp = $this->getRequest()->getClientIp();
    				$log = new Api_Model_DbTable_Logs();
    				$aleatorio = rand(2,9);
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
    				$datalog['browser'] = $device->getBrowser();
    				$datalog['vbrowser'] = $device->getBrowserVersion();
    				$datalog['browserinfo'] = $device->getUserAgent();
    				$log->_save($datalog);
    				$auth->getStorage()->write($data);
    				//Verify unique user connect
    				$logs = new Api_Model_DbTable_Logs();
    				$logdata['eid']=$eid;
    				$logdata['oid']=$oid;
    				$logdata['uid']=$cod;
    				$rlogs = $logs->_getConnect($logdata);
    				if (count($rlogs)>1){
    					//echo "Existe otra sesion abierta en algun otro lugar";exit();
    					$this->_redirect("/index/cerrar");
    				}
    				
    				$urlmod = base64_decode($data->rol['module']);
    				$this->_redirect($urlmod);
    				//Falta direccionar
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
}

<?php
 class Horary_SemesterController extends Zend_Controller_Action{

 	public function init(){
 		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="alumno"){
 			$this->_helper->redirector('index','index','default');
 		}
 		$this->sesion = $login;
 		require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');
 	}

 	public function indexAction(){
 		try {
 			$eid=$this->sesion->eid;
 			$oid=$this->sesion->oid;
 			$escid=$this->sesion->escid;
 			$uid=$this->sesion->uid;
 			$pid=$this->sesion->pid;
 			$subid=$this->sesion->subid;
 			$perid=$this->sesion->period->perid;
			$base_url = 'http://localhost:8080/';
	        $endpoint = '/s1st3m4s/und4c/horary_student';
	        $data = array('escid' => $escid,'eid' =>$eid,'oid' =>$oid,'perid'=>$perid,'subid'=>$subid,'uid'=>$uid);
	        $client = new Zend_Rest_Client($base_url);
	        $httpClient = $client->getHttpClient();
	        $httpClient->setConfig(array("timeout" => 1800));
	        $response = $client->restget($endpoint,$data);
	        $lista=$response->getBody();
	        $data = Zend_Json::decode($lista);
        	// print_r($data);
        	$this->view->horarys=$data; 
 			
 		} catch (Exception $e) {
 			print "Error: get Horary".$e->getMessage();
 		}
 	}

 	public function printhoraryAction(){
 		try {
 			$eid=$this->sesion->eid;
	        $oid=$this->sesion->oid;
	        $escid=$this->sesion->escid;
	        $facid=$this->sesion->facid;
	        $uid=$this->sesion->uid;
	        $pid=$this->sesion->pid;
	        $subid=$this->sesion->subid;
	        $perid=$this->sesion->period->perid;
	        $this->view->uid=$uid;
			
			$base_url = 'http://localhost:8080/';
	        $endpoint = '/s1st3m4s/und4c/horary_student';
	        $data = array('escid' => $escid,'eid' =>$eid,'oid' =>$oid,'perid'=>$perid,'subid'=>$subid,'uid'=>$uid);
	        $client = new Zend_Rest_Client($base_url);
	        $httpClient = $client->getHttpClient();
	        $httpClient->setConfig(array("timeout" => 1800));
	        $response = $client->restget($endpoint,$data);
	        $lista=$response->getBody();
	        $data = Zend_Json::decode($lista);
        	$this->view->horarys=$data;
        	// print_r($data);
        	$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        	$esc = new Api_Model_DbTable_Speciality();
        	$desc = $esc->_getOne($where);
        	$this->view->desc=$desc;

        	$whered=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
        	$fac= new Api_Model_DbTable_Faculty();
        	$dfac = $fac->_getOne($whered);
        	$this->view->dfac=$dfac;

        	$wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'uid'=>$uid,'subid'=>$subid,'pid'=>$pid); 
        	$user = new Api_Model_DbTable_Users();
        	$duser = $user->_getInfoUser($wheres);
        	$this->view->duser=$duser;
        	// print_r($duser); exit();			
 		} catch (Exception $e) {
 			print "Error: print Horary".$e->getMessage();
 		}
 	}
 }
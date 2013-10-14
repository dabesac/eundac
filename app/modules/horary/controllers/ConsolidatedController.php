<?php
 class Horary_ConsolidatedController extends Zend_Controller_Action{

 	public function init(){
 		$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
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

 	public function printconsolidatedAction(){
 		try {
            $this->_helper->layout()->disableLayout();
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
        	$spe=array();
        	$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        	$esc = new Api_Model_DbTable_Speciality();
        	$desc = $esc->_getOne($where);
        	$parent=$desc['parent'];
        	$wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        	$parentesc= $esc->_getOne($wher);
        	if ($parentesc) {
        		$pala='ESPECIALIDAD DE ';
      			$spe['esc']=$parentesc['name'];
      			$spe['parent']=$pala.$desc['name'];
        		$this->view->spe=$spe;
        	}
        	else{
        		$spe['esc']=$desc['name'];
        		$spe['parent']=''; 	
        		$this->view->spe=$spe;
        	}

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

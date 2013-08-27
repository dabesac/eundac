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
 			// $escid=$this->sesion->escid;
 			// $uid=$this->sesion->uid;
 			$escid='4SI';
 			$uid='0924401019';
 			// $pid=$this->sesion->pid;
 			$subid=$this->sesion->subid;
 			$perid=$this->sesion->period->perid;
 			// $where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid);
 			// $peri= new Api_Model_DbTable_Periods();
 			// $dperi = $peri->_getOne($where);
 			// $dateini=$dperi['class_start_date'];
 			// $hor=new Api_Model_DbTable_Horary();
 			// $datefin=$hor->_getsumdate($dateini,'6');
 			// $datefin=$datefin[0]['dia'];
 			// print_r($datefin); exit();
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
 }
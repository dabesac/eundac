<?php

class Acreditacion_IndexController extends Zend_Controller_Action {

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction()
    {	

	   	$model = "standares_acredit";
    	$params = array(
			'eid' => base64_encode($this->sesion->eid),
			'oid' =>base64_encode($this->sesion->oid),
    		'escid' => base64_encode($this->sesion->escid),
    		);
    	$prueba = new Eundac_Connect_Api($model,$params);
    	$data= $prueba->connectAuth();
    	print_r($data);

    	/*$base_url = 'http://172.16.0.110:8080/';
	        $endpoint = '/'.base64_encode('s1st3m4s').'/'.base64_encode('und4c').'/standares_acredit';
		    $data = array(
					'eid' => base64_encode($this->sesion->eid),
					'oid' =>base64_encode($this->sesion->oid),
		    		'escid' => base64_encode($this->sesion->escid),
    		);
		    // print_r($data);
		    $client = new Zend_Rest_Client($base_url);
		    $httpClient = $client->getHttpClient();
		    $httpClient->setConfig(array("timeout" => 1800));
		    $response = $client->restget($endpoint,$data);
		    $lista= $response->getBody();
		    $data = Zend_Json::decode($lista);
		    print_r($data);/*

    	/*$server = new Zend_XmlRpc_Client('http://172.16.0.211:8069/xmlrpc/common');
		$client = $server->getProxy();
		try {
			$database = 'acreditacion';
			$user = 'admin';
			$password = 'sistemas';
			$auth = $client->login($database,$user,$password);
	    	$object = new Zend_XmlRpc_Client('http://172.16.0.211:8069/xmlrpc/');
	    	$estandar = $object->getProxy();
	    	//$data = $estandar->execute($database,$user,$password,'search','ac.estandar.school',array('escid'=>'4SI'));

		} catch (Zend_XmlRpc_Client_FaultException $e) {
			print "error ".$e->getMassage();
		}*/
    }
}

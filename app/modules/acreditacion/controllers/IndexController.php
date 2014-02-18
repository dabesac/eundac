<?php

class Acreditacion_IndexController extends Zend_Controller_Action {

    public function init()
    {
       
    }
    public function indexAction()
    {
    	$server = new Zend_XmlRpc_Client('http://172.16.0.211:8069/xmlrpc/common');
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
		}
    }
}

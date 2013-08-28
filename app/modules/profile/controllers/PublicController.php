<?php

class Profile_PublicController extends Zend_Controller_Action {

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
    	
    }

    public function studentAction()
    {
    	try{
    		//print_r($this->sesion->infouser['fullname']);
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$pid=$this->sesion->pid;
    		$uid=$this->sesion->uid;
    		$escid=$this->sesion->escid;
    		$fullname=$this->sesion->infouser['fullname'];

    		$datos[0]=array("fullname"=>$fullname, "uid"=>$uid);

    		$where=array("eid"=>$eid, "oid"=>$eid, "escid"=>$escid);
    		//print_r($where);
    		$dbfacesp=new Api_Model_DbTable_Speciality();
    		$datos[1]=$dbfacesp->_getFacspeciality($where);
    		//print_r($datos);
    		$this->view->datos=$datos;
    	}catch(exception $e){
    		print "Error : ".$e->getMessage();
    	}
    }
}

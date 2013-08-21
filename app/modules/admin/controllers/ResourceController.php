<?php

class Admin_ResourceController extends Zend_Controller_Action {

    public function init()
    {
    	/*$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->modulo=="admin"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;*/
    	$this->eid='20154605046';
		$this->oid='1';
       
    }
    public function indexAction()
    {
    	$eid=$this->eid;
    	$oid=$this->oid;
		$dbresource=new Api_Model_DbTable_Resource();
    	$allresources=$dbresource->_getAll($where);
    	print_r($resource);
    	$this->view->allresources=$allresources;
    	
    }
}
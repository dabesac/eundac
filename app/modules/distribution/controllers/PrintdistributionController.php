<?php

class Distribution_PrintdistributionController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (($login->infouser['teacher']['is_director']<>"S")){
    	// 	$this->_helper->redirector('index','error','default');
    	// }
    	$this->sesion = $login;
    }

    public function indexAction(){
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $distid = base64_decode($this->_getParam("distid"));
            $perid = base64_decode($this->_getParam("perid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
<?php

class Poll_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (!$login->modulo=="poll"){
    		// $this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $poll = new Api_Model_DbTable_Polll();
            $all_data = $poll->_getAll($where=array('eid' => $eid, 'oid' => $oid), $order='', $start=0, $limit=0);
            $this->view->poll = $all_data;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function newAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            // $form = new 
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}

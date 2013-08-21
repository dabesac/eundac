<?php
class Admin_AclController extends Zend_Controller_Action{

	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
	}

	 public function indexAction() 
    {   
        $this->_helper->redirector("list");
    }
    public function listAction()
    {
        try 
        {
               
        } 
        catch (Exception $ex) 
        {
            print "Error listando las Oficinas: ".$ex->getMessage();
        }
    }

	
}
<?php
class Admin_AclController extends Zend_Controller_Action{

	public function init(){
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
        try 
        {
            $eid=$this->eid;
            $oid=$this->oid;
            $dbrol=new Api_Model_DbTable_Rol();
            $where=array("eid"=>$eid,"oid"=>$oid);
            $order=array("name");
            $rols=$dbrol->_getAll($where,$order);
            //print_r($rols);
            $this->view->rols=$rols;
        } 
        catch (Exception $ex) 
        {
            print "Error listando al Crear Roles: ".$ex->getMessage();
        }

    }
    public function listrolsAction()
    {
        try{
            
        } 
        catch (Exception $ex) 
        {
            print "Error listando al Crear Roles: ".$ex->getMessage();
        }
    }

	
}
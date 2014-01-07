<?php

class Docente_EditheaderController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="docente"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    }

    public function indexAction(){
        try {

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;

            $wherescid= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);

            $form= new Docente_Form_Speciality();

            $esc= new Api_Model_DbTable_Speciality();
            $datescid=$esc->_getOne($wherescid);      
            //echo $date=$datescid['header'];

            $form->populate($datescid);
            $this->view->form=$form;

            } catch (Exception $e) {
            print "Error: ".$e->getMessage();
             }

    }

}
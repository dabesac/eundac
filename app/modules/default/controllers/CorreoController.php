<?php

class CorreoController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

	public function indexAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$uid=base64_decode($this->_getParam('uid'));
      		$escid=base64_decode($this->_getParam('escid'));
      		$where['eid']=base64_decode($this->_getParam('eid'));
      		$where['oid']=base64_decode($this->_getParam('oid'));
      		$subid=base64_decode($this->_getParam('subid'));
      		$where['pid']=base64_decode($this->_getParam('pid'));
                  $wheres['eid']=$this->sesion->eid;
                  $wheres['oid']=$this->sesion->oid;
                  $wheres['pid']=$this->sesion->pid;
                  $dbperson=new Api_Model_DbTable_Person();
                  $dataperson=$dbperson->_getOne($where);
                  $this->view->dataperson=$dataperson;
                  $person=$dbperson->_getOne($wheres);
                  $this->view->person=$person;
      		$form = new Default_Form_Correo();
                  $this->view->form=$form;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

      public function enviarAction(){
            try {
                  $this->_helper->layout()->disableLayout();
                  $de = $this->_getParam('de');
                  $para = $this->_getParam('para');
                  $asunto = $this->_getParam('asunto');
                  $contenido = $this->_getParam('contenido');  
                  $this->view->para=$para;
                  $this->view->asunto=$asunto;
                  $this->view->contenido=$contenido;
              }
                  catch (Exception $e) {
                  print "Error: ".$e->getMessage();
            }
      }

	
}
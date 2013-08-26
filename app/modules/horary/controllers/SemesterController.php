<?php
 class Horary_SemesterController extends Zend_Controller_Action{

 	public function init(){
 		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="alumno"){
 			$this->_helper->redirector('index','index','default');
 		}
 		print_r($this->sesion = $login);
 	}

 	public function indexAction(){
 		try {
 			$eid=$this->sesiom->eid;
 			$oid=$this->sesion->oid;
 			$escid=$this->sesion->escid;
 			$uid=$this->sesion->uid;
 			$pid=$this->sesion->pid;
 			$subid=$this->sesion->subid;
 			$perid=$this->sesion->period->perid;
 			
 			
 		} catch (Exception $e) {
 			print "Error: get Horary".$e->getMessage();
 		}


 	}
 }
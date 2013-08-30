<?php 
class Register_RegisterealizedController extends Zend_Controller_Action {

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="register"){
 			$this->_helper->redirector('index','index','default');
 		}
 		$this->sesion = $login;
 		// print_r($this->sesion = $login);
 		// exit();

	}

	public function indexAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid=$this->sesion->period->perid;
			// $escid=$this->sesion->escid;
			$subid=$this->sesion->subid;
			// $uid=$this->sesion->uid;
			$escid='4SI';
 			$uid='0924401019';
 			$state='M';
 			$states='C';
			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid,'state'=>$state,'state'=>$states);
			$attrib=array('escid','perid','courseid','turno','notafinal','state');
	
			$dbgc= new Api_Model_DbTable_Registrationxcourse();
			$data=$dbgc->_getFilter($where,$attrib,'courseid');
			print_r($data);exit();
			
		} catch (Exception $e) {
			print "Error: get Registers".$e->getMessage();
		}

	}
}
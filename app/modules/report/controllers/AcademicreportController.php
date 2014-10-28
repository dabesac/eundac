<?php
class Report_AcademicreportController extends Zend_Controller_Action{
	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
	}

	public function indexAction(){
		try{
			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
			$where['eid'] = $eid;
			$where['oid'] = $oid;
			$anio =date('Y');
			$confaculty = new Api_Model_DbTable_Faculty();
			$datafac = $confaculty ->_getfilter($where);

			$this->view->faculty = $datafac;
			$this->view->anio = $anio;	
		} catch(Exception $e){
			print "Error: ".$e->getMessage();
		}
	}

	public function schoolsAction(){
		try{
			
		}catch(Exception $e){
			print 'Error; '.$e->getMessage();
		}
	}
}
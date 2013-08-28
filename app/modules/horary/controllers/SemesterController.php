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
 		$this->sesion = $login;
 		require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');
	}

	public function indexAction(){

		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			// $escid=$this->sesion->escid;
			$escid='4SI';
			// $perid='13B';
			$perid=$this->sesion->period->perid;
			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'perid'=>$perid);
			$sem= new Api_Model_DbTable_Semester();
			$dsem=$sem->_getSemesterXPeriodsXEscid($where);
			// print_r($dsem);exit();
			$this->view->semester=$dsem;
		} catch (Exception $e) {
			print "Error: get semester".$e->getMessage();
		}
	}

	public function horarysemesterAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			// $escid=$this->sesion->escid;
			$semid=$this->_getParam('semid');
			$escid='4SI';
			// $semid='3';
			$subid=$this->sesion->subid;
			$perid=$this->sesion->period->perid;
			$this->view->semid=$semid;

			$base_url = 'http://localhost:8080/';
	        $endpoint = '/s1st3m4s/und4c/horary_course';
	        $data = array('escid' => $escid,'eid' =>$eid,'oid' =>$oid,'perid'=>$perid,'subid'=>$subid,'semid'=>$semid);
	        $client = new Zend_Rest_Client($base_url);
	        $httpClient = $client->getHttpClient();
	        $httpClient->setConfig(array("timeout" => 1800));
	        $response = $client->restget($endpoint,$data);
	        $lista=$response->getBody();
	        $data = Zend_Json::decode($lista);
        	$this->view->horarys=$data; 

        	$where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'semid'=>$semid);
        	$cper= new Api_Model_DbTable_PeriodsCourses();
        	$dcur=$cper->_getCoursesxPeriodxspecialityxsemester($where);
        	$this->view->dcurso=$dcur;
        	// print_r($dcur);exit();

			
		} catch (Exception $e) {
			print "Error: get horary semester".$e->getMessage();
		}

	}
}
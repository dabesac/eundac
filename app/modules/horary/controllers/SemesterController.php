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
			$perid='13A';
			// $perid=$this->sesion->period->perid;
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
			$this->view->eid=$eid;
			$oid=$this->sesion->oid;
			$this->view->oid=$oid;
			$escid=$this->sesion->escid;
			$semid=$this->_getParam('semid');
			// $semid='8';
			$subid=$this->sesion->subid;
			// $perid=$this->sesion->period->perid;
			$perid='13A';
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
	        // print_r($data);exit();
        	$this->view->horarys=$data; 

        	$where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'perid'=>$perid,'escid'=>$escid,'semid'=>$semid);
        	$cper= new Api_Model_DbTable_PeriodsCourses();
        	$dcur=$cper->_getCoursesxPeriodxspecialityxsemester($where);
        	// $this->view->dcurso=$dcur;

        	$len=count($dcur);
        	for ($i=0; $i < $len; $i++) { 
        		$uid=$dcur[$i]['uid'];
        		$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
        		$user= new Api_Model_DbTable_Users();
        		$duser=$user->_getUserXUid($where);
        		$dcur[$i]['namet']= $duser[0]['last_name0']." ".$duser[0]['last_name1'].", ".$duser[0]['first_name'];
        		// print_r($dcur);
        	}
        	$this->view->dcurso=$dcur;

			
		} catch (Exception $e) {
			print "Error: get horary semester".$e->getMessage();
		}

	}

	public function printhorarysemesterAction(){
		$this->_helper->layout()->disableLayout();
		$eid=$this->sesion->eid;
		$oid=$this->sesion->oid;
		$escid=$this->sesion->escid;
		$facid=$this->sesion->facid;
		// $facid='4';
		// $escid='4SI';
		$semid=base64_decode($this->_getParam('semid'));
		$subid=$this->sesion->subid;
		// $perid=$this->sesion->period->perid;
		$perid='13A';
		$this->view->semid=$semid;

		$base_url = 'http://localhost:8080/';
	    $endpoint = '/s1st3m4s/und4c/horary_course';
	    $data = array('escid' => $escid,'eid' =>$eid,'oid' =>$oid,'perid'=>$perid,'subid'=>$subid,'semid'=>$semid);
	    // print_r($data);
	    $client = new Zend_Rest_Client($base_url);
	    $httpClient = $client->getHttpClient();
	    $httpClient->setConfig(array("timeout" => 1800));
	    $response = $client->restget($endpoint,$data);
	    $lista=$response->getBody();
	    $data = Zend_Json::decode($lista);
     
        $this->view->horarys=$data;
        $spe=array();
        $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        $esc = new Api_Model_DbTable_Speciality();
        $desc = $esc->_getOne($where);
        $this->view->desc=$desc;
        $parent=$desc['parent'];
        	$wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        	$parentesc= $esc->_getOne($wher);
        	if ($parentesc) {
        		$pala='ESPECIALIDAD DE ';
      			$spe['esc']=$parentesc['name'];
      			$spe['parent']=$pala.$desc['name'];
        		$this->view->spe=$spe;
        	}
        	else{
        		$spe['esc']=$desc['name'];
        		$spe['parent']=''; 	
        		$this->view->spe=$spe;
        	}

        $whered=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
        $fac= new Api_Model_DbTable_Faculty();
        $dfac = $fac->_getOne($whered);
        $this->view->dfac=$dfac;

        $where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'perid'=>$perid,'escid'=>$escid,'semid'=>$semid);
        $cper= new Api_Model_DbTable_PeriodsCourses();
        $dcur=$cper->_getCoursesxPeriodxspecialityxsemester($where);

        $len=count($dcur);
        	for ($i=0; $i < $len; $i++) { 
        		$uid=$dcur[$i]['uid'];
        		$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
        		$user= new Api_Model_DbTable_Users();
        		$duser=$user->_getUserXUid($where);
        		$dcur[$i]['namet']= $duser[0]['last_name0']." ".$duser[0]['last_name1'].", ".$duser[0]['first_name'];
        		// print_r($dcur);
        	}
        $this->view->dcurso=$dcur;

	}
}
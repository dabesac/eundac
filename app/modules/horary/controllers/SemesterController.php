<?php
class Horary_SemesterController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
 		require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');
	}

	public function indexAction(){

		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=$this->sesion->escid;
			$perid=$this->sesion->period->perid;
			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'perid'=>$perid);
			$sem= new Api_Model_DbTable_Semester();
			$dsem=$sem->_getSemesterXPeriodsXEscid($where);
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
			$subid=$this->sesion->subid;
			$perid=$this->sesion->period->perid;
			$this->view->semid=$semid;

			$wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        	$bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        	$datahours=$bd_hours->_getFilter($wheres);

        	if ($datahours) {   
        		$hora=new Api_Model_DbTable_Horary();
	        	if ($datahours[0]['hours_begin_afternoon']) {
	                $valhorasm[0]=$datahours[0]['hours_begin'];
	                $valhorast[0]=$datahours[0]['hours_begin_afternoon'];
	                $k=0;
	                while ($valhorasm[$k] < $datahours[0]['hours_begin_afternoon']) {
	                    $dho=$hora->_getsumminutes($valhorasm[$k],'50');
	                    $valhorasm[$k+1]=$dho[0]['hora'];
	                    $k++;
	                }
	                $len=count($valhorasm);
	                $w=0;
	                        
	                for ($g=0; $g < $len + 1 ; $g++) { 
	                    if ($valhorasm[$g]==$valhorast[0] && $w==0) {
	                        $valhoras[0]=$datahours[0]['hours_begin'];
	                        for ($k=0; $k < 20; $k++) { 
	                            $dho=$hora->_getsumminutes($valhoras[$k],'50');
	                            $valhoras[$k+1]=$dho[0]['hora'];
	                        }
	                        $this->view->valhoras=$valhoras;
	                        $w=1;
	                    }
	                }
	                if ($w==0) {
	                	unset($valhorasm[$k]);
	                    $this->view->valhorasm=$valhorasm;
	                    $j=0;
	                    while ( $j < 12) {
	                        $dho=$hora->_getsumminutes($valhorast[$j],'50');
	                        $valhorast[$j+1]=$dho[0]['hora'];
	                        $j++;
	                    }
	                    $endtarde=$valhorast[$j-1];
	                    $this->view->valhorast=$valhorast; 
	                }    
            	}
            	else{
	                $valhoras[0]=$datahours[0]['hours_begin'];
	                for ($k=0; $k < 20; $k++) { 
	                    $dho=$hora->_getsumminutes($valhoras[$k],'50');
	                    $valhoras[$k+1]=$dho[0]['hora'];
	                }
	                $this->view->valhoras=$valhoras;
            	}
		        
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
	        	// print_r($dcur);exit();
	        	// $this->view->dcurso=$dcur;

	        	$len=count($dcur);
	        	for ($i=0; $i < $len; $i++) {
	        		$escid=$dcur[$i]['escid'];
	        		$curid=$dcur[$i]['curid'];
	        		$semid=$dcur[$i]['semid'];
	        		$courseid=$dcur[$i]['courseid'];
	        		$whe=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
	        		$attrib=array('courseid','semid','credits');
	        		$bdcourse = new Api_Model_DbTable_Course();
	        		$datacourse[$i]= $bdcourse->_getFilter($whe,$attrib);
	        		$uid=$dcur[$i]['uid'];
	        		$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
	        		$user= new Api_Model_DbTable_Users();
	        		$duser=$user->_getUserXUid($where);
	        		$dcur[$i]['namet']= $duser[0]['last_name0']." ".$duser[0]['last_name1'].", ".$duser[0]['first_name'];
	        	}
	        	$this->view->datacourse=$datacourse;
	        	$this->view->dcurso=$dcur;
		    }

			
		} catch (Exception $e) {
			print "Error: get horary semester".$e->getMessage();
		}

	}

	public function printhorarysemesterAction(){
		$this->_helper->layout()->disableLayout();
		$eid=$this->sesion->eid;
		$oid=$this->sesion->oid;
		$escid=$this->sesion->escid;
		$faculty=$this->sesion->faculty->name;
		$this->view->faculty=$faculty;
		$semid=base64_decode($this->_getParam('semid'));
		$subid=$this->sesion->subid;
		$perid=$this->sesion->period->perid;
		$this->view->semid=$semid;

		$wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);   
        
        if ($datahours) {
	        $valhoras[0]=$datahours[0]['hours_begin'];
		    $hora=new Api_Model_DbTable_Horary();
		    for ($k=0; $k < 20; $k++) { 
		        $dho=$hora->_getsumminutes($valhoras[$k],'50');
		        $valhoras[$k+1]=$dho[0]['hora'];
		    }
		    $this->view->valhoras=$valhoras;
		    
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
}
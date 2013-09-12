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
	}

	public function indexAction(){
		try {
			$this->_helper->layout()->disableLayout();
           	$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid=$this->sesion->period->perid;
			$escid=$this->sesion->escid;
			$this->view->escid=$escid;
			$subid=$this->sesion->subid;
			$this->view->subid=$subid;
			$uid=$this->sesion->uid;
			$this->view->uid=$uid;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid);
			$attrib=array('escid','perid','courseid','turno','notafinal','state');
			$orders=array('perid','courseid');	
			$dbgc= new Api_Model_DbTable_Registrationxcourse();
			$data=$dbgc->_getFilter($where,$attrib,$orders);
			$len=count($data);
			$dbperiod= new Api_Model_DbTable_Periods();
			$dbcourse=new Api_Model_DbTable_Course();
			
			$newperiod=array();
			$c=0;
			$aux=$data[0]['perid'];
			$newperiod[0]=$aux;
			foreach ($data as $periods) {
				$perid=$periods['perid'];				 
				if ($perid!=$aux) {
					$c++;	
					$newperiod[$c]=$perid;
					$aux=$perid;
				}
			}
			$l=count($newperiod);
			for ($f=0; $f < $l ; $f++) { 
				$whered=array('eid'=>$eid,'oid'=>$oid,'perid'=>$newperiod[$f]);
				$attrib=array('perid','name');
				$dasd=$dbperiod->_getFilter($whered,$attrib);
				$newperiod[$f]=$dasd[0];
			}
			// print_r($newperiod);s
			for ($i=0; $i < $len; $i++) {
				$courseid=$data[$i]['courseid'];
				$whered=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name','semid','credits');
				$datac=$dbcourse->_getFilter($whered,$attrib);
				$datacourse[$i]=$datac[0];
			}
			// print_r($datacourse);
			// print_r($data);exit();
			$this->view->newperiod=$newperiod;
			$this->view->datacourse=$datacourse;
			$this->view->data=$data;
			
		} catch (Exception $e) {
			print "Error: get Registers".$e->getMessage();
		}

	}

	public function registerprintAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$uid=base64_decode($this->_getParam('uid'));
			$this->view->uid=$uid;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid);
			$attrib=array('escid','perid','courseid','turno','notafinal','state');
			$orders=array('perid','courseid');	
			$dbgc= new Api_Model_DbTable_Registrationxcourse();
			$data=$dbgc->_getFilter($where,$attrib,$orders);
			$len=count($data);
			$dbperiod= new Api_Model_DbTable_Periods();
			$dbcourse=new Api_Model_DbTable_Course();
			
			$newperiod=array();
			$c=0;
			$aux=$data[0]['perid'];
			$newperiod[0]=$aux;
			foreach ($data as $periods) {
				$perid=$periods['perid'];				 
				if ($perid!=$aux) {
					$c++;	
					$newperiod[$c]=$perid;
					$aux=$perid;
				}
			}
			$l=count($newperiod);
			for ($f=0; $f < $l ; $f++) { 
				$whered=array('eid'=>$eid,'oid'=>$oid,'perid'=>$newperiod[$f]);
				$attrib=array('perid','name');
				$dasd=$dbperiod->_getFilter($whered,$attrib);
				$newperiod[$f]=$dasd[0];
			}
			// print_r($newperiod);s
			for ($i=0; $i < $len; $i++) {
				$courseid=$data[$i]['courseid'];
				$whered=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name','semid','credits');
				$datac=$dbcourse->_getFilter($whered,$attrib);
				$datacourse[$i]=$datac[0];
			}
			
			$this->view->newperiod=$newperiod;
			$this->view->datacourse=$datacourse;
			$this->view->data=$data;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
			$spe=array();
		    $dbspeciality = new Api_Model_DbTable_Speciality();
		    $speciality = $dbspeciality ->_getOne($where);
		    $parent=$speciality['parent'];
		    $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
		    $parentesc= $dbspeciality->_getOne($wher);
		    if ($parentesc) {
		        $pala='ESPECIALIDAD DE ';
		        $spe['esc']=$parentesc['name'];
		        $spe['parent']=$pala.$speciality['name'];
		        $this->view->spe=$spe;
		    }
		    else{
		        $spe['esc']=$speciality['name'];
		        $spe['parent']='';  
		        $this->view->spe=$spe;
		    }
		    $whered['eid']=$eid;
		    $whered['oid']=$oid;
		    $whered['facid']= $speciality['facid'];
		    $dbfaculty = new Api_Model_DbTable_Faculty();
		    $faculty = $dbfaculty ->_getOne($whered);
		    $this->view->faculty=$faculty;      
		    $wheres=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
		    $dbperson = new Api_Model_DbTable_Users();
		    $person= $dbperson -> _getUserXUid($wheres);
		   	$this->view->person=$person;
					
		} catch (Exception $e) {
			print "Error: Print".$e->getMessage();
		}

	}

	public function printperiodAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$uid=base64_decode($this->_getParam('uid'));
			$this->view->uid=$uid;
			$perid=base64_decode($this->_getParam('perid'));
			$this->view->perid=$perid;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid,'perid'=>$perid);
			$attrib=array('escid','perid','courseid','turno','notafinal','state');
			$orders=array('perid','courseid');	
			$dbgc= new Api_Model_DbTable_Registrationxcourse();
			$data=$dbgc->_getFilter($where,$attrib,$orders);

			$len=count($data);
			$wher=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid);
			$attrib=array('perid','name');
			$dbperiod=new Api_Model_DbTable_Periods();
			$dataperiod=$dbperiod->_getFilter($wher,$attrib);
			// print_r($dataperiod);exit();
			$dbcourse=new Api_Model_DbTable_Course();
			
			for ($i=0; $i < $len; $i++) {
				$courseid=$data[$i]['courseid'];
				$whered=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name','semid','credits');
				$datac=$dbcourse->_getFilter($whered,$attrib);
				$datacourse[$i]=$datac[0];
			}
			// print_r($datacourse);
			// print_r($data);exit();
			$this->view->dataperiod=$dataperiod;
			$this->view->datap=$datap;
			$this->view->datacourse=$datacourse;
			$this->view->data=$data;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
			$spe=array();
		    $dbspeciality = new Api_Model_DbTable_Speciality();
		    $speciality = $dbspeciality ->_getOne($where);
		    $parent=$speciality['parent'];
		    $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
		    $parentesc= $dbspeciality->_getOne($wher);
		    if ($parentesc) {
		        $pala='ESPECIALIDAD DE ';
		        $spe['esc']=$parentesc['name'];
		        $spe['parent']=$pala.$speciality['name'];
		        $this->view->spe=$spe;
		    }
		    else{
		        $spe['esc']=$speciality['name'];
		        $spe['parent']='';  
		        $this->view->spe=$spe;
		    }
		    $whered['eid']=$eid;
		    $whered['oid']=$oid;
		    $whered['facid']= $speciality['facid'];
		    $dbfaculty = new Api_Model_DbTable_Faculty();
		    $faculty = $dbfaculty ->_getOne($whered);
		    $this->view->faculty=$faculty;      
		    $wheres=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
		    $dbperson = new Api_Model_DbTable_Users();
		    $person= $dbperson -> _getUserXUid($wheres);
		   	$this->view->person=$person;


		} catch (Exception $e) {
			print "Error: Print".$e->getMessage();
		}

	}
}
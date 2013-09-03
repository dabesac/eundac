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
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid=$this->sesion->period->perid;
			$escid=$this->sesion->escid;
			$subid=$this->sesion->subid;
			$uid=$this->sesion->uid;

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
			$this->view->datap=$datap;
			$this->view->datacourse=$datacourse;
			$this->view->data=$data;
			
		} catch (Exception $e) {
			print "Error: get Registers".$e->getMessage();
		}

	}
}